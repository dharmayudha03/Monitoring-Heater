/**
 * TUNGYU THERMOFORMING HEATER PREDICTIVE MAINTENANCE - ESP32 FIRMWARE
 * Version: v12.4_POSTGRES_LOCAL_INDEPENDENT_CONTACTOR_SYNC (Merged into monitoring_heater.ino)
 * -----------------------------------------------------------------
 * Fitur Utama v12.4:
 * 1. Integrasi langsung ke Aux Contact NO Kontaktor 1 (Pin 22) & Kontaktor 2 (Pin 23).
 * 2. Logika LOW = Kontaktor ON (Real-time update), HIGH = Kontaktor OFF (Lock/Hold last value).
 * 3. Fitur Peak-Hold Pintar mempermudah kalibrasi multiplier saat heater mati-nyala (cycling).
 * 4. Hapus total bug auto-reset Wi-Fi akibat noise elektromagnetik panel.
 * -----------------------------------------------------------------
 */

#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include <WiFiManager.h> 

// =================================================================
// 1. KONFIGURASI WEB SERVER LARAVEL API
// =================================================================
const String WEB_SERVER_URL = "http://192.168.11.107/heater-monitoring-system";
float upper_baseline = 10.939; 
float lower_baseline = 10.939;

// =================================================================
// 2. KONFIGURASI SENSOR & VARIABEL GLOBAL MULTIPLIER
// =================================================================
const double SENSOR_RATIO     = 30; // 30A / 1V (rasio SCT-013-030)
const int TOTAL_SENSOR = 6;
const int SENSOR_PINS[TOTAL_SENSOR] = {32, 33, 34, 35, 36, 39};

const String sensorIDs[TOTAL_SENSOR]  = {"CT01","CT02","CT03","CT04","CT05","CT06"};
const String zonaNames[TOTAL_SENSOR]  = {
  "Upper Mold RS", "Upper Mold ST", "Upper Mold TR",
  "Lower Mold RS", "Lower Mold ST", "Lower Mold TR"
};

double remoteMultiplier[TOTAL_SENSOR] = {2.681, 2.480, 3.013, 3.171, 3.199, 2.989};

// =================================================================
// TIMING CONTROL (BAGIAN GLOBAL)
// =================================================================
unsigned long lastExecutionTime = 0;
const unsigned long executionInterval = 300000; // Kirim ke Laravel tiap 5 menit (300000 ms)

unsigned long lastSamplingTime = 0;
const unsigned long samplingInterval = 5000;    // Baca sensor & pantau kontaktor tiap 5 detik (5000 ms)


unsigned long lastFetchMultiplierTime = 0;
const unsigned long fetchMultiplierInterval = 60000; // Dinaikkan ke 1 menit agar meringankan beban HTTP

unsigned long lastWiFiCheck = 0;
const unsigned long wifiCheckInterval = 30000; // Cek status Wi-Fi tiap 30 detik
bool sedangMencobaKoneksi = false;


// Deklarasi Prototip Fungsi
double bacaIRMSInternalADC(int pin, int sensorIndex, bool showDebug);
void kirimKeLaravelBulk(double arusHasil[]);
void ambilKonfigurasiSistem();

// =================================================================
// VARIABLE UNTUK INTERLOCK REKAYASA KONTAKTOR (PADA BAGIAN GLOBAL)
// =================================================================
const int PIN_KONTAKTOR_1 = 22; // Menangani CT1, CT2, CT3 (Upper Mold)
const int PIN_KONTAKTOR_2 = 23; // Menangani CT4, CT5, CT6 (Lower Mold)

// Menyimpan nilai arus aktif terakhir untuk kebutuhan kalibrasi saat heater OFF
double arusTerakhir[TOTAL_SENSOR] = {0.0, 0.0, 0.0, 0.0, 0.0, 0.0};

// Debounce filter fisik untuk menghindari noise/getaran kontaktor saat berpindah posisi
unsigned long waktuDebounceTerakhir = 0;
const unsigned long JEDA_DEBOUNCE = 100; // Dinaikkan ke 100ms agar lebih kebal noise panel

// Variable global untuk Smart Logging
unsigned long waktuLogTerakhir = 0;
const unsigned long INTERVAL_LOG_RUTIN = 3600000; 

String statusTerakhirUpper = "NORMAL";
String statusTerakhirLower = "NORMAL";

// =================================================================
// 3. SETUP
// =================================================================
void setup() {
  Serial.begin(115200);
  delay(1000);

  Serial.println(F("\n\n=================================================="));
  Serial.println(F("   TUNGYU HEATER PREDICTIVE MAINTENANCE            "));
  Serial.println(F("   Firmware v12.4 - Merged into monitoring_heater  "));
  Serial.println(F("=================================================="));

  // Konfigurasi Input Interlock Kontaktor
  pinMode(PIN_KONTAKTOR_1, INPUT_PULLUP);
  pinMode(PIN_KONTAKTOR_2, INPUT_PULLUP);

  // -----------------------------------------------------------------
  // ORKESTRASI SMART WIFIMANAGER PORTAL CONFIGURATION
  // -----------------------------------------------------------------
  WiFiManager wm;
  wm.setConfigPortalTimeout(180); // Batasi portal 3 menit

  Serial.println("[WIFI] Memulai autoconnect / menyalakan AP Portal...");
  if (!wm.autoConnect("Tungyu-Heater-IoT")) {
    Serial.println(">>> [WIFI EROR] Gagal konek & portal timeout. Merestart ESP32...");
    delay(3000);
    ESP.restart();
  }

  Serial.println("\n[WIFI] Sukses Terhubung! IP Perangkat: " + WiFi.localIP().toString());
  analogReadResolution(12);
  ambilKonfigurasiSistem();

  Serial.println("[STATUS] Setup selesai. Memulai pemantauan...\n");
}


// =================================================================
// 4. LOOP UTAMA (VERSI v12.5 - FAST SAMPLING & 5-MINUTES TRANSMISSION)
// =================================================================
void loop() {
  unsigned long currentMillis = millis();

  // --- A. KONTROL PENGECEKAN KONEKSI WI-FI (NON-BLOCKING & ANTI-LAG) ---
  if (currentMillis - lastWiFiCheck >= wifiCheckInterval) {
    lastWiFiCheck = currentMillis;
    
    if (WiFi.status() != WL_CONNECTED) {
      Serial.println(F("[WIFI WARN] Koneksi terputus!"));
      
      if (!sedangMencobaKoneksi) {
        Serial.println(F("[WIFI] Mencoba menyambung kembali di latar belakang..."));
        sedangMencobaKoneksi = true;
        WiFi.begin(); 
      }
    } else {
      if (sedangMencobaKoneksi) {
        Serial.println(F("[WIFI] Terhubung Kembali dengan Sukses!"));
        sedangMencobaKoneksi = false;
      }
    }
  }

  // --- B. SIKLUS PEMBACAAN SENSOR & MONITORING KONTAKTOR (TIAP 5 DETIK) ---
  // Bagian ini berjalan independen dari waktu pengiriman data ke Laravel API
  if (currentMillis - lastSamplingTime >= samplingInterval) {
    lastSamplingTime = currentMillis;

    int statusK1 = digitalRead(PIN_KONTAKTOR_1); // LOW = Kontaktor 1 AKTIF (ON)
    int statusK2 = digitalRead(PIN_KONTAKTOR_2); // LOW = Kontaktor 2 AKTIF (ON)

    for (int i = 0; i < TOTAL_SENSOR; i++) {
      bool kontaktorAktif = false;

      // Tentukan kelompok sensor dikontrol oleh kontaktor mana
      if (i < 3) {
        kontaktorAktif = (statusK1 == LOW); // CT1, CT2, CT3 dipantau K1
      } else {
        kontaktorAktif = (statusK2 == LOW); // CT4, CT5, CT6 dipantau K2
      }

      if (kontaktorAktif) {
        // Set 'false' agar log serial tidak penuh karena dibaca tiap 5 detik
        double arusSeketika = bacaIRMSInternalADC(SENSOR_PINS[i], i, false); 
        
        // Proteksi noise induksi panel: Hanya catat jika arus riil terdeteksi > 0.5 A
        if (arusSeketika > 0.5) {
          arusTerakhir[i] = arusSeketika; // Kunci & amankan nilai aktif terbaru ke memori RAM
        }
      }
    }
  }

  // --- C. SYNC CONFIGURATION FROM LARAVEL SERVER (INTERVAL BAWAAN) ---
  if (lastFetchMultiplierTime == 0 || (currentMillis - lastFetchMultiplierTime >= fetchMultiplierInterval)) {
    lastFetchMultiplierTime = currentMillis;
    ambilKonfigurasiSistem();
  }

  // --- D. SIKLUS EVALUASI & PENGIRIMAN DATA LIVE BULK (TIAP 5 MENIT) ---
  // Blok ini HANYA dieksekusi saat interval 5 menit (executionInterval) terpenuhi
  if (lastExecutionTime == 0 || (currentMillis - lastExecutionTime >= executionInterval)) {
    lastExecutionTime = currentMillis;

    Serial.println(F("\n================================================="));
    Serial.println(F("[BATCH SEND] Jadwal 5 Menit Tercapai! Mengemas Data..."));
    Serial.println(F("================================================="));

    // Siapkan array temporer khusus untuk dikirimkan ke fungsi API bulk Laravel
    double arusKirim[TOTAL_SENSOR];

    for (int i = 0; i < TOTAL_SENSOR; i++) {
      // Ambil data dari laci memori 'arusTerakhir' yang selalu diperbarui oleh roda sampling 5 detik
      arusKirim[i] = arusTerakhir[i]; 
      
      Serial.print("  -> " + sensorIDs[i] + " [" + zonaNames[i] + "]: ");
      Serial.print(String(arusKirim[i], 3));
      Serial.println(arusKirim[i] > 0.0 ? " A [DATA BEBAN]" : " A [OFF / BELUM ADA DATA]");
    }
    
    // Mengirim array hasil penangkapan sampling terbaik ke database PostgreSQL lokal Anda
    kirimKeLaravelBulk(arusKirim);
    Serial.println(F("=================================================\n"));
  }
}

// =================================================================
// 5. PENGIRIMAN DATA LIVE BULK KE LARAVEL POSTGRES (SAFE HTTP POST)
// =================================================================
void kirimKeLaravelBulk(double arusHasil[]) {
  if (WiFi.status() == WL_CONNECTED) {
    WiFiClient client; // SOLUSI: Objek client ditambahkan untuk mencegah crash memori
    HTTPClient http;
    String url = WEB_SERVER_URL + "/api/v1/heaters/bulk";
    
    http.begin(client, url); // SOLUSI: Memulai koneksi dengan client
    http.addHeader("Content-Type", "application/json");
    http.setTimeout(3000);   // SOLUSI: Batasi timeout maksimal 3 detik agar Watchdog tidak memicu reset

    // Optimasi payload pembentukan String
    String jsonPayload;
    jsonPayload.reserve(300); // Alokasikan memori awal agar tidak terjadi fragmentasi RAM
    jsonPayload = "{\"logs\":[";
    for (int i = 0; i < TOTAL_SENSOR; i++) {
      jsonPayload += "{\"heater_code\":\"" + sensorIDs[i] + "\",\"current\":" + String(arusHasil[i], 3) + "}";
      if (i < TOTAL_SENSOR - 1) jsonPayload += ",";
    }
    jsonPayload += "]}";
    
    int httpResponseCode = http.POST(jsonPayload);
    if (httpResponseCode > 0) {
      Serial.print("[LARAVEL API] Sukses Kirim Data Bulk! Code: ");
      Serial.println(httpResponseCode);
    } else {
      Serial.print("[LARAVEL API] Gagal Kirim Data Bulk, Error: ");
      Serial.println(http.errorToString(httpResponseCode).c_str());
    }
    http.end();
  }
}

// =================================================================
// 6. SINKRONISASI KONFIGURASI KALIBRASI DARI LARAVEL (SAFE HTTP GET)
// =================================================================
void ambilKonfigurasiSistem() {
  if (WiFi.status() == WL_CONNECTED) {
    WiFiClient client; // SOLUSI: Objek client ditambahkan untuk mencegah crash memori
    HTTPClient http;
    String url = WEB_SERVER_URL + "/api/v1/heaters/konfigurasi-sistem";

    http.begin(client, url); // SOLUSI: Memulai koneksi dengan client
    http.setTimeout(3000);   // SOLUSI: Batasi timeout maksimal 3 detik agar Watchdog tidak memicu reset
    
    int httpResponseCode = http.GET();
    if (httpResponseCode == 200) {
      String payload = http.getString();
      JsonDocument doc;
      DeserializationError error = deserializeJson(doc, payload);

      if (!error) {
        if (doc.containsKey("m_ct1")) remoteMultiplier[0] = doc["m_ct1"].as<double>();
        if (doc.containsKey("m_ct2")) remoteMultiplier[1] = doc["m_ct2"].as<double>();
        if (doc.containsKey("m_ct3")) remoteMultiplier[2] = doc["m_ct3"].as<double>();
        if (doc.containsKey("m_ct4")) remoteMultiplier[3] = doc["m_ct4"].as<double>();
        if (doc.containsKey("m_ct5")) remoteMultiplier[4] = doc["m_ct5"].as<double>();
        if (doc.containsKey("m_ct6")) remoteMultiplier[5] = doc["m_ct6"].as<double>();

        if (doc.containsKey("upper_baseline")) upper_baseline = doc["upper_baseline"].as<float>();
        if (doc.containsKey("lower_baseline")) lower_baseline = doc["lower_baseline"].as<float>();

        Serial.println(F("[SYNC LARAVEL] Sukses Sinkronisasi Multiplier & Baseline dari Database!"));
      } else {
        Serial.print(F("[SYNC LARAVEL] Gagal parsing JSON: "));
        Serial.println(error.c_str());
      }
    } else if (httpResponseCode > 0) {
      Serial.print(F("[SYNC LARAVEL] HTTP GET Gagal, Code: "));
      Serial.println(httpResponseCode);
    }
    http.end();
  }
}

// =================================================================
// 7. FUNGSI BACA ADC DENGAN MULTIPLIER LIVE
// =================================================================
double bacaIRMSInternalADC(int pin, int sensorIndex, bool showDebug) {
  uint32_t totalMVolt = 0;
  int sample = 40;
  for(int i = 0; i < sample; i++) {
    totalMVolt += analogReadMilliVolts(pin);
    delay(1);
  }
  double mVoltRaw = (double)totalMVolt / (double)sample;

  double lowCutoff = 15.0; 
  double voltSCT = 0.0;
  
  double currentMultiplier = remoteMultiplier[sensorIndex];
  if (mVoltRaw > lowCutoff) {
    double mVoltSinyal = mVoltRaw;
    voltSCT = (mVoltSinyal * currentMultiplier) / 1000.0;
  }

  double irms = voltSCT * SENSOR_RATIO;
  if (irms < 0.50) {
    irms = 0.0;
  }

  if (showDebug) {
    Serial.print("[DEBUG] Pin " + String(pin));
    Serial.print(" | Raw ADC: " + String(mVoltRaw, 0) + " mV");
    Serial.print(" | Live Multiplier: " + String(currentMultiplier, 3));
    Serial.print(" | Arus: " + String(irms, 3) + " A");
    Serial.println();
  }

  return irms;
}
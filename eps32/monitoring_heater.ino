/**
 * TUNGYU THERMOFORMING HEATER PREDICTIVE MAINTENANCE - ESP32 FIRMWARE
 * Version: v12.0_POSTGRES_LOCAL (DIRECT LOCAL LARAVEL API - NO FIREBASE)
 * -----------------------------------------------------------------
 * Riwayat singkat:
 * - v12.0_POSTGRES_LOCAL : Hubungkan ESP32 langsung ke API web Laravel lokal (PostgreSQL).
 *                          Bypass Firebase sepenuhnya untuk ketersambungan independen.
 * -----------------------------------------------------------------
 * PENTING: Ganti URL WEB_SERVER_URL di bawah sesuai IP local komputer server XAMPP Anda.
 */

#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include <WiFiManager.h> 

// =================================================================
// 1. KONFIGURASI WEB SERVER LARAVEL API
// =================================================================
// Contoh IP XAMPP Server lokal Anda (Ubah ke IP komputer server Anda, atau gunakan domain ngrok/serveo)
const String WEB_SERVER_URL = "http://192.168.11.107/heater-monitoring-system"; 

// Baseline acuan untuk fungsi Smart Logging (Diupdate otomatis dari database Laravel)
float upper_baseline = 10.939; 
float lower_baseline = 10.939;

// =================================================================
// 2. KONFIGURASI SENSOR & VARIABEL GLOBAL MULTIPLIER
// =================================================================
const bool DEMO_MODE = false;
const double SENSOR_RATIO     = 30; // 30A / 1V (rasio SCT-013-030)
const int TOTAL_SENSOR = 6;
const int SENSOR_PINS[TOTAL_SENSOR] = {32, 33, 34, 35, 36, 39};

const String sensorIDs[TOTAL_SENSOR]  = {"CT01","CT02","CT03","CT04","CT05","CT06"};
const String zonaNames[TOTAL_SENSOR]  = {
  "Upper Mold RS", "Upper Mold ST", "Upper Mold TR",
  "Lower Mold RS", "Lower Mold ST", "Lower Mold TR"
};

// Variabel Penampung Nilai Multiplier Jarak Jauh (Default Awal, bisa dikalibrasi via Web Settings)
double remoteMultiplier[TOTAL_SENSOR] = {2.681, 2.480, 3.013, 3.171, 3.199, 2.989};

// Manajemen Waktu Siklus (Pembacaan & Logging)
unsigned long lastExecutionTime = 0;
const unsigned long executionInterval = 5000; // Pembacaan data sensor tiap 5 detik

unsigned long lastFetchMultiplierTime = 0;
const unsigned long fetchMultiplierInterval = 10000; // Sinkronisasi otomatis kalibrasi dari Laravel database tiap 10 detik

unsigned long lastWiFiCheck = 0;
const unsigned long wifiCheckInterval = 30000; // Cek status Wi-Fi tiap 30 detik

// Deklarasi Prototip Fungsi
double bacaIRMSInternalADC(int pin, int sensorIndex, bool showDebug);
double bacaIRMS_Demo(int sensorIndex);
void kirimKeLaravelBulk(double arusHasil[]);
void ambilKonfigurasiSistem();

// =================================================================
// VARIABLE UNTUK INTERLOCK REKAYASA GPIO 22
// =================================================================
const int PIN_TOMBOL_MODE = 22; // GPIO 22 sebagai input tombol
bool sistemAktif = true;            // Status awal sistem (Default Jalan)
int statusTombolTerakhir = HIGH; // Menggunakan PULLUP (HIGH berarti belum ditekan)
unsigned long waktuDebounceTerakhir = 0;
const unsigned long JEDA_DEBOUNCE = 50; // 50 milidetik untuk kestabilan tombol

// Variable global untuk Smart Logging
unsigned long waktuLogTerakhir = 0;
const unsigned long INTERVAL_LOG_RUTIN = 3600000; // 1 Jam dalam milidetik

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
  Serial.println(F("   Firmware v12.0 - Direct Postgres (Laravel API)  "));
  Serial.println(F("=================================================="));

  pinMode(PIN_TOMBOL_MODE, INPUT_PULLUP);

  // -----------------------------------------------------------------
  // ORKESTRASI SMART WIFIMANAGER PORTAL CONFIGURATION
  // -----------------------------------------------------------------
  WiFiManager wm;
  
  // Batasi waktu tunggu portal 3 menit (180 detik). 
  wm.setConfigPortalTimeout(180); 

  // Jika Wi-Fi pabrik putus/ganti, ESP32 otomatis memancarkan Wi-Fi AP mandiri
  Serial.println("[WIFI] Memulai autoconnect / menyalakan AP Portal...");
  if (!wm.autoConnect("Tungyu-Heater-IoT")) {
    Serial.println(">>> [WIFI EROR] Gagal konek & portal timeout. Merestart ESP32...");
    delay(3000);
    ESP.restart();
  }

  // Jika lolos baris ini, berarti sukses terkoneksi ke jaringan Wi-Fi
  Serial.println("\n[WIFI] Sukses Terhubung! IP Perangkat: " + WiFi.localIP().toString());

  analogReadResolution(12);
  ambilKonfigurasiSistem();

  Serial.println("[STATUS] Setup selesai. Memulai pemantauan...\n");
}

// =================================================================
// 4. LOOP UTAMA
// =================================================================
void loop() {
  unsigned long currentMillis = millis();

  // --- A. KONTROL PENGECEKAN KONEKSI WI-FI SECARA BERKALA (NON-BLOCKING) ---
  if (currentMillis - lastWiFiCheck >= wifiCheckInterval) {
    lastWiFiCheck = currentMillis;
    if (WiFi.status() != WL_CONNECTED) {
      Serial.println("[WIFI WARN] Koneksi terputus! Mencoba menyambung kembali...");
      WiFi.disconnect();
      WiFi.reconnect();
    }
  }
  
  // --- B. ENGINE PEMBACAAN TOMBOL SAKELAR (GPIO 22) - DUAL FUNCTION (SHORT & LONG PRESS) ---
  int bacaTombol = digitalRead(PIN_TOMBOL_MODE);
  static unsigned long waktuTekanTombol = 0;
  static bool tombolSedangDitekan = false;

  if (bacaTombol == LOW) {
    if (!tombolSedangDitekan) {
      waktuTekanTombol = currentMillis;
      tombolSedangDitekan = true;
    } else {
      // Jika ditekan lama (5 detik), hapus setelan Wi-Fi dan restart untuk memicu portal
      if (currentMillis - waktuTekanTombol >= 5000) {
        Serial.println(F("\n>>> [WIFI RESET] Tombol ditekan 5 detik! Menghapus setelan Wi-Fi dan restart..."));
        WiFiManager wm;
        wm.resetSettings();
        delay(1000);
        ESP.restart();
      }
    }
  } else { // HIGH (Dilepas)
    if (tombolSedangDitekan) {
      unsigned long lamaDitekan = currentMillis - waktuTekanTombol;
      tombolSedangDitekan = false;
      
      // Jika ditekan singkat (di bawah 5 detik dan di atas jeda debounce)
      if (lamaDitekan >= JEDA_DEBOUNCE && lamaDitekan < 5000) {
        sistemAktif = !sistemAktif; // Toggle status ON <-> OFF
        Serial.print(">>> [MODE SWITCH] Status Sistem Berubah! Aktif = ");
        Serial.println(sistemAktif ? "YES" : "NO");
      }
    }
  }

  // --- C. INTERLOCK FILTER: BYPASS JIKA MODE MAINTENANCE ---
  if (!sistemAktif) {
    delay(100);
    return; // Kembalikan eksekusi ke atas loop
  }

  // --- D. SYNC CONFIGURATION FROM LARAVEL SERVER ---
  if (currentMillis - lastFetchMultiplierTime >= fetchMultiplierInterval) {
    lastFetchMultiplierTime = currentMillis;
    ambilKonfigurasiSistem();
  }

  if (lastExecutionTime != 0 && (currentMillis - lastExecutionTime < executionInterval)) {
    return;
  }
  lastExecutionTime = currentMillis;

  Serial.println("\n--- Memulai Siklus Pembacaan & Pengiriman Data ---");
  double arus[TOTAL_SENSOR];

  for (int i = 0; i < TOTAL_SENSOR; i++) {
    if (DEMO_MODE) {
      arus[i] = bacaIRMS_Demo(i);
    } else {
      arus[i] = bacaIRMSInternalADC(SENSOR_PINS[i], i, true);
    }
    Serial.println("[MONITOR] " + sensorIDs[i] + " [" + zonaNames[i] + "]: " + String(arus[i], 3) + " A");
    delay(50); 
  }
  
  // Kirim data langsung ke Laravel API
  kirimKeLaravelBulk(arus);

  Serial.println("--- Siklus Selesai ---");
}

// =================================================================
// 5. FUNGSI PENGIRIMAN DATA LIVE BULK KE LARAVEL POSTGRES (HTTP POST)
// =================================================================
void kirimKeLaravelBulk(double arusHasil[]) {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    String url = WEB_SERVER_URL + "/api/v1/heaters/bulk";
    
    http.begin(url);
    http.addHeader("Content-Type", "application/json");
    
    // Construct bulk JSON payload matching Laravel API validator
    String jsonPayload = "{\"logs\":[";
    for (int i = 0; i < TOTAL_SENSOR; i++) {
      jsonPayload += "{";
      jsonPayload += "\"heater_code\":\"" + sensorIDs[i] + "\",";
      jsonPayload += "\"current\":" + String(arusHasil[i], 3);
      jsonPayload += "}";
      if (i < TOTAL_SENSOR - 1) jsonPayload += ",";
    }
    jsonPayload += "]}";
    
    int httpResponseCode = http.POST(jsonPayload);
    if (httpResponseCode > 0) {
      Serial.print("[LARAVEL API] Sukses Kirim Data Bulk! Code: ");
      Serial.println(httpResponseCode);
      String response = http.getString();
      Serial.println(response);
    } else {
      Serial.print("[LARAVEL API] Gagal Kirim Data Bulk, Error: ");
      Serial.println(http.errorToString(httpResponseCode).c_str());
    }
    http.end();
  }
}

// =================================================================
// 6. SINKRONISASI KONFIGURASI KALIBRASI DARI LARAVEL (HTTP GET)
// =================================================================
void ambilKonfigurasiSistem() {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    String url = WEB_SERVER_URL + "/api/v1/heaters/konfigurasi-sistem";

    http.begin(url);
    int httpResponseCode = http.GET();

    if (httpResponseCode == 200) {
      String payload = http.getString();
      JsonDocument doc;
      DeserializationError error = deserializeJson(doc, payload);

      if (!error) {
        // Parse Nilai Multiplier Kalibrasi dari database Postgres
        if (doc.containsKey("m_ct1")) remoteMultiplier[0] = doc["m_ct1"].as<double>();
        if (doc.containsKey("m_ct2")) remoteMultiplier[1] = doc["m_ct2"].as<double>();
        if (doc.containsKey("m_ct3")) remoteMultiplier[2] = doc["m_ct3"].as<double>();
        if (doc.containsKey("m_ct4")) remoteMultiplier[3] = doc["m_ct4"].as<double>();
        if (doc.containsKey("m_ct5")) remoteMultiplier[4] = doc["m_ct5"].as<double>();
        if (doc.containsKey("m_ct6")) remoteMultiplier[5] = doc["m_ct6"].as<double>();

        // Parse Nilai Baseline Adaptif
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

// =================================================================
// 8. DATA DUMMY DEMO MODE
// =================================================================
double bacaIRMS_Demo(int sensorIndex) {
  double baseValues[6] = {10.94, 10.90, 10.95, 10.94, 10.89, 10.92};
  return baseValues[sensorIndex] + ((double)random(-10, 10) / 100.0);
}
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            
            // Ambang Batas Arus (Default Baru Sesuai Hitungan Delta RST)
            $table->decimal('normal_min', 8, 2)->default(11.00);  // Normal >= 11.0 A
            $table->decimal('warning_min', 8, 2)->default(7.60); // Warning >= 7.6 A, Danger < 7.6 A
            
            // Konfigurasi Kalibrasi (Multiplier Sensor Arus CT01 - CT06)
            $table->decimal('m_ct1', 8, 3)->default(1.425);
            $table->decimal('m_ct2', 8, 3)->default(1.467);
            $table->decimal('m_ct3', 8, 3)->default(1.297);
            $table->decimal('m_ct4', 8, 3)->default(1.192);
            $table->decimal('m_ct5', 8, 3)->default(1.372);
            $table->decimal('m_ct6', 8, 3)->default(1.157);

            // Baseline Nominal (Default 11.000 A)
            $table->decimal('upper_baseline', 8, 3)->default(11.000);
            $table->decimal('lower_baseline', 8, 3)->default(11.000);

            $table->boolean('telegram_enabled')->default(true);
            $table->integer('sampling_interval')->default(5);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};

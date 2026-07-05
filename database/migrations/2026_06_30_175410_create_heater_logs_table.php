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
        Schema::create('heater_logs', function (Blueprint $table) {

            $table->id();
            $table->foreignId('heater_id')
                ->constrained('heaters')
                ->cascadeOnDelete();
            $table->string('firebase_key')->nullable();
            $table->integer('adc_value')->nullable();
            $table->decimal('current', 8, 2);
            $table->decimal('voltage', 8, 2)->nullable();
            $table->decimal('temperature', 8, 2)->nullable();
            $table->string('status')->default('NORMAL');
            $table->timestamp('received_at');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('heater_logs');
    }
};
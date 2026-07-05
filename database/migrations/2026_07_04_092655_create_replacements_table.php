<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('replacements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('heater_id')->constrained('heaters')->onDelete('cascade');
            $table->string('old_heater_code');
            $table->string('new_heater_code');
            $table->string('reason')->nullable();
            $table->string('replaced_by')->default('Teknisi Maintenance');
            $table->timestamp('replacement_date')->useCurrent();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('replacements');
    }
};

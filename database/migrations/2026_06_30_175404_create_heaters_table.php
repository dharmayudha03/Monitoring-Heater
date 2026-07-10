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
        Schema::create('heaters', function (Blueprint $table) {
            $table->id();
            $table->string('heater_code', 20)->unique();
            $table->string('machine_name', 100)->default('Injection Tungyu');
            $table->string('heater_name', 100);
            $table->string('zone', 100);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->decimal('last_current', 8, 2)->nullable();
            $table->string('last_status', 20)->nullable();
            $table->timestamp('last_received_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('heaters');
    }
};

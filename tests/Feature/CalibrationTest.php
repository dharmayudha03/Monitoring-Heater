<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Setting;
use App\Models\Heater;
use App\Models\HeaterLog;

class CalibrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_successfully_calibrates_sensor_multiplier_and_updates_recent_logs()
    {
        // 1. Create a user and authenticate
        $user = User::factory()->create([
            'role' => 'admin'
        ]);
        $this->actingAs($user);

        // 2. Setup initial settings
        $settings = Setting::create([
            'normal_min' => 9.00,
            'warning_min' => 7.60,
            'm_ct1' => 2.00,
            'm_ct2' => 2.00,
            'm_ct3' => 2.00,
            'm_ct4' => 2.00,
            'm_ct5' => 2.00,
            'm_ct6' => 2.00,
            'upper_baseline' => 11.00,
            'lower_baseline' => 11.00,
            'telegram_enabled' => false,
            'sampling_interval' => 5
        ]);

        // 3. Create a heater and a log
        $heater = Heater::create([
            'heater_code' => 'CT01',
            'machine_name' => 'Tungyu',
            'heater_name' => 'Upper Mold RS',
            'zone' => 'Upper Mold RS',
            'is_active' => true,
            'last_current' => 10.00,
            'last_status' => 'NORMAL',
            'last_received_at' => now()
        ]);

        $log = HeaterLog::create([
            'heater_id' => $heater->id,
            'current' => 10.00,
            'status' => 'NORMAL',
            'received_at' => now()
        ]);

        // 4. Send calibration request
        // Calibrating CT01: target multiplier becomes 1.5
        // New current: 10.0 * (1.5 / 2.0) = 7.50 A
        // 7.50 A is < 7.60 A, so new status should be DANGER
        $response = $this->post('/settings/calibrate', [
            'heater_code' => 'CT01',
            'type' => 'manual',
            'manual_multiplier' => '1.500',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        
        // 5. Assert database has updated settings
        $this->assertDatabaseHas('settings', [
            'm_ct1' => 1.500
        ]);

        // 6. Assert latest log is updated
        $this->assertDatabaseHas('heater_logs', [
            'id' => $log->id,
            'current' => 7.50,
            'status' => 'DANGER'
        ]);

        // 7. Assert heater real-time cache columns are updated
        $this->assertDatabaseHas('heaters', [
            'id' => $heater->id,
            'last_current' => 7.50,
            'last_status' => 'DANGER'
        ]);
    }
}

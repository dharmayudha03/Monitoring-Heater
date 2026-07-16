<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Heater;
use App\Models\HeaterLog;
use Carbon\Carbon;

class TimezoneHandlingTest extends TestCase
{
    /** @test */
    public function it_interprets_received_at_timestamp_directly_as_asia_jakarta_without_hour_shifting()
    {
        $log = new HeaterLog();
        
        // Simulate a UTC-configured Eloquent cast which returned parsed UTC Carbon instance
        $log->received_at = Carbon::parse('2026-07-16 15:56:20', 'UTC');
        
        $resolved = $log->received_at;
        
        $this->assertEquals('Asia/Jakarta', $resolved->timezoneName);
        $this->assertEquals('15:56:20', $resolved->format('H:i:s'));
        $this->assertEquals('2026-07-16', $resolved->format('Y-m-d'));
    }

    /** @test */
    public function it_interprets_heater_last_received_at_directly_as_asia_jakarta_without_hour_shifting()
    {
        $heater = new Heater();
        $heater->last_current = 11.5;
        $heater->last_status = 'NORMAL';
        $heater->last_received_at = Carbon::parse('2026-07-16 15:56:20', 'UTC');
        
        $resolved = $heater->last_received_at;
        
        $this->assertEquals('Asia/Jakarta', $resolved->timezoneName);
        $this->assertEquals('15:56:20', $resolved->format('H:i:s'));
        
        $latestLog = $heater->latest_log;
        $this->assertNotNull($latestLog);
        $this->assertEquals('15:56:20', $latestLog->time_only);
        $this->assertEquals('16-07-2026 15:56:20', $latestLog->received_at_formatted);
    }
}

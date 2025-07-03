<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SensorReading;
use Carbon\Carbon;

class SensorReadingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample sensor readings for the last 24 hours
        $now = Carbon::now();

        for ($i = 23; $i >= 0; $i--) {
            $timestamp = $now->copy()->subHours($i);

            // Generate realistic sensor data with some variation
            $baseTemp = 25 + (sin($i * 0.5) * 5); // Temperature varies between 20-30°C
            $baseHumidity = 60 + (cos($i * 0.3) * 15); // Humidity varies between 45-75%
            $baseWaterLevel = 20 + (sin($i * 0.2) * 5); // Water level varies between 15-25cm
            $baseSoilMoisture = 45 + (cos($i * 0.4) * 10); // Soil moisture varies between 35-55%

            // Add some random variation
            $temperature = $baseTemp + (rand(-10, 10) / 10);
            $humidity = max(0, min(100, $baseHumidity + (rand(-5, 5))));
            $waterLevel = max(0, min(100, $baseWaterLevel + (rand(-2, 2))));
            $soilMoisture = max(0, min(100, $baseSoilMoisture + (rand(-3, 3))));

            // Rain and fire are rare events
            $raindrop = (rand(1, 100) <= 5) ? 1 : 0; // 5% chance of rain
            $flame = (rand(1, 100) <= 1) ? 1 : 0; // 1% chance of fire

            SensorReading::create([
                'sensor_id' => 'default',
                'location' => 'main_farm',
                'temperature' => $temperature,
                'humidity' => $humidity,
                'water_level' => $waterLevel,
                'raindrop' => (bool) $raindrop,
                'soil_moisture' => $soilMoisture,
                'flame' => (bool) $flame,
                'reading_timestamp' => $timestamp,
                'additional_data' => [
                    'battery_level' => rand(60, 100),
                    'signal_strength' => rand(70, 100),
                    'device_status' => 'online'
                ]
            ]);
        }

        // Create some readings for different sensors
        $sensors = [
            ['id' => 'greenhouse_1', 'location' => 'greenhouse_1'],
            ['id' => 'outdoor_field', 'location' => 'outdoor_field'],
            ['id' => 'irrigation_zone_1', 'location' => 'irrigation_zone_1']
        ];

        foreach ($sensors as $sensor) {
            for ($i = 5; $i >= 0; $i--) {
                $timestamp = $now->copy()->subHours($i);

                SensorReading::create([
                    'sensor_id' => $sensor['id'],
                    'location' => $sensor['location'],
                    'temperature' => 25 + (rand(-50, 50) / 10),
                    'humidity' => rand(40, 80),
                    'water_level' => rand(15, 30),
                    'raindrop' => (bool) rand(0, 1),
                    'soil_moisture' => rand(30, 60),
                    'flame' => false,
                    'reading_timestamp' => $timestamp,
                    'additional_data' => [
                        'battery_level' => rand(60, 100),
                        'signal_strength' => rand(70, 100),
                        'device_status' => 'online'
                    ]
                ]);
            }
        }

        $this->command->info('Sample sensor readings created successfully!');
    }
}

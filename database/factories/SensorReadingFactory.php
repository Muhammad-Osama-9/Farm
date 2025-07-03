<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SensorReading>
 */
class SensorReadingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sensor_id' => $this->faker->randomElement(['default', 'greenhouse_1', 'outdoor_field', 'irrigation_zone_1']),
            'location' => $this->faker->randomElement(['main_farm', 'greenhouse_1', 'outdoor_field', 'irrigation_zone_1']),
            'temperature' => $this->faker->randomFloat(2, 15, 35),
            'humidity' => $this->faker->randomFloat(2, 30, 90),
            'water_level' => $this->faker->randomFloat(2, 10, 50),
            'raindrop' => $this->faker->boolean(10), // 10% chance of rain
            'soil_moisture' => $this->faker->randomFloat(2, 20, 80),
            'flame' => $this->faker->boolean(2), // 2% chance of fire
            'reading_timestamp' => $this->faker->dateTimeBetween('-24 hours', 'now'),
            'additional_data' => [
                'battery_level' => $this->faker->numberBetween(60, 100),
                'signal_strength' => $this->faker->numberBetween(70, 100),
                'device_status' => $this->faker->randomElement(['online', 'offline', 'maintenance']),
                'firmware_version' => 'v1.2.3',
                'last_maintenance' => $this->faker->dateTimeBetween('-30 days', 'now')->format('Y-m-d H:i:s')
            ]
        ];
    }

    /**
     * Indicate that the sensor reading is from a greenhouse.
     */
    public function greenhouse(): static
    {
        return $this->state(fn(array $attributes) => [
            'sensor_id' => 'greenhouse_1',
            'location' => 'greenhouse_1',
            'temperature' => $this->faker->randomFloat(2, 20, 30), // Greenhouse is warmer
            'humidity' => $this->faker->randomFloat(2, 50, 85), // Higher humidity in greenhouse
        ]);
    }

    /**
     * Indicate that the sensor reading is from an outdoor field.
     */
    public function outdoor(): static
    {
        return $this->state(fn(array $attributes) => [
            'sensor_id' => 'outdoor_field',
            'location' => 'outdoor_field',
            'temperature' => $this->faker->randomFloat(2, 10, 35), // More temperature variation
            'humidity' => $this->faker->randomFloat(2, 30, 80), // More humidity variation
            'raindrop' => $this->faker->boolean(15), // Higher chance of rain outdoors
        ]);
    }

    /**
     * Indicate that the sensor reading has an alert (fire or rain).
     */
    public function withAlert(): static
    {
        return $this->state(fn(array $attributes) => [
            'flame' => $this->faker->boolean(50), // 50% chance of fire
            'raindrop' => $this->faker->boolean(50), // 50% chance of rain
        ]);
    }

    /**
     * Indicate that the sensor reading is from a recent time period.
     */
    public function recent(): static
    {
        return $this->state(fn(array $attributes) => [
            'reading_timestamp' => $this->faker->dateTimeBetween('-1 hour', 'now'),
        ]);
    }

    /**
     * Indicate that the sensor reading has low battery.
     */
    public function lowBattery(): static
    {
        return $this->state(fn(array $attributes) => [
            'additional_data' => [
                'battery_level' => $this->faker->numberBetween(10, 30),
                'signal_strength' => $this->faker->numberBetween(40, 70),
                'device_status' => 'low_battery',
                'firmware_version' => 'v1.2.3',
                'last_maintenance' => $this->faker->dateTimeBetween('-30 days', 'now')->format('Y-m-d H:i:s')
            ]
        ]);
    }
}

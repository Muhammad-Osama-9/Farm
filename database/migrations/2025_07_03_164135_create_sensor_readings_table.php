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
        Schema::create('sensor_readings', function (Blueprint $table) {
            $table->id();

            // Sensor identification
            $table->string('sensor_id', 50)->nullable()->index();
            $table->string('location', 100)->nullable()->index();

            // Sensor readings
            $table->decimal('temperature', 5, 2)->nullable()->comment('Temperature in Celsius');
            $table->decimal('humidity', 5, 2)->nullable()->comment('Humidity percentage');
            $table->decimal('water_level', 5, 2)->nullable()->comment('Water level in cm');
            $table->boolean('raindrop')->nullable()->comment('Rain detection: 0=no rain, 1=rain detected');
            $table->decimal('soil_moisture', 5, 2)->nullable()->comment('Soil moisture percentage');
            $table->boolean('flame')->nullable()->comment('Fire detection: 0=no fire, 1=fire detected');

            // Additional metadata
            $table->timestamp('reading_timestamp')->nullable()->index();
            $table->json('additional_data')->nullable()->comment('Any additional sensor data');

            // Timestamps
            $table->timestamps();

            // Indexes for better query performance
            $table->index(['sensor_id', 'created_at']);
            $table->index(['location', 'created_at']);
            $table->index('reading_timestamp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensor_readings');
    }
};

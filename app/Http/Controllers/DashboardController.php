<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\SensorReading;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the Smart Farm Dashboard.
     */
    public function index()
    {
        // Get the newest sensor data from database
        $sensorData = $this->getLatestSensorData();

        // Get comprehensive dashboard statistics
        $dashboardStats = $this->getDashboardStatistics();

        // Get recent alerts
        $recentAlerts = $this->getRecentAlerts();

        // Get sensor health status
        $sensorHealth = $this->getSensorHealth();

        return view('dashboard', array_merge($sensorData, [
            'stats' => $dashboardStats,
            'alerts' => $recentAlerts,
            'sensorHealth' => $sensorHealth,
            'lastUpdated' => $sensorData['timestamp'] ?? now()->toISOString()
        ]));
    }

    /**
     * Get the newest sensor data from database
     */
    private function getLatestSensorData()
    {
        // Always get the latest reading from database
        $latestReading = SensorReading::latest('reading_timestamp')->first();

        if ($latestReading) {
            $data = $latestReading->toDashboardArray();

            // Update cache for performance
            $this->updateCache($latestReading);

            return $this->mapToDashboardFormat($data);
        }

        // Return default values if no data exists
        return [
            'temperature' => 25.0,
            'humidity' => 60,
            'waterLevel' => 20,     // in cm
            'raindrop' => 0,        // 0 = no rain, 1 = rain detected
            'soilMoisture' => 45,   // in percentage
            'flame' => 0,           // 0 = no flame, 1 = fire detected
            'timestamp' => now()->toISOString(),
            'sensor_id' => 'default',
            'location' => 'main_farm'
        ];
    }

    /**
     * Get comprehensive dashboard statistics
     */
    private function getDashboardStatistics()
    {
        $stats = [];

        // Get 24-hour averages
        $averages24h = SensorReading::getAverageReadings(24);
        if ($averages24h) {
            $stats['averages_24h'] = [
                'temperature' => round($averages24h->avg_temperature, 2),
                'humidity' => round($averages24h->avg_humidity, 2),
                'water_level' => round($averages24h->avg_water_level, 2),
                'soil_moisture' => round($averages24h->avg_soil_moisture, 2),
                'total_readings' => $averages24h->total_readings
            ];
        }

        // Get 7-day averages
        $averages7d = SensorReading::getAverageReadings(168); // 7 days * 24 hours
        if ($averages7d) {
            $stats['averages_7d'] = [
                'temperature' => round($averages7d->avg_temperature, 2),
                'humidity' => round($averages7d->avg_humidity, 2),
                'water_level' => round($averages7d->avg_water_level, 2),
                'soil_moisture' => round($averages7d->avg_soil_moisture, 2),
                'total_readings' => $averages7d->total_readings
            ];
        }

        // Get reading counts by location
        $locationStats = SensorReading::selectRaw('
            location,
            COUNT(*) as total_readings,
            COUNT(DISTINCT sensor_id) as unique_sensors,
            MAX(reading_timestamp) as last_reading
        ')
            ->where('reading_timestamp', '>=', now()->subDays(7))
            ->groupBy('location')
            ->get();

        $stats['location_stats'] = $locationStats;

        // Get environmental trends
        $stats['trends'] = $this->getEnvironmentalTrends();

        return $stats;
    }

    /**
     * Get recent alerts from the last 24 hours
     */
    private function getRecentAlerts()
    {
        $alerts = [];

        // Get fire alerts
        $fireAlerts = SensorReading::where('flame', true)
            ->where('reading_timestamp', '>=', now()->subHours(24))
            ->orderBy('reading_timestamp', 'desc')
            ->get();

        foreach ($fireAlerts as $alert) {
            $alerts[] = [
                'type' => 'fire',
                'severity' => 'high',
                'message' => 'Fire detected!',
                'location' => $alert->location,
                'sensor_id' => $alert->sensor_id,
                'timestamp' => $alert->reading_timestamp,
                'icon' => '🔥',
                'color' => 'red'
            ];
        }

        // Get rain alerts
        $rainAlerts = SensorReading::where('raindrop', true)
            ->where('reading_timestamp', '>=', now()->subHours(24))
            ->orderBy('reading_timestamp', 'desc')
            ->get();

        foreach ($rainAlerts as $alert) {
            $alerts[] = [
                'type' => 'rain',
                'severity' => 'medium',
                'message' => 'Rain detected',
                'location' => $alert->location,
                'sensor_id' => $alert->sensor_id,
                'timestamp' => $alert->reading_timestamp,
                'icon' => '☔',
                'color' => 'blue'
            ];
        }

        // Get temperature alerts
        $tempAlerts = SensorReading::where('temperature', '>', 35)
            ->where('reading_timestamp', '>=', now()->subHours(24))
            ->orderBy('reading_timestamp', 'desc')
            ->get();

        foreach ($tempAlerts as $alert) {
            $alerts[] = [
                'type' => 'temperature',
                'severity' => 'medium',
                'message' => 'High temperature alert: ' . $alert->temperature . '°C',
                'location' => $alert->location,
                'sensor_id' => $alert->sensor_id,
                'timestamp' => $alert->reading_timestamp,
                'icon' => '🌡️',
                'color' => 'orange'
            ];
        }

        // Get soil moisture alerts
        $moistureAlerts = SensorReading::where('soil_moisture', '<', 30)
            ->where('reading_timestamp', '>=', now()->subHours(24))
            ->orderBy('reading_timestamp', 'desc')
            ->get();

        foreach ($moistureAlerts as $alert) {
            $alerts[] = [
                'type' => 'soil_moisture',
                'severity' => 'low',
                'message' => 'Low soil moisture: ' . $alert->soil_moisture . '%',
                'location' => $alert->location,
                'sensor_id' => $alert->sensor_id,
                'timestamp' => $alert->reading_timestamp,
                'icon' => '🌱',
                'color' => 'yellow'
            ];
        }

        return $alerts;
    }

    /**
     * Get sensor health status
     */
    private function getSensorHealth()
    {
        $health = [];

        // Get latest readings for each sensor
        $sensors = SensorReading::select('sensor_id')
            ->distinct()
            ->get();

        foreach ($sensors as $sensor) {
            $latestReading = SensorReading::where('sensor_id', $sensor->sensor_id)
                ->latest('reading_timestamp')
                ->first();

            if ($latestReading) {
                $additionalData = $latestReading->additional_data ?? [];
                $batteryLevel = $additionalData['battery_level'] ?? 100;
                $signalStrength = $additionalData['signal_strength'] ?? 100;
                $deviceStatus = $additionalData['device_status'] ?? 'online';

                $health[] = [
                    'sensor_id' => $sensor->sensor_id,
                    'location' => $latestReading->location,
                    'battery_level' => $batteryLevel,
                    'signal_strength' => $signalStrength,
                    'device_status' => $deviceStatus,
                    'last_seen' => $latestReading->reading_timestamp,
                    'status' => $this->getSensorStatus($batteryLevel, $signalStrength, $deviceStatus)
                ];
            }
        }

        return $health;
    }

    /**
     * Get environmental trends
     */
    private function getEnvironmentalTrends()
    {
        $trends = [];

        // Get hourly temperature averages for the last 24 hours
        $tempTrends = SensorReading::selectRaw('
            DATE_FORMAT(reading_timestamp, "%Y-%m-%d %H:00:00") as hour,
            AVG(temperature) as avg_temperature,
            MIN(temperature) as min_temperature,
            MAX(temperature) as max_temperature
        ')
            ->where('reading_timestamp', '>=', now()->subHours(24))
            ->groupBy('hour')
            ->orderBy('hour', 'desc')
            ->limit(24)
            ->get();

        $trends['temperature'] = $tempTrends;

        // Get humidity trends
        $humidityTrends = SensorReading::selectRaw('
            DATE_FORMAT(reading_timestamp, "%Y-%m-%d %H:00:00") as hour,
            AVG(humidity) as avg_humidity
        ')
            ->where('reading_timestamp', '>=', now()->subHours(24))
            ->groupBy('hour')
            ->orderBy('hour', 'desc')
            ->limit(24)
            ->get();

        $trends['humidity'] = $humidityTrends;

        return $trends;
    }

    /**
     * Determine sensor status based on battery, signal, and device status
     */
    private function getSensorStatus($batteryLevel, $signalStrength, $deviceStatus)
    {
        if ($deviceStatus !== 'online') {
            return 'offline';
        }

        if ($batteryLevel < 20) {
            return 'low_battery';
        }

        if ($signalStrength < 50) {
            return 'weak_signal';
        }

        return 'healthy';
    }

    /**
     * Map API data format to dashboard format
     */
    private function mapToDashboardFormat($data)
    {
        return [
            'temperature' => $data['temperature'] ?? 25.0,
            'humidity' => $data['humidity'] ?? 60,
            'waterLevel' => $data['water_level'] ?? 20,
            'raindrop' => $data['raindrop'] ?? 0,
            'soilMoisture' => $data['soil_moisture'] ?? 45,
            'flame' => $data['flame'] ?? 0,
            'timestamp' => $data['timestamp'] ?? now()->toISOString(),
            'sensor_id' => $data['sensor_id'] ?? 'default',
            'location' => $data['location'] ?? 'main_farm'
        ];
    }

    /**
     * Update cache with latest reading
     */
    private function updateCache(SensorReading $reading)
    {
        $sensorId = $reading->sensor_id ?? 'default';
        $cacheData = $reading->toDashboardArray();

        Cache::put("sensor_latest_{$sensorId}", $cacheData, now()->addHours(24));
    }
}

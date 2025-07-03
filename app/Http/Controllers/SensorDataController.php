<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use App\Models\SensorReading;

class SensorDataController extends Controller
{
    /**
     * Update sensor readings via JSON API
     */
    public function updateReadings(Request $request)
    {
        // Validate the incoming JSON data
        $validator = Validator::make($request->all(), [
            'temperature' => 'nullable|numeric|between:-50,100',
            'humidity' => 'nullable|numeric|between:0,100',
            'water_level' => 'nullable|numeric|between:0,100',
            'raindrop' => 'nullable|in:0,1',
            'soil_moisture' => 'nullable|numeric|between:0,100',
            'flame' => 'nullable|in:0,1',
            'timestamp' => 'nullable|date',
            'sensor_id' => 'nullable|string|max:50',
            'location' => 'nullable|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Get the sensor data from request
            $sensorData = $request->only([
                'temperature',
                'humidity',
                'water_level',
                'raindrop',
                'soil_moisture',
                'flame',
                'timestamp',
                'sensor_id',
                'location'
            ]);

            // Add timestamp if not provided
            if (!isset($sensorData['timestamp'])) {
                $sensorData['timestamp'] = now()->toISOString();
            }

            // Store in database
            $reading = SensorReading::createFromApiData($sensorData);

            // Update cache for fast access
            $this->updateCache($reading);

            // Log the sensor update
            Log::info('Sensor data updated', [
                'sensor_id' => $reading->sensor_id,
                'reading_id' => $reading->id,
                'data' => $sensorData
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Sensor readings updated successfully',
                'data' => $reading->toDashboardArray(),
                'reading_id' => $reading->id,
                'timestamp' => now()->toISOString()
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to update sensor readings: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update sensor readings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current sensor readings
     */
    public function getReadings()
    {
        try {
            // Try to get from cache first
            $cachedData = Cache::get('sensor_latest_default');

            if ($cachedData) {
                return response()->json([
                    'success' => true,
                    'data' => $cachedData,
                    'source' => 'cache',
                    'timestamp' => now()->toISOString()
                ], 200);
            }

            // If not in cache, get from database
            $latestReading = SensorReading::getLatestReading();

            if ($latestReading) {
                $data = $latestReading->toDashboardArray();

                // Update cache
                $this->updateCache($latestReading);

                return response()->json([
                    'success' => true,
                    'data' => $data,
                    'source' => 'database',
                    'timestamp' => now()->toISOString()
                ], 200);
            }

            // Return default values if no data exists
            $defaultData = [
                'temperature' => 25.0,
                'humidity' => 60,
                'water_level' => 20,
                'raindrop' => 0,
                'soil_moisture' => 45,
                'flame' => 0,
                'timestamp' => now()->toISOString(),
                'sensor_id' => 'default',
                'location' => 'main_farm'
            ];

            return response()->json([
                'success' => true,
                'data' => $defaultData,
                'source' => 'default',
                'timestamp' => now()->toISOString()
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to get sensor readings: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve sensor readings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sensor readings history
     */
    public function getHistory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hours' => 'nullable|integer|min:1|max:168', // Max 7 days
            'sensor_id' => 'nullable|string|max:50',
            'limit' => 'nullable|integer|min:1|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $hours = $request->input('hours', 24);
            $sensorId = $request->input('sensor_id');
            $limit = $request->input('limit', 100);

            $readings = SensorReading::getReadingsForPeriod($hours, $sensorId)
                ->take($limit)
                ->map(function ($reading) {
                    return $reading->toDashboardArray();
                });

            return response()->json([
                'success' => true,
                'data' => $readings,
                'period_hours' => $hours,
                'total_count' => $readings->count(),
                'timestamp' => now()->toISOString()
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to get sensor history: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve sensor history',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sensor statistics
     */
    public function getStats(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sensor_id' => 'nullable|string|max:50',
            'hours' => 'nullable|integer|min:1|max:168'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $sensorId = $request->input('sensor_id');
            $hours = $request->input('hours', 24);

            $stats = SensorReading::getDashboardStats($sensorId);

            if (!$stats) {
                return response()->json([
                    'success' => false,
                    'message' => 'No sensor data available'
                ], 404);
            }

            // Get additional statistics for the specified period
            $averages = SensorReading::getAverageReadings($hours, $sensorId);
            if ($averages) {
                $stats['averages_custom'] = [
                    'period_hours' => $hours,
                    'temperature' => round($averages->avg_temperature, 2),
                    'humidity' => round($averages->avg_humidity, 2),
                    'water_level' => round($averages->avg_water_level, 2),
                    'soil_moisture' => round($averages->avg_soil_moisture, 2),
                    'total_readings' => $averages->total_readings
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $stats,
                'timestamp' => now()->toISOString()
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to get sensor stats: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve sensor statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk update multiple sensor readings
     */
    public function bulkUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'readings' => 'required|array|min:1',
            'readings.*.temperature' => 'nullable|numeric|between:-50,100',
            'readings.*.humidity' => 'nullable|numeric|between:0,100',
            'readings.*.water_level' => 'nullable|numeric|between:0,100',
            'readings.*.raindrop' => 'nullable|in:0,1',
            'readings.*.soil_moisture' => 'nullable|numeric|between:0,100',
            'readings.*.flame' => 'nullable|in:0,1',
            'readings.*.sensor_id' => 'nullable|string|max:50',
            'readings.*.location' => 'nullable|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $readings = $request->input('readings');
            $createdReadings = [];

            foreach ($readings as $readingData) {
                $reading = SensorReading::createFromApiData($readingData);
                $createdReadings[] = $reading;

                // Update cache for each sensor
                $this->updateCache($reading);
            }

            Log::info('Bulk sensor data updated', [
                'count' => count($createdReadings),
                'readings' => $readings
            ]);

            return response()->json([
                'success' => true,
                'message' => "Successfully updated " . count($createdReadings) . " sensor readings",
                'updated_count' => count($createdReadings),
                'reading_ids' => collect($createdReadings)->pluck('id'),
                'timestamp' => now()->toISOString()
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to bulk update sensor readings: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to bulk update sensor readings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update cache with latest reading
     */
    private function updateCache(SensorReading $reading)
    {
        $sensorId = $reading->sensor_id ?? 'default';
        $cacheData = $reading->toDashboardArray();

        // Store latest reading in cache
        Cache::put("sensor_latest_{$sensorId}", $cacheData, now()->addHours(24));

        // Update history cache
        $historyKey = "sensor_history_{$sensorId}";
        $history = Cache::get($historyKey, []);
        $history[] = $cacheData;

        // Keep only last 100 readings
        if (count($history) > 100) {
            $history = array_slice($history, -100);
        }

        Cache::put($historyKey, $history, now()->addDays(7));
    }

    /**
     * Get latest sensor data from cache or database
     */
    private function getLatestSensorData()
    {
        // Try to get from cache first
        $sensorData = Cache::get('sensor_latest_default', []);

        // If no cached data, get from database
        if (empty($sensorData)) {
            $latestReading = SensorReading::getLatestReading();
            if ($latestReading) {
                $sensorData = $latestReading->toDashboardArray();
                $this->updateCache($latestReading);
            } else {
                // Return default values if no data exists
                $sensorData = [
                    'temperature' => 25.0,
                    'humidity' => 60,
                    'water_level' => 20,
                    'raindrop' => 0,
                    'soil_moisture' => 45,
                    'flame' => 0,
                    'timestamp' => now()->toISOString(),
                    'sensor_id' => 'default',
                    'location' => 'main_farm'
                ];
            }
        }

        return $sensorData;
    }

    /**
     * Get sensor history from cache or database
     */
    private function getSensorHistory(int $hours, ?string $sensorId = null)
    {
        $sensorId = $sensorId ?? 'default';
        $historyKey = "sensor_history_{$sensorId}";
        $history = Cache::get($historyKey, []);

        // If no cached history or insufficient data, get from database
        if (empty($history) || $hours < 24) {
            $readings = SensorReading::getReadingsForPeriod($hours, $sensorId);
            $history = $readings->map(function ($reading) {
                return $reading->toDashboardArray();
            })->toArray();
        } else {
            // Filter by time if needed
            $cutoffTime = now()->subHours($hours);
            $history = array_filter($history, function ($reading) use ($cutoffTime) {
                return isset($reading['timestamp']) &&
                    strtotime($reading['timestamp']) >= $cutoffTime->timestamp;
            });
        }

        return array_values($history);
    }
}
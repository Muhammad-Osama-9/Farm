<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class SensorReading extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'sensor_readings';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'sensor_id',
        'location',
        'temperature',
        'humidity',
        'water_level',
        'raindrop',
        'soil_moisture',
        'flame',
        'reading_timestamp',
        'additional_data'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'temperature' => 'decimal:2',
        'humidity' => 'decimal:2',
        'water_level' => 'decimal:2',
        'raindrop' => 'boolean',
        'soil_moisture' => 'decimal:2',
        'flame' => 'boolean',
        'reading_timestamp' => 'datetime',
        'additional_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'id'
    ];

    /**
     * Get the latest reading for a specific sensor
     */
    public static function getLatestReading($sensorId = 'default')
    {
        return static::where('sensor_id', $sensorId)
            ->latest('reading_timestamp')
            ->first();
    }

    /**
     * Get readings for a specific time period
     */
    public static function getReadingsForPeriod($hours = 24, $sensorId = null)
    {
        $query = static::where('reading_timestamp', '>=', now()->subHours($hours));

        if ($sensorId) {
            $query->where('sensor_id', $sensorId);
        }

        return $query->orderBy('reading_timestamp', 'desc')->get();
    }

    /**
     * Get average readings for a specific time period
     */
    public static function getAverageReadings($hours = 24, $sensorId = null)
    {
        $query = static::where('reading_timestamp', '>=', now()->subHours($hours));

        if ($sensorId) {
            $query->where('sensor_id', $sensorId);
        }

        return $query->selectRaw('
            AVG(temperature) as avg_temperature,
            AVG(humidity) as avg_humidity,
            AVG(water_level) as avg_water_level,
            AVG(soil_moisture) as avg_soil_moisture,
            COUNT(*) as total_readings
        ')->first();
    }

    /**
     * Scope to filter by sensor ID
     */
    public function scopeForSensor(Builder $query, $sensorId)
    {
        return $query->where('sensor_id', $sensorId);
    }

    /**
     * Scope to filter by location
     */
    public function scopeForLocation(Builder $query, $location)
    {
        return $query->where('location', $location);
    }

    /**
     * Scope to filter by time period
     */
    public function scopeForPeriod(Builder $query, $hours)
    {
        return $query->where('reading_timestamp', '>=', now()->subHours($hours));
    }

    /**
     * Scope to get only readings with alerts (flame or rain detected)
     */
    public function scopeWithAlerts(Builder $query)
    {
        return $query->where(function ($q) {
            $q->where('flame', true)
                ->orWhere('raindrop', true);
        });
    }

    /**
     * Get temperature status (normal, high, low)
     */
    public function getTemperatureStatusAttribute()
    {
        if ($this->temperature === null)
            return 'unknown';

        if ($this->temperature < 10)
            return 'low';
        if ($this->temperature > 35)
            return 'high';
        return 'normal';
    }

    /**
     * Get humidity status
     */
    public function getHumidityStatusAttribute()
    {
        if ($this->humidity === null)
            return 'unknown';

        if ($this->humidity < 30)
            return 'low';
        if ($this->humidity > 80)
            return 'high';
        return 'normal';
    }

    /**
     * Get soil moisture status
     */
    public function getSoilMoistureStatusAttribute()
    {
        if ($this->soil_moisture === null)
            return 'unknown';

        if ($this->soil_moisture < 30)
            return 'dry';
        if ($this->soil_moisture > 70)
            return 'wet';
        return 'optimal';
    }

    /**
     * Check if there are any alerts
     */
    public function getHasAlertsAttribute()
    {
        return $this->flame || $this->raindrop;
    }

    /**
     * Get alert messages
     */
    public function getAlertMessagesAttribute()
    {
        $alerts = [];

        if ($this->flame) {
            $alerts[] = 'Fire detected!';
        }

        if ($this->raindrop) {
            $alerts[] = 'Rain detected';
        }

        if ($this->temperature > 35) {
            $alerts[] = 'High temperature alert';
        }

        if ($this->soil_moisture < 30) {
            $alerts[] = 'Low soil moisture';
        }

        return $alerts;
    }

    /**
     * Convert to array format compatible with dashboard
     */
    public function toDashboardArray()
    {
        return [
            'temperature' => $this->temperature,
            'humidity' => $this->humidity,
            'waterLevel' => $this->water_level,
            'raindrop' => $this->raindrop ? 1 : 0,
            'soilMoisture' => $this->soil_moisture,
            'flame' => $this->flame ? 1 : 0,
            'timestamp' => $this->reading_timestamp?->toISOString(),
            'sensor_id' => $this->sensor_id,
            'location' => $this->location
        ];
    }

    /**
     * Create a reading from API data
     */
    public static function createFromApiData(array $data)
    {
        // Convert API field names to database field names
        $mappedData = [
            'sensor_id' => $data['sensor_id'] ?? 'default',
            'location' => $data['location'] ?? 'main_farm',
            'temperature' => $data['temperature'] ?? null,
            'humidity' => $data['humidity'] ?? null,
            'water_level' => $data['water_level'] ?? null,
            'raindrop' => isset($data['raindrop']) ? (bool) $data['raindrop'] : null,
            'soil_moisture' => $data['soil_moisture'] ?? null,
            'flame' => isset($data['flame']) ? (bool) $data['flame'] : null,
            'reading_timestamp' => isset($data['timestamp'])
                ? Carbon::parse($data['timestamp'])
                : now(),
            'additional_data' => array_diff_key($data, array_flip([
                'sensor_id',
                'location',
                'temperature',
                'humidity',
                'water_level',
                'raindrop',
                'soil_moisture',
                'flame',
                'timestamp'
            ]))
        ];

        return static::create($mappedData);
    }

    /**
     * Get statistics for dashboard
     */
    public static function getDashboardStats($sensorId = null)
    {
        $query = static::query();

        if ($sensorId) {
            $query->where('sensor_id', $sensorId);
        }

        $latest = $query->latest('reading_timestamp')->first();

        if (!$latest) {
            return null;
        }

        $stats = [
            'current' => $latest->toDashboardArray(),
            'alerts' => $latest->alert_messages,
            'has_alerts' => $latest->has_alerts,
            'temperature_status' => $latest->temperature_status,
            'humidity_status' => $latest->humidity_status,
            'soil_moisture_status' => $latest->soil_moisture_status
        ];

        // Get 24-hour averages
        $averages = static::getAverageReadings(24, $sensorId);
        if ($averages) {
            $stats['averages_24h'] = [
                'temperature' => round($averages->avg_temperature, 2),
                'humidity' => round($averages->avg_humidity, 2),
                'water_level' => round($averages->avg_water_level, 2),
                'soil_moisture' => round($averages->avg_soil_moisture, 2),
                'total_readings' => $averages->total_readings
            ];
        }

        return $stats;
    }
}

-- Farm Management System - Sensor Readings Table
-- This table stores all sensor data from IoT devices and sensors

CREATE TABLE `sensor_readings` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    
    -- Sensor identification
    `sensor_id` varchar(50) DEFAULT NULL COMMENT 'Unique identifier for the sensor',
    `location` varchar(100) DEFAULT NULL COMMENT 'Location description (e.g., greenhouse_1, outdoor_field)',
    
    -- Sensor readings (all nullable to allow partial updates)
    `temperature` decimal(5,2) DEFAULT NULL COMMENT 'Temperature in Celsius (-50 to 100)',
    `humidity` decimal(5,2) DEFAULT NULL COMMENT 'Humidity percentage (0 to 100)',
    `water_level` decimal(5,2) DEFAULT NULL COMMENT 'Water level in cm (0 to 100)',
    `raindrop` tinyint(1) DEFAULT NULL COMMENT 'Rain detection: 0=no rain, 1=rain detected',
    `soil_moisture` decimal(5,2) DEFAULT NULL COMMENT 'Soil moisture percentage (0 to 100)',
    `flame` tinyint(1) DEFAULT NULL COMMENT 'Fire detection: 0=no fire, 1=fire detected',
    
    -- Additional metadata
    `reading_timestamp` timestamp NULL DEFAULT NULL COMMENT 'Timestamp when the reading was taken',
    `additional_data` json DEFAULT NULL COMMENT 'Any additional sensor data (battery level, signal strength, etc.)',
    
    -- Laravel timestamps
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    
    -- Primary key
    PRIMARY KEY (`id`),
    
    -- Indexes for better query performance
    KEY `sensor_readings_sensor_id_index` (`sensor_id`),
    KEY `sensor_readings_location_index` (`location`),
    KEY `sensor_readings_reading_timestamp_index` (`reading_timestamp`),
    KEY `sensor_readings_sensor_id_created_at_index` (`sensor_id`, `created_at`),
    KEY `sensor_readings_location_created_at_index` (`location`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stores sensor readings from farm IoT devices';

-- Sample data insertion examples:

-- Example 1: Basic sensor reading
INSERT INTO `sensor_readings` (
    `sensor_id`, 
    `location`, 
    `temperature`, 
    `humidity`, 
    `water_level`, 
    `raindrop`, 
    `soil_moisture`, 
    `flame`, 
    `reading_timestamp`, 
    `additional_data`, 
    `created_at`, 
    `updated_at`
) VALUES (
    'sensor_001',
    'greenhouse_1',
    25.50,
    65.20,
    18.50,
    0,
    42.30,
    0,
    NOW(),
    '{"battery_level": 85, "signal_strength": 95, "device_status": "online"}',
    NOW(),
    NOW()
);

-- Example 2: Reading with rain detection
INSERT INTO `sensor_readings` (
    `sensor_id`, 
    `location`, 
    `temperature`, 
    `humidity`, 
    `water_level`, 
    `raindrop`, 
    `soil_moisture`, 
    `flame`, 
    `reading_timestamp`, 
    `additional_data`, 
    `created_at`, 
    `updated_at`
) VALUES (
    'sensor_002',
    'outdoor_field',
    22.80,
    78.50,
    25.20,
    1,
    48.70,
    0,
    NOW(),
    '{"battery_level": 72, "signal_strength": 88, "device_status": "online", "rain_intensity": "light"}',
    NOW(),
    NOW()
);

-- Example 3: Reading with fire alert
INSERT INTO `sensor_readings` (
    `sensor_id`, 
    `location`, 
    `temperature`, 
    `humidity`, 
    `water_level`, 
    `raindrop`, 
    `soil_moisture`, 
    `flame`, 
    `reading_timestamp`, 
    `additional_data`, 
    `created_at`, 
    `updated_at`
) VALUES (
    'sensor_003',
    'irrigation_zone_1',
    35.20,
    45.80,
    12.30,
    0,
    28.90,
    1,
    NOW(),
    '{"battery_level": 95, "signal_strength": 92, "device_status": "alert", "fire_intensity": "high"}',
    NOW(),
    NOW()
);

-- Useful queries for data analysis:

-- 1. Get latest reading for each sensor
SELECT 
    sensor_id,
    location,
    temperature,
    humidity,
    water_level,
    raindrop,
    soil_moisture,
    flame,
    reading_timestamp
FROM sensor_readings sr1
WHERE reading_timestamp = (
    SELECT MAX(reading_timestamp) 
    FROM sensor_readings sr2 
    WHERE sr2.sensor_id = sr1.sensor_id
);

-- 2. Get average readings for the last 24 hours
SELECT 
    sensor_id,
    location,
    AVG(temperature) as avg_temperature,
    AVG(humidity) as avg_humidity,
    AVG(water_level) as avg_water_level,
    AVG(soil_moisture) as avg_soil_moisture,
    COUNT(*) as total_readings
FROM sensor_readings 
WHERE reading_timestamp >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
GROUP BY sensor_id, location;

-- 3. Get readings with alerts (fire or rain)
SELECT 
    sensor_id,
    location,
    temperature,
    humidity,
    water_level,
    raindrop,
    soil_moisture,
    flame,
    reading_timestamp,
    CASE 
        WHEN flame = 1 THEN 'FIRE ALERT'
        WHEN raindrop = 1 THEN 'RAIN DETECTED'
        ELSE 'NORMAL'
    END as alert_status
FROM sensor_readings 
WHERE flame = 1 OR raindrop = 1
ORDER BY reading_timestamp DESC;

-- 4. Get temperature trends (hourly averages)
SELECT 
    DATE_FORMAT(reading_timestamp, '%Y-%m-%d %H:00:00') as hour,
    sensor_id,
    AVG(temperature) as avg_temperature,
    MIN(temperature) as min_temperature,
    MAX(temperature) as max_temperature
FROM sensor_readings 
WHERE reading_timestamp >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
GROUP BY DATE_FORMAT(reading_timestamp, '%Y-%m-%d %H:00:00'), sensor_id
ORDER BY hour DESC;

-- 5. Get sensor health status (battery levels)
SELECT 
    sensor_id,
    location,
    JSON_EXTRACT(additional_data, '$.battery_level') as battery_level,
    JSON_EXTRACT(additional_data, '$.signal_strength') as signal_strength,
    JSON_EXTRACT(additional_data, '$.device_status') as device_status,
    reading_timestamp
FROM sensor_readings 
WHERE reading_timestamp = (
    SELECT MAX(reading_timestamp) 
    FROM sensor_readings sr2 
    WHERE sr2.sensor_id = sensor_readings.sensor_id
)
ORDER BY battery_level ASC;

-- 6. Get soil moisture analysis
SELECT 
    sensor_id,
    location,
    soil_moisture,
    CASE 
        WHEN soil_moisture < 30 THEN 'DRY - Needs irrigation'
        WHEN soil_moisture BETWEEN 30 AND 70 THEN 'OPTIMAL'
        WHEN soil_moisture > 70 THEN 'WET - Reduce irrigation'
        ELSE 'UNKNOWN'
    END as moisture_status,
    reading_timestamp
FROM sensor_readings 
WHERE soil_moisture IS NOT NULL
ORDER BY reading_timestamp DESC;

-- 7. Get readings count by location
SELECT 
    location,
    COUNT(*) as total_readings,
    COUNT(DISTINCT sensor_id) as unique_sensors,
    MIN(reading_timestamp) as first_reading,
    MAX(reading_timestamp) as last_reading
FROM sensor_readings 
GROUP BY location
ORDER BY total_readings DESC;

-- 8. Get environmental conditions summary
SELECT 
    DATE(reading_timestamp) as date,
    sensor_id,
    location,
    AVG(temperature) as avg_temperature,
    AVG(humidity) as avg_humidity,
    SUM(raindrop) as rain_events,
    SUM(flame) as fire_alerts,
    COUNT(*) as total_readings
FROM sensor_readings 
WHERE reading_timestamp >= DATE_SUB(NOW(), INTERVAL 7 DAY)
GROUP BY DATE(reading_timestamp), sensor_id, location
ORDER BY date DESC, sensor_id; 
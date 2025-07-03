# Farm Management System - Sensor Data API Documentation

## Overview

This API allows you to update and retrieve sensor readings for the farm management dashboard. The API accepts JSON format and provides real-time data updates with persistent database storage.

## Base URL

```
http://your-domain.com/api/sensors
```

## Authentication

Currently, the sensor API endpoints are public to allow IoT devices to send data without authentication. For production, consider adding API key authentication.

## Endpoints

### 1. Update Single Sensor Reading

**POST** `/api/sensors/update`

Updates a single sensor reading with JSON data and stores it in the database.

#### Request Body (JSON)

```json
{
    "temperature": 27.5,
    "humidity": 65,
    "water_level": 18,
    "raindrop": 0,
    "soil_moisture": 42,
    "flame": 0,
    "sensor_id": "sensor_001",
    "location": "greenhouse_1",
    "timestamp": "2024-01-15T10:30:00Z"
}
```

#### Field Descriptions

-   `temperature` (optional): Temperature in Celsius (-50 to 100)
-   `humidity` (optional): Humidity percentage (0 to 100)
-   `water_level` (optional): Water level in cm (0 to 100)
-   `raindrop` (optional): Rain detection (0 = no rain, 1 = rain detected)
-   `soil_moisture` (optional): Soil moisture percentage (0 to 100)
-   `flame` (optional): Fire detection (0 = no fire, 1 = fire detected)
-   `sensor_id` (optional): Unique identifier for the sensor
-   `location` (optional): Location description
-   `timestamp` (optional): ISO 8601 timestamp (auto-generated if not provided)

#### Response

```json
{
    "success": true,
    "message": "Sensor readings updated successfully",
    "data": {
        "temperature": 27.5,
        "humidity": 65,
        "water_level": 18,
        "raindrop": 0,
        "soil_moisture": 42,
        "flame": 0,
        "sensor_id": "sensor_001",
        "location": "greenhouse_1",
        "timestamp": "2024-01-15T10:30:00Z"
    },
    "reading_id": 123,
    "timestamp": "2024-01-15T10:30:00Z"
}
```

### 2. Get Current Sensor Readings

**GET** `/api/sensors/readings`

Retrieves the latest sensor readings from cache or database.

#### Response

```json
{
    "success": true,
    "data": {
        "temperature": 27.5,
        "humidity": 65,
        "water_level": 18,
        "raindrop": 0,
        "soil_moisture": 42,
        "flame": 0,
        "timestamp": "2024-01-15T10:30:00Z",
        "sensor_id": "sensor_001",
        "location": "greenhouse_1"
    },
    "source": "cache",
    "timestamp": "2024-01-15T10:30:00Z"
}
```

### 3. Get Sensor History

**GET** `/api/sensors/history`

Retrieves historical sensor data from the database.

#### Query Parameters

-   `hours` (optional): Number of hours to look back (1-168, default: 24)
-   `sensor_id` (optional): Specific sensor ID to filter
-   `limit` (optional): Maximum number of readings to return (1-1000, default: 100)

#### Example Request

```
GET /api/sensors/history?hours=48&sensor_id=sensor_001&limit=50
```

#### Response

```json
{
    "success": true,
    "data": [
        {
            "temperature": 27.5,
            "humidity": 65,
            "water_level": 18,
            "raindrop": 0,
            "soil_moisture": 42,
            "flame": 0,
            "timestamp": "2024-01-15T10:30:00Z",
            "sensor_id": "sensor_001",
            "location": "greenhouse_1"
        }
    ],
    "period_hours": 48,
    "total_count": 1,
    "timestamp": "2024-01-15T10:30:00Z"
}
```

### 4. Get Sensor Statistics

**GET** `/api/sensors/stats`

Retrieves comprehensive sensor statistics including current readings, alerts, and averages.

#### Query Parameters

-   `sensor_id` (optional): Specific sensor ID to filter
-   `hours` (optional): Number of hours for custom period averages (1-168, default: 24)

#### Example Request

```
GET /api/sensors/stats?sensor_id=sensor_001&hours=48
```

#### Response

```json
{
    "success": true,
    "data": {
        "current": {
            "temperature": 27.5,
            "humidity": 65,
            "water_level": 18,
            "raindrop": 0,
            "soil_moisture": 42,
            "flame": 0,
            "timestamp": "2024-01-15T10:30:00Z",
            "sensor_id": "sensor_001",
            "location": "greenhouse_1"
        },
        "alerts": [],
        "has_alerts": false,
        "temperature_status": "normal",
        "humidity_status": "normal",
        "soil_moisture_status": "optimal",
        "averages_24h": {
            "temperature": 26.8,
            "humidity": 62.5,
            "water_level": 19.2,
            "soil_moisture": 43.1,
            "total_readings": 24
        },
        "averages_custom": {
            "period_hours": 48,
            "temperature": 26.5,
            "humidity": 63.2,
            "water_level": 18.8,
            "soil_moisture": 42.9,
            "total_readings": 48
        }
    },
    "timestamp": "2024-01-15T10:30:00Z"
}
```

### 5. Bulk Update Sensor Readings

**POST** `/api/sensors/bulk-update`

Updates multiple sensor readings at once and stores them in the database.

#### Request Body (JSON)

```json
{
    "readings": [
        {
            "temperature": 27.5,
            "humidity": 65,
            "water_level": 18,
            "raindrop": 0,
            "soil_moisture": 42,
            "flame": 0,
            "sensor_id": "sensor_001",
            "location": "greenhouse_1"
        },
        {
            "temperature": 26.8,
            "humidity": 62,
            "water_level": 15,
            "raindrop": 1,
            "soil_moisture": 45,
            "flame": 0,
            "sensor_id": "sensor_002",
            "location": "outdoor_field"
        }
    ]
}
```

#### Response

```json
{
    "success": true,
    "message": "Successfully updated 2 sensor readings",
    "updated_count": 2,
    "reading_ids": [123, 124],
    "timestamp": "2024-01-15T10:30:00Z"
}
```

## Error Responses

### Validation Error (422)

```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "temperature": ["The temperature must be between -50 and 100."],
        "humidity": ["The humidity must be between 0 and 100."]
    }
}
```

### Server Error (500)

```json
{
    "success": false,
    "message": "Failed to update sensor readings",
    "error": "Database connection failed"
}
```

## Usage Examples

### Arduino/ESP32 Example

```cpp
#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>

const char* ssid = "your_wifi_ssid";
const char* password = "your_wifi_password";
const char* serverUrl = "http://your-domain.com/api/sensors/update";

void setup() {
    Serial.begin(115200);
    WiFi.begin(ssid, password);

    while (WiFi.status() != WL_CONNECTED) {
        delay(1000);
        Serial.println("Connecting to WiFi...");
    }
}

void loop() {
    if (WiFi.status() == WL_CONNECTED) {
        HTTPClient http;
        http.begin(serverUrl);
        http.addHeader("Content-Type", "application/json");

        // Create JSON payload
        StaticJsonDocument<200> doc;
        doc["temperature"] = 25.5;
        doc["humidity"] = 60;
        doc["water_level"] = 20;
        doc["raindrop"] = 0;
        doc["soil_moisture"] = 45;
        doc["flame"] = 0;
        doc["sensor_id"] = "arduino_sensor_001";
        doc["location"] = "greenhouse_main";

        String jsonString;
        serializeJson(doc, jsonString);

        int httpResponseCode = http.POST(jsonString);

        if (httpResponseCode > 0) {
            String response = http.getString();
            Serial.println("HTTP Response: " + response);
        } else {
            Serial.println("Error on HTTP request");
        }

        http.end();
    }

    delay(30000); // Send data every 30 seconds
}
```

### Python Example

```python
import requests
import json
import time
from datetime import datetime

def send_sensor_data():
    url = "http://your-domain.com/api/sensors/update"

    data = {
        "temperature": 25.5,
        "humidity": 60,
        "water_level": 20,
        "raindrop": 0,
        "soil_moisture": 45,
        "flame": 0,
        "sensor_id": "python_sensor_001",
        "location": "greenhouse_main",
        "timestamp": datetime.now().isoformat()
    }

    headers = {
        "Content-Type": "application/json"
    }

    try:
        response = requests.post(url, json=data, headers=headers)
        if response.status_code == 200:
            print("Data sent successfully:", response.json())
        else:
            print("Error:", response.status_code, response.text)
    except Exception as e:
        print("Error sending data:", str(e))

# Send data every 30 seconds
while True:
    send_sensor_data()
    time.sleep(30)
```

### JavaScript/Node.js Example

```javascript
const axios = require("axios");

async function sendSensorData() {
    const url = "http://your-domain.com/api/sensors/update";

    const data = {
        temperature: 25.5,
        humidity: 60,
        water_level: 20,
        raindrop: 0,
        soil_moisture: 45,
        flame: 0,
        sensor_id: "nodejs_sensor_001",
        location: "greenhouse_main",
        timestamp: new Date().toISOString(),
    };

    try {
        const response = await axios.post(url, data, {
            headers: {
                "Content-Type": "application/json",
            },
        });

        console.log("Data sent successfully:", response.data);
    } catch (error) {
        console.error(
            "Error sending data:",
            error.response?.data || error.message
        );
    }
}

// Send data every 30 seconds
setInterval(sendSensorData, 30000);
```

## Data Storage

-   **Database Storage**: All sensor readings are stored in the `sensor_readings` table for persistent storage
-   **Cache Storage**: Latest readings are cached for fast access and real-time dashboard updates
-   **History**: Complete historical data is maintained in the database with automatic indexing
-   **Additional Data**: JSON field for storing extra sensor metadata (battery level, signal strength, etc.)

## Database Schema

The `sensor_readings` table includes:

-   `id` - Primary key
-   `sensor_id` - Sensor identifier (indexed)
-   `location` - Location description (indexed)
-   `temperature` - Temperature in Celsius (decimal)
-   `humidity` - Humidity percentage (decimal)
-   `water_level` - Water level in cm (decimal)
-   `raindrop` - Rain detection (boolean)
-   `soil_moisture` - Soil moisture percentage (decimal)
-   `flame` - Fire detection (boolean)
-   `reading_timestamp` - Timestamp of the reading (indexed)
-   `additional_data` - JSON field for extra metadata
-   `created_at` / `updated_at` - Laravel timestamps

## Security Considerations

1. **API Keys**: Implement API key authentication for production
2. **Rate Limiting**: Add rate limiting to prevent abuse
3. **HTTPS**: Use HTTPS in production
4. **Input Validation**: All inputs are validated and sanitized
5. **Logging**: All API calls are logged for monitoring
6. **Database Security**: Use prepared statements and proper database permissions

## Dashboard Integration

The dashboard automatically displays the latest sensor readings from the database. When you update sensor data via the API, the dashboard will show the new values on the next page refresh or when real-time updates are implemented.

## Database Setup

To set up the database:

1. **Run Migration**:

    ```bash
    php artisan migrate
    ```

2. **Seed Sample Data**:

    ```bash
    php artisan db:seed --class=SensorReadingSeeder
    ```

3. **Create Test Data** (optional):
    ```bash
    php artisan tinker
    ```
    ```php
    App\Models\SensorReading::factory(100)->create();
    ```

## Model Features

The `SensorReading` model includes:

-   **Scopes**: Filter by sensor, location, time period, alerts
-   **Accessors**: Temperature/humidity/soil moisture status
-   **Methods**: Dashboard statistics, history retrieval
-   **Casts**: Automatic type conversion for JSON and boolean fields
-   **Validation**: Built-in validation rules
-   **Relationships**: Ready for future relationship models

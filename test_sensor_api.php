<?php
/**
 * Test Script for Farm Sensor API
 * 
 * This script demonstrates how to use the sensor data API endpoints
 * to update and retrieve dashboard readings.
 */

// Configuration
$baseUrl = 'http://localhost:8000'; // Change this to your domain
$apiUrl = $baseUrl . '/api/sensors';

echo "🌾 Farm Management System - Sensor API Test\n";
echo "==========================================\n\n";

// Test 1: Update single sensor reading
echo "1. Testing Single Sensor Update...\n";
$singleData = [
    'temperature' => 28.5,
    'humidity' => 68,
    'water_level' => 22,
    'raindrop' => 0,
    'soil_moisture' => 48,
    'flame' => 0,
    'sensor_id' => 'test_sensor_001',
    'location' => 'test_greenhouse'
];

$response = sendRequest($apiUrl . '/update', $singleData, 'POST');
echo "Response: " . json_encode($response, JSON_PRETTY_PRINT) . "\n\n";

// Test 2: Get current readings
echo "2. Testing Get Current Readings...\n";
$response = sendRequest($apiUrl . '/readings', [], 'GET');
echo "Response: " . json_encode($response, JSON_PRETTY_PRINT) . "\n\n";

// Test 3: Bulk update multiple sensors
echo "3. Testing Bulk Update...\n";
$bulkData = [
    'readings' => [
        [
            'temperature' => 26.8,
            'humidity' => 65,
            'water_level' => 20,
            'raindrop' => 1,
            'soil_moisture' => 45,
            'flame' => 0,
            'sensor_id' => 'sensor_001',
            'location' => 'greenhouse_1'
        ],
        [
            'temperature' => 29.2,
            'humidity' => 72,
            'water_level' => 25,
            'raindrop' => 0,
            'soil_moisture' => 52,
            'flame' => 0,
            'sensor_id' => 'sensor_002',
            'location' => 'outdoor_field'
        ]
    ]
];

$response = sendRequest($apiUrl . '/bulk-update', $bulkData, 'POST');
echo "Response: " . json_encode($response, JSON_PRETTY_PRINT) . "\n\n";

// Test 4: Get sensor history
echo "4. Testing Get Sensor History...\n";
$response = sendRequest($apiUrl . '/history?hours=24', [], 'GET');
echo "Response: " . json_encode($response, JSON_PRETTY_PRINT) . "\n\n";

// Test 5: Test validation error
echo "5. Testing Validation Error...\n";
$invalidData = [
    'temperature' => 150, // Invalid: too high
    'humidity' => -5,     // Invalid: negative
    'water_level' => 50,
    'raindrop' => 2,      // Invalid: should be 0 or 1
    'soil_moisture' => 45,
    'flame' => 0
];

$response = sendRequest($apiUrl . '/update', $invalidData, 'POST');
echo "Response: " . json_encode($response, JSON_PRETTY_PRINT) . "\n\n";

echo "✅ API Testing Complete!\n";
echo "Check your dashboard to see the updated readings.\n";

/**
 * Send HTTP request to the API
 */
function sendRequest($url, $data, $method = 'GET')
{
    $ch = curl_init();

    $options = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json'
        ]
    ];

    if ($method === 'POST') {
        $options[CURLOPT_POST] = true;
        $options[CURLOPT_POSTFIELDS] = json_encode($data);
    }

    curl_setopt_array($ch, $options);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_error($ch)) {
        return [
            'success' => false,
            'error' => 'cURL Error: ' . curl_error($ch)
        ];
    }

    curl_close($ch);

    $decodedResponse = json_decode($response, true);

    return [
        'http_code' => $httpCode,
        'response' => $decodedResponse ?: $response
    ];
}

/**
 * Alternative function using file_get_contents (if cURL is not available)
 */
function sendRequestSimple($url, $data, $method = 'GET')
{
    $context = [
        'http' => [
            'method' => $method,
            'header' => [
                'Content-Type: application/json',
                'Accept: application/json'
            ],
            'timeout' => 30
        ]
    ];

    if ($method === 'POST') {
        $context['http']['content'] = json_encode($data);
    }

    $context = stream_context_create($context);

    try {
        $response = file_get_contents($url, false, $context);
        return json_decode($response, true) ?: $response;
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => 'Request Error: ' . $e->getMessage()
        ];
    }
}
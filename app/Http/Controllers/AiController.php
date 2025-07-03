<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiController extends Controller
{
    /**
     * Display the AI Farm Assistant page
     */
    public function index()
    {
        return view('ai-assistant');
    }

    /**
     * Process AI request and return response
     */
    public function processRequest(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'context' => 'nullable|string|max:500'
        ]);

        try {
            // Get the user's message
            $message = $request->input('message');
            $context = $request->input('context', 'farm management');

            // Create a farm-specific prompt
            $prompt = $this->createFarmPrompt($message, $context);

            // For now, we'll simulate an AI response
            // In production, you would integrate with OpenAI, Claude, or another AI service
            $response = $this->generateAiResponse($prompt);

            return response()->json([
                'success' => true,
                'response' => $response,
                'timestamp' => now()->format('Y-m-d H:i:s')
            ]);

        } catch (\Exception $e) {
            Log::error('AI request failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Sorry, I encountered an error. Please try again.',
                'timestamp' => now()->format('Y-m-d H:i:s')
            ], 500);
        }
    }

    /**
     * Create a farm-specific prompt for AI
     */
    private function createFarmPrompt(string $message, string $context): string
    {
        $basePrompt = "You are an expert farm management AI assistant. You help farmers with: 
        - Crop management and optimization
        - Soil health and fertilization
        - Pest control and disease management
        - Weather impact on farming
        - Equipment and technology recommendations
        - Sustainable farming practices
        - Market trends and crop selection
        
        Context: {$context}
        User Question: {$message}
        
        Please provide a helpful, practical response focused on farm management:";

        return $basePrompt;
    }

    /**
     * Generate AI response (simulated for now)
     */
    private function generateAiResponse(string $prompt): string
    {
        // This is a simulated response - in production, integrate with actual AI service
        $responses = [
            "Based on your question, I recommend monitoring soil moisture levels regularly and adjusting irrigation schedules accordingly. Consider using smart sensors for real-time data collection.",

            "For optimal crop health, ensure proper spacing between plants and maintain consistent watering schedules. Regular soil testing can help identify nutrient deficiencies early.",

            "Weather conditions suggest implementing protective measures for your crops. Consider using row covers or adjusting planting schedules to avoid frost damage.",

            "Sustainable farming practices like crop rotation and organic pest control can improve soil health while reducing environmental impact. Start with small changes and gradually expand.",

            "Market analysis shows strong demand for organic produce. Consider transitioning to organic farming methods, which can also improve soil quality over time.",

            "Technology integration can significantly improve farm efficiency. Start with basic monitoring systems and gradually add automation as your comfort level increases.",

            "Pest management should focus on prevention rather than reaction. Regular scouting and early intervention can prevent major infestations and reduce pesticide use."
        ];

        // Return a random response for demonstration
        return $responses[array_rand($responses)];
    }

    /**
     * Get AI suggestions based on sensor data
     */
    public function getSuggestions(Request $request)
    {
        try {
            // Simulate AI suggestions based on farm data
            $suggestions = [
                [
                    'type' => 'irrigation',
                    'title' => 'Irrigation Optimization',
                    'message' => 'Based on current soil moisture levels, consider reducing irrigation frequency by 20%.',
                    'priority' => 'medium',
                    'icon' => '💧'
                ],
                [
                    'type' => 'temperature',
                    'title' => 'Temperature Alert',
                    'message' => 'Temperature is optimal for crop growth. Maintain current conditions.',
                    'priority' => 'low',
                    'icon' => '🌡️'
                ],
                [
                    'type' => 'pest_control',
                    'title' => 'Pest Prevention',
                    'message' => 'Humidity levels are conducive to pest growth. Consider preventive measures.',
                    'priority' => 'high',
                    'icon' => '🦗'
                ]
            ];

            return response()->json([
                'success' => true,
                'suggestions' => $suggestions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to generate suggestions'
            ], 500);
        }
    }
}
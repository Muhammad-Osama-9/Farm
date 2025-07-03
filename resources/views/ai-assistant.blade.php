@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-green-50 via-blue-50 to-purple-50 py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Back Button -->
            <div class="mb-6">
                <a href="{{ url('/home') }}"
                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl font-medium hover:from-blue-600 hover:to-purple-700 transform hover:scale-105 transition-all duration-200 shadow-lg">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Home
                </a>
            </div>

            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">
                    <span class="bg-gradient-to-r from-green-600 to-blue-600 bg-clip-text text-transparent">
                        AI Farm Assistant
                    </span>
                </h1>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Get expert advice on crop management, soil health, pest control, and sustainable farming practices
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- AI Chat Section -->
                <div class="lg:col-span-2">
                    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20">
                        <!-- Chat Header -->
                        <div class="p-6 border-b border-gray-200">
                            <div class="flex items-center space-x-3">
                                <div
                                    class="w-10 h-10 bg-gradient-to-r from-green-500 to-blue-500 rounded-full flex items-center justify-center">
                                    <span class="text-white text-lg">🤖</span>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Farm AI Assistant</h3>
                                    <p class="text-sm text-gray-500">Ask me anything about farming!</p>
                                </div>
                            </div>
                        </div>

                        <!-- Chat Messages -->
                        <div id="chat-messages" class="h-96 overflow-y-auto p-6 space-y-4">
                            <!-- Welcome message -->
                            <div class="flex items-start space-x-3">
                                <div
                                    class="w-8 h-8 bg-gradient-to-r from-green-500 to-blue-500 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-white text-sm">🤖</span>
                                </div>
                                <div class="bg-gray-100 rounded-2xl px-4 py-3 max-w-xs lg:max-w-md">
                                    <p class="text-gray-800">
                                        Hello! I'm your AI farm assistant. I can help you with crop management, soil health,
                                        pest control, weather advice, and sustainable farming practices. What would you like
                                        to know?
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Chat Input -->
                        <div class="p-6 border-t border-gray-200">
                            <form id="ai-chat-form" class="flex space-x-3">
                                <div class="flex-1">
                                    <textarea id="message-input" name="message" rows="2"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent resize-none"
                                        placeholder="Ask about crop management, soil health, pest control, or any farming topic..."
                                        required></textarea>
                                </div>
                                <button type="submit"
                                    class="px-6 py-3 bg-gradient-to-r from-green-600 to-blue-600 text-white rounded-xl hover:from-green-700 hover:to-blue-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200 flex items-center space-x-2">
                                    <span>Send</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- AI Suggestions Panel -->
                <div class="lg:col-span-1">
                    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                            <span class="mr-2">💡</span>
                            AI Suggestions
                        </h3>

                        <div id="ai-suggestions" class="space-y-4">
                            <!-- Suggestions will be loaded here -->
                        </div>

                        <!-- Quick Actions -->
                        <div class="mt-6">
                            <h4 class="text-sm font-medium text-gray-700 mb-3">Quick Questions</h4>
                            <div class="space-y-2">
                                <button
                                    class="quick-question w-full text-left px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors"
                                    data-question="How can I improve soil health?">
                                    🌱 How can I improve soil health?
                                </button>
                                <button
                                    class="quick-question w-full text-left px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors"
                                    data-question="What are the best pest control methods?">
                                    🦗 Best pest control methods?
                                </button>
                                <button
                                    class="quick-question w-full text-left px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors"
                                    data-question="How to optimize irrigation?">
                                    💧 Optimize irrigation?
                                </button>
                                <button
                                    class="quick-question w-full text-left px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors"
                                    data-question="Sustainable farming practices?">
                                    🌿 Sustainable practices?
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const chatForm = document.getElementById('ai-chat-form');
            const messageInput = document.getElementById('message-input');
            const chatMessages = document.getElementById('chat-messages');
            const aiSuggestions = document.getElementById('ai-suggestions');
            const quickQuestions = document.querySelectorAll('.quick-question');

            // Load AI suggestions
            loadAiSuggestions();

            // Handle form submission
            chatForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const message = messageInput.value.trim();
                if (!message) return;

                // Add user message to chat
                addMessage(message, 'user');
                messageInput.value = '';

                // Send to AI API
                sendToAI(message);
            });

            // Handle quick questions
            quickQuestions.forEach(button => {
                button.addEventListener('click', function () {
                    const question = this.dataset.question;
                    messageInput.value = question;
                    chatForm.dispatchEvent(new Event('submit'));
                });
            });

            function addMessage(message, sender) {
                const messageDiv = document.createElement('div');
                messageDiv.className = 'flex items-start space-x-3';

                if (sender === 'user') {
                    messageDiv.innerHTML = `
                                    <div class="flex-1"></div>
                                    <div class="bg-gradient-to-r from-green-500 to-blue-500 text-white rounded-2xl px-4 py-3 max-w-xs lg:max-w-md">
                                        <p>${message}</p>
                                    </div>
                                    <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-blue-500 rounded-full flex items-center justify-center flex-shrink-0">
                                        <span class="text-white text-sm">👤</span>
                                    </div>
                                `;
                } else {
                    messageDiv.innerHTML = `
                                    <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-blue-500 rounded-full flex items-center justify-center flex-shrink-0">
                                        <span class="text-white text-sm">🤖</span>
                                    </div>
                                    <div class="bg-gray-100 rounded-2xl px-4 py-3 max-w-xs lg:max-w-md">
                                        <p class="text-gray-800">${message}</p>
                                    </div>
                                `;
                }

                chatMessages.appendChild(messageDiv);
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }

            function sendToAI(message) {
                // Show loading indicator
                const loadingDiv = document.createElement('div');
                loadingDiv.className = 'flex items-start space-x-3';
                loadingDiv.innerHTML = `
                                <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-blue-500 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-white text-sm">🤖</span>
                                </div>
                                <div class="bg-gray-100 rounded-2xl px-4 py-3">
                                    <div class="flex space-x-1">
                                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                                    </div>
                                </div>
                            `;
                chatMessages.appendChild(loadingDiv);
                chatMessages.scrollTop = chatMessages.scrollHeight;

                // Send request to AI API
                fetch('/api/ai/process', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        message: message,
                        context: 'farm management'
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        // Remove loading indicator
                        chatMessages.removeChild(loadingDiv);

                        if (data.success) {
                            addMessage(data.response, 'ai');
                        } else {
                            addMessage('Sorry, I encountered an error. Please try again.', 'ai');
                        }
                    })
                    .catch(error => {
                        // Remove loading indicator
                        chatMessages.removeChild(loadingDiv);
                        addMessage('Sorry, I encountered an error. Please try again.', 'ai');
                    });
            }

            function loadAiSuggestions() {
                fetch('/api/ai/suggestions')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            displaySuggestions(data.suggestions);
                        }
                    })
                    .catch(error => {
                        console.error('Failed to load suggestions:', error);
                    });
            }

            function displaySuggestions(suggestions) {
                aiSuggestions.innerHTML = '';

                suggestions.forEach(suggestion => {
                    const priorityColors = {
                        high: 'border-red-200 bg-red-50',
                        medium: 'border-yellow-200 bg-yellow-50',
                        low: 'border-green-200 bg-green-50'
                    };

                    const suggestionDiv = document.createElement('div');
                    suggestionDiv.className = `p-4 rounded-xl border ${priorityColors[suggestion.priority]} hover:shadow-md transition-shadow cursor-pointer`;
                    suggestionDiv.innerHTML = `
                                    <div class="flex items-start space-x-3">
                                        <span class="text-2xl">${suggestion.icon}</span>
                                        <div class="flex-1">
                                            <h4 class="font-medium text-gray-900">${suggestion.title}</h4>
                                            <p class="text-sm text-gray-600 mt-1">${suggestion.message}</p>
                                        </div>
                                    </div>
                                `;

                    suggestionDiv.addEventListener('click', function () {
                        messageInput.value = suggestion.message;
                        chatForm.dispatchEvent(new Event('submit'));
                    });

                    aiSuggestions.appendChild(suggestionDiv);
                });
            }
        });
    </script>
@endsection
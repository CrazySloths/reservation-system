<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Expired - LGU1</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full">
            <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                <!-- Icon -->
                <div class="w-20 h-20 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock text-amber-600">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>

                <!-- Title -->
                <h1 class="text-2xl font-bold text-gray-900 mb-3">Session Expired</h1>
                
                <!-- Message -->
                <p class="text-gray-600 mb-6">
                    Your session has expired for security reasons. Please log in again to continue.
                </p>

                <!-- Action Button -->
                <a href="{{ route('login') }}" 
                   class="inline-block w-full px-6 py-3 bg-lgu-button text-lgu-button-text font-semibold rounded-lg hover:bg-lgu-highlight transition-colors shadow-sm">
                    Go to Login
                </a>

                <!-- Additional Info -->
                <p class="text-sm text-gray-500 mt-6">
                    Need help? Contact your system administrator.
                </p>
            </div>
        </div>
    </div>
</body>
</html>


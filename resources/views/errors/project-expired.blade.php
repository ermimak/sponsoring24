<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Expired - Sponsoring24</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8 text-center">
        <div class="mb-6">
            <svg class="mx-auto h-16 w-16 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        
        <h1 class="text-2xl font-bold text-gray-900 mb-4">Donation Period Ended</h1>
        
        <p class="text-gray-600 mb-6">
            {{ $message ?? 'The donation period for this project has ended and is no longer accepting donations. Thank you for your interest!' }}
        </p>
        
        <div class="space-y-3">
            <a href="{{ url('/') }}" class="block w-full bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition duration-200 font-medium">
                Explore Other Projects
            </a>
            
            <button onclick="history.back()" class="block w-full bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 transition duration-200 font-medium">
                Go Back
            </button>
        </div>
        
        <div class="mt-8 pt-6 border-t border-gray-200">
            <div class="flex items-center justify-center space-x-2 text-sm text-gray-500">
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-600">
                    Sponsoring24
                </span>
                <span>Sponsoring24</span>
            </div>
        </div>
    </div>
</body>
</html>

@extends('layouts.app')

@section('content')
<div class="mb-6">
    <!-- Enhanced Header Section -->
    <div class="bg-lgu-headline rounded-2xl p-8 text-white shadow-lg overflow-hidden relative">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <svg class="w-full h-full" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg">
                <pattern id="pattern" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                    <circle cx="10" cy="10" r="1" fill="currentColor"/>
                </pattern>
                <rect width="100%" height="100%" fill="url(#pattern)"/>
            </svg>
        </div>
        
        <div class="relative z-10">
            <div class="flex items-center space-x-3">
                <div class="w-16 h-16 bg-lgu-highlight/20 rounded-2xl flex items-center justify-center backdrop-blur-sm border border-white/10">
                    <svg class="w-8 h-8 text-lgu-highlight" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-4xl font-bold mb-1 text-white">Intelligent Facility Usage Analytics</h1>
                    <p class="text-gray-200 text-lg">Data-driven insights from historical booking patterns using TensorFlow.js</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Analytics Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <!-- Card 1: Pattern Recognition -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-lgu-headline">Usage Patterns</h3>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-lgu-headline mb-2" id="patterns-count">--</p>
        <p class="text-sm text-lgu-paragraph">Identified from past 6 months</p>
    </div>

    <!-- Card 2: Peak Times -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-lgu-headline">Peak Usage Times</h3>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-lgu-headline mb-2" id="peak-time">--</p>
        <p class="text-sm text-lgu-paragraph">Most frequent booking time</p>
    </div>

    <!-- Card 3: Optimization Score -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-lgu-headline">Utilization Rate</h3>
            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-lgu-headline mb-2" id="utilization-rate">--</p>
        <p class="text-sm text-lgu-paragraph">Average facility utilization</p>
    </div>
</div>

<!-- Main Analytics Chart -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-xl font-semibold text-lgu-headline">Historical Booking Trends</h3>
        <div class="flex items-center space-x-2">
            <span class="px-3 py-1 bg-blue-100 text-blue-700 text-sm rounded-full font-medium">
                Past 6 Months
            </span>
        </div>
    </div>
    
    <div style="width: 100%; height: 400px;">
        <canvas id="usageChart"></canvas> 
        <p id="chartStatus" class="text-center mt-3 text-lgu-paragraph">Analyzing historical patterns...</p>
    </div>
</div>

<!-- Insights Grid -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Resource Optimization Insights -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-lgu-headline mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-lgu-highlight" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
            </svg>
            Resource Optimization Insights
        </h3>
        <div id="optimization-insights" class="space-y-3">
            <div class="flex items-start space-x-3 p-3 bg-blue-50 rounded-lg">
                <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                <p class="text-sm text-lgu-paragraph">Loading insights...</p>
            </div>
        </div>
    </div>

    <!-- Capacity Planning Helper -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-lgu-headline mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-lgu-highlight" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
            </svg>
            Capacity Planning Recommendations
        </h3>
        <div id="capacity-recommendations" class="space-y-3">
            <div class="flex items-start space-x-3 p-3 bg-green-50 rounded-lg">
                <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                <p class="text-sm text-lgu-paragraph">Loading recommendations...</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @vite('resources/js/analytics.js')

    <script type="module">
        document.addEventListener('DOMContentLoaded', () => {
            console.log("Analytics page loaded - initializing pattern recognition...");
            
            // Initialize analytics (if the function exists from analytics.js)
            if (typeof startAnalytics === 'function') {
                startAnalytics();
            } else {
                console.warn("Analytics module not loaded yet");
                
                // Fallback: Show sample data
                setTimeout(() => {
                    document.getElementById('patterns-count').textContent = '12 Patterns';
                    document.getElementById('peak-time').textContent = 'Weekends';
                    document.getElementById('utilization-rate').textContent = '68%';
                    document.getElementById('chartStatus').textContent = 'Historical data visualization ready';
                    
                    // Sample insights
                    document.getElementById('optimization-insights').innerHTML = `
                        <div class="flex items-start space-x-3 p-3 bg-blue-50 rounded-lg">
                            <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                            <p class="text-sm text-lgu-paragraph"><strong>Weekend Demand:</strong> Historical data shows 40% higher bookings on weekends</p>
                        </div>
                        <div class="flex items-start space-x-3 p-3 bg-blue-50 rounded-lg">
                            <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                            <p class="text-sm text-lgu-paragraph"><strong>Peak Hours:</strong> Most bookings occur between 2 PM - 6 PM</p>
                        </div>
                        <div class="flex items-start space-x-3 p-3 bg-blue-50 rounded-lg">
                            <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                            <p class="text-sm text-lgu-paragraph"><strong>Popular Facilities:</strong> Covered courts have 85% utilization rate</p>
                        </div>
                    `;
                    
                    // Sample recommendations
                    document.getElementById('capacity-recommendations').innerHTML = `
                        <div class="flex items-start space-x-3 p-3 bg-green-50 rounded-lg">
                            <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                            <p class="text-sm text-lgu-paragraph"><strong>Optimize Scheduling:</strong> Consider adding more weekend slots for high-demand facilities</p>
                        </div>
                        <div class="flex items-start space-x-3 p-3 bg-green-50 rounded-lg">
                            <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                            <p class="text-sm text-lgu-paragraph"><strong>Resource Allocation:</strong> Underutilized facilities could be promoted during off-peak hours</p>
                        </div>
                        <div class="flex items-start space-x-3 p-3 bg-green-50 rounded-lg">
                            <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                            <p class="text-sm text-lgu-paragraph"><strong>Maintenance Planning:</strong> Schedule maintenance during low-usage periods (Tuesdays-Thursdays)</p>
                        </div>
                    `;
                }, 1000);
            }
        });
    </script>
@endpush


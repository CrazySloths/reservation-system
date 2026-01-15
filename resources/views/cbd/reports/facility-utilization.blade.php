@extends('layouts.cbd')

@section('title', 'Facility Utilization - CBD')

@section('page-content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div>
            <h2 class="text-2xl font-bold text-[#0f3d3e]">Facility Utilization Report</h2>
            <p class="text-gray-600 mt-1">Coming Soon - Track facility usage and performance metrics</p>
        </div>
    </div>

    <!-- Placeholder Content -->
    <div class="bg-white rounded-lg shadow-sm p-12">
        <div class="text-center">
            <i data-lucide="construction" class="w-24 h-24 mx-auto mb-4 text-gray-400"></i>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">Under Development</h3>
            <p class="text-gray-600 mb-6">This report is currently being developed and will be available soon.</p>
            <a href="{{ route('cbd.dashboard') }}" class="inline-flex items-center px-6 py-3 bg-[#0f3d3e] text-white rounded-lg hover:bg-opacity-90 transition-all">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to Dashboard
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Initialize Lucide icons
if (typeof lucide !== 'undefined') {
    lucide.createIcons();
}
</script>
@endpush
@endsection


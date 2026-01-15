@extends('layouts.admin')

@section('page-title', 'Add Staff Member')
@section('page-subtitle', 'Create a new staff account for booking verification')

@section('page-content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-md p-gr-lg border-2 border-lgu-stroke">
        {{-- Header --}}
        <div class="flex items-center gap-gr-sm mb-gr-lg">
            <a href="{{ route('admin.staff.index') }}" class="p-gr-xs rounded-lg hover:bg-gray-100">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h2 class="text-h2 font-bold text-lgu-headline">Add Staff Member</h2>
                <p class="text-sm text-lgu-paragraph">Fill in the details to create a new staff account</p>
            </div>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('admin.staff.store') }}" class="space-y-gr-md">
            @csrf

            {{-- Name --}}
            <div>
                <label for="name" class="block text-sm font-semibold mb-gr-xs text-lgu-headline">
                    Full Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                       class="w-full px-gr-sm py-gr-xs rounded-lg border-2 border-lgu-stroke focus:border-lgu-green focus:ring-0
                       @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-semibold mb-gr-xs text-lgu-headline">
                    Email Address <span class="text-red-500">*</span>
                </label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                       class="w-full px-gr-sm py-gr-xs rounded-lg border-2 border-lgu-stroke focus:border-lgu-green focus:ring-0
                       @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Phone --}}
            <div>
                <label for="phone" class="block text-sm font-semibold mb-gr-xs text-lgu-headline">
                    Phone Number <span class="text-red-500">*</span>
                </label>
                <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" required
                       placeholder="+63 XXX XXX XXXX"
                       class="w-full px-gr-sm py-gr-xs rounded-lg border-2 border-lgu-stroke focus:border-lgu-green focus:ring-0
                       @error('phone') border-red-500 @enderror">
                @error('phone')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- City Assignment --}}
            <div>
                <label for="city_id" class="block text-sm font-semibold mb-gr-xs text-lgu-headline">
                    City Assignment <span class="text-red-500">*</span>
                </label>
                <select name="city_id" id="city_id" required
                        class="w-full px-gr-sm py-gr-xs rounded-lg border-2 border-lgu-stroke focus:border-lgu-green focus:ring-0
                        @error('city_id') border-red-500 @enderror">
                    <option value="">-- Select City --</option>
                    @foreach($cities as $city)
                        <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>
                            {{ $city->city_name }}
                        </option>
                    @endforeach
                </select>
                @error('city_id')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-lgu-paragraph">Staff will verify bookings for this city</p>
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="block text-sm font-semibold mb-gr-xs text-lgu-headline">
                    Password <span class="text-red-500">*</span>
                </label>
                <input type="password" name="password" id="password" required
                       class="w-full px-gr-sm py-gr-xs rounded-lg border-2 border-lgu-stroke focus:border-lgu-green focus:ring-0
                       @error('password') border-red-500 @enderror">
                @error('password')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-lgu-paragraph">Minimum 8 characters</p>
            </div>

            {{-- Confirm Password --}}
            <div>
                <label for="password_confirmation" class="block text-sm font-semibold mb-gr-xs text-lgu-headline">
                    Confirm Password <span class="text-red-500">*</span>
                </label>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                       class="w-full px-gr-sm py-gr-xs rounded-lg border-2 border-lgu-stroke focus:border-lgu-green focus:ring-0">
            </div>

            {{-- Actions --}}
            <div class="flex gap-gr-sm pt-gr-sm border-t-2 border-lgu-stroke">
                <button type="submit" class="flex-1 btn-primary">
                    <i data-lucide="user-plus" class="w-5 h-5"></i>
                    Add Staff Member
                </button>
                <a href="{{ route('admin.staff.index') }}" class="flex-1 btn-secondary">
                    <i data-lucide="x" class="w-5 h-5"></i>
                    Cancel
                </a>
            </div>
        </form>
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


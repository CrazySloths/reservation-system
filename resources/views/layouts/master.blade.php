<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'LGU1 Public Facilities')</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}">
    
    <!-- Poppins Font (PROJECT_DESIGN_RULES.md requirement) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- ApexCharts for dashboard graphs (per ARCHITECTURE.md) -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.45.0/dist/apexcharts.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Lucide Icons (PROJECT_DESIGN_RULES.md requirement) -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    @stack('styles')
</head>
<body class="h-full">
    <div class="min-h-full">
        @yield('content')
    </div>
    
    @stack('scripts')

    <!-- Initialize Lucide Icons -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>

    <!-- 2-MINUTE SESSION TIMEOUT (CRITICAL FOR DEFENSE - PROJECT_DESIGN_RULES.md) -->
    <!-- Silent logout after 2 minutes of inactivity - no warnings, no modals -->
    <!-- Only runs when user is authenticated (custom session check) -->
    @if(session('user_id'))
    <script>
        (function() {
            // Extra safety: Don't run on auth pages (login, register, password reset)
            const currentPath = window.location.pathname;
            if (currentPath.includes('/login') || currentPath.includes('/register') || currentPath.includes('/password')) {
                return; // Exit immediately
            }

            let sessionTimeout;
            const SESSION_DURATION = {{ config('session.lifetime') }} * 60 * 1000; // Session timeout in milliseconds

            function resetSessionTimer() {
                clearTimeout(sessionTimeout);
                
                // Silent logout after session timeout
                sessionTimeout = setTimeout(() => {
                    // Create a form and submit POST request to logout
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/logout';
                    
                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    if (csrfToken) {
                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = csrfToken.content;
                        form.appendChild(csrfInput);
                    }
                    
                    document.body.appendChild(form);
                    form.submit();
                }, SESSION_DURATION);
            }

            ['click', 'mousemove', 'keypress', 'scroll', 'touchstart'].forEach(event => {
                document.addEventListener(event, resetSessionTimer);
            });

            document.addEventListener('DOMContentLoaded', resetSessionTimer);
        })();
    </script>
    @endif
</body>
</html>


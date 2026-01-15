<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('citizen-sidebar');
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebarClose = document.getElementById('citizen-sidebar-close');
    const sidebarOverlay = document.getElementById('sidebar-overlay');

    // Toggle sidebar
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('-translate-x-full');
            sidebarOverlay.classList.toggle('hidden');
        });
    }

    // Close sidebar
    if (sidebarClose) {
        sidebarClose.addEventListener('click', function() {
            sidebar.classList.add('-translate-x-full');
            sidebarOverlay.classList.add('hidden');
        });
    }

    // Close sidebar on overlay click
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function() {
            sidebar.classList.add('-translate-x-full');
            this.classList.add('hidden');
        });
    }
});

// --- Profile Expand/Collapse Toggle with Animations ---
let profileExpanded = false;
let isAnimating = false;

function toggleProfileExpanded() {
    if (isAnimating) return;
    
    const compactProfile = document.getElementById('profile-compact');
    const expandedDetails = document.getElementById('profile-expanded-details');
    
    if (!compactProfile || !expandedDetails) return;
    
    isAnimating = true;
    profileExpanded = !profileExpanded;
    
    if (profileExpanded) {
        compactProfile.style.opacity = '1';
        compactProfile.style.transform = 'translateY(0)';
        
        requestAnimationFrame(() => {
            compactProfile.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
            compactProfile.style.opacity = '0';
            compactProfile.style.transform = 'translateY(-10px)';
        });
        
        setTimeout(() => {
            compactProfile.classList.add('hidden');
            expandedDetails.classList.remove('hidden');
            expandedDetails.style.opacity = '0';
            expandedDetails.style.transform = 'translateY(10px)';
            
            requestAnimationFrame(() => {
                expandedDetails.style.transition = 'opacity 0.4s ease-in, transform 0.4s ease-in';
                expandedDetails.style.opacity = '1';
                expandedDetails.style.transform = 'translateY(0)';
            });
            
            setTimeout(() => {
                isAnimating = false;
            }, 400);
        }, 300);
        
    } else {
        expandedDetails.style.opacity = '1';
        expandedDetails.style.transform = 'translateY(0)';
        
        requestAnimationFrame(() => {
            expandedDetails.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
            expandedDetails.style.opacity = '0';
            expandedDetails.style.transform = 'translateY(10px)';
        });
        
        setTimeout(() => {
            expandedDetails.classList.add('hidden');
            compactProfile.classList.remove('hidden');
            compactProfile.style.opacity = '0';
            compactProfile.style.transform = 'translateY(-10px)';
            
            requestAnimationFrame(() => {
                compactProfile.style.transition = 'opacity 0.4s ease-in, transform 0.4s ease-in';
                compactProfile.style.opacity = '1';
                compactProfile.style.transform = 'translateY(0)';
            });
            
            setTimeout(() => {
                isAnimating = false;
            }, 400);
        }, 300);
    }
}

window.toggleProfileExpanded = toggleProfileExpanded;

// Settings Dropdown
const settingsButton = document.getElementById('citizen-settings-button');
const settingsDropdown = document.getElementById('citizen-settings-dropdown');

if (settingsButton) {
    settingsButton.addEventListener('click', function(event) {
        event.stopPropagation();
        settingsDropdown.classList.toggle('hidden');
    });
}

window.addEventListener('click', function(event) {
    if (settingsDropdown && !settingsDropdown.classList.contains('hidden') && !settingsButton.contains(event.target)) {
        settingsDropdown.classList.add('hidden');
    }
});

function confirmCitizenLogout() {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Sign Out?',
            text: "You will be logged out of your citizen portal.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#fa5246',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, sign me out',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('citizenLogoutForm').submit();
            }
        });
    } else {
        if (confirm('Are you sure you want to logout?')) {
            document.getElementById('citizenLogoutForm').submit();
        }
    }
}

window.confirmCitizenLogout = confirmCitizenLogout;

// Coming Soon Feature Alert
function showComingSoon(featureName) {
    Swal.fire({
        title: 'Coming Soon!',
        html: `
            <div class="text-center">
                <div class="mb-4">
                    <i data-lucide="rocket" class="w-16 h-16 mx-auto text-lgu-highlight"></i>
                </div>
                <p class="text-body text-lgu-paragraph mb-2">
                    <strong>${featureName}</strong> is currently under development.
                </p>
                <p class="text-small text-gray-600">
                    This feature will be available in a future update.
                </p>
            </div>
        `,
        icon: null,
        showCancelButton: false,
        confirmButtonText: '<i data-lucide="arrow-left" class="w-4 h-4 inline mr-2"></i>Go Back',
        confirmButtonColor: '#047857',
        customClass: {
            popup: 'rounded-xl',
            confirmButton: 'px-6 py-3 rounded-lg'
        },
        didOpen: () => {
            // Initialize Lucide icons in the modal
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }
    });
}

window.showComingSoon = showComingSoon;

// CSS for active states and transitions
const style = document.createElement('style');
style.textContent = `
    .sidebar-link {
        color: #9CA3AF;
    }
    
    .sidebar-link:hover {
        color: #FFFFFF;
        background-color: #00332c;
    }
    
    .sidebar-link.active {
        color: #faae2b;
        background-color: #00332c;
        border-left: 3px solid #faae2b;
    }
    
    #profile-compact,
    #profile-expanded-details {
        will-change: opacity, transform;
    }
    
    #profile-compact:hover .bg-lgu-highlight,
    #profile-expanded-details .bg-lgu-highlight:hover {
        animation: avatarPulse 0.6s ease-in-out;
    }
    
    @keyframes avatarPulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
            box-shadow: 0 0 20px rgba(250, 174, 43, 0.6);
        }
    }
    
    #citizen-sidebar nav::-webkit-scrollbar {
        width: 4px;
    }
    
    #citizen-sidebar nav::-webkit-scrollbar-track {
        background: transparent;
    }
    
    #citizen-sidebar nav::-webkit-scrollbar-thumb {
        background: #faae2b;
        border-radius: 4px;
    }
    
    #citizen-sidebar nav::-webkit-scrollbar-thumb:hover {
        background: #e09900;
    }
`;
document.head.appendChild(style);

// Set active menu item based on current URL
function setActiveLink() {
    const currentPath = window.location.pathname;
    const currentUrl = window.location.href;
    const sidebarLinks = document.querySelectorAll('.sidebar-link');
    
    // Remove active from all links first
    sidebarLinks.forEach(link => {
        link.classList.remove('active');
    });
    
    let activeFound = false;
    
    // Check links in order of specificity (most specific first)
    sidebarLinks.forEach(link => {
        if (activeFound) return; // Skip if we already found an active link
        
        const linkHref = link.getAttribute('href');
        if (!linkHref || linkHref === '#') return;
        
        // Most specific matches first
        if (linkHref.includes('/citizen/calendar') && currentPath.includes('/citizen/calendar')) {
            link.classList.add('active');
            activeFound = true;
        }
        else if (linkHref.includes('/citizen/facilities') && currentPath.includes('/citizen/facilities')) {
            link.classList.add('active');
            activeFound = true;
        }
        else if (linkHref.includes('/citizen/reservations/history') && currentPath.includes('/citizen/reservations/history')) {
            link.classList.add('active');
            activeFound = true;
        }
        else if (linkHref.includes('/citizen/reservations') && currentPath.includes('/citizen/reservations') && !currentPath.includes('history')) {
            link.classList.add('active');
            activeFound = true;
        }
        else if (linkHref.includes('/citizen/payments') && currentPath.includes('/citizen/payments')) {
            link.classList.add('active');
            activeFound = true;
        }
        else if (linkHref.includes('/citizen/bulletin') && currentPath.includes('/citizen/bulletin')) {
            link.classList.add('active');
            activeFound = true;
        }
        else if (linkHref.includes('/citizen/profile') && currentPath.includes('/citizen/profile')) {
            link.classList.add('active');
            activeFound = true;
        }
        else if (currentUrl === linkHref || currentPath === linkHref) {
            link.classList.add('active');
            activeFound = true;
        }
        else if ((linkHref.includes('/citizen/dashboard') || linkHref.includes('citizen.dashboard')) && currentPath === '/citizen/dashboard') {
            // Citizen Dashboard should only be active if no other specific match was found
            if (!activeFound) {
                link.classList.add('active');
                activeFound = true;
            }
        }
    });
}

// Set active link on page load
setActiveLink();
</script>


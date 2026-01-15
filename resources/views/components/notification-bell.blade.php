<!-- Notification Bell Icon (Facebook-style) -->
<div class="relative" x-data="notificationBell()" x-init="init()">
    <!-- Bell Icon Button -->
    <button @click="toggleDropdown()" class="relative p-2 text-gray-700 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
        <!-- Bell Icon (Lucide) -->
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-bell">
            <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/>
            <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>
        </svg>
        
        <!-- Unread Count Badge -->
        <span x-show="unreadCount > 0" 
              x-text="unreadCount > 99 ? '99+' : unreadCount"
              class="absolute -top-1 -right-1 flex items-center justify-center text-[9px] font-bold leading-none text-white bg-red-600 rounded-full w-[18px] h-[18px]"
              style="min-width: 18px; min-height: 18px;">
        </span>
    </button>

    <!-- Dropdown Menu -->
    <div x-show="isOpen" 
         @click.away="isOpen = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-xl border border-gray-200 z-50"
         style="display: none;">
        
        <!-- Header -->
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Notifications</h3>
            <button @click="markAllAsRead()" 
                    x-show="unreadCount > 0"
                    class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                Mark all as read
            </button>
        </div>

        <!-- Notifications List -->
        <div class="max-h-96 overflow-y-auto">
            <template x-if="notifications.length === 0">
                <div class="px-4 py-8 text-center text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-bell-off mx-auto mb-3 text-gray-400">
                        <path d="M8.7 3A6 6 0 0 1 18 8a21.3 21.3 0 0 0 .6 5"/>
                        <path d="M17 17H3s3-2 3-9a4.67 4.67 0 0 1 .3-1.7"/>
                        <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>
                        <path d="m2 2 20 20"/>
                    </svg>
                    <p class="text-sm">No notifications yet</p>
                </div>
            </template>

            <template x-for="notification in notifications" :key="notification.id">
                <div @click="showNotificationDetails(notification)" 
                     :class="notification.is_read ? 'bg-white' : 'bg-blue-50'"
                     class="px-4 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition-colors">
                    <div class="flex items-start">
                        <!-- Notification Icon -->
                        <div class="flex-shrink-0 mr-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-info text-blue-600">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path d="M12 16v-4"/>
                                    <path d="M12 8h.01"/>
                                </svg>
                            </div>
                        </div>
                        
                        <!-- Notification Content -->
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900" x-text="notification.message"></p>
                            <p class="text-xs text-gray-500 mt-1" x-text="notification.time_ago"></p>
                        </div>

                        <!-- Unread Indicator (only show if not read) -->
                        <div class="flex-shrink-0 ml-2" x-show="!notification.is_read">
                            <div class="w-2 h-2 bg-blue-600 rounded-full"></div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Footer -->
        <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
            <a href="{{ route('notifications.index') }}" 
               class="block text-center text-sm font-medium text-blue-600 hover:text-blue-800">
                View all notifications
            </a>
        </div>
    </div>
</div>

<script>
function notificationBell() {
    return {
        isOpen: false,
        notifications: [],
        unreadCount: 0,
        
        init() {
            this.fetchNotifications();
            // Poll for new notifications every 30 seconds
            setInterval(() => this.fetchNotifications(), 30000);
        },
        
        toggleDropdown() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.fetchNotifications();
            }
        },
        
        async fetchNotifications() {
            try {
                const response = await fetch('{{ route("notifications.unread") }}');
                const data = await response.json();
                this.notifications = data.notifications || [];
                this.unreadCount = data.unread_count || 0;
            } catch (error) {
                console.error('Failed to fetch notifications:', error);
            }
        },
        
        async markAsRead(notificationId) {
            try {
                await fetch(`/notifications/${notificationId}/read`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                this.fetchNotifications();
            } catch (error) {
                console.error('Failed to mark notification as read:', error);
            }
        },
        
        async showNotificationDetails(notification) {
            // Close dropdown
            this.isOpen = false;
            
            // Prepare HTML content with payment instructions if available
            let htmlContent = `<div class="text-left"><p class="text-gray-700 mb-3">${notification.message}</p>`;
            
            // Add payment button if this is a staff verification notification
            if (notification.data && notification.data.payment_slip_id) {
                htmlContent += `
                    <div class="mt-4 p-3 bg-blue-50 border-l-4 border-blue-600 rounded">
                        <p class="text-sm font-semibold text-blue-900 mb-2">Payment Required</p>
                        <p class="text-xs text-blue-800 mb-3">Amount: ₱${Number(notification.data.amount_due).toLocaleString('en-PH', {minimumFractionDigits: 2})}</p>
                        <p class="text-xs text-blue-800 font-semibold mb-1">Payment Options:</p>
                        <ul class="text-xs text-blue-800 list-disc list-inside mb-2">
                            <li><strong>Cash</strong> - Pay at City Treasurer's Office (CTO)</li>
                            <li><strong>Cashless</strong> - GCash, Maya, or Bank Transfer</li>
                        </ul>
                        <p class="text-xs text-blue-800">Click "View Payment Slip" to proceed.</p>
                    </div>
                `;
            }
            
            htmlContent += `<p class="text-xs text-gray-500 mt-3">${notification.time_ago}</p></div>`;
            
            // Show SweetAlert2 modal
            const result = await Swal.fire({
                title: 'Notification',
                html: htmlContent,
                icon: 'info',
                confirmButtonText: notification.data && notification.data.payment_slip_id ? 'View Payment Slip' : 'OK',
                confirmButtonColor: '#0f3d3e',
                showCancelButton: notification.data && notification.data.payment_slip_id,
                cancelButtonText: 'Close',
                cancelButtonColor: '#6b7280',
            });
            
            // If user clicked "View Payment Slip", redirect to payment slips page
            if (result.isConfirmed && notification.data && notification.data.payment_slip_id) {
                window.location.href = '/citizen/payments';
            }
            
            // Mark as read
            await this.markAsRead(notification.id);
        },
        
        async markAllAsRead() {
            try {
                await fetch('{{ route("notifications.read-all") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                this.fetchNotifications();
            } catch (error) {
                console.error('Failed to mark all as read:', error);
            }
        }
    }
}
</script>


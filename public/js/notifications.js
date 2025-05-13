/**
 * Notifications JavaScript
 * Handles real-time notification updates and interactions
 */

document.addEventListener('DOMContentLoaded', function() {
    // Check for new notifications every 30 seconds
    setInterval(checkForNewNotifications, 30000);
    
    // Initial check for notifications
    checkForNewNotifications();
    
    // Add event listeners for notification actions
    setupNotificationActions();
});

/**
 * Check for new notifications via AJAX
 */
function checkForNewNotifications() {
    fetch('/notifications/unread')
        .then(response => response.json())
        .then(data => {
            updateNotificationBadge(data.count);
            updateNotificationDropdown(data.notifications);
        })
        .catch(error => console.error('Error fetching notifications:', error));
}

/**
 * Update the notification badge count
 */
function updateNotificationBadge(count) {
    const badge = document.querySelector('.notification-badge');
    
    if (count > 0) {
        if (badge) {
            // Update existing badge
            badge.textContent = count > 99 ? '99+' : count;
        } else {
            // Create new badge if it doesn't exist
            const button = document.querySelector('#notificationDropdown');
            if (button) {
                const newBadge = document.createElement('span');
                newBadge.className = 'position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notification-badge';
                newBadge.textContent = count > 99 ? '99+' : count;
                
                const srOnly = document.createElement('span');
                srOnly.className = 'visually-hidden';
                srOnly.textContent = 'unread notifications';
                
                newBadge.appendChild(srOnly);
                button.appendChild(newBadge);
            }
        }
    } else if (badge) {
        // Remove badge if count is 0
        badge.remove();
    }
}

/**
 * Update the notification dropdown content
 */
function updateNotificationDropdown(notifications) {
    const notificationBody = document.querySelector('.notification-body');
    if (!notificationBody) return;
    
    if (notifications.length === 0) {
        notificationBody.innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-bell-slash fa-2x text-muted mb-2"></i>
                <p class="mb-0 text-muted">No notifications</p>
            </div>
        `;
        return;
    }
    
    let notificationHTML = '<div class="list-group list-group-flush">';
    
    notifications.forEach(notification => {
        const data = notification.data;
        let iconClass = 'fas fa-bell';
        let bgClass = 'new-job';
        
        // Determine icon and background class based on notification type
        if (data.type) {
            switch(data.type) {
                case 'new_job':
                    iconClass = 'fas fa-briefcase';
                    bgClass = 'new-job';
                    break;
                case 'application_status':
                    iconClass = 'fas fa-clipboard-check';
                    bgClass = 'application-status';
                    break;
                case 'new_application':
                    iconClass = 'fas fa-file-alt';
                    bgClass = 'new-application';
                    break;
                case 'new_user':
                    iconClass = 'fas fa-user-plus';
                    bgClass = 'new-user';
                    break;
                case 'new_job_admin':
                    iconClass = 'fas fa-exclamation-circle';
                    bgClass = 'new-job-admin';
                    break;
            }
        }
        
        notificationHTML += `
            <div class="list-group-item notification-item unread p-2" data-notification-id="${notification.id}">
                <div class="d-flex align-items-start">
                    <div class="notification-icon ${bgClass} me-2" style="width: 30px; height: 30px; font-size: 0.8rem;">
                        <i class="${iconClass}"></i>
                    </div>
                    
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <p class="mb-0 small fw-medium">${data.message || 'You have a new notification.'}</p>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <small class="text-muted">${notification.created_at}</small>
                            
                            <form action="/notifications/${notification.id}/read" method="POST" class="mark-read-form">
                                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
                                <button type="submit" class="btn btn-sm p-0 text-primary" style="font-size: 0.7rem;">
                                    Mark as read
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    notificationHTML += '</div>';
    notificationBody.innerHTML = notificationHTML;
    
    // Re-attach event listeners to new elements
    setupNotificationActions();
}

/**
 * Setup event listeners for notification actions
 */
function setupNotificationActions() {
    // Mark as read buttons
    document.querySelectorAll('.mark-read-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(form);
            const notificationId = form.closest('.notification-item').dataset.notificationId;
            
            fetch(`/notifications/${notificationId}/read`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Update UI after marking as read
                checkForNewNotifications();
            })
            .catch(error => console.error('Error marking notification as read:', error));
        });
    });
}

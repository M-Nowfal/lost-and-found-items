/**
 * Lost & Found Portal - Notifications JavaScript
 */

let notificationInterval = null;

// Initialize notifications
document.addEventListener('DOMContentLoaded', () => {
    loadNotifications();
    
    // Poll for new notifications every 60 seconds
    notificationInterval = setInterval(loadNotifications, 60000);
});

// Load Notifications
async function loadNotifications() {
    try {
        const response = await API.get('/notifications.php');
        updateNotificationBadge(response.unread_count);
    } catch (error) {
        console.error('Failed to load notifications:', error);
    }
}

// Update Badge Count
function updateNotificationBadge(count) {
    const badges = document.querySelectorAll('.notification-badge');
    badges.forEach(badge => {
        if (count > 0) {
            badge.textContent = count > 99 ? '99+' : count;
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'none';
        }
    });
}

// Cleanup on page leave
window.addEventListener('beforeunload', () => {
    if (notificationInterval) {
        clearInterval(notificationInterval);
    }
});

/**
 * Lost & Found Portal - Notifications JavaScript
 */

let notificationInterval = null;

// Initialize notifications
document.addEventListener('DOMContentLoaded', () => {
    loadNotifications();
    
    // Poll for new notifications every 30 seconds
    notificationInterval = setInterval(loadNotifications, 30000);
    
    // Mark as read on click
    document.getElementById('notificationList')?.addEventListener('click', async (e) => {
        const item = e.target.closest('[data-notification-id]');
        if (item && !item.classList.contains('read')) {
            await markAsRead(item.dataset.notificationId);
        }
    });
    
    // Mark all as read
    document.getElementById('markAllRead')?.addEventListener('click', markAllAsRead);
});

// Load Notifications
async function loadNotifications() {
    try {
        const response = await API.get('/notifications.php');
        
        updateNotificationBadge(response.unread_count);
        renderNotifications(response.notifications);
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

// Render Notifications List
function renderNotifications(notifications) {
    const list = document.getElementById('notificationList');
    const emptyState = document.getElementById('notificationEmpty');
    
    if (!list) return;
    
    if (!notifications || notifications.length === 0) {
        list.innerHTML = '';
        if (emptyState) emptyState.style.display = 'block';
        return;
    }
    
    if (emptyState) emptyState.style.display = 'none';
    
    list.innerHTML = notifications.map(notif => `
        <div class="notification-item ${notif.read ? 'read' : 'unread'}" data-notification-id="${notif.id}">
            <div class="notification-icon notification-icon-${notif.type}">
                ${getNotificationIcon(notif.type)}
            </div>
            <div class="notification-content">
                <p class="notification-message">${notif.message}</p>
                <span class="notification-time">${timeAgo(notif.created_at)}</span>
            </div>
        </div>
    `).join('');
}

// Get Icon for Notification Type
function getNotificationIcon(type) {
    const icons = {
        match: '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>',
        verification: '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
        system: '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
    };
    return icons[type] || icons.system;
}

// Mark Single Notification as Read
async function markAsRead(id) {
    try {
        await API.post('/notifications.php', { id, action: 'mark_read' });
        
        const item = document.querySelector(`[data-notification-id="${id}"]`);
        if (item) {
            item.classList.remove('unread');
            item.classList.add('read');
        }
        
        // Update badge count
        const badge = document.querySelector('.notification-badge');
        if (badge) {
            const count = parseInt(badge.textContent) - 1;
            updateNotificationBadge(Math.max(0, count));
        }
    } catch (error) {
        console.error('Failed to mark notification as read:', error);
    }
}

// Mark All as Read
async function markAllAsRead() {
    try {
        await API.post('/notifications.php', { action: 'mark_all_read' });
        
        // Update UI
        document.querySelectorAll('.notification-item.unread').forEach(item => {
            item.classList.remove('unread');
            item.classList.add('read');
        });
        
        updateNotificationBadge(0);
        Toast.success('All notifications marked as read');
    } catch (error) {
        Toast.error('Failed to mark all as read');
    }
}

// Show Notification Dropdown
function toggleNotifications() {
    const dropdown = document.getElementById('notificationDropdown');
    if (dropdown) {
        dropdown.classList.toggle('show');
        
        // Close when clicking outside
        if (dropdown.classList.contains('show')) {
            document.addEventListener('click', function closeDropdown(e) {
                if (!e.target.closest('.notification-bell') && !e.target.closest('#notificationDropdown')) {
                    dropdown.classList.remove('show');
                    document.removeEventListener('click', closeDropdown);
                }
            });
        }
    }
}

// Cleanup on page leave
window.addEventListener('beforeunload', () => {
    if (notificationInterval) {
        clearInterval(notificationInterval);
    }
});

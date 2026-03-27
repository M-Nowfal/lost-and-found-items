/**
 * Lost & Found Portal - Main JavaScript
 */

// API Base URL - auto-detect project base from the script source path
const API_BASE = (() => {
    // If ADMIN_API_BASE is set (admin pages), use it
    if (typeof ADMIN_API_BASE !== 'undefined') return ADMIN_API_BASE + 'api';
    // Otherwise detect from the current script's src attribute  
    const scripts = document.querySelectorAll('script[src*="main.js"]');
    if (scripts.length > 0) {
        const src = scripts[scripts.length - 1].src;
        const base = src.substring(0, src.indexOf('/assets/'));
        return base + '/api';
    }
    return window.location.origin + '/api';
})();

// Toast Notifications
const Toast = {
    container: null,

    init() {
        if (!this.container) {
            this.container = document.createElement('div');
            this.container.className = 'toast-container';
            document.body.appendChild(this.container);
        }
    },

    show(message, type = 'info', duration = 4000) {
        this.init();

        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;

        const icons = {
            success: '<svg width="20" height="20" fill="none" stroke="#10b981" stroke-width="2"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            error: '<svg width="20" height="20" fill="none" stroke="#ef4444" stroke-width="2"><path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            info: '<svg width="20" height="20" fill="none" stroke="#3b82f6" stroke-width="2"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            warning: '<svg width="20" height="20" fill="none" stroke="#f59e0b" stroke-width="2"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>'
        };

        toast.innerHTML = `
            ${icons[type]}
            <span>${message}</span>
        `;

        this.container.appendChild(toast);

        setTimeout(() => {
            toast.style.animation = 'slideIn 0.3s ease reverse';
            setTimeout(() => toast.remove(), 300);
        }, duration);
    },

    success(message) { this.show(message, 'success'); },
    error(message) { this.show(message, 'error'); },
    info(message) { this.show(message, 'info'); },
    warning(message) { this.show(message, 'warning'); }
};

// API Helper
const API = {
    async request(endpoint, options = {}) {
        const url = `${API_BASE}${endpoint}`;

        const config = {
            headers: {
                'Content-Type': 'application/json',
                ...options.headers
            },
            ...options
        };

        if (config.body && typeof config.body === 'object' && !(config.body instanceof FormData)) {
            config.body = JSON.stringify(config.body);
        }

        if (config.body instanceof FormData) {
            delete config.headers['Content-Type'];
        }

        try {
            const response = await fetch(url, config);
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || data.errors?.general || 'Request failed');
            }

            return data;
        } catch (error) {
            if (error.message === 'Unauthorized') {
                window.location.href = API_BASE.replace('/api', '/views/auth/login.php');
            }
            throw error;
        }
    },

    get(endpoint) {
        return this.request(endpoint, { method: 'GET' });
    },

    post(endpoint, body) {
        return this.request(endpoint, { method: 'POST', body });
    },

    put(endpoint, body) {
        return this.request(endpoint, { method: 'PUT', body });
    },

    delete(endpoint, body) {
        return this.request(endpoint, { method: 'DELETE', body });
    }
};

// Form Validation
const Validation = {
    email(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    },

    required(value) {
        return value !== null && value !== undefined && value.toString().trim() !== '';
    },

    minLength(value, min) {
        return value && value.length >= min;
    },

    passwordsMatch(password, confirmPassword) {
        return password === confirmPassword;
    }
};

// Format Date
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('en-US', options);
}

// Time Ago
function timeAgo(dateString) {
    const seconds = Math.floor((new Date() - new Date(dateString)) / 1000);

    const intervals = {
        year: 31536000,
        month: 2592000,
        week: 604800,
        day: 86400,
        hour: 3600,
        minute: 60
    };

    for (const [unit, secondsInUnit] of Object.entries(intervals)) {
        const interval = Math.floor(seconds / secondsInUnit);
        if (interval >= 1) {
            return `${interval} ${unit}${interval > 1 ? 's' : ''} ago`;
        }
    }

    return 'Just now';
}

// Debounce Function
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Modal Functions
const Modal = {
    show(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    },

    hide(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
    },

    init() {
        document.querySelectorAll('.modal-overlay').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });
        });

        document.querySelectorAll('[data-modal-close]').forEach(btn => {
            btn.addEventListener('click', () => {
                const modalId = btn.dataset.modalClose;
                this.hide(modalId);
            });
        });
    }
};

// Skeleton Loading
function showSkeleton(container, count = 6) {
    let html = '';
    for (let i = 0; i < count; i++) {
        html += `
            <div class="glass-card">
                <div class="skeleton" style="height: 200px; border-radius: 20px 20px 0 0;"></div>
                <div style="padding: 20px;">
                    <div class="skeleton" style="height: 20px; width: 80%; margin-bottom: 10px;"></div>
                    <div class="skeleton" style="height: 16px; width: 60%;"></div>
                </div>
            </div>
        `;
    }
    container.innerHTML = html;
}

// Mobile Menu
function toggleMobileMenu() {
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.querySelector('.sidebar-overlay');

    if (sidebar) sidebar.classList.toggle('active');
    if (overlay) overlay.classList.toggle('active');
}

// Initialize on DOM Load
document.addEventListener('DOMContentLoaded', () => {
    Modal.init();

    // Close modals on Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay.active').forEach(modal => {
                modal.classList.remove('active');
            });
            document.body.style.overflow = '';
        }
    });
});

// Export for use in other scripts
window.Toast = Toast;
window.API = API;
window.Validation = Validation;
window.formatDate = formatDate;
window.timeAgo = timeAgo;
window.debounce = debounce;
window.Modal = Modal;
window.showSkeleton = showSkeleton;
window.toggleMobileMenu = toggleMobileMenu;

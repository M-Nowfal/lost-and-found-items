<?php
/**
 * Admin Sidebar Component
 * Included by admin_layout.php - $adminBaseUrl is set in admin_layout.php
 */
if (!isset($currentPage)) $currentPage = '';
if (!isset($adminBaseUrl)) $adminBaseUrl = '/lost-and-found-items/';

// Get pending matches count for badge
require_once ROOT_PATH . 'models/Match.php';
$sidebarMatchModel = new MatchModel();
$pendingCount = $sidebarMatchModel->count('pending');
?>
<aside class="admin-sidebar" id="adminSidebar">
    <!-- Logo -->
    <div class="admin-sidebar-brand">
        <a href="<?= $adminBaseUrl ?>" class="flex items-center gap-3">
            <div class="admin-sidebar-logo">
                <svg width="32" height="32" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="18" cy="18" r="16" fill="url(#gradientAdmin)" />
                    <path d="M18 10C14.134 10 11 13.134 11 17C11 21.5 18 26 18 26C18 26 25 21.5 25 17C25 13.134 21.866 10 18 10Z" stroke="white" stroke-width="2" fill="none"/>
                    <circle cx="18" cy="17" r="3" stroke="white" stroke-width="2" fill="none"/>
                    <defs>
                        <linearGradient id="gradientAdmin" x1="2" y1="2" x2="34" y2="34" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#667EEA"/>
                            <stop offset="1" stop-color="#764BA2"/>
                        </linearGradient>
                    </defs>
                </svg>
            </div>
            <div>
                <span class="text-white font-bold text-lg tracking-tight">Admin Panel</span>
                <p class="text-gray-400 text-xs">Lost & Found Portal</p>
            </div>
        </a>
    </div>

    <!-- Navigation -->
    <nav class="admin-sidebar-nav">
        <p class="admin-sidebar-section-title">Main Menu</p>
        
        <a href="<?= $adminBaseUrl ?>views/admin/dashboard.php" class="admin-sidebar-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zM14 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/></svg>
            <span>Dashboard</span>
        </a>
        <a href="<?= $adminBaseUrl ?>views/admin/users.php" class="admin-sidebar-link <?= $currentPage === 'users' ? 'active' : '' ?>">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            <span>Users</span>
        </a>
        <a href="<?= $adminBaseUrl ?>views/admin/items.php" class="admin-sidebar-link <?= $currentPage === 'items' ? 'active' : '' ?>">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            <span>Items</span>
        </a>
        <a href="<?= $adminBaseUrl ?>views/admin/matches.php" class="admin-sidebar-link <?= $currentPage === 'matches' ? 'active' : '' ?>">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
            <span>Matches</span>
            <?php if ($pendingCount > 0): ?>
            <span class="admin-sidebar-badge"><?= $pendingCount ?></span>
            <?php endif; ?>
        </a>

        <div class="admin-sidebar-divider"></div>
        <p class="admin-sidebar-section-title">Other</p>

        <a href="<?= $adminBaseUrl ?>views/user/dashboard.php" class="admin-sidebar-link">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/></svg>
            <span>Back to Portal</span>
        </a>
        <a href="#" onclick="logout(); return false;" class="admin-sidebar-link admin-sidebar-link-danger">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            <span>Logout</span>
        </a>
    </nav>

    <!-- Sidebar Footer -->
    <div class="admin-sidebar-footer">
        <div class="flex items-center gap-3">
            <div class="admin-avatar-sidebar"><?= strtoupper(substr($_SESSION['name'] ?? 'A', 0, 1)) ?></div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-white truncate"><?= $_SESSION['name'] ?? 'Admin' ?></p>
                <p class="text-xs text-gray-400 truncate"><?= $_SESSION['email'] ?? '' ?></p>
            </div>
        </div>
    </div>
</aside>

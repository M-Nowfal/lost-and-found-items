<?php
/**
 * Admin Dashboard
 * Lost & Found Portal
 */
require_once '../../config/constants.php';
require_once '../../config/database.php';
requireAdmin();

$pageTitle = 'Dashboard';
$currentPage = 'dashboard';

require_once '../../models/User.php';
require_once '../../models/Item.php';
require_once '../../models/Match.php';

$userModel = new User();
$itemModel = new Item();
$matchModel = new MatchModel();

$stats = [
    'users' => [
        'total' => $userModel->count(),
        'admins' => $userModel->count('admin'),
        'unverified' => count($userModel->getUnverified())
    ],
    'items' => $itemModel->getStats(),
    'matches' => [
        'pending' => $matchModel->count('pending'),
        'approved' => $matchModel->count('approved'),
        'total' => $matchModel->count()
    ]
];

$recentItems = $itemModel->getRecent(5);
$recentUsers = $userModel->getAll(5, 0);
$pendingMatches = $matchModel->getPending();

ob_start();
?>

<!-- Stats Overview -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
    <!-- Total Users -->
    <div class="admin-stat-card">
        <div class="admin-stat-icon admin-stat-icon-indigo">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
        </div>
        <div class="admin-stat-content">
            <p class="admin-stat-label">Total Users</p>
            <p class="admin-stat-number"><?= $stats['users']['total'] ?></p>
        </div>
        <div class="admin-stat-footer">
            <span class="admin-stat-meta">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"/></svg>
                <?= $stats['users']['unverified'] ?> pending
            </span>
        </div>
    </div>

    <!-- Total Items -->
    <div class="admin-stat-card">
        <div class="admin-stat-icon admin-stat-icon-purple">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
        </div>
        <div class="admin-stat-content">
            <p class="admin-stat-label">Total Items</p>
            <p class="admin-stat-number"><?= $stats['items']['total'] ?></p>
        </div>
        <div class="admin-stat-footer">
            <span class="admin-stat-meta">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"/></svg>
                <?= $stats['items']['pending'] ?> pending
            </span>
        </div>
    </div>

    <!-- Lost Items -->
    <div class="admin-stat-card">
        <div class="admin-stat-icon admin-stat-icon-amber">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
        <div class="admin-stat-content">
            <p class="admin-stat-label">Lost Items</p>
            <p class="admin-stat-number"><?= $stats['items']['lost'] ?></p>
        </div>
        <div class="admin-stat-footer">
            <span class="admin-stat-meta text-amber-600">Active reports</span>
        </div>
    </div>

    <!-- Pending Matches -->
    <div class="admin-stat-card">
        <div class="admin-stat-icon admin-stat-icon-emerald">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
            </svg>
        </div>
        <div class="admin-stat-content">
            <p class="admin-stat-label">Matches</p>
            <p class="admin-stat-number"><?= $stats['matches']['total'] ?></p>
        </div>
        <div class="admin-stat-footer">
            <span class="admin-stat-meta">
                <?= $stats['matches']['approved'] ?> approved &bull; <?= $stats['matches']['pending'] ?> pending
            </span>
        </div>
    </div>
</div>

<!-- Pending Matches Alert -->
<?php if (!empty($pendingMatches)): ?>
<div class="admin-alert-card mb-8">
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <div class="flex-1">
            <h3 class="font-semibold text-lg text-white"><?= count($pendingMatches) ?> Match<?= count($pendingMatches) > 1 ? 'es' : '' ?> Pending Review</h3>
            <p class="text-white/80 text-sm">Review and approve pending item matches</p>
        </div>
        <a href="matches.php" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-indigo-600 font-semibold rounded-xl hover:bg-indigo-50 transition-all shadow-lg flex-shrink-0">
            Review
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>
</div>
<?php endif; ?>

<!-- Content Grid -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Recent Items -->
    <div class="admin-content-card">
        <div class="admin-content-card-header">
            <h2 class="text-lg font-bold text-gray-900">Recent Items</h2>
            <a href="items.php" class="admin-view-all-link">
                View All
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
        
        <?php if (empty($recentItems)): ?>
        <div class="text-center py-12 text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            <p>No items reported yet</p>
        </div>
        <?php else: ?>
        <div class="admin-list">
            <?php foreach ($recentItems as $item): ?>
            <div class="admin-list-item">
                <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center overflow-hidden flex-shrink-0">
                    <?php if ($item['image_path']): ?>
                    <img src="/<?= $item['image_path'] ?>" class="w-full h-full object-cover" alt="">
                    <?php else: ?>
                    <span class="text-lg">📦</span>
                    <?php endif; ?>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-gray-900 truncate text-sm"><?= htmlspecialchars($item['title']) ?></p>
                    <p class="text-xs text-gray-500"><?= htmlspecialchars($item['user_name']) ?></p>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <span class="badge badge-<?= $item['type'] ?> text-xs"><?= $item['type'] ?></span>
                    <?php if (!$item['verified']): ?>
                    <span class="w-2 h-2 rounded-full bg-amber-400" title="Pending verification"></span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Recent Users -->
    <div class="admin-content-card">
        <div class="admin-content-card-header">
            <h2 class="text-lg font-bold text-gray-900">Recent Users</h2>
            <a href="users.php" class="admin-view-all-link">
                View All
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
        
        <?php if (empty($recentUsers)): ?>
        <div class="text-center py-12 text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            <p>No users registered</p>
        </div>
        <?php else: ?>
        <div class="admin-list">
            <?php foreach ($recentUsers as $user): ?>
            <div class="admin-list-item">
                <div class="admin-avatar-small"><?= strtoupper(substr($user['name'], 0, 1)) ?></div>
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-gray-900 truncate text-sm"><?= htmlspecialchars($user['name']) ?></p>
                    <p class="text-xs text-gray-500 truncate"><?= htmlspecialchars($user['email']) ?></p>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <span class="badge <?= $user['role'] === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' ?> text-xs"><?= $user['role'] ?></span>
                    <?php if ($user['role'] === 'user' && !$user['verified']): ?>
                    <span class="w-2 h-2 rounded-full bg-amber-400" title="Unverified"></span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include '../layouts/admin_layout.php';
?>

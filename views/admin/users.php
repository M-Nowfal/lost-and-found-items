<?php
/**
 * Admin Users Management
 * Lost & Found Portal
 */
require_once '../../config/constants.php';
require_once '../../config/database.php';
requireAdmin();

$pageTitle = 'Manage Users';
$currentPage = 'users';

require_once '../../models/User.php';
$userModel = new User();
$users = $userModel->getAll(100, 0);

ob_start();
?>

<!-- Users Table -->
<div class="admin-content-card overflow-hidden">
    <div class="admin-content-card-header">
        <h2 class="text-lg font-bold text-gray-900">All Users</h2>
        <span class="text-sm text-gray-500"><?= count($users) ?> total</span>
    </div>
    
    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="admin-avatar-small"><?= strtoupper(substr($user['name'], 0, 1)) ?></div>
                            <span class="font-medium text-gray-900"><?= htmlspecialchars($user['name']) ?></span>
                        </div>
                    </td>
                    <td class="text-gray-600"><?= htmlspecialchars($user['email']) ?></td>
                    <td>
                        <span class="badge <?= $user['role'] === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' ?>"><?= ucfirst($user['role']) ?></span>
                    </td>
                    <td>
                        <?php if ($user['role'] === 'admin'): ?>
                        <span class="badge badge-verified">Admin</span>
                        <?php elseif ($user['verified']): ?>
                        <span class="badge badge-verified">Verified</span>
                        <?php else: ?>
                        <span class="badge badge-pending">Pending</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-gray-500 text-sm"><?= formatDate($user['created_at']) ?></td>
                    <td>
                        <div class="flex items-center gap-2">
                            <?php if ($user['role'] === 'user'): ?>
                            <button onclick="toggleUserVerification(<?= $user['id'] ?>, <?= $user['verified'] ? 'false' : 'true' ?>)" class="btn btn-sm <?= $user['verified'] ? 'btn-danger' : 'btn-success' ?>">
                                <?= $user['verified'] ? 'Revoke' : 'Verify' ?>
                            </button>
                            <?php else: ?>
                            <span class="text-xs text-gray-400">—</span>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
async function toggleUserVerification(userId, verified) {
    try {
        const response = await fetch(ADMIN_API_BASE + 'api/admin/verify-user.php', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_id: userId, verified })
        });
        const data = await response.json();
        
        if (data.success) {
            Toast.success(data.message);
            setTimeout(() => location.reload(), 1000);
        } else {
            Toast.error(data.message || 'Failed');
        }
    } catch (error) {
        Toast.error('Request failed');
    }
}
</script>

<?php
$content = ob_get_clean();
include '../layouts/admin_layout.php';
?>

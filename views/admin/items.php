<?php
/**
 * Admin Items Management
 * Lost & Found Portal
 */
require_once '../../config/constants.php';
require_once '../../config/database.php';
requireAdmin();

$pageTitle = 'Manage Items';
$currentPage = 'items';

require_once '../../models/Item.php';
$itemModel = new Item();
$items = $itemModel->getAll(100, 0, []);

ob_start();
?>

<!-- Items Table -->
<div class="admin-content-card overflow-hidden">
    <div class="admin-content-card-header">
        <h2 class="text-lg font-bold text-gray-900">All Items</h2>
        <span class="text-sm text-gray-500"><?= count($items) ?> total</span>
    </div>
    
    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Type</th>
                    <th>Category</th>
                    <th>Owner</th>
                    <th>Status</th>
                    <th>Verified</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center overflow-hidden flex-shrink-0">
                                <?php if ($item['image_path']): ?>
                                <img src="/<?= $item['image_path'] ?>" class="w-full h-full object-cover" alt="">
                                <?php else: ?>
                                <span class="text-lg">📦</span>
                                <?php endif; ?>
                            </div>
                            <div class="min-w-0">
                                <p class="font-medium text-gray-900 truncate"><?= htmlspecialchars($item['title']) ?></p>
                                <p class="text-xs text-gray-500 truncate"><?= htmlspecialchars($item['location']) ?></p>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge badge-<?= $item['type'] ?>"><?= ucfirst($item['type']) ?></span></td>
                    <td class="text-gray-600 text-sm"><?= htmlspecialchars($item['category']) ?></td>
                    <td class="text-gray-600 text-sm"><?= htmlspecialchars($item['user_name']) ?></td>
                    <td><span class="badge badge-<?= $item['status'] ?>"><?= ucfirst($item['status']) ?></span></td>
                    <td>
                        <?php if ($item['verified']): ?>
                        <span class="badge badge-verified">Verified</span>
                        <?php else: ?>
                        <span class="badge badge-pending">Pending</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="flex items-center gap-2">
                            <button onclick="toggleItemVerification(<?= $item['id'] ?>, <?= $item['verified'] ? 'false' : 'true' ?>)" class="btn btn-sm <?= $item['verified'] ? 'btn-outline' : 'btn-success' ?>">
                                <?= $item['verified'] ? 'Unverify' : 'Verify' ?>
                            </button>
                            <button onclick="deleteItem(<?= $item['id'] ?>)" class="btn btn-sm btn-danger">Delete</button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
async function toggleItemVerification(itemId, verified) {
    try {
        const response = await fetch(ADMIN_API_BASE + 'api/admin/verify-item.php', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ item_id: itemId, verified })
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

async function deleteItem(itemId) {
    if (!confirm('Are you sure you want to delete this item?')) return;
    
    try {
        const response = await fetch(ADMIN_API_BASE + 'api/admin/delete-item.php', {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ item_id: itemId })
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

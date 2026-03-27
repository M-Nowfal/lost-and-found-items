<?php
/**
 * Admin Matches Management
 * Lost & Found Portal
 */
require_once '../../config/constants.php';
require_once '../../config/database.php';
requireAdmin();

$pageTitle = 'Manage Matches';
$currentPage = 'matches';

require_once '../../models/Match.php';
$matchModel = new MatchModel();
$matches = $matchModel->getAll(100, 0);

ob_start();
?>

<?php if (empty($matches)): ?>
<!-- Empty State -->
<div class="admin-content-card">
    <div class="text-center py-16">
        <div class="w-20 h-20 mx-auto mb-6 rounded-2xl bg-gray-100 flex items-center justify-center">
            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
            </svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">No matches yet</h3>
        <p class="text-gray-500 max-w-sm mx-auto">Matches will appear here when users report items that match each other.</p>
    </div>
</div>

<?php else: ?>
<!-- Matches List -->
<div class="space-y-4 sm:space-y-6">
    <?php foreach ($matches as $match): ?>
    <div class="admin-content-card">
        <div class="p-5 sm:p-6">
            <!-- Match Header -->
            <div class="flex flex-wrap items-center gap-3 mb-5">
                <span class="badge badge-<?= $match['status'] ?>"><?= ucfirst($match['status']) ?></span>
                <span class="text-sm text-gray-500">
                    Similarity: <strong class="text-indigo-600"><?= $match['similarity_score'] ?>%</strong>
                </span>
                <span class="text-sm text-gray-400">&bull;</span>
                <span class="text-sm text-gray-500"><?= timeAgo($match['created_at']) ?></span>
                
                <?php if ($match['status'] === 'pending'): ?>
                <div class="ml-auto flex items-center gap-2">
                    <button onclick="approveMatch(<?= $match['id'] ?>)" class="btn btn-success btn-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Approve
                    </button>
                    <button onclick="rejectMatch(<?= $match['id'] ?>)" class="btn btn-danger btn-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Reject
                    </button>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Match Items -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 bg-amber-50 rounded-xl border border-amber-100">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-lg">🔍</span>
                        <span class="font-semibold text-amber-800 text-sm">Lost Item</span>
                    </div>
                    <p class="font-semibold text-gray-900"><?= htmlspecialchars($match['lost_title']) ?></p>
                    <p class="text-sm text-gray-600 mt-1">By: <?= htmlspecialchars($match['lost_user_name']) ?></p>
                    <p class="text-xs text-gray-500 mt-1">📍 <?= htmlspecialchars($match['lost_location']) ?></p>
                </div>
                
                <div class="p-4 bg-emerald-50 rounded-xl border border-emerald-100">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-lg">✨</span>
                        <span class="font-semibold text-emerald-800 text-sm">Found Item</span>
                    </div>
                    <p class="font-semibold text-gray-900"><?= htmlspecialchars($match['found_title']) ?></p>
                    <p class="text-sm text-gray-600 mt-1">By: <?= htmlspecialchars($match['found_user_name']) ?></p>
                    <p class="text-xs text-gray-500 mt-1">📍 <?= htmlspecialchars($match['found_location']) ?></p>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<script>
async function approveMatch(matchId) {
    try {
        const response = await fetch(ADMIN_API_BASE + 'api/admin/approve-match.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ match_id: matchId })
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

async function rejectMatch(matchId) {
    try {
        const response = await fetch(ADMIN_API_BASE + 'api/admin/reject-match.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ match_id: matchId })
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

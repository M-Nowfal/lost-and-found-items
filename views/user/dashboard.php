<?php
/**
 * User Dashboard
 * Lost & Found Portal
 */
require_once '../../config/constants.php';
require_once '../../config/database.php';
requireLogin();

$pageTitle = 'Dashboard';
$currentPage = 'dashboard';

require_once '../../models/Item.php';
require_once '../../models/Match.php';
require_once '../../models/Notification.php';

$itemModel = new Item();
$matchModel = new MatchModel();
$notificationModel = new Notification();

$userItems = $itemModel->findByUserId(getCurrentUserId());
$myMatches = $matchModel->getByUserId(getCurrentUserId());
$unreadCount = $notificationModel->countUnread(getCurrentUserId());
$stats = $itemModel->getStats();

$lostCount = 0;
$foundCount = 0;
$pendingCount = 0;
foreach ($userItems as $item) {
    if ($item['type'] === 'lost') $lostCount++;
    else $foundCount++;
    if ($item['status'] === 'pending') $pendingCount++;
}

ob_start();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Welcome Section -->
    <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h1 class="text-4xl font-black text-slate-900 tracking-tight mb-2">
                Welcome back, <span class="bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-purple-600"><?= htmlspecialchars($_SESSION['name']) ?></span>! 👋
            </h1>
            <p class="text-slate-500 font-medium text-lg">Manage your reports and track potential matches in real-time.</p>
        </div>
        <div class="flex gap-3">
            <a href="report.php?type=lost" class="px-6 py-3 bg-white text-slate-700 font-bold rounded-2xl shadow-sm border border-slate-200 hover:bg-slate-50 transition-all flex items-center gap-2">
                <span class="text-xl">🔍</span> Report Lost
            </a>
            <a href="report.php?type=found" class="px-6 py-3 bg-indigo-600 text-white font-bold rounded-2xl shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition-all flex items-center gap-2">
                <span class="text-xl">✨</span> Report Found
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm hover:shadow-md transition-all group">
            <div class="flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-indigo-50 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div>
                    <p class="text-3xl font-black text-slate-900 leading-none mb-1"><?= count($userItems) ?></p>
                    <p class="text-sm font-bold text-slate-400 uppercase tracking-wider">My Total Items</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm hover:shadow-md transition-all group">
            <div class="flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-amber-50 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-3xl font-black text-slate-900 leading-none mb-1"><?= $lostCount ?></p>
                    <p class="text-sm font-bold text-slate-400 uppercase tracking-wider">Lost Reports</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm hover:shadow-md transition-all group">
            <div class="flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-emerald-50 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-3xl font-black text-slate-900 leading-none mb-1"><?= $foundCount ?></p>
                    <p class="text-sm font-bold text-slate-400 uppercase tracking-wider">Found Reports</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm hover:shadow-md transition-all group">
            <div class="flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-purple-50 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                </div>
                <div>
                    <p class="text-3xl font-black text-slate-900 leading-none mb-1"><?= count($myMatches) ?></p>
                    <p class="text-sm font-bold text-slate-400 uppercase tracking-wider">Matches Found</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Layout Grid -->
    <div class="grid lg:grid-cols-3 gap-8">
        <!-- My Recent Items -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
                <div class="p-8 border-b border-slate-50 flex justify-between items-center bg-slate-50/50">
                    <h2 class="text-2xl font-black text-slate-900 tracking-tight">My Recent Reports</h2>
                    <a href="items.php" class="text-sm font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1 group">
                        View all <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
                
                <div class="p-4">
                    <?php if (empty($userItems)): ?>
                    <div class="py-20 text-center">
                        <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-1">No items reported yet</h3>
                        <p class="text-slate-500 mb-6">Start by reporting a lost or found item.</p>
                        <a href="report.php" class="inline-flex px-6 py-3 bg-indigo-600 text-white font-bold rounded-2xl hover:bg-indigo-700 transition-all">Report Now</a>
                    </div>
                    <?php else: ?>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <?php foreach (array_slice($userItems, 0, 4) as $item): ?>
                        <div class="p-4 rounded-2xl border border-slate-100 hover:border-indigo-100 hover:bg-slate-50 transition-all flex items-center gap-4 group">
                            <div class="w-20 h-20 rounded-xl bg-slate-100 flex-shrink-0 overflow-hidden relative border border-slate-200">
                                <?php if ($item['image_path']): ?>
                                <img src="<?= BASE_URL . $item['image_path'] ?>" alt="" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center bg-slate-50">
                                    <span class="text-3xl opacity-40">📦</span>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-widest <?= $item['type'] === 'lost' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' ?>">
                                        <?= $item['type'] ?>
                                    </span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-slate-100 text-slate-600">
                                        <?= $item['status'] ?>
                                    </span>
                                </div>
                                <h4 class="font-bold text-slate-900 truncate group-hover:text-indigo-600 transition-colors"><?= htmlspecialchars($item['title']) ?></h4>
                                <p class="text-xs font-semibold text-slate-400 flex items-center gap-1 mt-0.5">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <?= htmlspecialchars($item['location']) ?>
                                </p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar Section: Matches & Tips -->
        <div class="space-y-8">
            <!-- Recent Matches -->
            <div class="bg-indigo-600 rounded-[2rem] shadow-xl shadow-indigo-100 overflow-hidden text-white">
                <div class="p-8 border-b border-white/10 flex justify-between items-center">
                    <h2 class="text-xl font-black tracking-tight">Recent Matches</h2>
                    <?php if (!empty($myMatches)): ?>
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <?php endif; ?>
                </div>
                
                <div class="p-6">
                    <?php if (empty($myMatches)): ?>
                    <div class="text-center py-8 opacity-60">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                        <p class="text-sm font-bold">Waiting for matches...</p>
                    </div>
                    <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach (array_slice($myMatches, 0, 3) as $match): ?>
                        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/10 hover:bg-white/20 transition-all cursor-pointer">
                            <div class="flex items-center justify-between mb-2">
                                <span class="bg-emerald-400 text-indigo-900 text-[10px] font-black uppercase tracking-tighter px-2 py-0.5 rounded-full"><?= $match['status'] ?></span>
                                <span class="text-[10px] font-bold opacity-60"><?= timeAgo($match['created_at']) ?></span>
                            </div>
                            <p class="text-sm font-bold leading-tight mb-1 truncate"><?= htmlspecialchars($match['lost_title']) ?> ↔ <?= htmlspecialchars($match['found_title']) ?></p>
                            <div class="flex items-center gap-1.5">
                                <div class="flex-1 h-1.5 bg-white/20 rounded-full overflow-hidden">
                                    <div class="h-full bg-emerald-400" style="width: <?= $match['similarity_score'] ?>%"></div>
                                </div>
                                <span class="text-[10px] font-black"><?= $match['similarity_score'] ?>%</span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <a href="matches.php" class="block w-full py-3 bg-white text-indigo-600 rounded-xl text-center text-xs font-black uppercase tracking-widest hover:bg-indigo-50 transition-colors mt-4">Review all matches</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Tips Panel -->
            <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm p-8">
                <h3 class="text-lg font-black text-slate-900 mb-6">Reporting Tips</h3>
                <div class="space-y-6">
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center flex-shrink-0">
                            <span class="text-xl">📸</span>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-800 mb-0.5">Add Clear Photos</p>
                            <p class="text-xs text-slate-500 font-medium leading-relaxed">Images significantly increase matching accuracy by our AI algorithm.</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center flex-shrink-0">
                            <span class="text-xl">📍</span>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-800 mb-0.5">Specific Location</p>
                            <p class="text-xs text-slate-500 font-medium leading-relaxed">Be precise about where the item was lost or found for better results.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
<!-- Success Modal -->
<div id="successModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
    <div class="relative bg-white rounded-[2.5rem] shadow-2xl shadow-indigo-200/50 w-full max-w-sm overflow-hidden transform transition-all scale-100 opacity-100">
        <div class="p-8 text-center pt-12">
            <div class="w-24 h-24 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-6 relative">
                <div class="absolute inset-0 rounded-full bg-emerald-400/20 animate-ping"></div>
                <svg class="w-12 h-12 text-emerald-500 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-black text-slate-900 mb-2 mt-4 tracking-tight">Report Received!</h3>
            <p class="text-slate-500 font-medium mb-8">
                <?= $_SESSION['success_msg'] ?? 'Your item has been reported successfully.' ?>
            </p>
            <div class="space-y-3">
                <button onclick="closeModal()" class="w-full py-4 bg-indigo-600 text-white font-black uppercase tracking-widest rounded-2xl hover:bg-indigo-700 shadow-lg shadow-indigo-100 transition-all">Continue to Dashboard</button>
                <a href="items.php" class="block w-full py-4 bg-slate-50 text-slate-700 font-bold rounded-2xl hover:bg-slate-100 transition-all">View My Items</a>
            </div>
        </div>
        <div class="h-1.5 w-full bg-slate-100 overflow-hidden mt-2">
            <div id="modalTimer" class="h-full bg-indigo-500 w-full"></div>
        </div>
    </div>
</div>

<script>
function closeModal() {
    const modal = document.getElementById('successModal');
    modal.classList.add('opacity-0', 'scale-95');
    setTimeout(() => {
        modal.display = 'none';
        window.history.replaceState({}, document.title, window.location.pathname);
        window.location.reload();
    }, 300);
}

// Auto-close after 8 seconds
setTimeout(() => {
    const modal = document.getElementById('successModal');
    if (modal) {
        document.getElementById('modalTimer').style.transition = 'width 8s linear';
        document.getElementById('modalTimer').style.width = '0%';
        setTimeout(closeModal, 8000);
    }
}, 10);
</script>
<?php 
    // Clear message from session
    unset($_SESSION['success_msg']);
endif; ?>

<?php
$content = ob_get_clean();
include '../layouts/header.php';
?>

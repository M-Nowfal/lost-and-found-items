<?php
/**
 * My Matches
 * Lost & Found Portal
 */
require_once '../../config/constants.php';
require_once '../../config/database.php';
requireLogin();

$pageTitle = 'My Matches';
$currentPage = 'matches';

require_once '../../models/Match.php';
$matchModel = new MatchModel();
$myMatches = $matchModel->getByUserId(getCurrentUserId());

ob_start();
?>

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Page Header -->
    <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 text-purple-600 font-bold text-sm uppercase tracking-widest mb-2">
                <span class="w-2 h-2 rounded-full bg-purple-600 animate-pulse"></span>
                Reunions
            </div>
            <h1 class="text-4xl font-black text-slate-900 tracking-tight">Potential & Approved Matches</h1>
            <p class="text-slate-500 font-medium mt-1">Review items that our AI system identified as potential matches.</p>
        </div>
    </div>

    <?php if (empty($myMatches)): ?>
    <!-- Empty State -->
    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-xl shadow-indigo-50/50 p-20 text-center">
        <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-12 h-12 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
            </svg>
        </div>
        <h3 class="text-2xl font-black text-slate-900 mb-2">No matches found yet</h3>
        <p class="text-slate-500 font-medium max-w-xs mx-auto">We'll notify you as soon as our AI finds a potential match for your items.</p>
        <a href="dashboard.php" class="inline-block mt-8 px-8 py-3 bg-indigo-600 text-white font-bold rounded-2xl hover:bg-indigo-700 transition-all">Back to Dashboard</a>
    </div>
    <?php else: ?>
    <!-- Match Grid -->
    <div class="space-y-6">
        <?php foreach ($myMatches as $match): ?>
        <div class="bg-white rounded-[2rem] border border-slate-100 shadow-lg hover:shadow-xl transition-all overflow-hidden group">
            <div class="p-6 md:p-8">
                <div class="flex flex-col md:flex-row items-center gap-8">
                    <!-- Lost Item -->
                    <div class="flex-1 w-full">
                        <div class="mb-3 flex items-center justify-between">
                            <span class="text-[10px] font-black uppercase tracking-widest text-amber-600 bg-amber-50 px-2 py-1 rounded-md">Lost Item</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-2xl bg-slate-50 flex-shrink-0 overflow-hidden border border-slate-100">
                                <?php if ($match['lost_image']): ?>
                                <img src="<?= BASE_URL . $match['lost_image'] ?>" class="w-full h-full object-cover">
                                <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center text-2xl opacity-20">📦</div>
                                <?php endif; ?>
                            </div>
                            <div class="min-w-0">
                                <h4 class="font-bold text-slate-900 truncate"><?= htmlspecialchars($match['lost_title']) ?></h4>
                                <p class="text-xs text-slate-400 font-medium flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <?= htmlspecialchars($match['lost_location']) ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Connection Stats -->
                    <div class="flex flex-col items-center gap-2 px-6 py-4 bg-slate-50 rounded-2xl border border-slate-100 flex-shrink-0 w-full md:w-auto">
                        <div class="text-2xl">🤝</div>
                        <div class="text-center">
                            <p class="text-lg font-black text-slate-900 leading-none mb-1"><?= $match['similarity_score'] ?>%</p>
                            <p class="text-[10px] font-black uppercase tracking-tighter text-slate-400">Match Score</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest mt-1 <?= $match['status'] === 'approved' ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-200 text-slate-600' ?>">
                            <?= $match['status'] ?>
                        </span>
                    </div>

                    <!-- Found Item -->
                    <div class="flex-1 w-full text-right md:text-left">
                        <div class="mb-3 flex items-center justify-end md:justify-start">
                            <span class="text-[10px] font-black uppercase tracking-widest text-emerald-600 bg-emerald-50 px-2 py-1 rounded-md">Found Item</span>
                        </div>
                        <div class="flex items-center flex-row-reverse md:flex-row gap-4">
                            <div class="w-16 h-16 rounded-2xl bg-slate-50 flex-shrink-0 overflow-hidden border border-slate-100">
                                <?php if ($match['found_image']): ?>
                                <img src="<?= BASE_URL . $match['found_image'] ?>" class="w-full h-full object-cover">
                                <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center text-2xl opacity-20">📦</div>
                                <?php endif; ?>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h4 class="font-bold text-slate-900 truncate"><?= htmlspecialchars($match['found_title']) ?></h4>
                                <p class="text-xs text-slate-400 font-medium flex items-center justify-end md:justify-start gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <?= htmlspecialchars($match['found_location']) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-8 pt-6 border-t border-slate-50 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <p class="text-xs font-bold text-slate-400 flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Identified on <?= date('M d, Y', strtotime($match['created_at'])) ?>
                    </p>
                    <div class="flex gap-3 w-full sm:w-auto">
                        <a href="item-detail.php?id=<?= $match['lost_item_id'] ?>" class="flex-1 sm:flex-none px-6 py-2.5 bg-slate-100 text-slate-700 text-xs font-bold rounded-xl hover:bg-slate-200 transition-all text-center">View Found Item</a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include '../layouts/header.php';
?>

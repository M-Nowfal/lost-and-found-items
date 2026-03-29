<?php
/**
 * My Items
 * Lost & Found Portal
 */
require_once '../../config/constants.php';
require_once '../../config/database.php';
requireLogin();

$pageTitle = 'My Items';
$currentPage = 'items';
$id = $_GET['id'] ?? null;
$typeFilter = $_GET['type'] ?? null;

require_once '../../models/Item.php';
require_once '../../controllers/ItemController.php';

$itemController = new ItemController();
$userItems = $itemController->getUserItems(null, $typeFilter);

ob_start();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Page Header -->
    <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 text-indigo-600 font-bold text-sm uppercase tracking-widest mb-2">
                <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                My Profile
            </div>
            <h1 class="text-4xl font-black text-slate-900 tracking-tight">Your Reported Items</h1>
            <p class="text-slate-500 font-medium mt-2">Manage and track all the items you've reported to the portal.</p>
        </div>
        <div class="flex gap-3">
            <a href="report.php" class="px-8 py-3.5 bg-indigo-600 text-white font-bold rounded-2xl shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition-all flex items-center gap-2 group">
                <span class="text-xl group-hover:rotate-12 transition-transform">➕</span> Report New Item
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="mb-8 flex gap-4 overflow-x-auto pb-2 scrollbar-hide">
        <a href="items.php" class="px-6 py-2.5 <?= !$typeFilter ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100' : 'bg-white text-slate-600 border border-slate-200 hover:border-indigo-200 hover:bg-indigo-50/50' ?> font-bold rounded-xl text-sm transition-all whitespace-nowrap">All Items</a>
        <a href="items.php?type=lost" class="px-6 py-2.5 <?= $typeFilter === 'lost' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100' : 'bg-white text-slate-600 border border-slate-200 hover:border-indigo-200 hover:bg-indigo-50/50' ?> font-bold rounded-xl text-sm transition-all whitespace-nowrap">Lost Items</a>
        <a href="items.php?type=found" class="px-6 py-2.5 <?= $typeFilter === 'found' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100' : 'bg-white text-slate-600 border border-slate-200 hover:border-indigo-200 hover:bg-indigo-50/50' ?> font-bold rounded-xl text-sm transition-all whitespace-nowrap">Found Items</a>
        <a href="items.php?status=claimed" class="px-6 py-2.5 bg-white text-slate-600 border border-slate-200 font-bold rounded-xl text-sm hover:border-indigo-200 hover:bg-indigo-50/50 transition-all whitespace-nowrap">Resolved</a>
    </div>

    <?php if (empty($userItems)): ?>
    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm py-24 text-center">
        <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-12 h-12 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
        </div>
        <h2 class="text-2xl font-black text-slate-900 mb-2">You haven't reported anything yet</h2>
        <p class="text-slate-500 font-medium mb-8 max-w-sm mx-auto">Lost something or found something? Help the community by reporting it now.</p>
        <a href="report.php" class="inline-flex px-8 py-3.5 bg-indigo-600 text-white font-bold rounded-2xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100">Get Started</a>
    </div>
    <?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php foreach ($userItems as $item): ?>
        <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-xl hover:shadow-indigo-50/50 transition-all group overflow-hidden flex flex-col">
            <!-- Image Section -->
            <div class="aspect-video w-full relative overflow-hidden bg-slate-100 border-b border-slate-50">
                <?php if ($item['image_path']): ?>
                <img src="<?= BASE_URL . $item['image_path'] ?>" alt="" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                <?php else: ?>
                <div class="w-full h-full flex items-center justify-center bg-slate-50/50">
                    <span class="text-5xl opacity-20 group-hover:scale-125 transition-transform duration-500">📦</span>
                </div>
                <?php endif; ?>
                
                <!-- Overlay Badges -->
                <div class="absolute top-4 left-4 flex gap-2">
                    <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-[0.2em] shadow-lg shadow-black/10 backdrop-blur-md <?= $item['type'] === 'lost' ? 'bg-amber-500/90 text-white' : 'bg-emerald-500/90 text-white' ?>">
                        <?= $item['type'] ?>
                    </span>
                    <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-[0.2em] shadow-lg shadow-black/10 backdrop-blur-md bg-white/90 text-slate-700">
                        <?= $item['status'] ?>
                    </span>
                </div>
                
                <?php if ($item['verified']): ?>
                <div class="absolute top-4 right-4 w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center shadow-lg border-2 border-white" title="Verified Report">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.64.304 1.24.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path></svg>
                </div>
                <?php endif; ?>
            </div>

            <!-- Content Section -->
            <div class="p-6 flex-1 flex flex-col">
                <div class="flex justify-between items-start mb-3">
                    <h3 class="text-xl font-black text-slate-900 leading-tight group-hover:text-indigo-600 transition-colors"><?= htmlspecialchars($item['title']) ?></h3>
                </div>
                
                <p class="text-slate-500 font-medium text-sm mb-6 line-clamp-2">
                    <?= htmlspecialchars($item['description']) ?>
                </p>
                
                <div class="mt-auto space-y-4">
                    <div class="flex items-center justify-between text-xs font-bold text-slate-400">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 opacity-40 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <?= htmlspecialchars($item['location']) ?>
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 opacity-40 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <?= formatDate($item['date']) ?>
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-3 pt-4 border-t border-slate-50">
                        <a href="item-detail.php?id=<?= $item['id'] ?>" class="px-4 py-2.5 bg-slate-50 text-slate-700 text-xs font-black uppercase tracking-widest text-center rounded-xl hover:bg-slate-100 transition-all border border-slate-100">Details</a>
                        <a href="edit-item.php?id=<?= $item['id'] ?>" class="px-4 py-2.5 bg-white text-indigo-600 text-xs font-black uppercase tracking-widest text-center rounded-xl border border-indigo-100 hover:bg-indigo-50 transition-all shadow-sm">Edit</a>
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

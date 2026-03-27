<?php
/**
 * Item Details
 * Lost & Found Portal
 */
require_once '../../config/constants.php';
require_once '../../config/database.php';
requireLogin();

$itemId = $_GET['id'] ?? null;
if (!$itemId) {
    header('Location: dashboard.php');
    exit;
}

require_once '../../models/Item.php';
$itemModel = new Item();
$item = $itemModel->findById($itemId);

if (!$item) {
    header('Location: dashboard.php');
    exit;
}

$pageTitle = htmlspecialchars($item['title']);
$currentPage = 'items';

ob_start();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Breadcrumbs -->
    <nav class="flex items-center gap-3 text-sm font-bold text-slate-400 mb-10 overflow-x-auto whitespace-nowrap pb-2">
        <a href="dashboard.php" class="hover:text-indigo-600 transition-colors">Dashboard</a>
        <svg class="w-4 h-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="items.php" class="hover:text-indigo-600 transition-colors">My Items</a>
        <svg class="w-4 h-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-slate-900 truncate"><?= htmlspecialchars($item['title']) ?></span>
    </nav>

    <div class="grid lg:grid-cols-2 gap-12 items-start">
        <!-- Left: Image Gallery -->
        <div class="space-y-6">
            <div class="aspect-square w-full rounded-[3rem] bg-white border border-slate-100 shadow-2xl shadow-indigo-100/50 overflow-hidden relative group">
                <?php if ($item['image_path']): ?>
                <img src="<?= $baseUrl . $item['image_path'] ?>" alt="" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000">
                <?php else: ?>
                <div class="w-full h-full flex flex-col items-center justify-center bg-slate-50/50">
                    <span class="text-8xl mb-4 group-hover:scale-125 transition-transform duration-500">📦</span>
                    <p class="text-slate-400 font-bold uppercase tracking-[0.2em] text-xs">No Photo Available</p>
                </div>
                <?php endif; ?>
                
                <div class="absolute top-8 left-8">
                    <span class="px-6 py-2 rounded-full text-xs font-black uppercase tracking-[0.2em] shadow-xl shadow-black/10 backdrop-blur-md <?= $item['type'] === 'lost' ? 'bg-amber-500/90 text-white' : 'bg-emerald-500/90 text-white' ?>">
                        <?= $item['type'] ?>
                    </span>
                </div>
            </div>

            <!-- Share / Actions -->
            <!-- Share / Actions -->
            <div class="flex gap-4">
                <button onclick="shareItem()" class="flex-1 px-6 py-4 bg-white border border-slate-200 rounded-2xl text-slate-700 font-bold text-sm hover:bg-slate-50 transition-all flex items-center justify-center gap-2 shadow-sm">
                    <svg class="w-5 h-5 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                    Share Report
                </button>
                <?php if ($item['user_id'] == getCurrentUserId() || isAdmin()): ?>
                <button onclick="confirmDelete()" class="px-6 py-4 bg-white border border-slate-200 rounded-2xl text-red-500 font-bold text-sm hover:bg-red-50 hover:border-red-100 transition-all shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right: Information -->
        <div class="space-y-10">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-slate-100 text-slate-600">
                        Status: <?= $item['status'] ?>
                    </span>
                    <?php if ($item['verified']): ?>
                    <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-blue-100 text-blue-700 flex items-center gap-1.5">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.64.304 1.24.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path></svg>
                        Verified
                    </span>
                    <?php endif; ?>
                </div>
                <h1 class="text-5xl font-black text-slate-900 tracking-tight leading-tight mb-6"><?= htmlspecialchars($item['title']) ?></h1>
                <p class="text-slate-500 font-medium text-lg leading-relaxed"><?= nl2br(htmlspecialchars($item['description'])) ?></p>
            </div>

            <!-- Key Details Grid -->
            <div class="grid grid-cols-2 gap-6 pb-10 border-b border-slate-100">
                <div class="bg-slate-50 p-6 rounded-3xl">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Category</p>
                    <p class="text-lg font-bold text-slate-800"><?= CATEGORIES[$item['category']] ?? $item['category'] ?></p>
                </div>
                <div class="bg-slate-50 p-6 rounded-3xl">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Date Reported</p>
                    <p class="text-lg font-bold text-slate-800"><?= formatDate($item['date']) ?></p>
                </div>
                <div class="bg-slate-50 p-6 rounded-3xl col-span-2 flex items-center gap-6">
                    <div class="w-12 h-12 bg-indigo-100 rounded-2xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Location</p>
                        <p class="text-lg font-bold text-slate-800"><?= htmlspecialchars($item['location']) ?></p>
                    </div>
                </div>
            </div>

            <!-- Reported By -->
            <div class="flex items-center justify-between py-2">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-lg border-2 border-white shadow-sm ring-1 ring-slate-100">
                        <?= strtoupper(substr($item['user_name'] ?? 'U', 0, 1)) ?>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Reported By</p>
                        <p class="text-base font-bold text-slate-900"><?= htmlspecialchars($item['user_name'] ?? 'Anonymous') ?></p>
                    </div>
                </div>
                <?php if ($item['user_id'] == getCurrentUserId() || isAdmin()): ?>
                <a href="edit-item.php?id=<?= $item['id'] ?>" class="px-8 py-3 bg-indigo-600 text-white font-black uppercase tracking-widest rounded-2xl hover:bg-indigo-700 shadow-xl shadow-indigo-100 transition-all flex items-center gap-2 group">
                    Edit Report <svg class="w-4 h-4 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 z-[100] items-center justify-center p-4 hidden">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeDeleteModal()"></div>
    <div class="relative bg-white rounded-[2.5rem] shadow-2xl w-full max-w-sm overflow-hidden transform transition-all">
        <div class="p-8 text-center pt-12">
            <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-black text-slate-900 mb-2 tracking-tight">Delete Report?</h3>
            <p class="text-slate-500 font-medium mb-8">This action cannot be undone. All matching data will be permanently removed.</p>
            <div class="space-y-3">
                <form action="<?= BASE_URL ?>api/items/delete.php" method="POST">
                    <input type="hidden" name="id" value="<?= $itemId ?>">
                    <button type="submit" class="w-full py-4 bg-red-500 text-white font-black uppercase tracking-widest rounded-2xl hover:bg-red-600 shadow-lg shadow-red-100 transition-all">Delete Permanently</button>
                </form>
                <button onclick="closeDeleteModal()" class="w-full py-4 bg-slate-50 text-slate-700 font-bold rounded-2xl hover:bg-slate-100 transition-all">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- Simple Toast -->
<div id="toast" class="fixed bottom-8 left-1/2 -translate-x-1/2 z-[110] bg-slate-900 text-white px-6 py-3 rounded-2xl text-sm font-bold shadow-2xl transition-all opacity-0 translate-y-4 pointer-events-none">
    Link copied to clipboard!
</div>

<script>
function shareItem() {
    const shareData = {
        title: '<?= addslashes($item['title']) ?> | Lost & Found Portal',
        text: 'Check out this <?= $item['type'] ?> report on the Lost & Found Portal.',
        url: window.location.href
    };

    if (navigator.share) {
        navigator.share(shareData).catch(err => copyToClipboard());
    } else {
        copyToClipboard();
    }
}

function copyToClipboard() {
    navigator.clipboard.writeText(window.location.href).then(() => {
        showToast('Link copied to clipboard!');
    });
}

function showToast(msg) {
    const toast = document.getElementById('toast');
    toast.innerText = msg;
    toast.classList.remove('opacity-0', 'translate-y-4');
    toast.classList.add('opacity-100', 'translate-y-0');
    setTimeout(() => {
        toast.classList.add('opacity-0', 'translate-y-4');
        toast.classList.remove('opacity-100', 'translate-y-0');
    }, 3000);
}

function confirmDelete() {
    const modal = document.getElementById('deleteModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
</script>

<?php
$content = ob_get_clean();
include '../layouts/header.php';
?>

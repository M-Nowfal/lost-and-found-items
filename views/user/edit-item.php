<?php
/**
 * Edit Item
 * Lost & Found Portal
 */
require_once '../../config/constants.php';
require_once '../../config/database.php';
requireLogin();

require_once '../../controllers/ItemController.php';
$itemController = new ItemController();

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: items.php');
    exit;
}

$item = $itemController->getById($id);

// Authorization check
if (!$item || ($item['user_id'] !== getCurrentUserId() && !isAdmin())) {
    header('Location: items.php');
    exit;
}

$pageTitle = 'Edit Item';
$currentPage = 'items';

ob_start();
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Page Header -->
    <div class="mb-10 text-center">
        <div class="inline-flex items-center gap-2 px-4 py-2 bg-amber-50 text-amber-600 rounded-full text-xs font-black uppercase tracking-widest mb-4">
            <span class="w-2 h-2 rounded-full bg-amber-600 animate-pulse"></span>
            Modifying Existing Report
        </div>
        <h1 class="text-4xl font-black text-slate-900 tracking-tight mb-3">
            Edit <span class="bg-clip-text text-transparent bg-gradient-to-r from-amber-600 to-orange-600"><?= htmlspecialchars($item['title']) ?></span>
        </h1>
        <p class="text-slate-500 font-medium max-w-lg mx-auto">Update the details of your report to keep the information accurate.</p>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-xl shadow-amber-50/50 overflow-hidden">
        <form id="editForm" action="../../api/items/update.php" method="POST" enctype="multipart/form-data" class="p-8 md:p-12">
            <input type="hidden" name="id" value="<?= $id ?>">
            <input type="hidden" name="type" value="<?= $item['type'] ?>">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

            <div class="grid md:grid-cols-2 gap-8">
                <!-- Left Column -->
                <div class="space-y-6">
                    <div>
                        <label for="title" class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Item Title</label>
                        <input type="text" id="title" name="title" required value="<?= htmlspecialchars($item['title']) ?>" 
                               class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all font-medium text-slate-900">
                    </div>

                    <div>
                        <label for="category" class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Category</label>
                        <div class="relative">
                            <select id="category" name="category" required 
                                    class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all font-medium text-slate-900 appearance-none">
                                <?php foreach (CATEGORIES as $key => $label): ?>
                                <option value="<?= $key ?>" <?= $item['category'] === $key ? 'selected' : '' ?>><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="absolute right-5 top-1/2 -translate-y-1/2 pointer-events-none opacity-40">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="location" class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Location</label>
                        <input type="text" id="location" name="location" required value="<?= htmlspecialchars($item['location']) ?>" 
                               class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all font-medium text-slate-900">
                    </div>

                    <div>
                        <label for="date" class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Date</label>
                        <input type="date" id="date" name="date" required value="<?= $item['date'] ?>" 
                               class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all font-medium text-slate-900">
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <div>
                        <label for="description" class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Description</label>
                        <textarea id="description" name="description" rows="5" required 
                                  class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all font-medium text-slate-900 resize-none"><?= htmlspecialchars($item['description']) ?></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Current Photo</label>
                        <div class="relative group">
                            <div class="w-full h-48 bg-slate-100 rounded-[2rem] overflow-hidden border border-slate-200">
                                <?php if ($item['image_path']): ?>
                                <img src="<?= BASE_URL . $item['image_path'] ?>" alt="" class="w-full h-full object-cover">
                                <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center bg-slate-50">
                                    <span class="text-3xl opacity-20">📦 No Image</span>
                                </div>
                                <?php endif; ?>
                            </div>
                            <p class="text-xs text-slate-400 mt-2 text-center italic">Photos cannot be changed in this version. Delete and re-report if you need to update the image.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-12 flex flex-col md:flex-row items-center gap-6 justify-between pt-8 border-t border-slate-50">
                <a href="items.php" class="text-sm font-bold text-slate-400 hover:text-slate-600 transition-all">Cancel Changes</a>
                <button type="submit" class="w-full md:w-auto px-10 py-4 bg-amber-600 text-white font-black uppercase tracking-widest rounded-2xl hover:bg-amber-700 shadow-xl shadow-amber-100 transition-all flex items-center justify-center gap-3 group">
                    Save Changes <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include '../layouts/header.php';
?>

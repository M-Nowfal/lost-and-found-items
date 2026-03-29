<?php
/**
 * Report Lost or Found Item
 * Lost & Found Portal
 */
require_once '../../config/constants.php';
require_once '../../config/database.php';
requireLogin();

$pageTitle = 'Report Item';
$currentPage = 'report';
$type = $_GET['type'] ?? 'lost'; // default to lost

if (!in_array($type, ['lost', 'found'])) {
    $type = 'lost';
}

ob_start();
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Page Header -->
    <div class="mb-10 text-center">
        <div class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-50 text-indigo-600 rounded-full text-xs font-black uppercase tracking-widest mb-4">
            <span class="w-2 h-2 rounded-full bg-indigo-600 animate-pulse"></span>
            Step 1: Information Gathering
        </div>
        <h1 class="text-4xl font-black text-slate-900 tracking-tight mb-3">
            Report a <span class="bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-purple-600"><?= ucfirst($type) ?></span> Item
        </h1>
        <p class="text-slate-500 font-medium max-w-lg mx-auto">Please provide as much detail as possible to help our AI matching system find your item.</p>
    </div>

    <!-- Type Switcher -->
    <div class="mb-10 flex justify-center p-1.5 bg-slate-100 rounded-2xl max-w-xs mx-auto">
        <a href="?type=lost" class="flex-1 py-2.5 px-6 rounded-xl text-sm font-bold transition-all text-center <?= $type === 'lost' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' ?>">Lost Item</a>
        <a href="?type=found" class="flex-1 py-2.5 px-6 rounded-xl text-sm font-bold transition-all text-center <?= $type === 'found' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' ?>">Found Item</a>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-xl shadow-indigo-50/50 overflow-hidden">
        <form id="reportForm" action="../../api/items/create.php" method="POST" enctype="multipart/form-data" class="p-8 md:p-12">
            <input type="hidden" name="type" value="<?= $type ?>">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

            <div class="grid md:grid-cols-2 gap-8">
                <!-- Left Column -->
                <div class="space-y-6">
                    <div>
                        <label for="title" class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Item Title</label>
                        <input type="text" id="title" name="title" required placeholder="e.g. iPhone 13 Pro Max" 
                               class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-medium text-slate-900">
                    </div>

                    <div>
                        <label for="category" class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Category</label>
                        <div class="relative">
                            <select id="category" name="category" required 
                                    class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-medium text-slate-900 appearance-none">
                                <option value="" disabled selected>Select category</option>
                                <?php foreach (CATEGORIES as $key => $label): ?>
                                <option value="<?= $key ?>"><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="absolute right-5 top-1/2 -translate-y-1/2 pointer-events-none opacity-40">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="location" class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Location</label>
                        <input type="text" id="location" name="location" required placeholder="e.g. Central Park, near the fountain" 
                               class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-medium text-slate-900">
                    </div>

                    <div>
                        <label for="date" class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Date <?= ucfirst($type) ?></label>
                        <input type="date" id="date" name="date" required 
                               max="<?= date('Y-m-d') ?>"
                               class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-medium text-slate-900">
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <div>
                        <label for="description" class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Description</label>
                        <textarea id="description" name="description" rows="5" required placeholder="Describe any distinguishing features, colors, brands, etc." 
                                  class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-medium text-slate-900 resize-none"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Item Photo</label>
                        <div class="relative group">
                            <input type="file" id="image" name="image" accept="image/*" class="hidden" onchange="previewImage(this)">
                            <label for="image" class="flex flex-col items-center justify-center border-2 border-dashed border-slate-200 rounded-[2rem] p-8 bg-slate-50/50 hover:bg-white hover:border-indigo-300 transition-all cursor-pointer group-hover:shadow-lg group-hover:shadow-indigo-50/20">
                                <div id="preview-placeholder" class="text-center">
                                    <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-sm border border-slate-100 group-hover:scale-110 transition-transform">
                                        <svg class="w-8 h-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                    <p class="text-sm font-bold text-slate-700">Click to upload photo</p>
                                    <p class="text-xs font-medium text-slate-400 mt-1">PNG, JPG or WEBP (Max 5MB)</p>
                                </div>
                                <img id="image-preview" src="#" alt="Preview" class="hidden w-full h-48 object-cover rounded-2xl shadow-md border border-slate-200">
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-12 flex flex-col md:flex-row items-center gap-6 justify-between pt-8 border-t border-slate-50">
                <div class="flex items-center gap-3">
                    <div class="flex -space-x-2">
                        <div class="w-8 h-8 rounded-full border-2 border-white bg-indigo-500 flex items-center justify-center text-[10px] text-white font-bold shadow-sm">AI</div>
                        <div class="w-8 h-8 rounded-full border-2 border-white bg-purple-500 flex items-center justify-center text-[10px] text-white font-bold shadow-sm">✓</div>
                    </div>
                    <p class="text-xs font-bold text-slate-400">Our AI will automatically check for potential matches upon submission.</p>
                </div>
                <button type="submit" class="w-full md:w-auto px-10 py-4 bg-indigo-600 text-white font-black uppercase tracking-widest rounded-2xl hover:bg-indigo-700 shadow-xl shadow-indigo-100 transition-all flex items-center justify-center gap-3 group">
                    Submit Report <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function previewImage(input) {
    const preview = document.getElementById('image-preview');
    const placeholder = document.getElementById('preview-placeholder');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            placeholder.classList.add('hidden');
        }
        reader.readAsDataURL(input.files[0]);
    }
}

document.getElementById('reportForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    const dateInput = document.getElementById('date');
    const submitBtn = form.querySelector('button[type="submit"]');
    
    // 1. Client-side Date Validation
    const selectedDate = new Date(dateInput.value);
    const today = new Date();
    today.setHours(0, 0, 0, 0); // Reset time for comparison
    
    if (selectedDate > today) {
        Toast.error("Date cannot be in the future. Please select a valid date.");
        dateInput.classList.add('border-red-500', 'ring-red-500/20');
        dateInput.focus();
        return;
    }
    
    // 2. Submit via AJAX for better UI
    submitBtn.disabled = true;
    submitBtn.innerHTML = 'Submitting... <div class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>';
    
    try {
        const response = await fetch(form.action, {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            Toast.success(result.message || "Report submitted successfully!");
            setTimeout(() => {
                window.location.href = 'dashboard.php';
            }, 1500);
        } else {
            // Handle backend errors gracefully
            if (result.errors) {
                const firstError = Object.values(result.errors)[0];
                Toast.error(firstError);
            } else {
                Toast.error(result.message || "An error occurred during submission.");
            }
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Submit Report <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>';
        }
    } catch (error) {
        console.error('Submission error:', error);
        Toast.error("Failed to connect to the server. Please try again.");
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Submit Report <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>';
    }
});
</script>

<?php
$content = ob_get_clean();
include '../layouts/header.php';
?>

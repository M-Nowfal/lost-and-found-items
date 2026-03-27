<?php
/**
 * Search Items
 * Lost & Found Portal
 */
require_once '../../config/constants.php';
require_once '../../config/database.php';
requireLogin();

$pageTitle = 'Search Items';
$currentPage = 'search';

ob_start();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Page Header -->
    <div class="mb-12 text-center">
        <h1 class="text-5xl font-black text-slate-900 tracking-tight mb-4">
            Search Protected <span class="bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-purple-600">Items</span>
        </h1>
        <p class="text-slate-500 font-medium max-w-2xl mx-auto text-lg leading-relaxed">
            Find your lost belongings or look for items you've found. Our AI-powered search helps you filter through thousands of reports in seconds.
        </p>
    </div>

    <!-- Search & Filter Bar -->
    <div class="max-w-4xl mx-auto mb-16">
        <div class="bg-white p-2 rounded-[2.5rem] shadow-2xl shadow-indigo-100/50 border border-slate-100 flex flex-col md:flex-row gap-2">
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-6 flex items-center pointer-events-none opacity-40">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" id="searchInput" placeholder="Search by title, location or description..." 
                       class="w-full pl-14 pr-6 py-5 bg-transparent border-none focus:ring-0 font-bold text-slate-900 placeholder:text-slate-400 placeholder:font-medium text-lg">
            </div>
            
            <button class="px-8 py-4 bg-slate-900 text-white font-black uppercase tracking-widest rounded-[1.8rem] hover:bg-slate-800 transition-all flex items-center justify-center gap-3">
                Search <span class="text-xl">🚀</span>
            </button>
        </div>

        <!-- Advanced Filters -->
        <div class="mt-8 flex flex-wrap justify-center gap-3">
            <select id="typeFilter" class="px-6 py-3 bg-white border border-slate-200 rounded-2xl text-sm font-bold text-slate-600 focus:ring-2 focus:ring-indigo-100 transition-all appearance-none cursor-pointer hover:border-indigo-200">
                <option value="">All Types</option>
                <option value="lost">Lost Only</option>
                <option value="found">Found Only</option>
            </select>
            
            <select id="categoryFilter" class="px-6 py-3 bg-white border border-slate-200 rounded-2xl text-sm font-bold text-slate-600 focus:ring-2 focus:ring-indigo-100 transition-all appearance-none cursor-pointer hover:border-indigo-200">
                <option value="">All Categories</option>
                <?php foreach (CATEGORIES as $key => $label): ?>
                <option value="<?= $key ?>"><?= $label ?></option>
                <?php endforeach; ?>
            </select>

            <input type="date" id="dateFilter" class="px-6 py-3 bg-white border border-slate-200 rounded-2xl text-sm font-bold text-slate-600 focus:ring-2 focus:ring-indigo-100 transition-all cursor-pointer hover:border-indigo-200">
        </div>
    </div>

    <!-- Results Section -->
    <div id="searchResults" class="min-h-[400px]">
        <div id="searchSkeleton" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 hidden">
            <!-- Skeleton cards -->
            <?php for($i=0; $i<6; $i++): ?>
            <div class="bg-white rounded-[2rem] border border-slate-100 p-6 animate-pulse">
                <div class="aspect-video bg-slate-100 rounded-2xl mb-4"></div>
                <div class="h-6 bg-slate-100 rounded-lg w-3/4 mb-3"></div>
                <div class="h-4 bg-slate-50 rounded-lg w-1/2 mb-6"></div>
                <div class="h-10 bg-slate-50 rounded-xl w-full"></div>
            </div>
            <?php endfor; ?>
        </div>

        <div id="searchEmpty" class="text-center py-20 flex flex-col items-center">
            <div class="w-32 h-32 bg-slate-50 rounded-full flex items-center justify-center mb-6">
                <span class="text-6xl grayscale opacity-20">🔎</span>
            </div>
            <h3 class="text-2xl font-black text-slate-900 mb-2 tracking-tight">Ready to find something?</h3>
            <p class="text-slate-500 font-medium max-w-sm">Enter keywords above or use filters to browse reported items.</p>
        </div>

        <div id="searchGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Items will be loaded here via JS -->
        </div>
    </div>
</div>

<script>
// Search logic (simplified for demonstration)
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchInput');
    const typeFilter = document.getElementById('typeFilter');
    const categoryFilter = document.getElementById('categoryFilter');
    const dateFilter = document.getElementById('dateFilter');
    const searchGrid = document.getElementById('searchGrid');
    const searchEmpty = document.getElementById('searchEmpty');
    const searchSkeleton = document.getElementById('searchSkeleton');

    async function performSearch() {
        // Show skeleton, hide others
        searchGrid.innerHTML = '';
        searchEmpty.classList.add('hidden');
        searchSkeleton.classList.remove('hidden');

        try {
            const params = new URLSearchParams({
                keyword: searchInput.value,
                type: typeFilter.value,
                category: categoryFilter.value,
                date: dateFilter.value
            });

            // Using ADMIN_API_BASE from header.php
            const response = await fetch(`${ADMIN_API_BASE}api/items/search.php?${params}`);
            const data = await response.json();

            searchSkeleton.classList.add('hidden');

            if (data.items && data.items.length > 0) {
                data.items.forEach(item => {
                    searchGrid.appendChild(createItemCard(item));
                });
            } else {
                searchEmpty.classList.remove('hidden');
            }
        } catch (error) {
            console.error('Search failed:', error);
            searchSkeleton.classList.add('hidden');
            searchEmpty.classList.remove('hidden');
        }
    }

    function createItemCard(item) {
        const div = document.createElement('div');
        div.className = 'bg-white rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-xl transition-all group overflow-hidden flex flex-col';
        
        const typeClass = item.type === 'lost' ? 'bg-amber-500/90 text-white' : 'bg-emerald-500/90 text-white';
        const imagePath = item.image_path ? `<?= $baseUrl ?>${item.image_path}` : '';
        const imageHtml = imagePath 
            ? `<img src="${imagePath}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">`
            : `<div class="w-full h-full flex items-center justify-center bg-slate-50/50"><span class="text-5xl opacity-20">📦</span></div>`;

        div.innerHTML = `
            <div class="aspect-video w-full relative overflow-hidden bg-slate-100 border-b border-slate-50">
                ${imageHtml}
                <div class="absolute top-4 left-4">
                    <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-[0.2em] shadow-lg shadow-black/10 backdrop-blur-md ${typeClass}">
                        ${item.type}
                    </span>
                </div>
            </div>
            <div class="p-6 flex-1 flex flex-col">
                <h3 class="text-xl font-black text-slate-900 leading-tight group-hover:text-indigo-600 transition-colors mb-2">${item.title}</h3>
                <p class="text-slate-500 font-medium text-sm mb-6 line-clamp-2">${item.description}</p>
                <div class="mt-auto pt-4 border-t border-slate-50 flex items-center justify-between text-[10px] font-black text-slate-400 uppercase tracking-widest">
                    <span class="flex items-center gap-1.5 truncate pr-2">
                        📍 ${item.location}
                    </span>
                    <span class="whitespace-nowrap">
                        🗓️ ${new Date(item.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}
                    </span>
                </div>
                <a href="item-detail.php?id=${item.id}" class="mt-4 w-full py-3.5 bg-indigo-600 text-white text-xs font-black uppercase tracking-widest text-center rounded-xl shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all">View Details</a>
            </div>
        `;
        return div;
    }

    // Debounce search
    let timeout = null;
    searchInput.addEventListener('input', () => {
        clearTimeout(timeout);
        timeout = setTimeout(performSearch, 500);
    });

    [typeFilter, categoryFilter, dateFilter].forEach(el => {
        el.addEventListener('change', performSearch);
    });
});
</script>

<?php
$content = ob_get_clean();
include '../layouts/header.php';
?>

/**
 * Lost & Found Portal - Items JavaScript
 */

// Load Items
async function loadItems(myItems = false, type = null) {
    const container = document.getElementById('itemsGrid');
    if (!container) return;
    
    showSkeleton(container, 6);
    
    try {
        const endpoint = myItems ? '/items.php?my_items=true' : '/items.php';
        const response = await API.get(endpoint);
        
        let items = response.items || [];
        
        if (type) {
            items = items.filter(item => item.type === type);
        }
        
        renderItems(items, container);
    } catch (error) {
        container.innerHTML = `
            <div class="empty-state col-span-full">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3>Unable to load items</h3>
                <p>${error.message}</p>
            </div>
        `;
    }
}

// Render Items Grid
function renderItems(items, container) {
    if (!items || items.length === 0) {
        container.innerHTML = `
            <div class="empty-state col-span-full">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <h3>No items found</h3>
                <p>Be the first to report a lost or found item!</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = items.map(item => `
        <div class="glass-card item-card fade-in">
            ${item.image_path ? 
                `<img src="/${item.image_path}" alt="${item.title}" class="item-card-image">` :
                `<div class="item-card-placeholder">
                    <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>`
            }
            <div class="item-card-body">
                <div class="flex justify-between items-start mb-3">
                    <span class="badge badge-${item.type}">${item.type === 'lost' ? '🔍 Lost' : '✨ Found'}</span>
                    <span class="badge badge-${item.status}">${item.status}</span>
                </div>
                <h3 class="item-card-title">${item.title}</h3>
                <div class="item-card-meta">
                    <span>
                        <svg class="inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        ${item.category}
                    </span>
                    <span>
                        <svg class="inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        ${item.location}
                    </span>
                    <span>
                        <svg class="inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        ${formatDate(item.date)}
                    </span>
                </div>
                <p class="text-gray-600 text-sm mb-4 line-clamp-2">${item.description || 'No description provided'}</p>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">${timeAgo(item.created_at)}</span>
                    <a href="/views/user/item-detail.php?id=${item.id}" class="btn btn-primary btn-sm">View Details</a>
                </div>
            </div>
        </div>
    `).join('');
}

// Report Item Form Handler
document.addEventListener('DOMContentLoaded', () => {
    const reportForm = document.getElementById('reportItemForm');
    if (reportForm) {
        reportForm.addEventListener('submit', handleReportItem);
        
        // Type toggle
        const typeButtons = reportForm.querySelectorAll('[name="type"]');
        typeButtons.forEach(btn => {
            btn.addEventListener('change', () => {
                updateTypeUI(btn.value);
            });
        });
        
        // File upload drag & drop
        const fileUpload = document.getElementById('fileUpload');
        if (fileUpload) {
            fileUpload.addEventListener('dragover', (e) => {
                e.preventDefault();
                fileUpload.classList.add('dragover');
            });
            
            fileUpload.addEventListener('dragleave', () => {
                fileUpload.classList.remove('dragover');
            });
            
            fileUpload.addEventListener('drop', (e) => {
                e.preventDefault();
                fileUpload.classList.remove('dragover');
                const files = e.dataTransfer.files;
                const input = document.getElementById('image');
                if (input && files.length > 0) {
                    input.files = files;
                    updateFileName(files[0].name);
                }
            });
        }
    }
});

function updateTypeUI(type) {
    const titleInput = document.querySelector('[name="title"]');
    if (titleInput) {
        titleInput.placeholder = type === 'lost' ? 
            'What did you lose? (e.g., iPhone 14 Pro)' : 
            'What did you find? (e.g., Black Wallet)';
    }
}

function updateFileName(name) {
    const label = document.getElementById('fileLabel');
    if (label) {
        label.textContent = name;
    }
}

async function handleReportItem(e) {
    e.preventDefault();
    
    const form = e.target;
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    const formData = new FormData(form);
    
    // Validation
    const title = formData.get('title')?.trim();
    const category = formData.get('category');
    const location = formData.get('location')?.trim();
    const date = formData.get('date');
    const type = formData.get('type');
    
    if (!title || title.length < 3) {
        Toast.error('Please enter a valid title (at least 3 characters)');
        return;
    }
    
    if (!category) {
        Toast.error('Please select a category');
        return;
    }
    
    if (!location) {
        Toast.error('Please enter a location');
        return;
    }
    
    if (!date) {
        Toast.error('Please select a date');
        return;
    }
    
    // Submit
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<svg class="animate-spin" width="20" height="20" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Submitting...';
    
    try {
        const response = await fetch('/api/items.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (!response.ok) {
            throw new Error(data.message || data.errors?.general || 'Failed to submit');
        }
        
        Toast.success(data.message || 'Item reported successfully!');
        
        setTimeout(() => {
            window.location.href = '/views/user/items.php';
        }, 1500);
    } catch (error) {
        Toast.error(error.message);
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
}

// Search Handler
document.addEventListener('DOMContentLoaded', () => {
    const searchForm = document.getElementById('searchForm');
    if (searchForm) {
        const searchInput = searchForm.querySelector('input[name="q"]');
        
        // Debounced search
        const doSearch = debounce(async () => {
            await performSearch();
        }, 500);
        
        searchInput?.addEventListener('input', doSearch);
        
        searchForm.addEventListener('submit', (e) => {
            e.preventDefault();
            performSearch();
        });
        
        // Filter changes
        searchForm.querySelectorAll('select').forEach(select => {
            select.addEventListener('change', performSearch);
        });
    }
});

async function performSearch() {
    const form = document.getElementById('searchForm');
    if (!form) return;
    
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);
    
    const container = document.getElementById('searchResults');
    if (!container) return;
    
    showSkeleton(container, 6);
    
    try {
        const response = await API.get(`/search.php?${params.toString()}`);
        renderItems(response.items || [], container);
        
        const countEl = document.getElementById('resultCount');
        if (countEl) {
            countEl.textContent = `${response.count || 0} items found`;
        }
    } catch (error) {
        Toast.error(error.message);
    }
}

// Load potential matches for an item
async function loadPotentialMatches(itemId) {
    const container = document.getElementById('potentialMatches');
    if (!container) return;
    
    try {
        const item = await API.get(`/items.php?id=${itemId}`);
        // This would show potential matches - implement based on your needs
    } catch (error) {
        console.error('Failed to load potential matches:', error);
    }
}

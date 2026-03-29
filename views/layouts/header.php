<?php
/**
 * Global Header
 * Lost & Found Portal
 */
require_once __DIR__ . '/../../config/constants.php';

// Fix: compute correct web-accessible base path from DOCUMENT_ROOT
// BASE_URL/ASSETS_PATH are wrong for nested pages because they use dirname(SCRIPT_NAME)
$docRoot = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), '/');
$rootPath = rtrim(str_replace('\\', '/', ROOT_PATH), '/');
$webBase = str_replace($docRoot, '', $rootPath);
$baseUrl = $webBase . '/';
$assetsUrl = $webBase . '/assets/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' | ' : '' ?>Lost & Found Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <link rel="stylesheet" href="<?= $assetsUrl ?>css/style.css">
    <script>const ADMIN_API_BASE = '<?= $baseUrl ?>';</script>
</head>
<body class="min-h-screen flex flex-col bg-slate-50">
    <!-- Navigation -->
    <nav class="sticky top-0 z-50 bg-white/80 backdrop-blur-lg border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <a href="<?= $baseUrl ?>" class="flex items-center gap-2 group transition-all">
                    <div class="p-1 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-600 group-hover:shadow-lg group-hover:shadow-indigo-200 transition-all">
                        <svg width="32" height="32" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M18 10C14.134 10 11 13.134 11 17C11 21.5 18 26 18 26C18 26 25 21.5 25 17C25 13.134 21.866 10 18 10Z" stroke="white" stroke-width="2.5" fill="none"/>
                            <circle cx="18" cy="17" r="3" stroke="white" stroke-width="2.5" fill="none"/>
                        </svg>
                    </div>
                    <span class="text-xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-purple-600 tracking-tight">Lost & Found</span>
                </a>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center gap-1">
                    <?php if (!isLoggedIn()): ?>
                        <a href="<?= $baseUrl ?>" class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all <?= ($currentPage ?? '') === 'home' ? 'bg-indigo-50 text-indigo-600' : '' ?>">Home</a>
                        <a href="<?= $baseUrl ?>views/auth/login.php" class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all">Login</a>
                        <div class="ml-2">
                            <a href="<?= $baseUrl ?>views/auth/register.php" class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-indigo-200 hover:bg-indigo-700 hover:shadow-indigo-300 transition-all">Sign Up</a>
                        </div>
                    <?php else: ?>
                        <a href="<?= $baseUrl ?>views/user/dashboard.php" class="px-4 py-2 text-sm font-medium rounded-xl transition-all <?= ($currentPage ?? '') === 'dashboard' ? 'bg-indigo-50 text-indigo-600' : 'text-slate-600 hover:text-indigo-600 hover:bg-indigo-50' ?>">Dashboard</a>
                        <a href="<?= $baseUrl ?>views/user/search.php" class="px-4 py-2 text-sm font-medium rounded-xl transition-all <?= ($currentPage ?? '') === 'search' ? 'bg-indigo-50 text-indigo-600' : 'text-slate-600 hover:text-indigo-600 hover:bg-indigo-50' ?>">Search</a>
                        
                        <!-- Notification Bell -->
                        <div class="relative ml-2">
                            <a href="<?= $baseUrl ?>views/user/notifications.php" class="p-2 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all relative block">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                <span class="notification-badge absolute top-1.5 right-1.5 w-4 h-4 bg-red-500 text-white text-[10px] font-bold flex items-center justify-center rounded-full border-2 border-white shadow-sm" style="display: none;">0</span>
                            </a>
                        </div>

                        <!-- User Menu -->
                        <div class="relative ml-2">
                            <button class="flex items-center gap-2.5 pl-2 pr-1.5 py-1.5 rounded-2xl hover:bg-slate-100 transition-all border border-transparent hover:border-slate-200" onclick="toggleUserMenu()">
                                <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-indigo-500 to-purple-500 flex items-center justify-center text-white text-xs font-bold shadow-sm shadow-indigo-100">
                                    <?= strtoupper(substr($_SESSION['name'] ?? 'U', 0, 1)) ?>
                                </div>
                                <span class="hidden lg:inline text-sm font-semibold text-slate-700"><?= $_SESSION['name'] ?? 'User' ?></span>
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div id="userMenu" class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl shadow-slate-200 border border-slate-100 py-2 hidden z-50">
                                <div class="px-4 py-3 mb-2 border-b border-slate-50">
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Signed in as</p>
                                    <p class="text-sm font-bold text-slate-800 truncate"><?= $_SESSION['email'] ?? '' ?></p>
                                </div>
                                <a href="<?= $baseUrl ?>views/user/dashboard.php" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-slate-600 hover:text-indigo-600 hover:bg-indigo-50 transition-all">
                                    <svg class="w-4 h-4 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                    Dashboard
                                </a>
                                <a href="<?= $baseUrl ?>views/user/items.php" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-slate-600 hover:text-indigo-600 hover:bg-indigo-50 transition-all">
                                    <svg class="w-4 h-4 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    My Items
                                </a>
                                <a href="<?= $baseUrl ?>views/user/notifications.php" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-slate-600 hover:text-indigo-600 hover:bg-indigo-50 transition-all">
                                    <svg class="w-4 h-4 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                    Notifications
                                </a>
                                <?php if (isAdmin()): ?>
                                <div class="my-2 border-t border-slate-50"></div>
                                <a href="<?= $baseUrl ?>views/admin/dashboard.php" class="flex items-center gap-3 px-4 py-2.5 text-sm font-bold text-purple-700 hover:bg-purple-50 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    Admin Panel
                                </a>
                                <?php endif; ?>
                                <div class="my-2 border-t border-slate-50"></div>
                                <a href="#" onclick="logout(); return false;" class="flex items-center gap-3 px-4 py-2.5 text-sm font-bold text-red-500 hover:bg-red-50 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Logout
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Mobile Menu Button -->
                <button class="md:hidden p-2.5 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all" onclick="toggleMobileMenu()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
    </nav>

    <!-- Mobile Sidebar -->
    <aside class="sidebar h-full md:hidden">
        <div class="sidebar-header">
            <a href="<?= $baseUrl ?>" class="flex items-center gap-2">
                <div class="p-1 rounded-lg bg-gradient-to-tr from-indigo-600 to-purple-600">
                    <svg width="24" height="24" viewBox="0 0 36 36" fill="none">
                        <path d="M18 10C14.134 10 11 13.134 11 17C11 21.5 18 26 18 26C18 26 25 21.5 25 17C25 13.134 21.866 10 18 10Z" stroke="white" stroke-width="2.5" fill="none"/>
                    </svg>
                </div>
                <span class="text-lg font-black bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-purple-600">Lost & Found</span>
            </a>
        </div>

        <nav class="flex-1 space-y-1 my-5">
            <?php if (isLoggedIn()): ?>
                <a href="<?= $baseUrl ?>views/user/dashboard.php" class="sidebar-link <?= ($currentPage ?? '') === 'dashboard' ? 'active' : '' ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </a>
                <a href="<?= $baseUrl ?>views/user/search.php" class="sidebar-link <?= ($currentPage ?? '') === 'search' ? 'active' : '' ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Search Items
                </a>
                <a href="<?= $baseUrl ?>views/user/items.php" class="sidebar-link <?= ($currentPage ?? '') === 'items' ? 'active' : '' ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    My Reports
                </a>
                <a href="<?= $baseUrl ?>views/user/matches.php" class="sidebar-link <?= ($currentPage ?? '') === 'matches' ? 'active' : '' ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    My Matches
                </a>
                <a href="<?= $baseUrl ?>views/user/notifications.php" class="sidebar-link <?= ($currentPage ?? '') === 'notifications' ? 'active' : '' ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    Notifications
                </a>
            <?php else: ?>
                <a href="<?= $baseUrl ?>" class="sidebar-link">Home</a>
                <a href="<?= $baseUrl ?>views/auth/login.php" class="sidebar-link">Login</a>
                <a href="<?= $baseUrl ?>views/auth/register.php" class="sidebar-link">Sign Up</a>
            <?php endif; ?>
        </nav>

        <div class="mt-auto pt-6 border-t border-slate-50">
            <?php if (isLoggedIn()): ?>
                <a href="#" onclick="logout(); return false;" class="sidebar-link text-red-500 hover:text-red-500 hover:bg-red-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Logout
                </a>
            <?php endif; ?>
        </div>
    </aside>

    <!-- Mobile Sidebar Overlay -->
    <div class="sidebar-overlay fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[70] transition-opacity md:hidden" onclick="toggleMobileMenu()"></div>

    <!-- Main Content -->
    <main class="flex-1">
        <?= $content ?? '' ?>
    </main>

    <!-- Global Footer -->
    <footer class="bg-indigo-950 text-indigo-100 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-1 rounded-xl bg-indigo-500">
                            <svg width="28" height="28" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M18 10C14.134 10 11 13.134 11 17C11 21.5 18 26 18 26C18 26 25 21.5 25 17C25 13.134 21.866 10 18 10Z" stroke="white" stroke-width="2.5" fill="none"/>
                            </svg>
                        </div>
                        <span class="text-2xl font-black text-white tracking-tight">Lost & Found Portal</span>
                    </div>
                    <p class="text-indigo-300 max-w-md leading-relaxed">
                        The community-driven platform to help you find your lost belongings and reunite found items with their rightful owners. Fast, secure, and reliable.
                    </p>
                </div>
                <div>
                    <h4 class="font-bold text-white mb-6 uppercase tracking-widest text-xs">Quick Links</h4>
                    <ul class="space-y-4 text-sm font-medium">
                        <li><a href="<?= $baseUrl ?>views/user/dashboard.php" class="hover:text-indigo-400 transition-colors">User Dashboard</a></li>
                        <li><a href="<?= $baseUrl ?>views/user/report.php" class="hover:text-indigo-400 transition-colors">Report New Item</a></li>
                        <li><a href="<?= $baseUrl ?>views/user/search.php" class="hover:text-indigo-400 transition-colors">Search All Items</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-white mb-6 uppercase tracking-widest text-xs">Contact Us</h4>
                    <ul class="space-y-4 text-sm font-medium">
                        <li class="flex items-center gap-3">
                            <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            support@lostfound.portal
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            24/7 Priority Support
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-indigo-900 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-indigo-400 text-xs font-bold">
                <p>&copy; <?= date('Y') ?> Lost & Found Portal. Built with &hearts; for the community.</p>
                <div class="flex gap-6">
                    <a href="#" class="hover:text-indigo-300">Privacy Policy</a>
                    <a href="#" class="hover:text-indigo-300">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="<?= $assetsUrl ?>js/main.js"></script>
    <script src="<?= $assetsUrl ?>js/auth.js"></script>
    <?php if (isLoggedIn()): ?>
    <script src="<?= $assetsUrl ?>js/notifications.js"></script>
    <?php endif; ?>
    
    <script>
        function toggleUserMenu() {
            const menu = document.getElementById('userMenu');
            menu.classList.toggle('hidden');
            
            document.addEventListener('click', function closeMenu(e) {
                if (!e.target.closest('#userMenu') && !e.target.closest('button')) {
                    menu.classList.add('hidden');
                    document.removeEventListener('click', closeMenu);
                }
            });
        }
    </script>
</body>
</html>

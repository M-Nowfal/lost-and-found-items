<?php
/**
 * Admin Layout
 * Standalone layout for admin pages - does NOT use header.php
 * Lost & Found Portal
 */
if (!isset($currentPage)) $currentPage = '';
if (!isset($pageTitle)) $pageTitle = 'Admin Dashboard';

// Fix: compute correct web-accessible base path from DOCUMENT_ROOT
// BASE_URL/ASSETS_PATH are wrong for nested pages because they use dirname(SCRIPT_NAME)
$docRoot = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), '/');
$rootPath = rtrim(str_replace('\\', '/', ROOT_PATH), '/');
$webBase = str_replace($docRoot, '', $rootPath);
$adminAssetsUrl = $webBase . '/assets/';
$adminBaseUrl = $webBase . '/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> | Admin - Lost & Found Portal</title>
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
    <link rel="stylesheet" href="<?= $adminAssetsUrl ?>css/style.css">
</head>
<body class="min-h-screen bg-gray-50 font-sans">

<!-- Mobile Sidebar Overlay -->
<div class="admin-sidebar-overlay" id="adminSidebarOverlay" onclick="toggleAdminSidebar()"></div>

<div class="flex min-h-screen">
    <!-- Sidebar -->
    <?php include __DIR__ . '/admin_sidebar.php'; ?>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Top Bar -->
        <header class="admin-topbar">
            <div class="flex items-center justify-between px-4 sm:px-6 py-4">
                <div class="flex items-center gap-3">
                    <button class="lg:hidden p-2 rounded-xl hover:bg-indigo-50 text-gray-600 hover:text-indigo-600 transition-all" onclick="toggleAdminSidebar()" aria-label="Toggle sidebar">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900"><?= $pageTitle ?></h1>
                        <p class="text-xs text-gray-500 hidden sm:block"><?= date('l, F j, Y') ?></p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <a href="<?= $adminBaseUrl ?>views/user/dashboard.php" class="hidden sm:inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/></svg>
                        Portal
                    </a>
                    <div class="flex items-center gap-3 pl-3 border-l border-gray-200">
                        <div class="admin-avatar"><?= strtoupper(substr($_SESSION['name'] ?? 'A', 0, 1)) ?></div>
                        <div class="hidden sm:block">
                            <p class="text-sm font-semibold text-gray-900"><?= $_SESSION['name'] ?? 'Admin' ?></p>
                            <p class="text-xs text-gray-500">Administrator</p>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            <?= $content ?? '' ?>
        </main>

        <!-- Admin Footer -->
        <footer class="px-6 py-4 text-center text-xs text-gray-400 border-t border-gray-100">
            &copy; <?= date('Y') ?> Lost & Found Portal &mdash; Admin Panel
        </footer>
    </div>
</div>

<script>const ADMIN_API_BASE = '<?= $adminBaseUrl ?>';</script>
<script src="<?= $adminAssetsUrl ?>js/main.js"></script>
<script src="<?= $adminAssetsUrl ?>js/auth.js"></script>

<script>
function toggleAdminSidebar() {
    const sidebar = document.getElementById('adminSidebar');
    const overlay = document.getElementById('adminSidebarOverlay');
    sidebar.classList.toggle('active');
    overlay.classList.toggle('active');
    document.body.classList.toggle('overflow-hidden');
}

// Close sidebar on window resize to desktop
window.addEventListener('resize', function() {
    if (window.innerWidth >= 1024) {
        const sidebar = document.getElementById('adminSidebar');
        const overlay = document.getElementById('adminSidebarOverlay');
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
        document.body.classList.remove('overflow-hidden');
    }
});
</script>

</body>
</html>

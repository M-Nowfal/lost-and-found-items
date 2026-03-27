<?php
/**
 * Landing Page
 * Lost & Found Portal
 */
require_once 'config/constants.php';
require_once 'config/database.php';

$pageTitle = 'Home';
$currentPage = 'home';

// Redirect logged-in users
if (isLoggedIn()) {
    redirect('views/user/dashboard.php');
}

// Get recent items
$itemModel = null;
$recentItems = [];
try {
    require_once 'models/Item.php';
    $itemModel = new Item();
    $recentItems = $itemModel->getRecent(6);
} catch (Exception $e) {
    // Database not ready yet
}

// Get stats
$stats = ['lost' => 0, 'found' => 0, 'matched' => 0];
try {
    if ($itemModel) {
        $stats = $itemModel->getStats();
    }
} catch (Exception $e) {
    // Ignore
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lost & Found Portal - Find What You've Lost</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
</head>
<body class="font-sans antialiased">

<!-- Navigation -->
<nav class="bg-white/90 backdrop-blur-md border-b border-gray-100 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <a href="<?= BASE_URL ?>" class="flex items-center gap-2">
                <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="18" cy="18" r="16" fill="url(#logoGrad)"/>
                    <path d="M18 10C14.134 10 11 13.134 11 17C11 21.5 18 26 18 26C18 26 25 21.5 25 17C25 13.134 21.866 10 18 10Z" stroke="white" stroke-width="2" fill="none"/>
                    <circle cx="18" cy="17" r="3" stroke="white" stroke-width="2" fill="none"/>
                    <defs><linearGradient id="logoGrad" x1="2" y1="2" x2="34" y2="34"><stop stop-color="#667EEA"/><stop offset="1" stop-color="#764BA2"/></linearGradient></defs>
                </svg>
                <span class="text-xl font-bold bg-gradient-to-r from-indigo-500 to-purple-600 bg-clip-text text-transparent">Lost & Found</span>
            </a>

            <div class="hidden md:flex items-center gap-4">
                <a href="<?= BASE_URL ?>views/auth/login.php" class="px-4 py-2 text-gray-600 hover:text-indigo-600 font-medium transition">Sign In</a>
                <a href="<?= BASE_URL ?>views/auth/register.php" class="px-5 py-2.5 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold rounded-xl hover:from-indigo-600 hover:to-purple-700 transition shadow-lg shadow-indigo-500/25">
                    Get Started
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="relative py-20 lg:py-32 overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50"></div>
    <div class="absolute top-20 right-0 w-96 h-96 bg-purple-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30"></div>
    <div class="absolute bottom-20 left-0 w-96 h-96 bg-indigo-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30"></div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto">
            <span class="inline-block px-4 py-1.5 rounded-full bg-indigo-100 text-indigo-700 text-sm font-semibold mb-6">
                🎉 Welcome to the community
            </span>
            
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-gray-900 mb-6">
                Find What You've 
                <span class="bg-gradient-to-r from-indigo-500 to-purple-600 bg-clip-text text-transparent">Lost</span>
            </h1>
            
            <p class="text-lg sm:text-xl text-gray-600 mb-10 max-w-2xl mx-auto">
                Lost something? Found something? We're here to help reunite people with their belongings. 
                Join our community and start searching today.
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="<?= BASE_URL ?>views/auth/register.php" class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold rounded-xl hover:from-indigo-600 hover:to-purple-700 transition shadow-xl shadow-indigo-500/30 hover:-translate-y-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    Get Started Free
                </a>
                <a href="<?= BASE_URL ?>views/auth/login.php" class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-white text-gray-700 font-semibold rounded-xl border-2 border-gray-200 hover:border-indigo-300 hover:text-indigo-600 transition">
                    Sign In
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="py-16 -mt-12 relative z-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-lg p-6 text-center border border-white/50">
                <div class="text-3xl sm:text-4xl font-extrabold bg-gradient-to-r from-indigo-500 to-purple-600 bg-clip-text text-transparent mb-2"><?= $stats['lost'] ?></div>
                <div class="text-gray-600 font-medium">Lost Items</div>
            </div>
            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-lg p-6 text-center border border-white/50">
                <div class="text-3xl sm:text-4xl font-extrabold bg-gradient-to-r from-pink-500 to-rose-600 bg-clip-text text-transparent mb-2"><?= $stats['found'] ?></div>
                <div class="text-gray-600 font-medium">Found Items</div>
            </div>
            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-lg p-6 text-center border border-white/50">
                <div class="text-3xl sm:text-4xl font-extrabold bg-gradient-to-r from-emerald-500 to-green-600 bg-clip-text text-transparent mb-2"><?= $stats['matched'] ?></div>
                <div class="text-gray-600 font-medium">Matches Made</div>
            </div>
            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-lg p-6 text-center border border-white/50">
                <div class="text-3xl sm:text-4xl font-extrabold bg-gradient-to-r from-blue-500 to-cyan-600 bg-clip-text text-transparent mb-2">24/7</div>
                <div class="text-gray-600 font-medium">Support</div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">How It Works</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Our platform makes it easy to report lost items or help found items get back to their owners.</p>
        </div>
        
        <div class="grid md:grid-cols-3 gap-8">
            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-lg p-8 text-center border border-white/50 hover:shadow-xl transition">
                <div class="w-16 h-16 mx-auto mb-6 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Report Your Item</h3>
                <p class="text-gray-600">Lost something? Create a report with details like location, date, and description.</p>
            </div>
            
            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-lg p-8 text-center border border-white/50 hover:shadow-xl transition">
                <div class="w-16 h-16 mx-auto mb-6 rounded-2xl bg-gradient-to-br from-pink-500 to-rose-600 flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Smart Matching</h3>
                <p class="text-gray-600">Our system automatically matches lost items with found reports based on similarity.</p>
            </div>
            
            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-lg p-8 text-center border border-white/50 hover:shadow-xl transition">
                <div class="w-16 h-16 mx-auto mb-6 rounded-2xl bg-gradient-to-br from-emerald-500 to-green-600 flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Get Notified</h3>
                <p class="text-gray-600">Receive instant notifications when a potential match is found for your items.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 rounded-3xl p-12 text-center text-white shadow-2xl">
            <h2 class="text-3xl sm:text-4xl font-bold mb-4">Ready to Get Started?</h2>
            <p class="text-lg text-white/90 mb-8 max-w-2xl mx-auto">Join thousands of users who have successfully found their lost items or helped others.</p>
            <a href="<?= BASE_URL ?>views/auth/register.php" class="inline-flex items-center gap-2 bg-white text-indigo-600 px-8 py-4 rounded-xl font-semibold hover:bg-indigo-50 transition shadow-xl">
                Create Your Account
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="bg-gray-900 text-gray-400 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-2">
                <svg width="32" height="32" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="18" cy="18" r="16" fill="url(#footerGrad)"/>
                    <path d="M18 10C14.134 10 11 13.134 11 17C11 21.5 18 26 18 26C18 26 25 21.5 25 17C25 13.134 21.866 10 18 10Z" stroke="white" stroke-width="2" fill="none"/>
                    <circle cx="18" cy="17" r="3" stroke="white" stroke-width="2" fill="none"/>
                    <defs><linearGradient id="footerGrad" x1="2" y1="2" x2="34" y2="34"><stop stop-color="#667EEA"/><stop offset="1" stop-color="#764BA2"/></linearGradient></defs>
                </svg>
                <span class="text-white font-bold">Lost & Found Portal</span>
            </div>
            <p class="text-sm">© <?= date('Y') ?> Lost & Found Portal. All rights reserved.</p>
        </div>
    </div>
</footer>

</body>
</html>

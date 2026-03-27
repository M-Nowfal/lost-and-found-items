<?php
/**
 * Login Page
 * Lost & Found Portal
 */
require_once '../../config/constants.php';
require_once '../../config/database.php';

$pageTitle = 'Login';
$currentPage = 'login';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect(BASE_URL . 'views/user/dashboard.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Lost & Found Portal</title>
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
<body class="min-h-screen flex flex-col bg-gray-50">

<div class="flex flex-1">
    <!-- Left Side - Form -->
    <div class="flex-1 flex items-center justify-center p-8">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <a href="<?= BASE_URL ?>" class="inline-flex items-center gap-2 mb-6">
                    <svg width="48" height="48" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="18" cy="18" r="16" fill="url(#loginGradient)" />
                        <path d="M18 10C14.134 10 11 13.134 11 17C11 21.5 18 26 18 26C18 26 25 21.5 25 17C25 13.134 21.866 10 18 10Z" stroke="white" stroke-width="2" fill="none"/>
                        <circle cx="18" cy="17" r="3" stroke="white" stroke-width="2" fill="none"/>
                        <defs>
                            <linearGradient id="loginGradient" x1="2" y1="2" x2="34" y2="34" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#667EEA"/>
                                <stop offset="1" stop-color="#764BA2"/>
                            </linearGradient>
                        </defs>
                    </svg>
                    <span class="text-2xl font-bold bg-gradient-to-r from-indigo-500 to-purple-600 bg-clip-text text-transparent">Lost & Found</span>
                </a>
                <h1 class="text-3xl font-bold text-gray-900">Welcome Back</h1>
                <p class="text-gray-600 mt-2">Sign in to access your account</p>
            </div>

            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl p-8 border border-white/50">
                <div id="errorMessage" class="hidden mb-4 p-3 rounded-lg bg-red-50 text-red-700 text-sm"></div>
                
                <form id="loginForm" class="space-y-5">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Email Address</label>
                        <div class="relative">
                            <input type="email" id="email" name="email" class="w-full px-4 py-3 pl-11 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 outline-none transition" placeholder="you@example.com" required>
                            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Password</label>
                        <div class="relative">
                            <input type="password" id="password" name="password" class="w-full px-4 py-3 pl-11 pr-11 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 outline-none transition" placeholder="Enter your password" required>
                            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <button type="button" onclick="togglePassword()" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <svg id="eyeIcon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" id="submitBtn" class="w-full py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold rounded-xl hover:from-indigo-600 hover:to-purple-700 transform hover:-translate-y-0.5 transition shadow-lg shadow-indigo-500/30">
                        Sign In
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-gray-600">Don't have an account? <a href="<?= BASE_URL ?>views/auth/register.php" class="text-indigo-600 font-semibold hover:text-indigo-700">Sign up</a></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Side - Illustration -->
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 items-center justify-center p-12">
        <div class="max-w-lg text-center text-white">
            <div class="mb-8">
                <svg class="w-48 h-48 mx-auto" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="50" cy="50" r="45" fill="rgba(255,255,255,0.1)"/>
                    <path d="M50 20C37.85 20 28 29.85 28 42C28 57 50 72 50 72C50 72 72 57 72 42C72 29.85 62.15 20 50 20Z" stroke="white" stroke-width="3" fill="none"/>
                    <circle cx="50" cy="42" r="10" stroke="white" stroke-width="3" fill="rgba(255,255,255,0.2)"/>
                </svg>
            </div>
            <h2 class="text-4xl font-bold mb-4">Find What You've Lost</h2>
            <p class="text-xl text-white/90 mb-8">Join our community and never worry about losing your belongings again.</p>
            <div class="flex justify-center gap-8 text-center">
                <div>
                    <div class="text-3xl font-bold">500+</div>
                    <div class="text-white/70">Items Found</div>
                </div>
                <div>
                    <div class="text-3xl font-bold">1K+</div>
                    <div class="text-white/70">Happy Users</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const input = document.getElementById('password');
    const icon = document.getElementById('eyeIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>';
    } else {
        input.type = 'password';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
    }
}

document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const btn = document.getElementById('submitBtn');
    const errorDiv = document.getElementById('errorMessage');
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    
    errorDiv.classList.add('hidden');
    btn.disabled = true;
    btn.innerHTML = '<svg class="animate-spin inline w-5 h-5 mr-2" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Signing in...';
    
    try {
        const response = await fetch('<?= str_replace('/views/auth', '', BASE_URL) ?>api/auth/login.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email, password })
        });
        
        const data = await response.json();
        
        if (data.success) {
            btn.innerHTML = '✓ Success!';
            btn.classList.remove('from-indigo-500', 'to-purple-600');
            btn.classList.add('from-emerald-500', 'to-green-600');
            
            setTimeout(() => {
                window.location.href = '<?= str_replace('/views/auth', '', BASE_URL) ?>views/' + data.redirect;
            }, 1000);
        } else {
            throw new Error(data.errors?.general || data.message || 'Login failed');
        }
    } catch (error) {
        errorDiv.textContent = error.message;
        errorDiv.classList.remove('hidden');
        btn.disabled = false;
        btn.innerHTML = 'Sign In';
    }
});
</script>

</body>
</html>

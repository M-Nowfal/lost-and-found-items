<?php
/**
 * Registration Page
 * Lost & Found Portal
 */
require_once '../../config/constants.php';
require_once '../../config/database.php';

$pageTitle = 'Register';
$currentPage = 'register';

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
    <title>Sign Up | Lost & Found Portal</title>
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
    <!-- Left Side - Illustration -->
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-pink-500 via-purple-500 to-indigo-500 items-center justify-center p-12">
        <div class="max-w-lg text-center text-white">
            <div class="mb-8">
                <svg class="w-48 h-48 mx-auto" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="50" cy="50" r="45" fill="rgba(255,255,255,0.1)"/>
                    <path d="M35 45L45 55L65 35" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="50" cy="50" r="35" stroke="white" stroke-width="3" stroke-dasharray="8 8"/>
                </svg>
            </div>
            <h2 class="text-4xl font-bold mb-4">Join Our Community</h2>
            <p class="text-xl text-white/90 mb-8">Create an account to report lost items, search for found items, and help others find what they've lost.</p>
            <div class="grid grid-cols-2 gap-6 text-center">
                <div class="bg-white/10 backdrop-blur rounded-xl p-4">
                    <div class="text-2xl mb-2">🔍</div>
                    <div class="text-sm">Report Lost Items</div>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-xl p-4">
                    <div class="text-2xl mb-2">✨</div>
                    <div class="text-sm">Report Found Items</div>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-xl p-4">
                    <div class="text-2xl mb-2">🤝</div>
                    <div class="text-sm">Get Matched</div>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-xl p-4">
                    <div class="text-2xl mb-2">🔔</div>
                    <div class="text-sm">Get Notified</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Side - Form -->
    <div class="flex-1 flex items-center justify-center p-8">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <a href="<?= BASE_URL ?>" class="inline-flex items-center gap-2 mb-6">
                    <svg width="48" height="48" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="18" cy="18" r="16" fill="url(#regGradient)" />
                        <path d="M18 10C14.134 10 11 13.134 11 17C11 21.5 18 26 18 26C18 26 25 21.5 25 17C25 13.134 21.866 10 18 10Z" stroke="white" stroke-width="2" fill="none"/>
                        <circle cx="18" cy="17" r="3" stroke="white" stroke-width="2" fill="none"/>
                        <defs>
                            <linearGradient id="regGradient" x1="2" y1="2" x2="34" y2="34" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#667EEA"/>
                                <stop offset="1" stop-color="#764BA2"/>
                            </linearGradient>
                        </defs>
                    </svg>
                    <span class="text-2xl font-bold bg-gradient-to-r from-indigo-500 to-purple-600 bg-clip-text text-transparent">Lost & Found</span>
                </a>
                <h1 class="text-3xl font-bold text-gray-900">Create Account</h1>
                <p class="text-gray-600 mt-2">Get started with your free account</p>
            </div>

            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl p-8 border border-white/50">
                <div id="errorMessage" class="hidden mb-4 p-3 rounded-lg bg-red-50 text-red-700 text-sm"></div>
                <div id="successMessage" class="hidden mb-4 p-3 rounded-lg bg-green-50 text-green-700 text-sm"></div>
                
                <form id="registerForm" class="space-y-5">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Full Name</label>
                        <div class="relative">
                            <input type="text" id="name" name="name" class="w-full px-4 py-3 pl-11 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 outline-none transition" placeholder="John Doe" required>
                            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                    </div>

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
                            <input type="password" id="password" name="password" class="w-full px-4 py-3 pl-11 pr-11 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 outline-none transition" placeholder="At least 6 characters" required>
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

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
                        <div class="relative">
                            <input type="password" id="confirm_password" name="confirm_password" class="w-full px-4 py-3 pl-11 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 outline-none transition" placeholder="Confirm your password" required>
                            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <input type="checkbox" id="terms" name="terms" class="mt-1 w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" required>
                        <label for="terms" class="text-sm text-gray-600">
                            I agree to the <a href="#" class="text-indigo-600 hover:text-indigo-700">Terms</a> and <a href="#" class="text-indigo-600 hover:text-indigo-700">Privacy Policy</a>
                        </label>
                    </div>

                    <button type="submit" id="submitBtn" class="w-full py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold rounded-xl hover:from-indigo-600 hover:to-purple-700 transform hover:-translate-y-0.5 transition shadow-lg shadow-indigo-500/30">
                        Create Account
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-gray-600">Already have an account? <a href="<?= BASE_URL ?>views/auth/login.php" class="text-indigo-600 font-semibold hover:text-indigo-700">Sign in</a></p>
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
    } else {
        input.type = 'password';
    }
}

document.getElementById('registerForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const btn = document.getElementById('submitBtn');
    const errorDiv = document.getElementById('errorMessage');
    const successDiv = document.getElementById('successMessage');
    
    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const confirm_password = document.getElementById('confirm_password').value;
    
    errorDiv.classList.add('hidden');
    successDiv.classList.add('hidden');
    
    if (password !== confirm_password) {
        errorDiv.textContent = 'Passwords do not match';
        errorDiv.classList.remove('hidden');
        return;
    }
    
    if (password.length < 6) {
        errorDiv.textContent = 'Password must be at least 6 characters';
        errorDiv.classList.remove('hidden');
        return;
    }
    
    btn.disabled = true;
    btn.innerHTML = '<svg class="animate-spin inline w-5 h-5 mr-2" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Creating account...';
    
    try {
        const response = await fetch('<?= BASE_URL ?>api/auth/register.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name, email, password, confirm_password })
        });
        
        const data = await response.json();
        
        if (data.success) {
            successDiv.textContent = data.message || 'Registration successful! Redirecting...';
            successDiv.classList.remove('hidden');
            btn.innerHTML = '✓ Success!';
            
            setTimeout(() => {
                window.location.href = '<?= BASE_URL ?>views/auth/login.php';
            }, 2000);
        } else {
            const errors = data.errors || {};
            const errorMsg = Object.values(errors).flat().join(', ') || data.message || 'Registration failed';
            throw new Error(errorMsg);
        }
    } catch (error) {
        errorDiv.textContent = error.message;
        errorDiv.classList.remove('hidden');
        btn.disabled = false;
        btn.innerHTML = 'Create Account';
    }
});
</script>

</body>
</html>

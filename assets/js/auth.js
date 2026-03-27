/**
 * Lost & Found Portal - Authentication JavaScript
 */

// Register Form Handler
document.addEventListener('DOMContentLoaded', () => {
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', handleRegister);
    }

    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', handleLogin);
    }

    // Password visibility toggle
    document.querySelectorAll('.password-toggle').forEach(toggle => {
        toggle.addEventListener('click', () => {
            const input = toggle.parentElement.querySelector('input');
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            toggle.innerHTML = isPassword ?
                '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>' :
                '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>';
        });
    });
});

async function handleRegister(e) {
    e.preventDefault();

    const form = e.target;
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;

    const name = form.querySelector('#name')?.value.trim();
    const email = form.querySelector('#email')?.value.trim();
    const password = form.querySelector('#password')?.value;
    const confirmPassword = form.querySelector('#confirm_password')?.value;

    // Validation
    if (!Validation.required(name)) {
        Toast.error('Please enter your name');
        return;
    }

    if (!Validation.email(email)) {
        Toast.error('Please enter a valid email');
        return;
    }

    if (!Validation.minLength(password, 6)) {
        Toast.error('Password must be at least 6 characters');
        return;
    }

    if (password !== confirmPassword) {
        Toast.error('Passwords do not match');
        return;
    }

    // Submit
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<svg class="animate-spin" width="20" height="20" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Creating account...';

    try {
        const response = await API.post('/auth/register.php', { name, email, password, confirm_password: confirmPassword });

        Toast.success(response.message || 'Registration successful!');

        setTimeout(() => {
            window.location.href = 'login.php';
        }, 1500);
    } catch (error) {
        Toast.error(error.message || 'Registration failed');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
}

async function handleLogin(e) {
    e.preventDefault();

    const form = e.target;
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;

    const email = form.querySelector('#email')?.value.trim();
    const password = form.querySelector('#password')?.value;

    // Validation
    if (!Validation.required(email) || !Validation.required(password)) {
        Toast.error('Please fill in all fields');
        return;
    }

    // Submit
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<svg class="animate-spin" width="20" height="20" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Signing in...';

    try {
        const response = await API.post('/auth/login.php', { email, password });

        Toast.success('Login successful!');

        setTimeout(() => {
            window.location.href = response.redirect || '/views/user/dashboard.php';
        }, 1000);
    } catch (error) {
        Toast.error(error.message || 'Login failed');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
}

// Logout function
async function logout() {
    try {
        await API.post('/auth/logout.php');
        const loginUrl = typeof API_BASE !== 'undefined' ? API_BASE.replace('/api', '/views/auth/login.php') : '/views/auth/login.php';
        Toast.success('Logged out successfully');
        setTimeout(() => {
            window.location.href = loginUrl;
        }, 1000);
    } catch (error) {
        const loginUrl2 = typeof API_BASE !== 'undefined' ? API_BASE.replace('/api', '/views/auth/login.php') : '/views/auth/login.php';
        window.location.href = loginUrl2;
    }
}

/* ============================================
   UPDATED LOGIN.JS - Backend Integration
   Connects to PHP backend for authentication
   ============================================ */

document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const errorMessage = document.getElementById('errorMessage');

    loginForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value;
        const role = document.getElementById('role').value;

        // Clear previous error messages
        errorMessage.classList.remove('show');

        // Validation
        if (!username || !password || !role) {
            showError('Please fill in all fields');
            return;
        }

        // Attempt backend login
        try {
            const response = await fetch('backend/api/auth.php?action=login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    username: username,
                    password: password,
                    role: role
                })
            });

            const data = await response.json();

            if (data.success) {
                // Store authentication data
                localStorage.setItem('authToken', data.token);
                localStorage.setItem('userRole', data.user.role);
                localStorage.setItem('username', data.user.username);
                localStorage.setItem('userId', data.user.id);
                localStorage.setItem('loginTime', new Date().getTime());

                // Redirect to appropriate dashboard
                if (data.user.role === 'chef') {
                    window.location.href = 'pages/chef-dashboard.html';
                } else if (data.user.role === 'admin') {
                    window.location.href = 'pages/admin-dashboard.html';
                }
            } else {
                showError(data.message || 'Login failed. Please try again.');
            }
        } catch (error) {
            console.error('Login error:', error);
            // Fallback to demo credentials if backend unavailable
            handleDemoLogin(username, password, role);
        }
    });

    function showError(message) {
        errorMessage.textContent = message;
        errorMessage.classList.add('show');
        setTimeout(() => {
            errorMessage.classList.remove('show');
        }, 5000);
    }

    // Fallback demo login for development
    function handleDemoLogin(username, password, role) {
        if (role === 'chef') {
            if (username === 'chef' && password === 'chef123') {
                loginSuccess('chef');
            } else {
                showError('Invalid chef credentials. Use chef/chef123');
            }
        } else if (role === 'admin') {
            if (username === 'admin' && password === 'admin123') {
                loginSuccess('admin');
            } else {
                showError('Invalid admin credentials. Use admin/admin123');
            }
        }
    }

    function loginSuccess(role) {
        // Store user session in localStorage
        localStorage.setItem('userRole', role);
        localStorage.setItem('username', document.getElementById('username').value);
        localStorage.setItem('loginTime', new Date().getTime());

        // Redirect to appropriate dashboard
        if (role === 'chef') {
            window.location.href = 'pages/chef-dashboard.html';
        } else if (role === 'admin') {
            window.location.href = 'pages/admin-dashboard.html';
        }
    }
});

// Check if user is already logged in
window.addEventListener('load', function() {
    const userRole = localStorage.getItem('userRole');
    if (userRole && window.location.pathname.includes('index.html')) {
        // User is logged in, redirect to dashboard
        if (userRole === 'chef') {
            window.location.href = 'pages/chef-dashboard.html';
        } else if (userRole === 'admin') {
            window.location.href = 'pages/admin-dashboard.html';
        }
    }
});

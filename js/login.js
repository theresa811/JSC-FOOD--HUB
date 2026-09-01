/* ============================================
   LOGIN.JS - Authentication Handler
   ============================================ */

document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const errorMessage = document.getElementById('errorMessage');

    loginForm.addEventListener('submit', function(e) {
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

        // Demo credentials validation
        if (role === 'chef') {
            if (username === 'chef' && password === 'chef123') {
                loginSuccess('chef', 'Chef Dashboard');
            } else {
                showError('Invalid chef credentials. Use chef/chef123');
            }
        } else if (role === 'admin') {
            if (username === 'admin' && password === 'admin123') {
                loginSuccess('admin', 'Admin Dashboard');
            } else {
                showError('Invalid admin credentials. Use admin/admin123');
            }
        } else {
            showError('Please select a valid role');
        }
    });

    function showError(message) {
        errorMessage.textContent = message;
        errorMessage.classList.add('show');
        setTimeout(() => {
            errorMessage.classList.remove('show');
        }, 5000);
    }

    function loginSuccess(role, dashboardName) {
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

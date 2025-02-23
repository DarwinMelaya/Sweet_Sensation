document.getElementById('registerForm').addEventListener('submit', function(event) {
    let valid = true;

    // Username validation
    const username = document.getElementById('username').value;
    if (username.length < 3) {
        valid = false;
        document.getElementById('usernameError').textContent = 'Username must be at least 3 characters long';
    } else {
        document.getElementById('usernameError').textContent = '';
    }

    // Email validation
    const email = document.getElementById('email').value;
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email)) {
        valid = false;
        document.getElementById('emailError').textContent = 'Invalid email address';
    } else {
        document.getElementById('emailError').textContent = '';
    }

    // Password validation
    const password = document.getElementById('password').value;
    if (password.length < 6) {
        valid = false;
        document.getElementById('passwordError').textContent = 'Password must be at least 6 characters long';
    } else {
        document.getElementById('passwordError').textContent = '';
    }

    // Confirm password validation
    const confirmPassword = document.getElementById('confirmPassword').value;
    if (password !== confirmPassword) {
        valid = false;
        document.getElementById('confirmPasswordError').textContent = 'Passwords do not match';
    } else {
        document.getElementById('confirmPasswordError').textContent = '';
    }

    if (!valid) {
        event.preventDefault();
    }
});

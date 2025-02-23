document.getElementById('loginForm').addEventListener('submit', function(event) {
    let valid = true;

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

    if (!valid) {
        event.preventDefault();
    }
});

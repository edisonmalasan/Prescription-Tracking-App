// public/assets/js/auth.js

document.addEventListener('DOMContentLoaded', () => {
    // Check if we are on the registration page
    const registrationForm = document.getElementById('registrationForm');
    if (registrationForm) {
        registrationForm.addEventListener('submit', handleRegistration);
    }

    // Check if we are on the login page
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', handleLogin);
    }

    // --- Utility Functions ---
    function showMessage(elementId, message, isError = false) {
        const messageElement = document.getElementById(elementId);
        if (messageElement) {
            messageElement.textContent = message;
            messageElement.style.color = isError ? 'red' : 'green';
            messageElement.style.fontWeight = 'bold';
        }
    }

    // --- Registration Logic ---
    function handleRegistration(event) {
        event.preventDefault(); // Prevent default form submission

        const email = document.getElementById('regEmail').value;
        const password = document.getElementById('regPassword').value;
        const confirmPassword = document.getElementById('confirmPassword').value;
        const firstName = document.getElementById('firstName').value;
        const lastName = document.getElementById('lastName').value;
        const phoneNumber = document.getElementById('phoneNumber').value;
        const agreeTerms = document.getElementById('agreeTerms').checked;

        // Basic client-side validation
        if (password !== confirmPassword) {
            showMessage('registrationMessage', 'Passwords do not match!', true);
            return;
        }

        if (!agreeTerms) {
            showMessage('registrationMessage', 'You must agree to the Terms of Service and Privacy Policy.', true);
            return;
        }

        // Simulate checking if user already exists
        const existingUsers = JSON.parse(localStorage.getItem('users')) || [];
        const userExists = existingUsers.some(user => user.email === email);

        if (userExists) {
            showMessage('registrationMessage', 'An account with this email already exists!', true);
            return;
        }

        // Store user data (insecurely in localStorage for demo)
        const newUser = {
            email: email,
            password: password, // In a real app, this would be hashed!
            firstName: firstName,
            lastName: lastName,
            phoneNumber: phoneNumber
        };

        existingUsers.push(newUser);
        localStorage.setItem('users', JSON.stringify(existingUsers));

        showMessage('registrationMessage', 'Registration successful! Redirecting to login...', false);
        // Simulate a delay before redirecting
        setTimeout(() => {
            window.location.href = 'login.html';
        }, 2000);
    }

    // --- Login Logic ---
    function handleLogin(event) {
        event.preventDefault(); // Prevent default form submission

        const email = document.getElementById('loginEmail').value;
        const password = document.getElementById('loginPassword').value;
        const rememberMe = document.getElementById('rememberMe').checked;

        const existingUsers = JSON.parse(localStorage.getItem('users')) || [];
        const user = existingUsers.find(u => u.email === email && u.password === password);

        if (user) {
            showMessage('loginMessage', 'Login successful! Redirecting...', false);
            
            // Simulate storing login state
            if (rememberMe) {
                localStorage.setItem('loggedInUser', JSON.stringify(user));
            } else {
                // For session-based login (no remember me), you might use sessionStorage
                sessionStorage.setItem('loggedInUser', JSON.stringify(user));
            }

            // In a real application, you would redirect to a dashboard or secured page
            setTimeout(() => {
                window.location.href = 'index.html'; // Or a dashboard page
            }, 1500);

        } else {
            showMessage('loginMessage', 'Invalid email or password!', true);
        }
    }
});
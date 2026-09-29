const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
const phoneRegex = /^[0-9+\-\s]{7,15}$/;
const passRegex = /^(?=.*\d).{8,}$/;

function showError(id, message) {
    const el = document.getElementById(id);
    if (el) el.textContent = message;
}

function clearErrors(form) {
    form.querySelectorAll('.field-error').forEach(el => el.textContent = '');
}

const registerForm = document.getElementById('register-form');
if (registerForm) {
    registerForm.addEventListener('submit', function (e) {
        clearErrors(registerForm);
        let ok = true;

        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const pass = document.getElementById('pass').value;
        const contact = document.getElementById('contact').value.trim();

        if (name.length < 2) {
            showError('name-error', 'Name must be at least 2 characters');
            ok = false;
        }
        if (!emailRegex.test(email)) {
            showError('email-error', 'Enter a valid email');
            ok = false;
        }
        if (!passRegex.test(pass)) {
            showError('pass-error', 'Password needs 8+ characters and a number');
            ok = false;
        }
        if (!phoneRegex.test(contact)) {
            showError('contact-error', 'Enter a valid phone number');
            ok = false;
        }

        if (!ok) {
            e.preventDefault();
            return;
        }

        const submitBtn = document.getElementById('register-submit');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Creating account...';
    });
}

const loginForm = document.getElementById('login-form');
if (loginForm) {
    loginForm.addEventListener('submit', function (e) {
        clearErrors(loginForm);
        let ok = true;

        const email = document.getElementById('email').value.trim();
        const pass = document.getElementById('pass').value;

        if (!emailRegex.test(email)) {
            showError('email-error', 'Enter a valid email');
            ok = false;
        }
        if (pass.length === 0) {
            showError('pass-error', 'Enter your password');
            ok = false;
        }

        if (!ok) {
            e.preventDefault();
            return;
        }

        const submitBtn = document.getElementById('login-submit');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Logging in...';
    });
}

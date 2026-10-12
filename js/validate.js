const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
const phoneRegex = /^[0-9+\-\s]{7,15}$/;

// Lab spec (Appendix, JavaScript Validation Patterns) requires at minimum
// 8+ characters with a digit: /^(?=.*\d).{8,}$/
// This also requires a letter, so a password like "11111111" is rejected.
const passRegex = /^(?=.*[A-Za-z])(?=.*\d).{8,}$/;

function showError(id, message) {
    const el = document.getElementById(id);
    if (el) el.textContent = message;
}

function clearErrors(form) {
    form.querySelectorAll('.field-error').forEach(el => el.textContent = '');
}

function markField(input, isValid) {
    input.classList.remove('valid', 'invalid');
    if (input.value.length === 0) return;
    input.classList.add(isValid ? 'valid' : 'invalid');
}

// --- live password strength meter (register page only) ---

function passwordScore(pw) {
    let score = 0;
    if (pw.length >= 8) score++;
    if (/\d/.test(pw)) score++;
    if (/[A-Za-z]/.test(pw)) score++;
    if (/[^A-Za-z0-9]/.test(pw) || pw.length >= 12) score++;
    return Math.min(score, 3);
}

const pwInput = document.getElementById('pass');
const pwMeter = document.getElementById('pw-meter');
if (pwInput && pwMeter) {
    const bars = pwMeter.querySelectorAll('.pw-meter-bar');
    const meterLabel = document.getElementById('pw-meter-label');
    const colors = ['#B3261E', '#E8871E', '#1F7A4D'];
    const labels = ['Too weak', 'Getting there', 'Strong password'];

    pwInput.addEventListener('input', function () {
        const score = passwordScore(pwInput.value);
        bars.forEach((bar, i) => {
            bar.style.background = pwInput.value.length === 0
                ? ''
                : (i < score ? colors[score - 1] : '');
        });
        meterLabel.textContent = pwInput.value.length === 0
            ? 'At least 8 characters, with a letter and a number'
            : labels[score - 1] || 'Too weak';
        markField(pwInput, passRegex.test(pwInput.value));
    });
}

// --- register form ---

const registerForm = document.getElementById('register-form');
if (registerForm) {
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const cityInput = document.getElementById('city');
    const contactInput = document.getElementById('contact');

    [nameInput, emailInput, contactInput].forEach(input => {
        if (!input) return;
        input.addEventListener('blur', function () {
            if (input === nameInput) markField(input, input.value.trim().length >= 2);
            if (input === emailInput) markField(input, emailRegex.test(input.value.trim()));
            if (input === contactInput) markField(input, phoneRegex.test(input.value.trim()));
        });
    });

    registerForm.addEventListener('submit', function (e) {
        clearErrors(registerForm);
        let ok = true;

        const name = nameInput.value.trim();
        const email = emailInput.value.trim();
        const pass = pwInput.value;
        const city = cityInput.value.trim();
        const contact = contactInput.value.trim();

        if (name.length < 2) {
            showError('name-error', 'Name must be at least 2 characters');
            markField(nameInput, false);
            ok = false;
        }
        if (!emailRegex.test(email)) {
            showError('email-error', 'Enter a valid email');
            markField(emailInput, false);
            ok = false;
        }
        if (!passRegex.test(pass)) {
            showError('pass-error', 'Password needs 8+ characters with a letter and a number');
            ok = false;
        }
        if (city.length === 0 || city.length > 30) {
            showError('city-error', 'Enter your city (30 characters at most)');
            markField(cityInput, false);
            ok = false;
        }
        if (!phoneRegex.test(contact)) {
            showError('contact-error', 'Enter a valid phone number');
            markField(contactInput, false);
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

// --- login form ---

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

<?php
// Registration page for Aquaflow
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Register — Aquaflow</title>
    <link rel="stylesheet" href="css/tailwind.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="bg-gray-50 text-gray-800">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="w-full max-w-lg bg-white rounded shadow p-6">
            <h1 class="text-2xl font-semibold mb-4">Create an account</h1>
            <form id="registerForm" novalidate>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium">Full name</label>
                        <input type="text" name="full_name" id="full_name" class="mt-1 block w-full border rounded px-3 py-2" required>
                        <p class="text-xs text-red-600 mt-1 hidden" data-error-for="full_name"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Email</label>
                        <input type="email" name="email" id="email" class="mt-1 block w-full border rounded px-3 py-2" required>
                        <p class="text-xs text-red-600 mt-1 hidden" data-error-for="email"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Phone</label>
                        <input type="tel" name="phone" id="phone" class="mt-1 block w-full border rounded px-3 py-2">
                        <p class="text-xs text-red-600 mt-1 hidden" data-error-for="phone"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Postal code</label>
                        <input type="text" name="postal_code" id="postal_code" class="mt-1 block w-full border rounded px-3 py-2">
                        <p class="text-xs text-red-600 mt-1 hidden" data-error-for="postal_code"></p>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium">Address</label>
                    <textarea name="address" id="address" rows="2" class="mt-1 block w-full border rounded px-3 py-2"></textarea>
                    <p class="text-xs text-red-600 mt-1 hidden" data-error-for="address"></p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium">City</label>
                        <input type="text" name="city" id="city" class="mt-1 block w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium">State</label>
                        <input type="text" name="state" id="state" class="mt-1 block w-full border rounded px-3 py-2">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium">Password</label>
                        <input type="password" name="password" id="password" class="mt-1 block w-full border rounded px-3 py-2" required>
                        <p class="text-xs text-red-600 mt-1 hidden" data-error-for="password"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Confirm Password</label>
                        <input type="password" name="confirm_password" id="confirm_password" class="mt-1 block w-full border rounded px-3 py-2" required>
                        <p class="text-xs text-red-600 mt-1 hidden" data-error-for="confirm_password"></p>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-between">
                    <button id="submitBtn" class="bg-blue-600 text-white px-4 py-2 rounded">Register</button>
                    <a href="login.php" class="text-sm text-gray-600">Already have an account? Login</a>
                </div>

                <div id="formMessage" class="mt-3 text-sm"></div>
            </form>
        </div>
    </div>

    <script>
        // Simple utilities
        const showError = (field, message) => {
            const el = document.querySelector('[data-error-for="' + field + '"]');
            if (el) {
                el.textContent = message;
                el.classList.remove('hidden');
            }
        };
        const clearErrors = () => {
            document.querySelectorAll('[data-error-for]').forEach(e => {
                e.textContent = '';
                e.classList.add('hidden');
            });
            const msg = document.getElementById('formMessage');
            if (msg) {
                msg.textContent = '';
                msg.className = 'mt-3 text-sm';
            }
        };

        const validateEmail = (email) => {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        };

        document.getElementById('registerForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            clearErrors();

            const full_name = document.getElementById('full_name').value.trim();
            const email = document.getElementById('email').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const address = document.getElementById('address').value.trim();
            const city = document.getElementById('city').value.trim();
            const state = document.getElementById('state').value.trim();
            const postal_code = document.getElementById('postal_code').value.trim();
            const password = document.getElementById('password').value;
            const confirm_password = document.getElementById('confirm_password').value;

            let hasError = false;
            if (!full_name) {
                showError('full_name', 'Full name is required');
                hasError = true;
            }
            if (!email) {
                showError('email', 'Email is required');
                hasError = true;
            } else if (!validateEmail(email)) {
                showError('email', 'Invalid email');
                hasError = true;
            }
            if (!password) {
                showError('password', 'Password is required');
                hasError = true;
            } else if (password.length < 8) {
                showError('password', 'Password must be atleast 8 characters');
                hasError = true;
            }
            if (password !== confirm_password) {
                showError('confirm_password', 'Passwords do not match');
                hasError = true;
            }

            if (hasError) return;

            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Registering...';

            const payload = {
                full_name,
                email,
                phone,
                address,
                city,
                state,
                postal_code,
                password
            };

            try {
                // Default endpoint: backend API relative to frontend folder
                const res = await fetch('../backend/api/auth/register.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();

                const msgEl = document.getElementById('formMessage');
                if (res.ok && data && data.success) {
                    msgEl.textContent = data.message || 'Registration successful. Redirecting to login...';
                    msgEl.className = 'mt-3 text-sm text-green-600';
                    setTimeout(() => {
                        window.location.href = 'login.php';
                    }, 1500);
                } else {
                    // Show either server errors or generic
                    const message = (data && data.message) ? data.message : 'Registration failed';
                    msgEl.textContent = message;
                    msgEl.className = 'mt-3 text-sm text-red-600';
                    if (data && data.errors) {
                        // map field errors if provided
                        for (const key in data.errors) {
                            if (data.errors.hasOwnProperty(key)) showError(key, data.errors[key]);
                        }
                    }
                }
            } catch (err) {
                const msgEl = document.getElementById('formMessage');
                msgEl.textContent = 'Network or server error. Check console for details.';
                msgEl.className = 'mt-3 text-sm text-red-600';
                console.error(err);
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Register';
            }
        });
    </script>
</body>

</html>
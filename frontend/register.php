<?php
// Registration page for Aquaflow
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Register — Aquaflow</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../favicon.png">
    <link rel="stylesheet" href="css/tailwind.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="bg-gray-50 text-gray-800">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="w-full max-w-lg bg-white rounded multi-shadow p-6">
            <h1 class="text-2xl font-semibold mb-4">Create an account</h1>
            <form id="registerForm" novalidate>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium">Full name</label>
                        <input type="text" name="full_name" id="full_name" placeholder="John Doe" class="mt-1 block w-full border rounded px-3 py-2" required>
                        <p class="text-xs text-red-600 mt-1 hidden" data-error-for="full_name"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Email</label>
                        <input type="email" name="email" id="email" placeholder="you@example.com" class="mt-1 block w-full border rounded px-3 py-2" required>
                        <p class="text-xs text-red-600 mt-1 hidden" data-error-for="email"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Phone</label>
                        <input type="tel" name="phone" id="phone" placeholder="+2348000000000" class="mt-1 block w-full border rounded px-3 py-2">
                        <p class="text-xs text-red-600 mt-1 hidden" data-error-for="phone"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Postal code</label>
                        <input type="text" name="postal_code" id="postal_code" placeholder="100001" class="mt-1 block w-full border rounded px-3 py-2">
                        <p class="text-xs text-red-600 mt-1 hidden" data-error-for="postal_code"></p>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium">Address</label>
                    <textarea name="address" id="address" rows="2" placeholder="Street address, area" class="mt-1 block w-full border rounded px-3 py-2"></textarea>
                    <p class="text-xs text-red-600 mt-1 hidden" data-error-for="address"></p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium">State</label>
                        <select name="state" id="state" class="mt-1 block w-full border rounded px-3 py-2">
                            <option value="">Select state</option>
                            <option value="lagos">Lagos</option>
                            <option value="kano">Kano</option>
                            <option value="rivers">Rivers</option>
                            <option value="kaduna">Kaduna</option>
                            <option value="adamawa">Adamawa</option>
                            <option value="taraba">Taraba</option>
                        </select>
                        <p class="text-xs text-red-600 mt-1 hidden" data-error-for="state"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium">City / LGA</label>
                        <select name="city" id="city" class="mt-1 block w-full border rounded px-3 py-2">
                            <option value="">Select city / LGA</option>
                        </select>
                        <p class="text-xs text-red-600 mt-1 hidden" data-error-for="city"></p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium">Password</label>
                        <input type="password" name="password" id="password" placeholder="At least 8 characters" class="mt-1 block w-full border rounded px-3 py-2" required>
                        <p class="text-xs text-red-600 mt-1 hidden" data-error-for="password"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Confirm Password</label>
                        <input type="password" name="confirm_password" id="confirm_password" placeholder="Repeat your password" class="mt-1 block w-full border rounded px-3 py-2" required>
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
            const city = document.getElementById('city').value;
            const state = document.getElementById('state').value;
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

            // validate selects
            if (!state) {
                showError('state', 'Please select a state');
                hasError = true;
            }
            if (!city) {
                showError('city', 'Please select a city or LGA');
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
    <script>
        const stateSelect = document.getElementById('state');
        const citySelect = document.getElementById('city');

        // populate cities given the loaded mapping object
        function populateCitiesFromData(stateKey, data) {
            citySelect.innerHTML = '<option value="">Select city / LGA</option>';
            if (!stateKey || !data || !data[stateKey]) return;
            const list = data[stateKey].cities || [];
            list.forEach(item => {
                const opt = document.createElement('option');
                opt.value = item.id;
                opt.textContent = item.name;
                citySelect.appendChild(opt);
            });
        }

        // populate state select from data
        function populateStates(data) {
            // keep the first default option
            stateSelect.innerHTML = '<option value="">Select state</option>';
            for (const key of Object.keys(data)) {
                const opt = document.createElement('option');
                opt.value = key;
                opt.textContent = data[key].name || key;
                stateSelect.appendChild(opt);
            }
        }

        // fallback small mapping (used if API fetch fails)
        const fallback = {
            "lagos": {
                "name": "Lagos",
                "cities": [{
                    "id": "ikeja",
                    "name": "Ikeja"
                }, {
                    "id": "surulere",
                    "name": "Surulere"
                }]
            },
            "kano": {
                "name": "Kano",
                "cities": [{
                    "id": "kano_municipal",
                    "name": "Kano Municipal"
                }, {
                    "id": "gwale",
                    "name": "Gwale"
                }]
            }
        };

        // Load mapping from backend API, then wire up events
        async function loadStateCityData() {
            try {
                const res = await fetch('../backend/api/location/states_cities.php');
                if (!res.ok) throw new Error('Failed to fetch');
                const data = await res.json();
                // If the data shape is nested object (our file), use it
                if (data && typeof data === 'object' && Object.keys(data).length) {
                    populateStates(data);
                    stateSelect.addEventListener('change', () => populateCitiesFromData(stateSelect.value, data));
                    // pre-populate if browser autofilled state
                    if (stateSelect.value) populateCitiesFromData(stateSelect.value, data);
                    return;
                }
            } catch (err) {
                console.warn('Could not load states JSON, using fallback', err);
            }

            // fallback path
            populateStates(fallback);
            stateSelect.addEventListener('change', () => populateCitiesFromData(stateSelect.value, fallback));
            if (stateSelect.value) populateCitiesFromData(stateSelect.value, fallback);
        }

        // start
        loadStateCityData();
    </script>
</body>

</html>
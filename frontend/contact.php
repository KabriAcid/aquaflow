<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Aquaflow</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="bg-gray-50">

    <nav class="bg-white multi-shadow">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex justify-between">
                <div class="flex space-x-7">
                    <div>
                        <a href="../index.php" class="flex items-center py-4 px-2">
                            <span class="font-bold text-gray-700 text-lg">Aquaflow</span>
                        </a>
                    </div>
                    <div class="hidden md:flex items-center space-x-1">
                        <a href="../index.php" class="py-4 px-2 text-gray-500 font-semibold hover:text-blue-500 transition duration-300">Home</a>
                        <a href="products.php" class="py-4 px-2 text-gray-500 font-semibold hover:text-blue-500 transition duration-300">Products</a>
                        <a href="contact.php" class="py-4 px-2 text-blue-500 border-b-4 border-blue-500 font-semibold">Contact</a>
                    </div>
                </div>
                <div class="hidden md:flex items-center space-x-3 ">
                    <a href="login.php" class="py-2 px-2 font-medium text-gray-500 rounded hover:bg-blue-500 hover:text-white transition duration-300">Log In</a>
                    <a href="register.php" class="py-2 px-2 font-medium text-white bg-blue-500 rounded hover:bg-blue-400 transition duration-300">Register</a>
                </div>
                <div class="md:hidden flex items-center">
                    <button class="outline-none mobile-menu-button">
                        <svg class=" w-6 h-6 text-gray-500 hover:text-blue-500 "
                            x-show="!showMenu"
                            fill="none"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path d="M4 6h16M4 12h16m-7 6h7"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <div class="hidden mobile-menu">
            <ul class="">
                <li><a href="../index.php" class="block text-sm px-2 py-4 text-white bg-blue-500 font-semibold">Home</a></li>
                <li><a href="products.php" class="block text-sm px-2 py-4 hover:bg-blue-500 transition duration-300">Products</a></li>
                <li><a href="contact.php" class="block text-sm px-2 py-4 hover:bg-blue-500 transition duration-300">Contact</a></li>
            </ul>
        </div>
    </nav>

    <main class="py-10">
        <div class="max-w-6xl mx-auto px-4">
            <div class="bg-white p-8 rounded-lg multi-shadow">
                <h1 class="text-3xl font-bold mb-6 text-gray-800">Contact Us</h1>
                <p class="text-gray-600 mb-8">We\'d love to hear from you. Please fill out the form below and we\'ll get back to you as soon as possible.</p>

                <form id="contactForm" novalidate>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                            <input type="text" name="name" id="name" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Your Name" required>
                            <p class="text-xs text-red-600 mt-1 hidden" data-error-for="name"></p>
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" id="email" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="you@example.com" required>
                            <p class="text-xs text-red-600 mt-1 hidden" data-error-for="email"></p>
                        </div>
                        <div class="md:col-span-2">
                            <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                            <textarea name="message" id="message" rows="4" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Your message..." required></textarea>
                            <p class="text-xs text-red-600 mt-1 hidden" data-error-for="message"></p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <button id="submitBtn" type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Send Message
                        </button>
                    </div>
                    <div id="formMessage" class="mt-3 text-sm"></div>
                </form>
            </div>
        </div>
    </main>

    <footer class="bg-white border-t py-6 mt-10">
        <div class="max-w-6xl mx-auto px-4 text-sm text-gray-600">
            <div class="flex items-center justify-between">
                <div>© 2025 Aquaflow. All rights reserved.</div>
                <div class="space-x-4">
                    <a href="#">Privacy</a>
                    <a href="contact.php">Contact</a>
                </div>
            </div>
        </div>
    </footer>

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

        document.getElementById('contactForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            clearErrors();

            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const message = document.getElementById('message').value.trim();

            let hasError = false;
            if (!name) {
                showError('name', 'Name is required');
                hasError = true;
            }
            if (!email) {
                showError('email', 'Email is required');
                hasError = true;
            } else if (!validateEmail(email)) {
                showError('email', 'Invalid email address');
                hasError = true;
            }
            if (!message) {
                showError('message', 'Message is required');
                hasError = true;
            }

            if (hasError) return;

            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Sending...';

            const payload = {
                name,
                email,
                message
            };

            try {
                // Default endpoint: backend API relative to frontend folder
                const res = await fetch('../backend/api/contact/submit.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();

                const msgEl = document.getElementById('formMessage');
                if (res.ok && data && data.success) {
                    msgEl.textContent = data.message || 'Your message has been sent successfully.';
                    msgEl.className = 'mt-3 text-sm text-green-600';
                    document.getElementById('contactForm').reset();
                } else {
                    const message = (data && data.message) ? data.message : 'Failed to send message.';
                    msgEl.textContent = message;
                    msgEl.className = 'mt-3 text-sm text-red-600';
                    if (data && data.errors) {
                        for (const key in data.errors) {
                            if (data.errors.hasOwnProperty(key)) showError(key, data.errors[key]);
                        }
                    }
                }
            } catch (err) {
                const msgEl = document.getElementById('formMessage');
                msgEl.textContent = 'A network or server error occurred. Please try again later.';
                msgEl.className = 'mt-3 text-sm text-red-600';
                console.error(err);
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Send Message';
            }
        });
    </script>

</body>

</html>
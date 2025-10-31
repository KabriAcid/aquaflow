<?php
/* PLACEHOLDER: Login page - implement form and client-side validation */
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Login — Aquaflow</title>
  <!-- Favicon -->
  <link rel="icon" type="image/png" href="../favicon.png">
  <link rel="stylesheet" href="css/tailwind.css">
  <link rel="stylesheet" href="css/style.css">
</head>

<body class="bg-gray-50 text-gray-800">
  <div class="min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md bg-white rounded p-6 multi-shadow">
      <div class="flex items-center justify-center py-6">
        <img src="../favicon.png" alt="Aquaflow" class="h-12">
        <span class="text-blue-500 font-semibold font-2xl block text-center">AquaFlow</span>
      </div>
      <h1 class="text-2xl font-semibold mb-2">Sign in to your account</h1>
      <p class="form-note mb-4">Enter your credentials to access your dashboard.</p>

      <!-- PLACEHOLDER: Add login form that posts to backend/api/auth/login.php -->
      <form id="loginForm" novalidate>
        <div class="mb-3">
          <label for="email" class="block text-sm font-medium">Email</label>
          <input id="email" name="email" type="email" placeholder="you@example.com" class="form-input mt-1 block w-full" required>
          <p class="form-error mt-1 hidden" data-error-for="email"></p>
        </div>

        <div class="mb-3">
          <label for="password" class="block text-sm font-medium">Password</label>
          <input id="password" name="password" type="password" placeholder="Your password" class="form-input mt-1 block w-full" required>
          <p class="form-error mt-1 hidden" data-error-for="password"></p>
        </div>

        <div class="flex items-center justify-between mb-4">
          <label class="flex items-center text-sm">
            <a href="#" class="text-sm text-gray-700 hover:underline">Forgot password?</a>
          </label>
          <a href="register.php" class="text-sm text-blue-600 hover:underline">Register Here</a>
        </div>

        <div>
          <button id="submitBtn" type="submit" class="btn-primary w-full">Sign in</button>
        </div>

        <div id="formMessage" class="mt-3 text-sm"></div>
      </form>
    </div>
  </div>

  <script>
    // simple helper functions
    const showFieldError = (field, message) => {
      const el = document.querySelector('[data-error-for="' + field + '"]');
      if (el) {
        el.textContent = message;
        el.classList.remove('hidden');
      }
    };
    const clearFieldErrors = () => {
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

    document.getElementById('loginForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      clearFieldErrors();

      const email = document.getElementById('email').value.trim();
      const password = document.getElementById('password').value;
      let hasError = false;
      if (!email) {
        showFieldError('email', 'Email is required');
        hasError = true;
      }
      if (!password) {
        showFieldError('password', 'Password is required');
        hasError = true;
      }
      if (hasError) return;

      const submitBtn = document.getElementById('submitBtn');
      submitBtn.disabled = true;
      submitBtn.textContent = 'Signing in...';

      try {
        const res = await fetch('../backend/api/auth/login.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            email,
            password
          })
        });
        const data = await res.json();
        const msgEl = document.getElementById('formMessage');
        if (res.ok && data && data.success) {
          msgEl.textContent = data.message || 'Login successful';
          msgEl.className = 'mt-3 text-sm text-green-600';
          // If backend returns role, redirect to role-specific dashboard (frontend/{role}/dashboard.php)
          const role = (data.data && data.data.role) ? String(data.data.role).toLowerCase().replace(/[^a-z0-9_]/g, '') : 'customer';
          setTimeout(() => {
            // relative path from this page: customer/dashboard.php
            window.location.href = role + '/dashboard.php';
          }, 600);
        } else {
          msgEl.textContent = (data && data.message) ? data.message : 'Login failed';
          msgEl.className = 'mt-3 text-sm text-red-600';
        }
      } catch (err) {
        const msgEl = document.getElementById('formMessage');
        msgEl.textContent = 'Network or server error';
        msgEl.className = 'mt-3 text-sm text-red-600';
        console.error(err);
      } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Sign in';
      }
    });
  </script>
</body>

</html>
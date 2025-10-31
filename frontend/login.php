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
    <div class="w-full max-w-md bg-white rounded shadow p-6">
      <h1 class="text-2xl font-semibold mb-4">Sign in to your account</h1>
      <!-- PLACEHOLDER: Add login form that posts to backend/api/auth/login.php -->
      <form>
        <label class="block text-sm font-medium">Email</label>
        <input class="mt-1 block w-full border rounded px-3 py-2" placeholder="you@example.com">
        <label class="block text-sm font-medium mt-4">Password</label>
        <input type="password" class="mt-1 block w-full border rounded px-3 py-2" placeholder="Your password">
        <div class="mt-6">
          <button class="w-full bg-blue-600 text-white py-2 rounded">Login</button>
        </div>
      </form>
    </div>
  </div>
</body>

</html>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>aquaflow - Login</title>
    <link rel="stylesheet" href="../css/tailwind.css" />
    <link rel="stylesheet" href="../css/style.css" />
  </head>
  <body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
      <h2 class="text-2xl font-bold text-center text-gray-800 mb-8">Login</h2>
      <form id="login-form">
        <div class="mb-4">
          <label for="email" class="block text-gray-700">Email</label>
          <input
            type="email"
            id="email"
            name="email"
            class="w-full px-4 py-2 mt-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            required
          />
        </div>
        <div class="mb-4">
          <label for="password" class="block text-gray-700">Password</label>
          <input
            type="password"
            id="password"
            name="password"
            class="w-full px-4 py-2 mt-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            required
          />
        </div>
        <div class="flex items-center justify-between mb-6">
          <div class="flex items-center">
            <input
              type="checkbox"
              id="remember"
              name="remember"
              class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
            />
            <label for="remember" class="ml-2 block text-sm text-gray-900"
              >Remember me</label
            >
          </div>
          <a href="#" class="text-sm text-blue-500 hover:underline"
            >Forgot password?</a
          >
        </div>
        <button
          type="submit"
          class="w-full bg-blue-500 text-white rounded-full px-4 py-2 hover:bg-blue-600"
        >
          Login
        </button>
      </form>
      <p class="text-center text-gray-600 mt-4">
        Don't have an account?
        <a href="register.php" class="text-blue-500 hover:underline"
          >Register</a
        >
      </p>
    </div>

    <script src="js/auth.js"></script>
  </body>
</html>

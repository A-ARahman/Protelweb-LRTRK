<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 h-screen flex justify-center items-center">
    <div class="bg-blue-600 p-8 rounded-lg shadow-xl w-96">
        <h1 class="text-white text-2xl font-bold mb-6 text-center">LORETRACK</h1>
        <div class="space-y-4">
            <div>
                <label for="username" class="block text-white">Username</label>
                <input type="text" id="username" placeholder="Username" class="mt-1 p-2 w-full rounded-md">
            </div>
            <div>
                <label for="password" class="block text-white">Password</label>
                <input type="password" id="password" placeholder="Password" class="mt-1 p-2 w-full rounded-md">
            </div>
            <div class="flex justify-between items-center">
                <a href="./reset-password.php" class="text-red-200 hover:underline">Reset Password</a>
                <button class="bg-white text-blue-600 px-4 py-2 rounded hover:bg-gray-100">LOGIN</button>
            </div>
        </div>
    </div>
</body>
</html>

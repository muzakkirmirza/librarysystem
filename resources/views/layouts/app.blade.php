<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Library & Inventory</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans text-gray-900">

    <!-- Top Navbar -->
    <nav class="bg-indigo-600 text-white p-4 shadow-md flex justify-between items-center">
        <h1 class="text-xl font-bold">Campus Library System</h1>
        <div id="nav-links" class="space-x-4">
            <a href="/login" class="hover:underline">Login</a>
            <a href="/register" class="hover:underline">Register</a>
        </div>
    </nav>

    <!-- Main Content Container -->
    <main class="container mx-auto mt-8 p-4">
        @yield('content')
    </main>

    <script>
        // Check if token exists to show logged-in UI status
        const token = localStorage.getItem('auth_token');
        if (token) {
            document.getElementById('nav-links').innerHTML = `
                <a href="/dashboard" class="hover:underline">Dashboard</a>
                <button onclick="logout()" class="bg-red-500 hover:bg-red-600 px-3 py-1 rounded text-white">Logout</button>
            `;
        }

        async function logout() {
            const token = localStorage.getItem('auth_token');
            await fetch('/api/logout', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });
            localStorage.removeItem('auth_token');
            window.location.href = '/login';
        }
    </script>
</body>
</html>
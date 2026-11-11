@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-md mt-10">
    <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Login to System</h2>

    <div id="error-msg" class="hidden mb-4 p-3 bg-red-100 text-red-700 rounded-md text-sm"></div>

    <form id="loginForm">
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
            <input type="email" id="email" required class="w-full p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
            <input type="password" id="password" required class="w-full p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 rounded-md transition duration-200">
            Login
        </button>
    </form>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const errorDiv = document.getElementById('error-msg');
    errorDiv.classList.add('hidden');

    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    try {
        const response = await fetch('/api/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ email, password })
        });

        const data = await response.json();

        if (response.ok) {
            localStorage.setItem('auth_token', data.access_token);
            window.location.href = '/dashboard';
        } else {
            errorDiv.innerText = data.message || 'Login failed';
            errorDiv.classList.remove('hidden');
        }
    } catch (err) {
        errorDiv.innerText = 'Something went wrong. Please try again.';
        errorDiv.classList.remove('hidden');
    }
});
</script>
@endsection
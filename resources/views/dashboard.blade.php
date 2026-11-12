@extends('layouts.app')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-3xl font-bold mb-4 text-gray-800">Welcome to Campus Library System</h2>
    <p class="text-gray-600 mb-6"></p>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-indigo-50 p-6 rounded-lg border border-indigo-100 shadow-sm">
            <h3 class="text-xl font-bold text-indigo-700 mb-2">Inventory Items</h3>
            <p class="text-gray-600 text-sm mb-4">View and manage library assets and items.</p>
            <a href="/inventory" class="inline-block bg-indigo-600 text-white text-sm px-4 py-2 rounded hover:bg-indigo-700">Manage Items</a>
        </div>

        <div class="bg-green-50 p-6 rounded-lg border border-green-100 shadow-sm">
            <h3 class="text-xl font-bold text-green-700 mb-2">Borrow Requests</h3>
            <p class="text-gray-600 text-sm mb-4">Check student item requests and approvals.</p>
            <a href="#" class="inline-block bg-green-600 text-white text-sm px-4 py-2 rounded hover:bg-green-700">View Requests</a>
        </div>

        <div class="bg-amber-50 p-6 rounded-lg border border-amber-100 shadow-sm">
            <h3 class="text-xl font-bold text-amber-700 mb-2">Maintenance Logs</h3>
            <p class="text-gray-600 text-sm mb-4">Track item repair history and status.</p>
            <a href="#" class="inline-block bg-amber-600 text-white text-sm px-4 py-2 rounded hover:bg-amber-700">View Logs</a>
        </div>
    </div>
</div>

<script>
    // Protect route on client-side
    if (!localStorage.getItem('auth_token')) {
        window.location.href = '/login';
    }
</script>
@endsection
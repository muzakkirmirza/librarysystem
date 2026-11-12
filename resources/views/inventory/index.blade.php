@extends('layouts.app')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <!-- Header & Toggle Add Form Button -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Inventory Management</h2>
            <p class="text-sm text-gray-500">Campus library aur lab ke saare items manage karein</p>
        </div>
        <button onclick="toggleAddForm()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-4 py-2 rounded-md shadow">
            + Add New Item
        </button>
    </div>

    <!-- Alert Message -->
    <div id="alert-box" class="hidden mb-4 p-3 rounded-md text-sm"></div>

    <!-- Add New Item Form (Hidden by default) -->
    <div id="add-item-box" class="hidden mb-8 bg-gray-50 p-5 rounded-lg border border-gray-200">
        <h3 class="text-lg font-bold text-gray-700 mb-4">Add New Inventory Item</h3>
        <form id="addItemForm" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Item Name</label>
                <input type="text" id="item_name" required placeholder="e.g. HP LaserJet Printer"
                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select id="item_category" required class="w-full p-2 border rounded-md bg-white focus:ring-2 focus:ring-indigo-500">
                    <option value="">Loading categories...</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Condition</label>
                <select id="item_condition" required class="w-full p-2 border rounded-md bg-white focus:ring-2 focus:ring-indigo-500">
                    <option value="Good">Good</option>
                    <option value="Fair">Fair</option>
                    <option value="Needs Repair">Needs Repair</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-md">
                    Save Item
                </button>
                <button type="button" onclick="toggleAddForm()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 py-2 px-3 rounded-md">
                    Cancel
                </button>
            </div>
        </form>
    </div>

    <!-- Search & Filter Bar -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 bg-gray-50 p-4 rounded-lg border">
        <div class="md:col-span-2">
            <input type="text" id="search_input" oninput="fetchItems()" placeholder="Search item by name..."
                class="w-full p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <div>
            <select id="filter_category" onchange="fetchItems()" class="w-full p-2 border rounded-md bg-white">
                <option value="">All Categories</option>
            </select>
        </div>

        <div>
            <select id="filter_availability" onchange="fetchItems()" class="w-full p-2 border rounded-md bg-white">
                <option value="">All Status</option>
                <option value="true">Available Only</option>
                <option value="false">Issued / Unavailable</option>
            </select>
        </div>
    </div>

    <!-- Inventory Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 text-gray-700 text-sm uppercase border-b">
                    <th class="p-3">ID</th>
                    <th class="p-3">Item Name</th>
                    <th class="p-3">Category</th>
                    <th class="p-3">Condition</th>
                    <th class="p-3">Status</th>
                    <th class="p-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody id="items-table-body" class="divide-y divide-gray-200 text-sm">
                <tr>
                    <td colspan="6" class="p-6 text-center text-gray-500">Loading inventory items...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
    const authToken = localStorage.getItem('auth_token');
    if (!authToken) {
        window.location.href = '/login';
    }

    const headers = {
        'Authorization': 'Bearer ' + authToken,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    };

    function toggleAddForm() {
        document.getElementById('add-item-box').classList.toggle('hidden');
    }

    function showAlert(message, isSuccess = true) {
        const alertBox = document.getElementById('alert-box');
        alertBox.innerText = message;
        alertBox.className = `mb-4 p-3 rounded-md text-sm ${isSuccess ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`;
        alertBox.classList.remove('hidden');
        setTimeout(() => alertBox.classList.add('hidden'), 3500);
    }

    // 1. Load Categories for Dropdowns
    async function loadCategories() {
        try {
            const res = await fetch('/api/categories', { headers });
            const json = await res.json();
            const categories = json.data || json;

            const addSelect = document.getElementById('item_category');
            const filterSelect = document.getElementById('filter_category');

            addSelect.innerHTML = '<option value="">Select Category</option>';
            filterSelect.innerHTML = '<option value="">All Categories</option>';

            categories.forEach(cat => {
                addSelect.innerHTML += `<option value="${cat.id}">${cat.name}</option>`;
                filterSelect.innerHTML += `<option value="${cat.id}">${cat.name}</option>`;
            });
        } catch (err) {
            console.error('Error loading categories:', err);
        }
    }

    // 2. Fetch & Render Inventory Items (with Search & Filters)
    async function fetchItems() {
        const search = document.getElementById('search_input').value;
        const categoryId = document.getElementById('filter_category').value;
        const availability = document.getElementById('filter_availability').value;

        const params = new URLSearchParams();
        if (search) params.append('search', search);
        if (categoryId) params.append('category_id', categoryId);
        if (availability !== '') params.append('is_available', availability);

        try {
            const res = await fetch(`/api/inventory-items?${params.toString()}`, { headers });
            const json = await res.json();
            const items = json.data || json;

            const tbody = document.getElementById('items-table-body');
            if (items.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="p-6 text-center text-gray-500">No inventory items found.</td></tr>';
                return;
            }

            tbody.innerHTML = items.map(item => `
                <tr class="hover:bg-gray-50">
                    <td class="p-3 font-medium text-gray-600">#${item.id}</td>
                    <td class="p-3 font-semibold text-gray-800">${item.name}</td>
                    <td class="p-3 text-gray-600">${item.category ? item.category.name : 'N/A'}</td>
                    <td class="p-3">
                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">${item.condition}</span>
                    </td>
                    <td class="p-3">
                        ${item.is_available
                            ? '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Available</span>'
                            : '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Issued</span>'}
                    </td>
                    <td class="p-3 text-right">
                        <button onclick="deleteItem(${item.id})" class="text-red-600 hover:text-red-800 font-medium text-xs bg-red-50 hover:bg-red-100 px-3 py-1 rounded">
                            Delete
                        </button>
                    </td>
                </tr>
            `).join('');
        } catch (err) {
            console.error('Error fetching items:', err);
        }
    }

    // 3. Add New Item Submit
    document.getElementById('addItemForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const payload = {
            name: document.getElementById('item_name').value,
            category_id: document.getElementById('item_category').value,
            condition: document.getElementById('item_condition').value,
            is_available: true
        };

        try {
            const res = await fetch('/api/inventory-items', {
                method: 'POST',
                headers,
                body: JSON.stringify(payload)
            });

            if (res.ok) {
                document.getElementById('addItemForm').reset();
                toggleAddForm();
                showAlert('New item added successfully!');
                fetchItems();
            } else {
                const errData = await res.json();
                showAlert(errData.message || 'Failed to add item', false);
            }
        } catch (err) {
            showAlert('Network error while adding item.', false);
        }
    });

    // 4. Delete Item
    async function deleteItem(id) {
        if (!confirm('Kya aap waqai is item ko delete karna chahte hain?')) return;

        try {
            const res = await fetch(`/api/inventory-items/${id}`, {
                method: 'DELETE',
                headers
            });

            if (res.ok) {
                showAlert('Item deleted successfully!');
                fetchItems();
            } else {
                showAlert('Failed to delete item.', false);
            }
        } catch (err) {
            showAlert('Network error while deleting item.', false);
        }
    }

    // Initial Load
    loadCategories();
    fetchItems();
</script>
@endsection
@extends('layouts.app')

@section('content')
<div class="space-y-8">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                    Borrow Requests 
                    <span id="role-badge" class="ml-4 px-3 py-1 bg-gray-200 text-gray-800 rounded-full text-xs font-bold uppercase">Loading Role...</span>
                </h2>
                <p class="text-sm text-gray-500">Items borrow karne ki request bhejein aur pending requests approve/reject karein</p>
            </div>
            <button onclick="toggleRequestForm()" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded-md shadow">
                + New Borrow Request
            </button>
        </div>

        <div id="alert-box" class="hidden mb-4 p-3 rounded-md text-sm"></div>

        <div id="request-form-box" class="hidden mb-8 bg-gray-50 p-5 rounded-lg border border-gray-200">
            <h3 class="text-lg font-bold text-gray-700 mb-4">Request an Item</h3>
            <form id="borrowRequestForm" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Item</label>
                    <select id="inventory_item_id" required class="w-full p-2 border rounded-md bg-white focus:ring-2 focus:ring-green-500">
                        <option value="">Loading available items...</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                    <input type="date" id="due_date" required class="w-full p-2 border rounded-md focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes / Purpose</label>
                    <input type="text" id="notes" placeholder="e.g. Lab assignment" class="w-full p-2 border rounded-md focus:ring-2 focus:ring-green-500">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-md">Submit Request</button>
                    <button type="button" onclick="toggleRequestForm()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 py-2 px-3 rounded-md">Cancel</button>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-700 text-sm uppercase border-b">
                        <th class="p-3">ID</th>
                        <th class="p-3">Requested By</th>
                        <th class="p-3">Item</th>
                        <th class="p-3">Status</th>
                        <th class="p-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="requests-table-body" class="divide-y divide-gray-200 text-sm">
                    <tr><td colspan="5" class="p-6 text-center text-gray-500">Loading borrow requests...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="mb-4">
            <h2 class="text-xl font-bold text-gray-800">Loan Transactions (Issued & Returned)</h2>
            <p class="text-sm text-gray-500">Approved items ki issue aur return history track karein</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-700 text-sm uppercase border-b">
                        <th class="p-3">Loan ID</th>
                        <th class="p-3">Borrower</th>
                        <th class="p-3">Item</th>
                        <th class="p-3">Issue Date</th>
                        <th class="p-3">Due Date</th>
                        <th class="p-3">Status</th>
                        <th class="p-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody id="loans-table-body" class="divide-y divide-gray-200 text-sm">
                    <tr><td colspan="7" class="p-6 text-center text-gray-500">Loading loan transactions...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    const authToken = localStorage.getItem('auth_token');
    if (!authToken) window.location.href = '/login';

    const headers = {
        'Authorization': 'Bearer ' + authToken,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    };

    let userRole = 'student';

    const nextWeek = new Date();
    nextWeek.setDate(nextWeek.getDate() + 7);
    document.getElementById('due_date').value = nextWeek.toISOString().split('T')[0];

    function toggleRequestForm() {
        document.getElementById('request-form-box').classList.toggle('hidden');
    }

    function showAlert(message, isSuccess = true) {
        const alertBox = document.getElementById('alert-box');
        alertBox.innerText = message;
        alertBox.className = `mb-4 p-3 rounded-md text-sm ${isSuccess ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`;
        alertBox.classList.remove('hidden');
        setTimeout(() => alertBox.classList.add('hidden'), 4000);
    }

    async function checkUserRole() {
        try {
            const res = await fetch('/api/user', { headers });
            const data = await res.json();
            userRole = data.role || 'student';
            
            // Set Role Badge UI
            const badge = document.getElementById('role-badge');
            badge.innerText = userRole + ' View';
            badge.className = userRole === 'admin' 
                ? 'ml-4 px-3 py-1 bg-purple-200 text-purple-800 rounded-full text-xs font-bold uppercase' 
                : 'ml-4 px-3 py-1 bg-blue-200 text-blue-800 rounded-full text-xs font-bold uppercase';
        } catch (err) { console.error('Error fetching user role:', err); }
    }

    async function loadAvailableItems() {
        try {
            const res = await fetch('/api/inventory-items?is_available=true', { headers });
            const items = (await res.json()).data || await res.json();
            const select = document.getElementById('inventory_item_id');
            select.innerHTML = '<option value="">Select Available Item</option>';
            items.forEach(item => {
                select.innerHTML += `<option value="${item.id}">${item.name} (#${item.id})</option>`;
            });
        } catch (err) {}
    }

    async function fetchBorrowRequests() {
        try {
            const res = await fetch('/api/borrow-requests', { headers });
            const requests = (await res.json()).data || await res.json();
            const tbody = document.getElementById('requests-table-body');
            if (requests.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="p-6 text-center text-gray-500">No borrow requests found.</td></tr>';
                return;
            }

            tbody.innerHTML = requests.map(req => {
                const status = (req.status || 'pending').toLowerCase();
                const userName = req.user ? req.user.name : `User #${req.user_id || ''}`;
                const itemName = req.item ? req.item.name : (req.inventory_item ? req.inventory_item.name : `Item #${req.inventory_item_id}`);
                
                let badgeClass = status === 'approved' ? 'bg-green-100 text-green-800' : (status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800');

                let actionButtons = '';
                if (userRole === 'admin') {
                    if (status === 'pending') {
                        actionButtons = `
                            <button onclick="updateRequestStatus(${req.id}, 'approved')" class="bg-green-600 hover:bg-green-700 text-white text-xs px-3 py-1 rounded">Approve</button>
                            <button onclick="updateRequestStatus(${req.id}, 'rejected')" class="bg-red-500 hover:bg-red-600 text-white text-xs px-3 py-1 rounded">Reject</button>
                        `;
                    } else {
                        actionButtons = '<span class="text-xs text-gray-400">Processed</span>';
                    }
                } else {
                    actionButtons = status === 'pending' ? '<span class="text-xs text-amber-600 font-semibold">Waiting for Admin</span>' : '<span class="text-xs text-gray-400">Processed</span>';
                }

                return `
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 font-medium text-gray-600">#${req.id}</td>
                        <td class="p-3 text-gray-800">${userName}</td>
                        <td class="p-3 font-semibold text-gray-800">${itemName}</td>
                        <td class="p-3"><span class="px-2 py-1 text-xs font-semibold rounded-full capitalize ${badgeClass}">${status}</span></td>
                        <td class="p-3 text-right space-x-2">${actionButtons}</td>
                    </tr>
                `;
            }).join('');
        } catch (err) {}
    }

    async function fetchLoanTransactions() {
        try {
            const res = await fetch('/api/loan-transactions', { headers });
            const loans = (await res.json()).data || await res.json();
            const tbody = document.getElementById('loans-table-body');
            if (loans.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="p-6 text-center text-gray-500">No loan transactions yet.</td></tr>';
                return;
            }

            tbody.innerHTML = loans.map(loan => {
                const isReturned = (loan.status && loan.status.toLowerCase() === 'returned') || Boolean(loan.return_date);
                const borrower = loan.user ? loan.user.name : `User #${loan.user_id || ''}`;
                const itemName = loan.item ? loan.item.name : (loan.inventory_item ? loan.inventory_item.name : `Item #${loan.inventory_item_id || ''}`);
                
                let returnBtn = '';
                if (userRole === 'admin') {
                    returnBtn = !isReturned ? `<button onclick="markLoanReturned(${loan.id})" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs px-3 py-1 rounded">Mark Returned</button>` : '<span class="text-xs text-gray-400">Completed</span>';
                } else {
                    returnBtn = '<span class="text-xs text-gray-400">-</span>';
                }

                return `
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 font-medium text-gray-600">#${loan.id}</td>
                        <td class="p-3 text-gray-800">${borrower}</td>
                        <td class="p-3 font-semibold text-gray-800">${itemName}</td>
                        <td class="p-3 text-gray-600">${loan.issue_date || 'N/A'}</td>
                        <td class="p-3 text-gray-600">${loan.due_date || 'N/A'}</td>
                        <td class="p-3">
                            ${isReturned ? '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-200 text-gray-700">Returned</span>' : '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">Issued</span>'}
                        </td>
                        <td class="p-3 text-right">${returnBtn}</td>
                    </tr>
                `;
            }).join('');
        } catch (err) {}
    }

    document.getElementById('borrowRequestForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        try {
            const res = await fetch('/api/borrow-requests', {
                method: 'POST',
                headers,
                body: JSON.stringify({ inventory_item_id: document.getElementById('inventory_item_id').value })
            });
            if (res.ok) {
                document.getElementById('borrowRequestForm').reset();
                toggleRequestForm();
                showAlert('Borrow request submitted successfully!');
                fetchBorrowRequests();
                loadAvailableItems();
            } else {
                showAlert('Failed to submit borrow request.', false);
            }
        } catch (err) {}
    });

    async function updateRequestStatus(id, status) {
        try {
            const res = await fetch(`/api/borrow-requests/${id}`, { method: 'PUT', headers, body: JSON.stringify({ status }) });
            if (res.ok) {
                showAlert(`Request ${status} successfully!`);
                fetchBorrowRequests();
                fetchLoanTransactions();
                loadAvailableItems();
            } else {
                showAlert('Failed to update request status.', false);
            }
        } catch (err) {}
    }

    async function markLoanReturned(id) {
        if (!confirm('Kya aap is item ko returned mark karna chahte hain?')) return;
        try {
            const res = await fetch(`/api/loan-transactions/${id}`, {
                method: 'PUT', headers, body: JSON.stringify({ status: 'returned', return_date: new Date().toISOString().split('T')[0] })
            });
            if (res.ok) {
                showAlert('Item marked as returned!');
                fetchLoanTransactions();
                loadAvailableItems();
            } else {
                showAlert('Failed to mark loan as returned.', false);
            }
        } catch (err) {}
    }

    async function init() {
        await checkUserRole();
        loadAvailableItems();
        fetchBorrowRequests();
        fetchLoanTransactions();
    }
    
    init();
</script>
@endsection
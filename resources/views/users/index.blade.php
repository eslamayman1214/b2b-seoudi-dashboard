<x-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">User Management</h1>
            <div class="flex space-x-2">
                @if (Auth::user()->can('upload', App\Models\Product::class))
                    <form action="{{ route('invite.create') }}" method="GET">
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Invite a New User</button>
                    </form>
                @endif
                <form action="{{ route('password.edit') }}" method="GET">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Change My Password</button>
                </form>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-green-500 text-white px-4 py-2 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('users.index') }}" method="GET" class="mb-4">
            <div class="flex items-center space-x-4">
                <input type="text" name="search" placeholder="Search by name or email"
                    class="border px-4 py-2 rounded">
                <div>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="roles[]" value="admin" class="form-checkbox">
                        <span class="ml-2">Admin</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="roles[]" value="user" class="form-checkbox">
                        <span class="ml-2">User</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="roles[]" value="super admin" class="form-checkbox">
                        <span class="ml-2">Super Admin</span>
                    </label>
                </div>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Filter</button>
                <a href="/users" class="px-4 py-2 bg-gray-400 hover:bg-gray-500">reset filters</a>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="table-auto w-full">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b border-gray-200">Name</th>
                        <th class="py-2 px-4 border-b border-gray-200">Email</th>
                        <th class="py-2 px-4 border-b border-gray-200">Role</th>
                        @if (Auth::user()->can('upload', App\Models\Product::class))
                            <th class="py-2 px-4 border-b border-gray-200">Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody id="user-table-body">
                    @foreach ($users as $user)
                        <tr id="user-row-{{ $user->id }}">
                            <td class="border px-4 py-2">{{ $user->name }}</td>
                            <td class="border px-4 py-2">{{ $user->email }}</td>
                           
                                <td class="border px-4 py-2">
                                    <!-- Role Dropdown -->
                                    <select id="role_{{ $user->id }}" class="border px-2 py-1"
                                        {{ Auth::user()->role == 'user' ? 'disabled' : '' }}
                                        {{ $user->id == Auth::user()->id || (Auth::user()->role == 'admin' && $user->role == 'super admin') ? 'disabled' : '' }}>
                                        <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin
                                        </option>
                                        <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User
                                        </option>
                                        @if ($user->role == 'super admin')
                                            <option value="super admin" selected disabled>Super Admin</option>
                                        @endif
                                        @if (Auth::user()->role == 'super admin' && $user->role != 'super admin')
                                            <option value="super admin">Super Admin</option>
                                        @endif
                                    </select>
                                </td>
                           
                            <td class="border px-4 py-2">
                                @if (Auth::user()->role != 'user' && $user->role != 'super admin' && $user->id != Auth::user()->id)
                                    <button onclick="saveRole({{ $user->id }})"
                                        class="bg-blue-500 text-white px-4 py-2 rounded">Save</button>
                                    <button onclick="confirmDelete({{ $user->id }})"
                                        class="text-red-500 hover:text-red-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        <div class="mt-4">
            {{ $users->links() }}
        </div>

    </div>

    <!-- JavaScript for Handling Actions -->
    <script>
        function saveRole(userId) {
            var role = document.getElementById('role_' + userId).value;

            // Send AJAX request to update user role in the database
            fetch('/users/' + userId + '/role', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        role: role
                    })
                })
                .then(response => {
                    if (response.ok) {
                        alert('User role updated successfully.');
                        location.reload(); // Reload the page to instantly update the list
                    } else {
                        throw new Error('Failed to update user role.');
                    }
                })
                .catch(error => {
                    console.error(error);
                    alert('An error occurred while updating user role.');
                });
        }

        function confirmDelete(userId) {
            if (confirm('Are you sure you want to delete this user?')) {
                // Send AJAX request to delete user from the database
                fetch('/users/' + userId, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => {
                        if (response.ok) {
                            alert('User deleted successfully.');
                            document.getElementById('user-row-' + userId).remove(); // Remove the user from the UI
                        } else {
                            throw new Error('Failed to delete user.');
                        }
                    })
                    .catch(error => {
                        console.error(error);
                        alert('An error occurred while deleting user.');
                    });
            }
        }
    </script>

</x-layout>

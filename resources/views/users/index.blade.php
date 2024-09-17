<x-layout>
    @section('title', 'users')
    @section('content')
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

            <!-- Rest of the user management table and content -->

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

            <div class="mt-4">
                {{ $users->links() }}
            </div>

        </div>

        <script>
            function saveRole(userId) {
                var role = document.getElementById('role_' + userId).value;

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
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'User role updated successfully.',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            throw new Error('Failed to update user role.');
                        }
                    })
                    .catch(error => {
                        console.error(error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while updating the user role.',
                        });
                    });
            }

            function confirmDelete(userId) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'This action cannot be undone!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('/users/' + userId, {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            })
                            .then(response => {
                                if (response.ok) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Deleted!',
                                        text: 'User deleted successfully.',
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        document.getElementById('user-row-' + userId).remove();
                                    });
                                } else {
                                    throw new Error('Failed to delete user.');
                                }
                            })
                            .catch(error => {
                                console.error(error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'An error occurred while deleting the user.',
                                });
                            });
                    }
                });
            }
        </script>
    @endsection

</x-layout>

<x-layout>
    @section('title', 'Add New Ticket')
    @section('content')
        <div class="container mx-auto px-4 py-8">
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-6">Create New Ticket</h2>

                <!-- SweetAlert2 Notifications -->
                @if (session('success'))
                    <script>
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: '{{ session('success') }}',
                            confirmButtonText: 'OK'
                        });
                    </script>
                @elseif (session('error'))
                    <script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: '{{ session('error') }}',
                            confirmButtonText: 'OK'
                        });
                    </script>
                @endif

                <!-- Ticket Creation Form -->
                <form action="{{ route('tickets.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <!-- Description -->
                    <div class="mb-4">
                        <label for="description" class="block text-gray-700">Description</label>
                        <textarea id="description" name="description" rows="4" class="w-full px-3 py-2 border rounded-md" required>{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Section -->
                    <div class="mb-4">
                        <label for="section" class="block text-gray-700">Section</label>
                        <input type="text" id="section" name="section" class="w-full px-3 py-2 border rounded-md"
                            value="{{ old('section') }}" required>
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label for="email" class="block text-gray-700">Email</label>
                        <input type="email" id="email" name="email" class="w-full px-3 py-2 border rounded-md"
                            value="{{ old('email') }}" required>
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Attachment (Optional) -->
                    <div class="mb-4">
                        <label for="attachment" class="block text-gray-700">Attachment (Optional)</label>
                        <input type="file" id="attachment" name="attachment" class="w-full px-3 py-2 border rounded-md">
                    </div>

                    <!-- Status (Dropdown) -->
                    <div class="mb-4">
                        <label for="status" class="block text-gray-700">Status</label>
                        <select id="status" name="status" class="w-full px-3 py-2 border rounded-md">
                            <option value="pending" selected>Pending</option>
                            <option value="in progress">In Progress</option>
                            <option value="resolved">Resolved</option>
                            <option value="re-open">Re-open</option>
                        </select>
                    </div>

                    <!-- Assignee -->
                    <div class="mb-4">
                        <label for="assigned" class="block text-gray-700">Assignee</label>
                        <select id="assigned" name="assigned" class="w-full px-3 py-2 border rounded-md">
                            <option value="">Not Assigned</option>
                            @foreach ($users as $id => $name)
                                <option value="{{ $id }}" {{ old('assigned') == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                        @error('assigned')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="mb-4">
                        <button type="submit"
                            class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-md">Create Ticket</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- SweetAlert2 JavaScript -->
        <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @endsection
</x-layout>

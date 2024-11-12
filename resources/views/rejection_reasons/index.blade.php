<x-layout>
    @section('title', 'Rejection Reasons')
    @section('content')
        <div class="container mx-auto px-4 py-8">
            <h1 class="text-2xl font-bold mb-6">Rejection Reasons</h1>

            <!-- Display Success or Error Messages -->
            @if (session('success'))
                <div
                    class="{{ session('success') == 'Rejection reason deleted successfully.' ? 'text-red-500 border border-red-500' : 'bg-green-500 text-white' }} px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-500 text-white px-4 py-3 rounded mb-4">
                    Add Rejection Reason Failed
                </div>
            @endif

            <!-- Add New Reason Button -->
            <button onclick="openAddModal()" class="bg-blue-500 text-white px-4 py-2 rounded-lg mb-6">
                Add New Reason
            </button>

            <!-- Rejection Reasons Table -->
            <div class="overflow-x-auto shadow rounded-lg border border-gray-300">
                <table class="table-auto w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="py-3 px-4 border border-gray-300">ID</th>
                            <th class="py-3 px-4 border border-gray-300">Label</th>
                            <th class="py-3 px-4 border border-gray-300">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rejectionReasons as $reason)
                            <tr>
                                <td class="border px-4 py-2">{{ $reason['value'] }}</td>
                                <td class="border px-4 py-2">{{ $reason['label'] }}</td>
                                <td class="border px-4 py-2">
                                    <button type="button" onclick="confirmDeletion({{ $reason['value'] }})"
                                        class="text-red-500 hover:text-red-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                    <form id="deleteForm-{{ $reason['value'] }}"
                                        action="{{ route('rejection_reasons.destroy', $reason['value']) }}" method="POST"
                                        style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Add Rejection Reason Modal -->
            <div id="addModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden">
                <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
                    <h2 class="text-lg font-bold mb-4">Add New Rejection Reason</h2>
                    <form action="{{ route('rejection_reasons.store') }}" method="POST" id="addForm">
                        @csrf
                        <label for="optionLabel" class="block text-sm font-semibold mb-2">Reason Label:</label>
                        <input type="text" name="optionLabel" id="optionLabel" required
                            class="w-full p-2 border border-gray-300 rounded-md mb-4">

                        <div class="flex justify-end">
                            <button type="button" onclick="closeAddModal()"
                                class="bg-gray-500 text-white px-4 py-2 rounded-lg mr-2">Cancel</button>
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg">Add</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            function openAddModal() {
                document.getElementById('addModal').classList.remove('hidden');
                document.getElementById('optionLabel').value = '';
            }

            function closeAddModal() {
                document.getElementById('addModal').classList.add('hidden');
            }

            // Confirm deletion with SweetAlert2
            function confirmDeletion(reasonId) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This action will delete the rejection reason.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Submit the form associated with the specific reason ID
                        document.getElementById(`deleteForm-${reasonId}`).submit();
                    }
                });
            }
        </script>
    @endsection
</x-layout>

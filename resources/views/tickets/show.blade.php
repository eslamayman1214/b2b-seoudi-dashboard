<x-layout>
    @section('title', 'Ticket Details')
    @section('content')
        <div class="container mx-auto px-4 py-8">
            <h1 class="text-xl font-bold mb-4">Ticket Details</h1>

            <!-- Display success or error message -->
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <form id="ticket-form" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Ticket ID (read-only) -->
                <div class="mb-4">
                    <label for="ticket_id" class="block text-gray-700">Ticket ID</label>
                    <input type="text" id="ticket_id" name="ticket_id" class="w-full px-3 py-2 border rounded-md"
                        value="{{ $ticket->id }}" readonly>
                </div>

                <!-- Description -->
                <div class="mb-4">
                    <label for="description" class="block text-gray-700">Description</label>
                    <textarea id="description" name="description" class="w-full px-3 py-2 border rounded-md"
                        placeholder="Enter ticket description" readonly>{{ old('description', $ticket->description) }}</textarea>
                </div>

                <!-- Section -->
                <div class="mb-4">
                    <label for="section" class="block text-gray-700">Section</label>
                    <input type="text" id="section" name="section" class="w-full px-3 py-2 border rounded-md"
                        value="{{ old('section', $ticket->section) }}" readonly>
                </div>

                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="block text-gray-700">Email</label>
                    <input type="email" id="email" name="email" class="w-full px-3 py-2 border rounded-md"
                        value="{{ old('email', $ticket->email) }}" readonly>
                </div>

                <!-- Status -->
                <div class="mb-4">
                    <label for="status" class="block text-gray-700">Status</label>
                    <select id="status" name="status" class="w-full px-3 py-2 border rounded-md">
                        <option value="pending" {{ $ticket->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in progress" {{ $ticket->status == 'in progress' ? 'selected' : '' }}>In Progress
                        </option>
                        <option value="resolved" {{ $ticket->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                    </select>
                </div>

                <!-- Assignee -->
                <div class="mb-4">
                    <label for="assigned" class="block text-gray-700">Assignee</label>
                    <select id="assigned" name="assigned" class="w-full px-3 py-2 border rounded-md"
                        @if (Auth::user()->role == 'user') disabled @endif>
                        <option value="">Not Assigned</option>
                        @foreach ($users as $id => $name)
                            <option value="{{ $id }}" {{ $ticket->assigned == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                    @if (Auth::user()->role == 'user')
                        <!-- Include hidden input to retain the current assigned value -->
                        <input type="hidden" name="assigned" value="{{ $ticket->assigned }}">
                    @endif
                </div>

                <!-- Attachments -->
                <div class="mb-4">
                    <label class="block text-gray-700">Attachments</label>
                    @if ($ticket->attachment)
                        <!-- Show existing attachment and download link -->
                        <a href="{{ route('tickets.downloadAttachment', $ticket->id) }}" class="text-blue-600">
                            Download Attachment ({{ basename($ticket->attachment) }})
                        </a>
                    @else
                        <p class="text-gray-600">No attachment provided.</p>
                    @endif
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-start gap-2 mt-4">
                    <!-- Update Button -->
                    <button id="update-button" type="button" onclick="updateTicket({{ $ticket->id }})"
                        class="px-4 py-2 bg-blue-500 text-white rounded-md cursor-not-allowed opacity-50" disabled>
                        @if (Auth::user()->role == 'user')
                            Update Status
                        @else
                            Update Ticket
                        @endif
                    </button>

                    <!-- Back Button -->
                    <a href="{{ route('tickets.index') }}"
                        class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md">Back</a>
                </div>
            </form>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const updateButton = document.getElementById('update-button');
                const initialStatus = '{{ $ticket->status }}';
                const initialAssigned = '{{ $ticket->assigned }}';

                // Add event listeners for status and assigned fields
                document.getElementById('status').addEventListener('change', checkForChanges);
                document.getElementById('assigned').addEventListener('change', checkForChanges);

                // Function to check if any changes were made to the fields
                function checkForChanges() {
                    const currentStatus = document.getElementById('status').value;
                    const currentAssigned = document.getElementById('assigned').value;

                    if (currentStatus !== initialStatus || currentAssigned !== initialAssigned) {
                        // Enable the button if there are changes
                        updateButton.disabled = false;
                        updateButton.classList.remove('opacity-50', 'cursor-not-allowed');
                        updateButton.classList.add('hover:bg-blue-600');
                    } else {
                        // Keep the button disabled if there are no changes
                        updateButton.disabled = true;
                        updateButton.classList.add('opacity-50', 'cursor-not-allowed');
                        updateButton.classList.remove('hover:bg-blue-600');
                    }
                }
            });

            function updateTicket(ticketId) {
                var formData = new FormData(document.getElementById('ticket-form'));

                fetch(`/tickets/${ticketId}`, {
                        method: 'POST', // Simulating PUT
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok.');
                        }
                        return response.json(); // Ensure response is JSON
                    })
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Ticket updated successfully',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                location.reload(); // Reload the page to see updated data
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Error updating ticket: ' + data.message,
                                icon: 'error',
                                confirmButtonText: 'Try Again'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Network Error!',
                            text: 'There was an issue with the network. Please try again later.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    });
            }
        </script>

    @endsection
</x-layout>

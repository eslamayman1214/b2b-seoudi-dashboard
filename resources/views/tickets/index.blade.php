<x-layout>
    @section('title', 'Tickets')
    @section('content')
        <div class="container mx-auto px-4 py-8">
            <!-- Filter and Search Bar -->
            <form action="{{ route('tickets.index') }}" method="GET" class="mb-4">
                <div class="flex items-center justify-between space-x-4 bg-gray-100 p-4 rounded-md shadow-sm">
                    <div class="flex-1">
                        <select name="status"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Filter by Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in progress" {{ request('status') == 'in progress' ? 'selected' : '' }}>In
                                Progress</option>
                            <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved
                            </option>
                        </select>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button type="submit"
                            class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md shadow-md focus:ring-2 focus:ring-blue-500">
                            Filter
                        </button>
                        <a href="{{ route('tickets.index') }}"
                            class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-black rounded-md shadow-md">
                            Reset
                        </a>
                        @if (Auth::user()->role != 'user')
                            <!-- Add New Ticket Button -->
                            <a href="{{ route('tickets.create') }}"
                                class="px-6 py-2 bg-green-500 hover:bg-green-600 text-white rounded-md shadow-md flex items-center">
                                <i class="fas fa-plus mr-2"></i> Add Ticket
                            </a>
                        @endif
                    </div>
                </div>
            </form>

            @if ($tickets->isEmpty())
                <p class="text-gray-600">No tickets available.</p>
            @else
                <!-- Tickets Table -->
                <div class="overflow-x-auto">
                    <table class="table-auto w-full">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 border-b border-gray-200">Ticket ID</th>
                                <th class="py-2 px-4 border-b border-gray-200">Department</th>
                                <th class="py-2 px-4 border-b border-gray-200">Email</th>
                                <th class="py-2 px-4 border-b border-gray-200">Status</th>
                                <th class="py-2 px-4 border-b border-gray-200">Assignee</th>
                                <th class="py-2 px-4 border-b border-gray-200">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tickets as $ticket)
                                <tr>
                                    <td class="border px-4 py-2">{{ $ticket->id }}</td>
                                    <td class="border px-4 py-2">{{ $ticket->department }}</td>
                                    <td class="border px-4 py-2">{{ $ticket->email }}</td>
                                    <td class="border px-4 py-2">
                                        <select class="border rounded-md" name="status" id="status_{{ $ticket->id }}">
                                            <option value="pending" {{ $ticket->status == 'pending' ? 'selected' : '' }}>
                                                Pending</option>
                                            <option value="in progress"
                                                {{ $ticket->status == 'in progress' ? 'selected' : '' }}>In Progress
                                            </option>
                                            <option value="resolved" {{ $ticket->status == 'resolved' ? 'selected' : '' }}>
                                                Resolved</option>
                                        </select>
                                    </td>
                                    <td class="border px-4 py-2">
                                        <select class="border rounded-md" name="assigned" id="assigned_{{ $ticket->id }}"
                                            @if (Auth::user()->role != 'super admin' && Auth::user()->role != 'admin') disabled @endif>
                                            <option value="">Not Assigned</option>
                                            @foreach ($users as $id => $name)
                                                <option value="{{ $id }}"
                                                    {{ $ticket->assigned == $id ? 'selected' : '' }}>
                                                    {{ $name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="border px-4 py-2">
                                        <!-- Action Buttons -->
                                        <div class="flex items-center space-x-2 border">
                                            <!-- Save Button -->
                                            <button onclick="saveTicket({{ $ticket->id }})"
                                                class="text-green-500 hover:text-green-700 border-r pr-2">
                                                <i class="fas fa-save"></i> Save
                                            </button>
                                            <!-- View Icon -->
                                            <a href="{{ route('tickets.show', $ticket->id) }}"
                                                class="text-blue-500 hover:text-blue-700 border-r pr-2">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if (Auth::user()->role == 'super admin')
                                                <!-- Delete Icon -->
                                                <button onclick="confirmDelete({{ $ticket->id }})"
                                                    class="text-red-500 hover:text-red-700 pl-2">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Links and Items Per Page Dropdown -->
                <div class="flex items-center justify-between mt-4">
                    <div class="flex items-center">
                        <label for="per_page" class="mr-2">Items per page:</label>
                        <form action="{{ route('tickets.index') }}" method="GET">
                            <select name="per_page" id="per_page" onchange="this.form.submit()"
                                class="px-4 py-2 border border-gray-300 rounded-md outline-none">
                                <option value="25"{{ request('per_page') == 25 ? ' selected' : '' }}>25</option>
                                <option value="50"{{ request('per_page') == 50 ? ' selected' : '' }}>50</option>
                                <option value="100"{{ request('per_page') == 100 ? ' selected' : '' }}>100</option>
                            </select>
                            @foreach (request()->except('per_page') as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                        </form>
                    </div>
                    <div>
                        {{ $tickets->appends(['per_page' => $perPage])->links() }}
                    </div>
                </div>
            @endif
        </div>

        <!-- Include FontAwesome for Icons -->
        <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

        <script>
            function saveTicket(ticketId) {
                var status = document.getElementById('status_' + ticketId).value;
                var assigned = document.getElementById('assigned_' + ticketId).value;

                fetch(`/tickets/update/${ticketId}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            status: status,
                            assigned: assigned
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Ticket updated successfully');
                        } else {
                            alert('Error updating ticket');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }

            function confirmDelete(ticketId) {
                if (confirm('Are you sure you want to delete this ticket?')) {
                    fetch(`/tickets/${ticketId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Ticket deleted successfully');
                                location.reload();
                            } else {
                                alert('Error deleting ticket');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                }
            }
        </script>
    @endsection
</x-layout>

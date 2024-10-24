<x-layout>
    @section('title', 'Ticket SLA')
    @section('content')
        <div class="container mx-auto px-4 py-8">
            <!-- Filter and Search Bar -->
            <form action="{{ route('tickets.sla') }}" method="GET" class="mb-4">
                <div class="flex items-center justify-between space-x-4 bg-gray-100 p-4 rounded-md shadow-sm">
                    <!-- Assignee Filter -->
                    <div class="flex-1">
                        <select name="user_name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Assignees</option>
                            @foreach ($users as $user)
                                <option value="{{ $user }}" {{ request('user_name') == $user ? 'selected' : '' }}>
                                    {{ $user }}
                                </option>
                            @endforeach
                            <option value="unassigned" {{ request('user_name') == 'unassigned' ? 'selected' : '' }}>
                                Not assigned
                            </option>
                        </select>
                    </div>

                    <!-- SLA Status Filter -->
                    <div class="flex-1">
                        <select name="sla_status"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All SLA Status</option>
                            <option value="in_sla" {{ request('sla_status') == 'in_sla' ? 'selected' : '' }}>In SLA</option>
                            <option value="out_sla" {{ request('sla_status') == 'out_sla' ? 'selected' : '' }}>Out of SLA
                            </option>
                        </select>
                    </div>

                    <!-- Reopened Tickets Checkbox -->
                    <div class="flex items-center space-x-2">
                        <input type="checkbox" name="show_reopened" id="show_reopened" value="1"
                            {{ request('show_reopened') ? 'checked' : '' }}
                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <label for="show_reopened" class="text-sm text-gray-700">
                            <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Show Reopened Only
                        </label>
                    </div>

                    <div class="flex items-center space-x-2">
                        <button type="submit"
                            class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md shadow-md focus:ring-2 focus:ring-blue-500">
                            Filter
                        </button>
                        <a href="{{ route('tickets.sla') }}"
                            class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-black rounded-md shadow-md">
                            Reset
                        </a>
                    </div>
                </div>
            </form>

            @if ($slaTickets->isEmpty())
                <p class="text-gray-600">No SLA tickets available.</p>
            @else
                <!-- SLA Tickets Table -->
                <div class="overflow-x-auto">
                    <table class="table-auto w-full">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 border-b border-gray-200">ID</th>
                                <th class="py-2 px-4 border-b border-gray-200">Ticket ID</th>
                                <th class="py-2 px-4 border-b border-gray-200">Assignee</th>
                                <th class="py-2 px-4 border-b border-gray-200">Created At</th>
                                <th class="py-2 px-4 border-b border-gray-200">Resolved At</th>
                                <th class="py-2 px-4 border-b border-gray-200">SLA Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($slaTickets as $ticket)
                                <tr class="{{ in_array($ticket->ticket_id, $repeatedTicketIds) ? 'bg-green-100' : '' }}">
                                    <td class="border px-4 py-2">{{ $ticket->id }}</td>
                                    <td class="border px-4 py-2">
                                        <a href="{{ route('tickets.show', $ticket->ticket_id) }}"
                                            class="text-blue-600 hover:text-blue-800 underline">
                                            {{ $ticket->ticket_id }}
                                        </a>
                                    </td>
                                    <td class="border px-4 py-2">{{ $ticket->user_name ?: 'Not assigned' }}</td>
                                    <td class="border px-4 py-2">
                                        {{ $ticket->assigned_date ?? $ticket->ticket_created_date }}
                                    </td>
                                    <td class="border px-4 py-2">{{ $ticket->resolved_date ?: 'Not Solved' }}</td>
                                    <!-- SLA Status: Show 'N/A' if user_name is null -->
                                    <td class="border px-4 py-2">
                                        @if (is_null($ticket->user_name) || is_null($ticket->resolved_date))
                                            <span class="text-gray-500">N/A</span>
                                        @else
                                            @if ($ticket->sla_status == 'in_sla')
                                                <span class="text-green-500">In SLA</span>
                                            @else
                                                <span class="text-red-500">Out of SLA</span>
                                            @endif
                                        @endif
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
                        <form action="{{ route('tickets.sla') }}" method="GET">
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

                    <!-- Pagination Links -->
                    <div>
                        {{ $slaTickets->links() }}
                    </div>
                </div>
            @endif
        </div>
    @endsection
</x-layout>

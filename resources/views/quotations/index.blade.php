<x-layout>
    @section('title', 'Quotations')
    @section('content')
        <div class="container mx-auto px-4 py-8">

            <!-- Filter Form -->
            <form method="GET" action="{{ route('quotations.index') }}" class="flex mb-4 items-center">
                <label for="status" class="mr-2">Filter by Status:</label>
                <select name="status" id="status" class="px-3 py-2 border rounded mr-4 w-64">
                    <option value="">All</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
                <button type="submit"
                    class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded mr-2">Filter</button>
                <a href="{{ route('quotations.index') }}"
                    class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded">Reset</a>
            </form>
            <!-- Quotation Table -->
            @if ($quotations->isEmpty())
                <p>No quotations available.</p>
            @else
                <table class="table-auto w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="py-2 px-4 border">ID</th>
                            <th class="py-2 px-4 border">Name</th>
                            <th class="py-2 px-4 border">Email</th>
                            <th class="py-2 px-4 border">Phone</th>
                            <th class="py-2 px-4 border">Company</th>
                            <th class="py-2 px-4 border">Description</th>
                            <th class="py-2 px-4 border">Quantity</th>
                            <th class="py-2 px-4 border">Desired delivery date</th>
                            <th class="py-2 px-4 border">Last Status</th>
                            <th class="py-2 px-4 border">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($quotations as $quotation)
                            <tr>
                                <td class="border px-4 py-2">{{ $quotation->id }}</td>
                                <td class="border px-4 py-2">{{ $quotation->name }}</td>
                                <td class="border px-4 py-2">{{ $quotation->email }}</td>
                                <td class="border px-4 py-2">{{ $quotation->phone ?? 'N/A' }}</td>
                                <td class="border px-4 py-2">{{ $quotation->company_name }}</td>
                                <td class="border px-4 py-2 whitespace-pre-line break-words">{{ $quotation->description }}
                                </td>
                                <td class="border px-4 py-2">{{ $quotation->quantity_required }}</td>
                                <td class="border px-4 py-2">{{ $quotation->desired_delivery_date ?? 'N/A' }}</td>
                                <td
                                    class="border px-4 py-2 {{ $quotation->last_status == 'pending' ? 'bg-gray-200 text-gray-800' : ($quotation->last_status == 'rejected' ? 'bg-red-200 text-red-800' : 'bg-green-200 text-green-800') }}">
                                    {{ ucfirst($quotation->last_status) ?? 'Pending' }}
                                </td>
                                <td class="border px-4 py-2">
                                    <a href="{{ route('quotations.reply', $quotation->id) }}"
                                        class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md">
                                        <i class="fas fa-edit"></i> <!-- Font Awesome reply icon -->
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <!-- Items per Page Selection -->
                <div class="flex mb-4 items-center">
                    <label for="perPage" class="mr-2">Items per page:</label>
                    <select name="perPage" id="perPage" class="px-3 py-2 border rounded mr-4"
                        onchange="window.location.href='?perPage=' + this.value + '&status={{ request('status') }}'">
                        <option value="25" {{ request('perPage') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('perPage') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('perPage') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
                <!-- Pagination Links -->
                <div class="mt-4 flex justify-center">
                    {{ $quotations->appends(['status' => request('status'), 'perPage' => request('perPage')])->links() }}
                </div>
            @endif
        </div>
    @endsection
</x-layout>

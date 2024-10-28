<x-layout>
    @section('title', 'Customer List')
    @section('content')
        <div class="container mx-auto px-4 py-8">
            <h1 class="text-2xl font-bold mb-4">Customers List</h1>

            <!-- Customers Table -->
            <div class="overflow-x-auto">
                <table class="table-auto w-full border-collapse border border-gray-300">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border border-gray-300">ID</th>
                            <th class="py-2 px-4 border border-gray-300">Name</th>
                            <th class="py-2 px-4 border border-gray-300">Email</th>
                            <th class="py-2 px-4 border border-gray-300">Phone Number</th>
                            <th class="py-2 px-4 border border-gray-300">WhatsApp Number</th>
                            <th class="py-2 px-4 border border-gray-300">Group</th>
                            <th class="py-2 px-4 border border-gray-300">Document</th>
                            <th class="py-2 px-4 border border-gray-300">Document Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customers as $customer)
                            <tr>
                                <td class="border px-4 py-2">{{ $customer['id'] ?? '' }}</td>
                                <td class="border px-4 py-2">{{ $customer['firstname'] ?? '' }}
                                    {{ $customer['lastname'] ?? '' }}</td>
                                <td class="border px-4 py-2">{{ $customer['email'] ?? '' }}</td>
                                <td class="border px-4 py-2">
                                    @php
                                        $phoneNumber =
                                            collect($customer['custom_attributes'])->firstWhere(
                                                'attribute_code',
                                                'phone_number',
                                            )['value'] ?? '';
                                    @endphp
                                    {{ $phoneNumber }}
                                </td>
                                <td class="border px-4 py-2">{{ $customer['whatsapp_number'] ?? '' }}</td>
                                <td class="border px-4 py-2">{{ $customer['group_code'] }}</td>
                                <td class="border px-4 py-2"> ' '</td>
                                <td class="border px-4 py-2">
                                    @php
                                        // Define a mapping of document status codes to human-readable descriptions
                                        $statusMapping = [
                                            '212' => 'Pending',
                                            '213' => 'Accepted',
                                            '214' => 'Rejected',
                                        ];

                                        // Find the document status value in custom_attributes
                                        $documentStatusCode =
                                            collect($customer['custom_attributes'])->firstWhere(
                                                'attribute_code',
                                                'document_status',
                                            )['value'] ?? '';

                                        // Map the code to its description, or show the code if not found in the mapping
                                        $documentStatus = $statusMapping[$documentStatusCode] ?? $documentStatusCode;

                                    @endphp
                                    {{ $documentStatus }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endsection
</x-layout>

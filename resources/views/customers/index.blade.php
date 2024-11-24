<x-layout>
    @section('title', 'Customer List')
    @section('content')
        <div class="container mx-auto px-4 py-8">
            <h1 class="text-2xl font-bold mb-6">Customers List</h1>

            <!-- Filters -->
            <div class="mb-4 flex items-center justify-between bg-gray-100 p-4 rounded-lg shadow-md">
                <form method="GET" action="{{ route('customers.index') }}"
                    class="mb-4 flex items-center bg-gray-100 p-4 rounded-lg shadow-md">
                    <label for="document_status" class="mr-4 font-semibold text-lg">Document Status:</label>
                    <select name="document_status" id="document_status"
                        class="mr-6 p-2 rounded-md border border-gray-300 text-lg">
                        <option value="">All</option>
                        @foreach ($statusOptions['statusOptions'] as $option)
                            <option value="{{ $option['value'] }}"
                                {{ request('document_status') == $option['value'] ? 'selected' : '' }}>
                                {{ $option['label'] }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="bg-blue-500 text-white px-5 py-2 rounded-lg text-lg mr-4">Filter</button>
                    <a href="{{ route('customers.index') }}"
                        class="bg-gray-500 text-white px-5 py-2 rounded-lg text-lg">Reset</a>
                </form>
                <!-- Rejection Reasons Button -->
                <a href="{{ route('rejection-reasons.index') }}"
                    class="bg-blue-500 text-white px-5 py-2 rounded-lg text-lg">
                    Rejection Reasons
                </a>
            </div>

            <!-- Customers Table -->
            <div class="overflow-x-auto shadow rounded-lg border border-gray-300">
                <table class="table-auto w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="py-3 px-4 border border-gray-300">ID</th>
                            <th class="py-3 px-4 border border-gray-300">Name</th>
                            <th class="py-3 px-4 border border-gray-300">Email</th>
                            <th class="py-3 px-4 border border-gray-300">Phone Number</th>
                            <th class="py-3 px-4 border border-gray-300">Group</th>
                            <th class="py-3 px-4 border border-gray-300">Document</th>
                            <th class="py-3 px-4 border border-gray-300">Document Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customersPaginated as $customer)
                            <tr>
                                <td class="border px-4 py-2">{{ $customer['id'] ?? '' }}</td>
                                <td class="border px-4 py-2">{{ $customer['firstname'] ?? '' }}
                                    {{ $customer['lastname'] ?? '' }}</td>
                                <td class="border px-4 py-2">{{ $customer['email'] ?? '' }}</td>
                                <td class="border px-4 py-2">
                                    {{ collect($customer['custom_attributes'])->firstWhere('attribute_code', 'phone_number')['value'] ?? '' }}
                                </td>
                                <td class="border px-4 py-2">{{ $customer['group_code'] }}</td>
                                <td class="border px-4 py-2">
                                    <a href="{{ route('customers.downloadDocument', $customer['id']) }}"
                                        class="text-blue-500" title="Download Document">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 inline-block"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path
                                                d="M12 5a1 1 0 00-1-1H9a1 1 0 00-1 1v5H5.586l4.707 4.707a1 1 0 001.414 0L16.414 10H13V5zM3 15a1 1 0 011-1h12a1 1 0 011 1v1a1 1 0 01-1 1H4a1 1 0 01-1-1v-1z" />
                                        </svg>
                                    </a>
                                </td>
                                <td class="border px-4 py-2">
                                    <div class="flex items-center">
                                        <select name="document_status" id="document_status_{{ $customer['id'] }}"
                                            class="p-2 rounded-md border border-gray-300 text-lg"
                                            data-original-value="{{ collect($customer['custom_attributes'])->firstWhere('attribute_code', 'document_status')['value'] ?? '' }}"
                                            onchange="enableSaveButton({{ $customer['id'] }})">
                                            @foreach ($statusOptions['statusOptions'] as $option)
                                                <option value="{{ $option['value'] }}"
                                                    {{ collect($customer['custom_attributes'])->firstWhere('attribute_code', 'document_status')['value'] == $option['value'] ? 'selected' : '' }}>
                                                    {{ $option['label'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <!-- Save Icon -->
                                        <button id="save_icon_{{ $customer['id'] }}"
                                            onclick="updateDocumentStatus({{ $customer['id'] }})"
                                            class="ml-2 text-gray-400 cursor-not-allowed bg-gray-200 rounded-lg px-4 py-2 transition-all duration-300 ease-in-out transform hover:scale-105 hover:shadow-md"
                                            title="Save Status" disabled>
                                            Save
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Pagination Control at the Bottom -->
            <div class="mt-6 flex items-center justify-between">
                <!-- Items per Page -->
                <div class="flex items-center">
                    <label for="perPage" class="mr-4 font-semibold text-lg">Items per page:</label>
                    <select name="perPage" id="perPage" class="p-2 rounded-md border border-gray-300 text-lg"
                        onchange="changePerPage()">
                        <option value="25" {{ request('perPage') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('perPage') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('perPage') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>

                <!-- Pagination Links -->
                <div>
                    {{ $customersPaginated->appends(request()->except('page'))->links() }}
                </div>
            </div>
        </div>

        <script>
            function changePerPage() {
                const perPage = document.getElementById('perPage').value;
                const url = new URL(window.location.href);
                url.searchParams.set('perPage', perPage);
                url.searchParams.delete('page'); // Reset to first page
                window.location.href = url.href;
            }

            // Enable Save button only if the value is different from the original
            function enableSaveButton(customerId) {
                const selectElem = document.getElementById(`document_status_${customerId}`);
                const saveButton = document.getElementById(`save_icon_${customerId}`);
                const originalValue = selectElem.getAttribute('data-original-value');
                const currentValue = selectElem.value;

                if (currentValue !== originalValue) {
                    saveButton.classList.remove('text-gray-400', 'cursor-not-allowed');
                    saveButton.classList.add('text-blue-600', 'cursor-pointer');
                    saveButton.disabled = false;
                } else {
                    saveButton.classList.add('text-gray-400', 'cursor-not-allowed');
                    saveButton.classList.remove('text-blue-600', 'cursor-pointer');
                    saveButton.disabled = true;
                }
            }

            function updateDocumentStatus(customerId) {
                const selectElem = document.getElementById(`document_status_${customerId}`);
                const documentStatusId = selectElem.value;

                fetch('{{ route('customers.updateDocumentStatus') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({
                            customerId: customerId,
                            documentStatusId: documentStatusId
                        }),
                    })
                    .then((response) => {
                        if (response.redirected) {
                            // If redirected, follow the redirect URL
                            window.location.href = response.url;
                            return;
                        }
                        return response.json();
                    })
                    .then((data) => {
                        if (data && data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false,
                            });

                            // Update the original value after a successful update
                            selectElem.setAttribute('data-original-value', documentStatusId);
                            resetSaveButton(customerId);
                        } else if (data) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message,
                            });

                            // Revert the select element's value to the original value
                            selectElem.value = selectElem.getAttribute('data-original-value');
                            enableSaveButton(customerId);
                        }
                    })
                    .catch((error) => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while updating document status',
                        });

                        // Revert the select element's value to the original value
                        selectElem.value = selectElem.getAttribute('data-original-value');
                        enableSaveButton(customerId);
                    });
            }


            // Reset Save button to inactive state after successful update
            function resetSaveButton(customerId) {
                const saveButton = document.getElementById(`save_icon_${customerId}`);
                saveButton.classList.add('text-gray-400', 'cursor-not-allowed');
                saveButton.classList.remove('text-blue-600', 'cursor-pointer');
                saveButton.disabled = true;
            }
        </script>
        </div>
    @endsection
</x-layout>

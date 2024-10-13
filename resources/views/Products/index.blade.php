<x-layout>
    @section('title', 'Home')
    @section('content')
        <div class="container mx-auto px-4 py-8">
            <!-- Filter and Search Bar -->
            <form action="/" method="GET" class="mb-4">
                <div class="flex items-center space-x-4">
                    <input type="text" name="sku" value="{{ $sku }}" placeholder="Search by SKU..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-md outline-none placeholder-gray-500">
                    <input type="text" id="start_date" name="start_date" value="{{ $startDate ?? '' }}"
                        placeholder="Start Date"
                        class="px-4 py-2 border border-gray-300 rounded-md outline-none placeholder-gray-500">
                    <input type="text" id="end_date" name="end_date" value="{{ $endDate ?? '' }}" placeholder="End Date"
                        class="px-4 py-2 border border-gray-300 rounded-md outline-none placeholder-gray-500">
                    <button type="submit" class="px-4 py-2 bg-blue-200 hover:bg-blue-300">
                        Filter
                    </button>
                    <a href="/" class="px-4 py-2 bg-gray-200 hover:bg-gray-300">
                        Reset
                    </a>
                    <!-- Add Product Button -->
                    <button id="addProductButton" type="button"
                        class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition duration-300 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                                clip-rule="evenodd" />
                        </svg>
                        Add Product
                    </button>
                </div>
            </form>

            @if ($products->isEmpty())
                <p class="text-gray-600">Sorry, no products match your search criteria. Please try again.</p>
            @else
                <!-- Products Table -->
                <div class="overflow-x-auto">
                    <table class="table-auto w-full">
                        <thead>
                            <tr>
                                @php
                                    $fields = [
                                        'id' => 'ID',
                                        'item_code' => 'Item Code',
                                        'sku' => 'SKU',
                                        'price' => 'Price',
                                        'stock' => 'Stock',
                                        'updated_at' => 'Last Update',
                                    ];
                                @endphp
                                @foreach ($fields as $field => $label)
                                    <th class="py-2 px-4 border-b border-gray-200">
                                        <a
                                            href="?{{ http_build_query(array_merge(request()->all(), ['sort_field' => $field, 'sort_direction' => request('sort_direction') === 'asc' ? 'desc' : 'asc'])) }}">
                                            {{ $label }}
                                            @if (request('sort_field') === $field)
                                                @if (request('sort_direction') === 'asc')
                                                    ↑
                                                @else
                                                    ↓
                                                @endif
                                            @else
                                                ↑
                                            @endif
                                        </a>
                                    </th>
                                @endforeach
                                @if (Auth::user()->can('upload', App\Models\Product::class))
                                    <th class="py-2 px-4 border-b border-gray-200">Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr>
                                    <td class="border px-4 py-2">{{ $product->id }}</td>
                                    <td class="border px-4 py-2">{{ $product->item_code }}</td>
                                    <td class="border px-4 py-2">{{ $product->sku }}</td>
                                    <td class="border px-4 py-2">{{ $product->price }}</td>
                                    <td class="border px-4 py-2">{{ $product->stock }}</td>
                                    <td class="border px-4 py-2">{{ $product->updated_at }}</td>
                                    @if (Auth::user()->can('upload', App\Models\Product::class))
                                        <td class="border px-4 py-2">
                                            <a href="{{ route('products.edit', $product->id) }}"
                                                class="text-blue-500 hover:text-blue-700">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </a>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Links and Items Per Page Dropdown -->
                <div class="flex items-center justify-between mt-4">
                    <div class="flex items-center">
                        <label for="per_page" class="mr-2">Items per page:</label>
                        <form action="/" method="GET">
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
                        {{ $products->appends(['per_page' => $perPage])->links() }}
                    </div>
                </div>
            @endif
            <!-- Modal for Adding Product -->
            <div id="addProductModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center">
                <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
                    <h2 class="text-2xl font-bold mb-4">Add New Product</h2>
                    <form id="addProductForm" method="POST" action="{{ route('products.store') }}">
                        @csrf
                        <div class="mb-4">
                            <label for="item_code" class="block text-sm font-medium text-gray-700 mb-1">Item Code</label>
                            <input type="text" name="item_code" id="item_code"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-red-500 text-xs mt-1 hidden" id="item_code_error"></p>
                        </div>
                        <div class="mb-4">
                            <label for="sku" class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
                            <input type="text" name="sku" id="sku"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-red-500 text-xs mt-1 hidden" id="sku_error"></p>
                        </div>
                        <div class="mb-4">
                            <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Price</label>
                            <input type="number" name="price" id="price" step="0.01"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-red-500 text-xs mt-1 hidden" id="price_error"></p>
                        </div>
                        <div class="mb-4">
                            <label for="stock" class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
                            <input type="number" name="stock" id="stock"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-red-500 text-xs mt-1 hidden" id="stock_error"></p>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit"
                                class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition duration-300">Save</button>
                            <button type="button" id="closeModal"
                                class="ml-2 px-4 py-2 bg-gray-300 rounded-md hover:bg-gray-400 transition duration-300">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Include Flatpickr -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Flatpickr initialization
                flatpickr("#start_date", {
                    dateFormat: "Y-m-d",
                    maxDate: "today",
                    onChange: function(selectedDates, dateStr, instance) {
                        let endDatePicker = document.querySelector("#end_date")._flatpickr;
                        endDatePicker.set('minDate', dateStr);
                    }
                });
                flatpickr("#end_date", {
                    dateFormat: "Y-m-d",
                    maxDate: "today",
                    onChange: function(selectedDates, dateStr, instance) {
                        let startDatePicker = document.querySelector("#start_date")._flatpickr;
                        startDatePicker.set('maxDate', dateStr);
                    }
                });

                // Modal functionality
                const modal = document.getElementById('addProductModal');
                const addProductButton = document.getElementById('addProductButton');
                const closeModal = document.getElementById('closeModal');
                const addProductForm = document.getElementById('addProductForm');

                addProductButton.addEventListener('click', () => {
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                });

                closeModal.addEventListener('click', () => {
                    closeModalAndResetForm();
                });

                // Close modal when clicking outside
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        closeModalAndResetForm();
                    }
                });

                function closeModalAndResetForm() {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    addProductForm.reset();
                    clearErrors();
                }

                function clearErrors() {
                    const errorElements = document.querySelectorAll('[id$="_error"]');
                    errorElements.forEach(el => {
                        el.textContent = '';
                        el.classList.add('hidden');
                    });
                }

                // Form submission
                addProductForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    clearErrors();

                    fetch('{{ route('products.store') }}', {
                            method: 'POST',
                            body: new FormData(this),
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Success!',
                                    text: 'Product added successfully.',
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    closeModalAndResetForm();
                                    window.location.reload();
                                });
                            } else {
                                if (data.errors) {
                                    Object.keys(data.errors).forEach(key => {
                                        const errorElement = document.getElementById(
                                            `${key}_error`);
                                        if (errorElement) {
                                            errorElement.textContent = data.errors[key][0];
                                            errorElement.classList.remove('hidden');
                                        }
                                    });
                                } else {
                                    // Handle general error
                                    Swal.fire('Error', 'Failed to save the product.', 'error');
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire('Error', 'Failed to save the product.', 'error');
                        });
                });
            });
        </script>
    @endsection

</x-layout>

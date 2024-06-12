<x-layout>
    <div class="container mx-auto px-4 py-8">
        <!-- Filter and Search Bar -->
        <form action="/" method="GET" class="mb-4">
            <div class="flex items-center space-x-4">
                <input type="text" name="sku" value="{{ $sku }}" placeholder="Search by SKU..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-md outline-none placeholder-gray-500">
                <input type="text" id="start_date" name="start_date" value="{{ $startDate }}" placeholder="Start Date"
                    class="px-4 py-2 border border-gray-300 rounded-md outline-none placeholder-gray-500">
                <input type="text" id="end_date" name="end_date" value="{{ $endDate }}" placeholder="End Date"
                    class="px-4 py-2 border border-gray-300 rounded-md outline-none placeholder-gray-500">
                <button type="submit" class="px-4 py-2 bg-blue-200 hover:bg-blue-300">
                    Filter
                </button>
                <a href="/" class="px-4 py-2 bg-gray-200 hover:bg-gray-300">
                    Clear
                </a>
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
                                $fields = ['id' => 'ID', 'item_code' => 'Item Code', 'sku' => 'SKU', 'price' => 'Price', 'stock' => 'Stock', 'updated_at' => 'Last Update'];
                            @endphp
                            @foreach ($fields as $field => $label)
                                <th class="py-2 px-4 border-b border-gray-200">
                                    <a href="?{{ http_build_query(array_merge(request()->all(), ['sort_field' => $field, 'sort_direction' => request('sort_direction') === 'asc' ? 'desc' : 'asc'])) }}">
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
                                                    d="M12 19l9 2-2-9-9-2-9 2 2 9 9-2zm0 0v-8m0 0-4 4m4-4 4 4" />
                                            </svg>
                                        </a>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination Links -->
            <div class="mt-4">
                {{ $products->links() }}
            </div>
        @endif
    </div>

    <!-- Include Flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
        });
    </script>
</x-layout>

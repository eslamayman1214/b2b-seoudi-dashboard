<x-layout>

    <div class="container mx-auto px-4 py-8">
        <!-- Search Bar -->
        <form action="/" method="GET" class="mb-4">
            <div class="flex items-center border border-gray-300 rounded-md">
                <input type="text" name="sku" value="{{ $sku }}" placeholder="Search by SKU..."
                    class="w-full px-4 py-2 outline-none placeholder-gray-500">
                <button type="submit" class="px-4 py-2 bg-gray-200 hover:bg-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 15l6-6M10 8a5 5 0 017.071 7.071 5 5 0 11-7.071-7.071"></path>
                    </svg>
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
                            <th class="px-4 py-2">ID</th>
                            <th class="px-4 py-2">Item Code</th>
                            <th class="px-4 py-2">SKU</th>
                            <th class="px-4 py-2">Price</th>
                            <th class="px-4 py-2">Stock</th>
                            <th class="px-4 py-2">Actions</th>
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
</x-layout>

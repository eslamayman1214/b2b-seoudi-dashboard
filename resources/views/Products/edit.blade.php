<x-layout>
    @section('title', 'edit product')
    @section('content')
        <div class="container mx-auto py-12">
            <div class="max-w-lg mx-auto bg-white shadow-lg rounded-lg">
                <div class="px-6 py-4">
                    <h2 class="text-2xl font-semibold text-gray-800">{{ __('Edit Product') }}</h2>

                    @if ($errors->any())
                        <div class="mt-4 p-4 bg-red-100 text-red-700 rounded">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('products.update', $product->id) }}" class="mt-6">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="item_code"
                                class="block text-sm font-medium text-gray-700">{{ __('Item Code') }}</label>
                            <input id="item_code" type="text"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm @error('item_code') border-red-500 @enderror"
                                name="item_code" value="{{ $product->item_code }}" required>

                            @error('item_code')
                                <span class="text-red-500 text-sm mt-1">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="sku"
                                class="block text-sm font-medium text-gray-700">{{ __('SKU') }}</label>
                            <input id="sku" type="text"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm @error('sku') border-red-500 @enderror"
                                name="sku" value="{{ $product->sku }}" required>

                            @error('sku')
                                <span class="text-red-500 text-sm mt-1">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="price"
                                class="block text-sm font-medium text-gray-700">{{ __('Price') }}</label>
                            <input id="price" type="text"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm @error('price') border-red-500 @enderror"
                                name="price" value="{{ $product->price }}" required>

                            @error('price')
                                <span class="text-red-500 text-sm mt-1">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="stock"
                                class="block text-sm font-medium text-gray-700">{{ __('Stock') }}</label>
                            <input id="stock" type="text"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm @error('stock') border-red-500 @enderror"
                                name="stock" value="{{ $product->stock }}" required>

                            @error('stock')
                                <span class="text-red-500 text-sm mt-1">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                {{ __('Update') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endsection

</x-layout>

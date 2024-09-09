<x-layout>
    @section('title', 'Edit Product')
    @section('content')
        <div class="container mx-auto py-12">
            <div class="max-w-6xl mx-auto bg-white shadow-lg rounded-lg">
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

                        <!-- Product Basic Data -->
                        <div class="mb-4 border-b pb-4">
                            <h3 class="text-xl font-semibold text-gray-800">{{ __('Product Basic Data') }}</h3>
                            <div class="flex space-x-4">
                                <div class="flex-1">
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
                                <div class="flex-1">
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
                                <div class="flex-1">
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
                                <div class="flex-1">
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
                            </div>
                        </div>

                        <!-- Tiers -->
                        <div class="mb-4">
                            <h3 class="text-xl font-semibold text-gray-800">{{ __('Tiers') }}</h3>
                            <div id="tiers-container" class="space-y-4">
                                @php
                                    $tiers = $tiers ?? [];
                                @endphp
                                @foreach ($tiers as $tier)
                                    <div class="tier flex space-x-4 items-end" data-tier-id="{{ $tier->id }}">
                                        <input type="hidden" name="tiers[{{ $loop->index }}][id]"
                                            value="{{ $tier->id }}">
                                        <div class="flex-1">
                                            <label
                                                class="block text-sm font-medium text-gray-700">{{ __('Tier Name') }}</label>
                                            <input type="text" name="tiers[{{ $loop->index }}][tier_name]"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                                value="{{ $tier->tier_name }}" readonly>
                                        </div>
                                        <!-- Price Type -->
                                        <div class="flex-1">
                                            <label
                                                class="block text-sm font-medium text-gray-700">{{ __('Price Type') }}</label>
                                            <select name="tiers[{{ $loop->index }}][price_type]"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm price-type-select"
                                                data-index="{{ $loop->index }}">
                                                <option value="range"
                                                    {{ $tier->price_type == 'range' ? 'selected' : '' }}>Range</option>
                                                <option value="fixed"
                                                    {{ $tier->price_type == 'fixed' ? 'selected' : '' }}>Fixed</option>
                                            </select>
                                        </div>
                                        <div class="flex-1">
                                            <label
                                                class="block text-sm font-medium text-gray-700">{{ __('Min Quantity') }}</label>
                                            <input type="number" name="tiers[{{ $loop->index }}][min_quantity]"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                                value="{{ $tier->min_quantity }}" required
                                                {{ $tier->price_type == 'fixed' ? 'readonly' : '' }}>
                                        </div>
                                        <div class="flex-1">
                                            <label
                                                class="block text-sm font-medium text-gray-700">{{ __('Max Quantity') }}</label>
                                            <input type="number" name="tiers[{{ $loop->index }}][max_quantity]"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                                value="{{ $tier->max_quantity }}" required
                                                {{ $tier->price_type == 'fixed' ? 'readonly' : '' }}>
                                        </div>
                                        <div class="flex-1">
                                            <label
                                                class="block text-sm font-medium text-gray-700">{{ __('Value') }}</label>
                                            <div class="flex">
                                                <input type="text" name="tiers[{{ $loop->index }}][value]"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                                    value="{{ $tier->value }}" required>
                                                <select name="tiers[{{ $loop->index }}][type]"
                                                    class="mt-1 ml-2 block rounded-md border-gray-300 shadow-sm">
                                                    <option value="price" {{ $tier->type == 'price' ? 'selected' : '' }}>
                                                        Price</option>
                                                    <option value="percentage"
                                                        {{ $tier->type == 'percentage' ? 'selected' : '' }}>Percentage
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <label
                                                class="block text-sm font-medium text-gray-700">{{ __('Customer Group') }}</label>
                                            <select name="tiers[{{ $loop->index }}][customer_group]"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                                @foreach ($customerGroups as $group)
                                                    <option value="{{ $group }}"
                                                        {{ $tier->customer_group == $group ? 'selected' : '' }}>
                                                        {{ $group }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>



                                        <div class="flex-none">
                                            <button type="button" class="text-red-500 delete-tier">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" id="add-tier"
                                class="mt-2 bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">
                                {{ __('Add Tier') }}
                            </button>
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
        <script>
            document.getElementById('add-tier').addEventListener('click', function() {
                const container = document.getElementById('tiers-container');
                const tiers = container.querySelectorAll('.tier');
                const index = tiers.length;

                if (index > 0) {
                    const lastTier = tiers[index - 1];
                    const minQuantity = parseInt(lastTier.querySelector('input[name*="[min_quantity]"]').value);
                    const maxQuantity = parseInt(lastTier.querySelector('input[name*="[max_quantity]"]').value);

                    if (maxQuantity <= minQuantity) {
                        alert(
                            `Invalid quantity range in ${lastTier.querySelector('input[name*="[tier_name]"]').value}. Please check the quantities.`
                        );
                        return;
                    }
                }

                let previousMaxQuantity = 1;

                const tierName = `tier${index + 1}`;
                const newTier = `
            <div class="tier flex space-x-4 items-end" data-tier-id="">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700">{{ __('Tier Name') }}</label>
                    <input type="text" name="tiers[${index}][tier_name]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        value="${tierName}" readonly>
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700">{{ __('Price Type') }}</label>
                    <select name="tiers[${index}][price_type]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm price-type-select" data-index="${index}">
                        <option value="range">Range</option>
                        <option value="fixed">Fixed</option>
                    </select>
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700">{{ __('Min Quantity') }}</label>
                    <input type="number" name="tiers[${index}][min_quantity]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        value="${previousMaxQuantity}" required>
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700">{{ __('Max Quantity') }}</label>
                    <input type="number" name="tiers[${index}][max_quantity]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        required>
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700">{{ __('Value') }}</label>
                    <div class="flex">
                        <input type="text" name="tiers[${index}][value]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                            required>
                        <select name="tiers[${index}][type]" class="mt-1 ml-2 block rounded-md border-gray-300 shadow-sm">
                            <option value="price">Price</option>
                            <option value="percentage">Percentage</option>
                        </select>
                    </div>
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700">{{ __('Customer Group') }}</label>
                    <select name="tiers[${index}][customer_group]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        @foreach ($customerGroups as $group)
                            <option value="{{ $group }}">{{ $group }}</option>
                        @endforeach
                    </select>
                </div>
               <div class="flex-none">
                    <button type="button" class="text-red-500 delete-tier">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        `;
                container.insertAdjacentHTML('beforeend', newTier);

                // Add event listeners for the new delete button and price type change
                handleTierDelete();
                handlePriceTypeChange();
            });

            function handleTierDelete() {
                const tiers = document.querySelectorAll('.tier');

                // Hide all delete buttons except the last tier
                tiers.forEach((tier, index) => {
                    const deleteButton = tier.querySelector('.delete-tier');
                    if (index === tiers.length - 1) {
                        deleteButton.classList.remove('invisible'); // Show delete button for the last tier
                    } else {
                        deleteButton.classList.add('invisible'); // Hide delete button for all other tiers
                    }
                });

                // Add event listener to the delete button of the last tier
                tiers[tiers.length - 1].querySelector('.delete-tier').addEventListener('click', function() {
                    const tier = this.closest('.tier');
                    tier.remove();

                    // Re-run this function to update delete button visibility
                    handleTierDelete();
                });
            }

            function handlePriceTypeChange() {
                document.querySelectorAll('.price-type-select').forEach(function(select) {
                    select.addEventListener('change', function() {
                        const index = this.getAttribute('data-index');
                        const minInput = document.querySelector(`input[name="tiers[${index}][min_quantity]"]`);
                        const maxInput = document.querySelector(`input[name="tiers[${index}][max_quantity]"]`);

                        if (this.value === 'fixed') {
                            minInput.value = '';
                            maxInput.value = '';
                            minInput.setAttribute('readonly', true);
                            maxInput.setAttribute('readonly', true);
                        } else {
                            minInput.removeAttribute('readonly');
                            maxInput.removeAttribute('readonly');
                        }
                    });
                });
            }

            // Initialize event listeners
            handleTierDelete();
            handlePriceTypeChange();
        </script>
    @endsection
</x-layout>

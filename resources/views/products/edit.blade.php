<x-layout>
    @section('title', 'Edit Product')
    @section('content')
        <div class="container mx-auto py-12">
            <div class="max-w-7xl mx-auto bg-white shadow-lg rounded-lg">
                <div class="px-8 py-6">
                    <h2 class="text-3xl font-bold text-gray-800 mb-6">{{ __('Edit Product') }}</h2>

                    @if ($errors->any())
                        <div class="mt-4 p-4 bg-red-100 text-red-700 rounded-md mb-6">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif
                    @if (session('fail'))
                        <div class="mt-4 p-4 bg-red-100 text-red-700 rounded-md mb-6">
                            {{ session('fail') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('products.update', $product->id) }}" class="space-y-8">
                        @csrf
                        @method('PUT')

                        <!-- Product Basic Data -->
                        <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
                            <h3 class="text-2xl font-semibold text-gray-800 mb-4">{{ __('Product Basic Data') }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                <div>
                                    <label for="item_code"
                                        class="block text-sm font-medium text-gray-700 mb-1">{{ __('Item Code') }}</label>
                                    <input id="item_code" type="text"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('item_code') border-red-500 @enderror"
                                        name="item_code" value="{{ old('item_code', $product->item_code) }}" required>
                                    @error('item_code')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="sku"
                                        class="block text-sm font-medium text-gray-700 mb-1">{{ __('SKU') }}</label>
                                    <input id="sku" type="text"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('sku') border-red-500 @enderror"
                                        name="sku" value="{{ old('sku', $product->sku) }}" required>
                                    @error('sku')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="price"
                                        class="block text-sm font-medium text-gray-700 mb-1">{{ __('Price') }}</label>
                                    <input id="price" type="number" step="0.01"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('price') border-red-500 @enderror"
                                        name="price" value="{{ old('price', $product->price) }}" required>
                                    @error('price')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="stock"
                                        class="block text-sm font-medium text-gray-700 mb-1">{{ __('Stock') }}</label>
                                    <input id="stock" type="number"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('stock') border-red-500 @enderror"
                                        name="stock" value="{{ old('stock', $product->stock) }}" required>
                                    @error('stock')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Tiers -->
                        <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
                            <h3 class="text-2xl font-semibold text-gray-800 mb-4">{{ __('Tiers') }}</h3>
                            <div id="tiers-container" class="space-y-8">
                                @foreach ($customerGroups as $group)
                                    <div class="customer-group-tiers p-4 bg-white rounded-md shadow"
                                        data-customer-group="{{ $group }}">
                                        <h4 class="text-xl font-semibold text-gray-700 mb-4">{{ $group }}</h4>
                                        @foreach ($tiers->where('customer_group', $group) as $index => $tier)
                                            <div class="tier grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 items-end mb-4 pb-4 border-b border-gray-200"
                                                data-tier-id="{{ $tier->id }}">
                                                <input type="hidden"
                                                    name="tiers[{{ $group }}][{{ $index }}][id]"
                                                    value="{{ $tier->id }}">
                                                <input type="hidden"
                                                    name="tiers[{{ $group }}][{{ $index }}][customer_group]"
                                                    value="{{ $group }}">
                                                <div>
                                                    <label
                                                        class="block text-sm font-medium text-gray-700 mb-1">{{ __('Tier Name') }}</label>
                                                    <input type="text"
                                                        name="tiers[{{ $group }}][{{ $index }}][tier_name]"
                                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                                        value="{{ old("tiers.{$group}.{$index}.tier_name", $tier->tier_name) }}"
                                                        readonly required>
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-sm font-medium text-gray-700 mb-1">{{ __('Price Type') }}</label>
                                                    <select
                                                        name="tiers[{{ $group }}][{{ $index }}][price_type]"
                                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 price-type-select"
                                                        data-group="{{ $group }}" data-index="{{ $index }}"
                                                        disabled>
                                                        <option value="range"
                                                            {{ old("tiers.{$group}.{$index}.price_type", $tier->price_type) == 'range' ? 'selected' : '' }}>
                                                            Range</option>
                                                        <option value="fixed"
                                                            {{ old("tiers.{$group}.{$index}.price_type", $tier->price_type) == 'fixed' ? 'selected' : '' }}>
                                                            Fixed</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-sm font-medium text-gray-700 mb-1">{{ __('Min Quantity') }}</label>
                                                    <input type="number"
                                                        name="tiers[{{ $group }}][{{ $index }}][min_quantity]"
                                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 min-quantity"
                                                        value="{{ old("tiers.{$group}.{$index}.min_quantity", $tier->min_quantity) }}"
                                                        {{ $tier->price_type == 'fixed' ? '' : 'readonly' }}>
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-sm font-medium text-gray-700 mb-1">{{ __('Max Quantity') }}</label>
                                                    <input type="number"
                                                        name="tiers[{{ $group }}][{{ $index }}][max_quantity]"
                                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 max-quantity"
                                                        value="{{ old("tiers.{$group}.{$index}.max_quantity", $tier->max_quantity) }}"
                                                        {{ $tier->price_type == 'fixed' ? 'readonly' : '' }} readonly>
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-sm font-medium text-gray-700 mb-1">{{ __('Value') }}</label>
                                                    <div class="flex">
                                                        <input type="number"
                                                            name="tiers[{{ $group }}][{{ $index }}][value]"
                                                            class="mt-1 block w-full rounded-l-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                                            value="{{ old("tiers.{$group}.{$index}.value", $tier->value) }}"
                                                            required>
                                                        <select
                                                            name="tiers[{{ $group }}][{{ $index }}][type]"
                                                            class="mt-1 block rounded-r-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 border-l-0">
                                                            <option value="price"
                                                                {{ old("tiers.{$group}.{$index}.type", $tier->type) == 'price' ? 'selected' : '' }}>
                                                                Price</option>
                                                            <option value="percentage"
                                                                {{ old("tiers.{$group}.{$index}.type", $tier->type) == 'percentage' ? 'selected' : '' }}>
                                                                Percentage</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div>
                                                    <button type="button"
                                                        class="text-red-500 hover:text-red-700 delete-tier">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                            stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                        <button type="button"
                                            class="add-tier mt-4 bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50 transition"
                                            data-customer-group="{{ $group }}">
                                            {{ __('Add Tier for ') . $group }}
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition">
                                {{ __('Update Product') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const tiersContainer = document.getElementById('tiers-container');

                tiersContainer.addEventListener('click', function(event) {
                    if (event.target.classList.contains('add-tier') || event.target.closest('.add-tier')) {
                        const customerGroup = event.target.closest('.add-tier').dataset.customerGroup;
                        addTier(customerGroup);
                    } else if (event.target.classList.contains('delete-tier') || event.target.closest(
                            '.delete-tier')) {
                        const tier = event.target.closest('.tier');
                        const customerGroup = tier.closest('.customer-group-tiers').dataset.customerGroup;
                        deleteTier(tier, customerGroup);
                    }
                });

                tiersContainer.addEventListener('change', function(event) {
                    if (event.target.classList.contains('price-type-select')) {
                        handlePriceTypeChange(event.target);
                    }
                });

                function addTier(customerGroup) {
                    const customerGroupContainer = tiersContainer.querySelector(
                        `[data-customer-group="${customerGroup}"]`);
                    const tiers = customerGroupContainer.querySelectorAll('.tier');
                    const index = tiers.length;

                    const hasFixedTier = Array.from(tiers).some(tier =>
                        tier.querySelector('select[name*="[price_type]"]').value === 'fixed'
                    );

                    if (index > 0) {
                        const lastTier = tiers[index - 1];
                        const minQuantity = parseInt(lastTier.querySelector('input[name*="[min_quantity]"]').value);
                        const maxQuantity = parseInt(lastTier.querySelector('input[name*="[max_quantity]"]').value);
                        const priceType = lastTier.querySelector('select[name*="[price_type]"]').value;

                        if (priceType === 'range' && (!maxQuantity || maxQuantity <= minQuantity)) {
                            Swal.fire({
                                title: 'Invalid Quantity Range',
                                text: `Please set a valid max quantity for the last tier before adding a new one.`,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                            return;
                        }
                    }

                    let previousMaxQuantity = 1;
                    for (let i = tiers.length - 1; i >= 0; i--) {
                        const tier = tiers[i];
                        const priceType = tier.querySelector('select[name*="[price_type]"]').value;
                        const lastMax = parseInt(tier.querySelector('input[name*="[max_quantity]"]').value);
                        if (!isNaN(lastMax)) {
                            previousMaxQuantity = lastMax + 1;
                            break;
                        }
                    }

                    const tierName = `${customerGroup}_tier${index + 1}`;
                    const newTier = `
    <div class="tier grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 items-end mb-4 pb-4 border-b border-gray-200" data-tier-id="">
        <input type="hidden" name="tiers[${customerGroup}][${index}][id]" value="">
        <input type="hidden" name="tiers[${customerGroup}][${index}][price_type]" value="range" class="hidden-price-type">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Tier Name') }}</label>
            <input type="text" name="tiers[${customerGroup}][${index}][tier_name]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="${tierName}" required readonly>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Price Type') }}</label>
            <select name="tiers[${customerGroup}][${index}][price_type]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 price-type-select" data-group="${customerGroup}" data-index="${index}" ${hasFixedTier ? 'disabled' : ''}>
                <option value="range" ${hasFixedTier ? 'selected' : ''}>Range</option>
                <option value="fixed" ${hasFixedTier ? 'disabled' : ''}>Fixed</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Min Quantity') }}</label>
            <input type="number" name="tiers[${customerGroup}][${index}][min_quantity]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="${previousMaxQuantity}" required readonly>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Max Quantity') }}</label>
            <input type="number" name="tiers[${customerGroup}][${index}][max_quantity]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Value') }}</label>
            <div class="flex">
                <input type="number" name="tiers[${customerGroup}][${index}][value]" class="mt-1 block w-full rounded-l-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                <select name="tiers[${customerGroup}][${index}][type]" class="mt-1 block rounded-r-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 border-l-0">
                    <option value="price">Price</option>
                    <option value="percentage">Percentage</option>
                </select>
            </div>
        </div>
        <div>
            <button type="button" class="text-red-500 hover:text-red-700 delete-tier">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        </div>
    </div>
`;
                    customerGroupContainer.insertAdjacentHTML('beforeend', newTier);
                    handlePriceTypeChange(customerGroupContainer.querySelector(
                        `[name="tiers[${customerGroup}][${index}][price_type]"]`));
                }

                function deleteTier(tier, customerGroup) {
                    const customerGroupContainer = tiersContainer.querySelector(
                        `[data-customer-group="${customerGroup}"]`);
                    const tiers = Array.from(customerGroupContainer.querySelectorAll('.tier'));
                    const currentTierIndex = tiers.indexOf(tier);

                    if (currentTierIndex !== tiers.length - 1) {
                        Swal.fire({
                            title: 'Cannot delete this tier',
                            text: 'You must delete tiers in order, starting from the last tier.',
                            icon: 'error',
                            confirmButtonColor: '#3085d6',
                        });
                        return;
                    }

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            tier.remove();
                            updateTierIndexes(customerGroup);
                            Swal.fire(
                                'Deleted!',
                                'The tier has been deleted.',
                                'success'
                            );
                        }
                    });
                }

                function handlePriceTypeChange(select) {
                    const customerGroup = select.getAttribute('data-group');
                    const index = parseInt(select.getAttribute('data-index'));
                    const tier = select.closest('.tier');
                    const minInput = tier.querySelector(
                        `input[name="tiers[${customerGroup}][${index}][min_quantity]"]`);
                    const maxInput = tier.querySelector(
                        `input[name="tiers[${customerGroup}][${index}][max_quantity]"]`);
                    const hiddenPriceTypeInput = tier.querySelector('.hidden-price-type');

                    hiddenPriceTypeInput.value = select.value; // Update hidden input

                    const customerGroupContainer = select.closest('.customer-group-tiers');
                    const tiers = Array.from(customerGroupContainer.querySelectorAll('.tier'));

                    if (select.value === 'fixed') {
                        minInput.value = '';
                        maxInput.value = '';
                        minInput.setAttribute('readonly', true);
                        maxInput.setAttribute('readonly', true);

                        // Set all tiers before this one to 'range'
                        for (let i = 0; i < index; i++) {
                            const previousTier = tiers[i];
                            const previousSelect = previousTier.querySelector('.price-type-select');
                            previousSelect.value = 'range';
                            previousSelect.dispatchEvent(new Event('change'));
                        }

                        // Disable all tiers after this one
                        for (let i = index + 1; i < tiers.length; i++) {
                            const nextTier = tiers[i];
                            const nextSelect = nextTier.querySelector('.price-type-select');
                            nextSelect.value = 'range';
                            nextSelect.disabled = true;
                            nextSelect.querySelector('option[value="fixed"]').disabled = true;
                        }
                    } else {
                        maxInput.removeAttribute('readonly');

                        // Set min quantity based on previous max quantity
                        if (index > 0) {
                            const previousTier = tiers[index - 1];
                            const previousMaxQuantity = parseInt(previousTier.querySelector(
                                'input[name*="[max_quantity]"]').value);
                            if (!isNaN(previousMaxQuantity)) {
                                minInput.value = previousMaxQuantity + 1;
                            }
                        } else {
                            minInput.value = 1;
                        }

                        // Enable price type selection for all tiers after this one until a fixed tier is found
                        let foundFixed = false;
                        for (let i = index + 1; i < tiers.length; i++) {
                            const nextTier = tiers[i];
                            const nextSelect = nextTier.querySelector('.price-type-select');
                            if (foundFixed) {
                                nextSelect.disabled = true;
                                nextSelect.querySelector('option[value="fixed"]').disabled = true;
                            } else {
                                nextSelect.disabled = false;
                                nextSelect.querySelector('option[value="fixed"]').disabled = false;
                            }
                            if (nextSelect.value === 'fixed') {
                                foundFixed = true;
                            }
                        }
                    }

                    updateTierIndexes(customerGroup);
                }

                function updateTierIndexes(customerGroup) {
                    const customerGroupContainer = tiersContainer.querySelector(
                        `[data-customer-group="${customerGroup}"]`);
                    const tiers = Array.from(customerGroupContainer.querySelectorAll('.tier'));

                    tiers.forEach((tier, index) => {
                        tier.querySelectorAll('[name^="tiers["]').forEach(element => {
                            const name = element.getAttribute('name');
                            const newName = name.replace(/\[\d+\]/, `[${index}]`);
                            element.setAttribute('name', newName);
                        });

                        const tierNameInput = tier.querySelector('input[name*="[tier_name]"]');
                        tierNameInput.value = `${customerGroup}_tier${index + 1}`;

                        const priceTypeSelect = tier.querySelector('.price-type-select');
                        priceTypeSelect.setAttribute('data-index', index);
                    });
                }

                // Initialize event listeners for existing tiers
                document.querySelectorAll('.price-type-select').forEach(select => {
                    handlePriceTypeChange(select);
                });
            });
        </script>
    @endsection
</x-layout>

<x-layout>
    @section('title', 'Settings')
    @section('content')
        <div class="container mx-auto px-4 py-8">
            <h1 class="text-2xl font-bold mb-4">Settings</h1>

            @if (session('success'))
                <div class="bg-green-500 text-white px-4 py-2 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Logging Form -->
            <form action="{{ route('settings.toggleLogging') }}" method="POST" class="mb-4">
                @csrf
                <fieldset class="border border-gray-300 rounded p-4">
                    <legend class="text-lg font-medium">Logging</legend>
                    <div class="mt-2">
                        <label class="inline-flex items-center">
                            <input type="radio" name="logging" value="1" {{ $loggingEnabled ? 'checked' : '' }}
                                class="form-radio text-blue-600" id="logging-enabled">
                            <span class="ml-2">Enable Logging</span>
                        </label>
                    </div>
                    <div class="mt-2">
                        <label class="inline-flex items-center">
                            <input type="radio" name="logging" value="0" {{ !$loggingEnabled ? 'checked' : '' }}
                                class="form-radio text-blue-600" id="logging-disabled">
                            <span class="ml-2">Disable Logging</span>
                        </label>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="text-white px-4 py-2 rounded" id="save-button-logging" disabled>
                            Save Settings
                        </button>
                    </div>
                </fieldset>
            </form>

            <!-- API Settings Form -->
            <form action="{{ route('settings.saveSettings') }}" method="POST" class="mb-4">
                @csrf
                <fieldset class="border border-gray-300 rounded p-4" id="API">
                    <legend class="text-lg font-medium">API Settings</legend>

                    <div class="mt-4">
                        <label for="base-url" class="block text-gray-700">Base URL</label>
                        <input type="text" name="base_url" id="base-url" value="{{ $baseUrl }}"
                            class="form-input mt-1 block w-full">
                    </div>

                    <div class="mt-4">
                        <label for="customer-endpoint" class="block text-gray-700">Customers End Point</label>
                        <input type="text" name="customer_endpoint" id="customer-endpoint"
                            value="{{ $customerEndpoint }}" class="form-input mt-1 block w-full">
                    </div>

                    <div class="mt-4">
                        <label for="customer-token" class="block text-gray-700">Customer Token</label>
                        <input type="text" name="customer_token" id="customer-token"
                            value="{{ $customerToken ? substr($customerToken, 0, 4) . str_repeat('*', strlen($customerToken) - 4) : '' }}"
                            class="form-input mt-1 block w-full">
                    </div>

                    <div class="mt-4">
                        <label for="products-endpoint" class="block text-gray-700">Products End Point</label>
                        <input type="text" name="products_endpoint" id="products-endpoint"
                            value="{{ $productsEndpoint }}" class="form-input mt-1 block w-full">
                    </div>

                    <div class="mt-4">
                        <label for="products-token" class="block text-gray-700">Products Token</label>
                        <input type="text" name="products_token" id="products-token"
                            value="{{ $productsToken ? substr($productsToken, 0, 4) . str_repeat('*', strlen($productsToken) - 4) : '' }}"
                            class="form-input mt-1 block w-full">
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="text-white bg-blue-500 hover:bg-blue-700 px-4 py-2 rounded"
                            id="save-button-api" disabled>
                            Save Settings
                        </button>
                    </div>
                </fieldset>
            </form>
        </div>
        <!-- JavaScript to handle save button state for logging and API -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Logging Save Button Logic
                const loggingEnabled = @json($loggingEnabled);
                const saveButtonLogging = document.getElementById('save-button-logging');
                const radioButtonsLogging = document.querySelectorAll('input[name="logging"]');

                const updateButtonStateLogging = () => {
                    const selectedValueLogging = document.querySelector('input[name="logging"]:checked').value;
                    if (selectedValueLogging == loggingEnabled) {
                        saveButtonLogging.disabled = true;
                        saveButtonLogging.classList.remove('bg-blue-500', 'hover:bg-blue-700');
                        saveButtonLogging.classList.add('bg-gray-500', 'hover:bg-gray-700');
                    } else {
                        saveButtonLogging.disabled = false;
                        saveButtonLogging.classList.remove('bg-gray-500', 'hover:bg-gray-700');
                        saveButtonLogging.classList.add('bg-blue-500', 'hover:bg-blue-700');
                    }
                };

                radioButtonsLogging.forEach(function(radio) {
                    radio.addEventListener('change', updateButtonStateLogging);
                });

                updateButtonStateLogging(); // Initialize the button state

                // API Save Button Logic and Field Constraints
                const saveButtonApi = document.getElementById('save-button-api');
                const apiFields = document.querySelectorAll('#API input[type="text"]');
                const customerEndpoint = document.getElementById('customer-endpoint');
                const customerToken = document.getElementById('customer-token');
                const productsEndpoint = document.getElementById('products-endpoint');
                const productsToken = document.getElementById('products-token');

                const updateButtonStateApi = () => {
                    let isModified = false;

                    apiFields.forEach(field => {
                        if (field.value !== field.defaultValue) {
                            isModified = true;
                        }
                    });

                    // Update save button state
                    saveButtonApi.disabled = !isModified;
                    if (isModified) {
                        saveButtonApi.classList.remove('bg-gray-500', 'hover:bg-gray-700');
                        saveButtonApi.classList.add('bg-blue-500', 'hover:bg-blue-700');
                    } else {
                        saveButtonApi.classList.remove('bg-blue-500', 'hover:bg-blue-700');
                        saveButtonApi.classList.add('bg-gray-500', 'hover:bg-gray-700');
                    }

                    // Enforce constraints
                    if (customerEndpoint.value !== '' && customerToken.value === '') {
                        customerToken.setCustomValidity(
                            'Customer Token is required if Customer End Point is provided.');
                    } else if (customerEndpoint.value === '' && customerToken.value !== '') {
                        customerEndpoint.setCustomValidity(
                            'Customer End Point is required if Customer Token is provided.');
                    } else {
                        customerToken.setCustomValidity('');
                        customerEndpoint.setCustomValidity('');
                    }

                    if (productsEndpoint.value !== '' && productsToken.value === '') {
                        productsToken.setCustomValidity(
                            'Products Token is required if Products End Point is provided.');
                    } else if (productsEndpoint.value === '' && productsToken.value !== '') {
                        productsEndpoint.setCustomValidity(
                            'Products End Point is required if Products Token is provided.');
                    } else {
                        productsToken.setCustomValidity('');
                        productsEndpoint.setCustomValidity('');
                    }
                };

                apiFields.forEach(function(field) {
                    field.addEventListener('input', updateButtonStateApi);
                });

                updateButtonStateApi(); // Initialize the button state
            });
        </script>
    @endsection
</x-layout>

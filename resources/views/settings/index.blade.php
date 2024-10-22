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
                        <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white px-4 py-2 rounded"
                            id="save-button-logging" disabled>
                            Save Settings
                        </button>
                    </div>
                </fieldset>
            </form>

            <!-- SLA Limit Form -->
            <form action="{{ route('settings.saveSlaLimit') }}" method="POST" class="mb-4" id="sla-limit-form">
                @csrf
                <fieldset class="border border-gray-300 rounded p-4">
                    <legend class="text-lg font-medium">SLA Limit</legend>
                    <div class="mt-4">
                        <label for="sla-limit" class="block text-gray-700">SLA Limit (Hours)</label>
                        <input type="number" name="sla_limit" id="sla-limit" value="{{ $slaLimit }}"
                            class="form-input mt-1 block w-full" min="1">
                        <p class="text-sm text-gray-500">Enter the SLA limit in hours (must be an integer greater than or
                            equal 1).
                        </p>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white px-4 py-2 rounded"
                            id="save-button-sla" disabled>
                            Save SLA Limit
                        </button>
                    </div>
                </fieldset>
            </form>

            <!-- API Settings Form -->
            <form action="{{ route('settings.saveSettings') }}" method="POST" class="mb-4" id="api-settings-form">
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

                    <!-- Buttons Container -->
                    <div class="mt-6 flex space-x-4">
                        <!-- Fetch Customer Groups Button -->
                        <button type="button" id="fetch-customer-groups-btn"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <i class="fas fa-users mr-2"></i> Fetch Customer Groups
                        </button>

                        <!-- Fetch Products Button -->
                        <button type="button" id="fetch-products-btn"
                            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring focus:ring-green-200 focus:ring-opacity-50">
                            <i class="fas fa-box mr-2"></i> Sync Products
                        </button>

                        <!-- Save Settings Button -->
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded"
                            id="save-button-api" disabled>
                            Save Settings
                        </button>
                    </div>
                </fieldset>
            </form>
        </div>

        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <!-- JavaScript to handle button states and AJAX requests -->
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

                // SLA Limit Form Validation
                const slaInput = document.getElementById('sla-limit');
                const saveButtonSla = document.getElementById('save-button-sla');
                let originalSlaValue = "{{ $slaLimit }}";

                const validateSlaInput = () => {
                    const slaValue = slaInput.value;
                    const isValid = /^\d+$/.test(slaValue) && parseInt(slaValue) >= 1;
                    saveButtonSla.disabled = !isValid || slaValue === originalSlaValue;
                    saveButtonSla.classList.toggle('bg-blue-500', isValid && slaValue !== originalSlaValue);
                    saveButtonSla.classList.toggle('hover:bg-blue-700', isValid && slaValue !== originalSlaValue);
                    saveButtonSla.classList.toggle('bg-gray-500', !isValid || slaValue === originalSlaValue);
                    saveButtonSla.classList.toggle('hover:bg-gray-700', !isValid || slaValue === originalSlaValue);
                };

                slaInput.addEventListener('input', validateSlaInput);
                validateSlaInput(); // Initialize the button state

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

                // Fetch Customer Groups Button Logic
                const fetchCustomerGroupsBtn = document.getElementById('fetch-customer-groups-btn');
                fetchCustomerGroupsBtn.addEventListener('click', function() {
                    Swal.fire({
                        title: 'Fetching Customer Groups...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    fetch("{{ route('settings.fetchCustomerGroups') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({})
                        })
                        .then(response => response.json().then(data => ({
                            status: response.status,
                            body: data
                        })))
                        .then(({
                            status,
                            body
                        }) => {
                            if (status === 200) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: body.message || 'Customer groups fetched successfully.',
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: body.message || 'Failed to fetch customer groups.',
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An unexpected error occurred.',
                            });
                            console.error('Error fetching customer groups:', error);
                        });
                });

                // Fetch Products Button Logic
                const fetchProductsBtn = document.getElementById('fetch-products-btn');
                fetchProductsBtn.addEventListener('click', function() {
                    Swal.fire({
                        title: 'Sending Products to API...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    fetch("{{ route('settings.fetchProducts') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({})
                        })
                        .then(response => response.json().then(data => ({
                            status: response.status,
                            body: data
                        })))
                        .then(({
                            status,
                            body
                        }) => {
                            if (status === 200) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: body.message || 'Products sent successfully.',
                                });
                            } else if (status === 204) {
                                Swal.fire({
                                    icon: 'info',
                                    title: 'No Content',
                                    text: body.message || 'No edited products to send.',
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: body.message || 'Failed to send products.',
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An unexpected error occurred.',
                            });
                            console.error('Error sending products:', error);
                        });
                });
            });
        </script>
    @endsection
</x-layout>

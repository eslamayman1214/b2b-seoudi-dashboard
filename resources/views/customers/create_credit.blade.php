<x-layout>
    @section('title', 'Create Customer Credit')

    @section('content')
        <div class="container mx-auto py-8">
            <h1 class="text-2xl font-bold mb-6">Create Customer Credit</h1>

            @if ($errors->any())
                <div class="alert alert-error bg-red-200 text-red-800 p-4 mb-4 rounded-md">
                    <strong>Error!</strong> {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('customer.credit.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label for="customer_id" class="block text-lg font-semibold">Customer ID</label>
                    <input type="number" id="customer_id" name="customer_id" value="{{ $customerId }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md" readonly>
                </div>

                <div class="mb-4">
                    <label for="credit_limit" class="block text-lg font-semibold">Credit Limit</label>
                    <input type="number" id="credit_limit" name="credit_limit" step="0.01"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md" required oninput="validateCreditLimit()">
                </div>

                <div class="flex space-x-4">
                    <button type="submit" id="create_button" disabled
                        class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition duration-300 disabled:bg-gray-400">
                        Create Credit
                    </button>

                    <button type="button" onclick="window.history.back()"
                        class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition duration-300">
                        Back
                    </button>
                </div>
            </form>
        </div>

        <script>
            function validateCreditLimit() {
                const creditLimitInput = document.getElementById('credit_limit');
                const createButton = document.getElementById('create_button');

                const value = creditLimitInput.value.trim();

                // Enable the button only if the input is a valid number and not empty
                if (value !== '' && !isNaN(value)) {
                    createButton.disabled = false;
                } else {
                    createButton.disabled = true;
                }
            }

            // Initial check to ensure button is disabled on page load
            validateCreditLimit();
        </script>
    @endsection
</x-layout>

<x-layout>
    @section('title', 'Customer Credit Details')

    @section('content')
        <div class="container mx-auto py-8">
            <h1 class="text-2xl font-bold mb-6">Customer Credit Details (Customer ID : {{ $creditData['customer_id'] }})</h1>

            <!-- Display Success or Error Messages -->
            @if (session('success'))
                <script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: '{{ session('success') }}',
                        confirmButtonText: 'OK',
                    });
                </script>
            @endif

            @if ($errors->any())
                <script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: '{{ $errors->first() }}',
                        confirmButtonText: 'OK',
                    });
                </script>
            @endif

            <!-- Credit Limit Update Form -->
            <form action="{{ route('customer.credit.update', ['customerId' => $creditData['customer_id']]) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="credit_limit" class="block text-lg font-semibold">Credit Limit</label>
                    <input type="number" id="credit_limit" name="credit_limit"
                        value="{{ old('credit_limit', $creditData['credit_limit']) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md" oninput="checkCreditChange()">
                </div>

                <button type="submit" id="update_button" disabled
                    class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition duration-300 disabled:bg-gray-400">
                    Update Credit
                </button>

                <button type="button" onclick="window.history.back()"
                    class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition duration-300">
                    Back
                </button>
            </form>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const creditLimitInput = document.getElementById('credit_limit');
                const updateButton = document.getElementById('update_button');
                const initialCreditLimit = parseFloat(creditLimitInput.value);

                // Function to check if the credit limit value has changed and is valid
                function checkCreditChange() {
                    const currentCreditLimit = parseFloat(creditLimitInput.value);

                    // Check if the input is a valid number and not empty
                    if (!isNaN(currentCreditLimit) && creditLimitInput.value.trim() !== '' && currentCreditLimit !==
                        initialCreditLimit) {
                        updateButton.disabled = false;
                    } else {
                        updateButton.disabled = true;
                    }
                }

                // Initial check to ensure button is disabled on page load
                checkCreditChange();

                // Attach the function to the input event
                creditLimitInput.addEventListener('input', checkCreditChange);
            });
        </script>
    @endsection
</x-layout>

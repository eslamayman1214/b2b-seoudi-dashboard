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
                    <label for="credit_limit" class="block text-lg font-semibold">Credit Category</label>
                    <select name="credit_limit" id="credit_limit" class="w-full px-4 py-2 border border-gray-300 rounded-md"
                        required>
                        @foreach ($categories as $category)
                            <option value="{{ $category->value }}"
                                {{ $creditData['credit_limit'] == $category->value ? 'selected' : '' }}>
                                {{ ucfirst($category->category_name) }}
                            </option>
                        @endforeach
                    </select>
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
                const creditLimitSelect = document.getElementById('credit_limit');
                const updateButton = document.getElementById('update_button');
                const initialCreditLimit = creditLimitSelect.value;

                // Function to check if the selected value has changed
                function checkCreditChange() {
                    updateButton.disabled = creditLimitSelect.value === initialCreditLimit;
                }

                // Attach the function to the change event
                creditLimitSelect.addEventListener('change', checkCreditChange);

                // Initial check to ensure the button is disabled on page load
                checkCreditChange();
            });
        </script>
    @endsection
</x-layout>

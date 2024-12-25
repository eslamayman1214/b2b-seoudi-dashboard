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
                    <label for="credit_limit" class="block text-lg font-semibold">Credit Category</label>
                    <select name="credit_limit" id="credit_limit" class="w-full px-4 py-2 border border-gray-300 rounded-md"
                        required>
                        <option value="" disabled selected>Select a category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->value }}">{{ ucfirst($category->category_name) }}</option>
                        @endforeach
                    </select>
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
            document.getElementById('credit_limit').addEventListener('change', function() {
                document.getElementById('create_button').disabled = !this.value;
            });
        </script>
    @endsection
</x-layout>

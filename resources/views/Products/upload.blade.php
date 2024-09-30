<x-layout>
    @section('title', 'Upload CSV')
    @section('content')
        <div class="container mx-auto py-12">
            <div class="max-w-lg mx-auto bg-white shadow-lg rounded-lg">
                <div class="px-6 py-4">
                    <h2 class="text-2xl font-semibold text-gray-800">{{ __('Upload CSV File') }}</h2>

                    {{-- Success Message --}}
                    @if (session('success'))
                        <div class="mt-4 p-4 bg-green-100 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- Error Message --}}
                    @if ($errors->any())
                        <div class="mt-4 p-4 bg-red-100 text-red-700 rounded">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Upload Products CSV --}}
                    <form method="POST" action="{{ route('products.upload') }}" enctype="multipart/form-data" class="mt-6">
                        @csrf

                        <div class="mb-4">
                            <label for="csv_file"
                                class="block text-sm font-medium text-gray-700">{{ __('Product CSV File') }}</label>
                            <input id="csv_file" type="file"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm @error('csv_file') border-red-500 @enderror"
                                name="csv_file" required>

                            @error('csv_file')
                                <span class="text-red-500 text-sm mt-1">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                {{ __('Upload Products') }}
                            </button>
                        </div>
                    </form>

                    {{-- Upload Tiers CSV --}}
                    <form method="POST" action="{{ route('tiers.upload') }}" enctype="multipart/form-data" class="mt-6">
                        @csrf

                        <div class="mb-4">
                            <label for="tiers_csv_file"
                                class="block text-sm font-medium text-gray-700">{{ __('Tiers CSV File') }}</label>
                            <input id="tiers_csv_file" type="file"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm @error('tiers_csv_file') border-red-500 @enderror"
                                name="tiers_csv_file" required>

                            @error('tiers_csv_file')
                                <span class="text-red-500 text-sm mt-1">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                                {{ __('Upload Tiers') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endsection
</x-layout>

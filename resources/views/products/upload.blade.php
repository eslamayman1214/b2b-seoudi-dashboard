<x-layout>
    @section('title', 'Upload CSV')
    @section('content')
        <div class="container mx-auto py-12">
            <div class="max-w-lg mx-auto bg-white shadow-lg rounded-lg">
                <div class="px-6 py-4">
                    <h2 class="text-2xl font-semibold text-gray-800 text-center">{{ __('Upload and Download CSV Files') }}
                    </h2>

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
                            <input id="csv_file" type="file"
                                class="block w-auto rounded-md border-gray-300 shadow-sm mb-2 @error('csv_file') border-red-500 @enderror"
                                name="csv_file" required>

                            <div class="flex justify-between items-center">
                                <a href="{{ route('products.template.download') }}"
                                    class="text-blue-600 underline hover:text-blue-800">
                                    {{ __('Download Products Template') }}
                                </a>
                                <button type="submit"
                                    class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    {{ __('Upload Products') }}
                                </button>
                            </div>
                        </div>

                        @error('csv_file')
                            <p class="text-red-500 text-sm mt-1">
                                <strong>{{ $message }}</strong>
                            </p>
                        @enderror
                    </form>

                    {{-- Upload Tiers CSV --}}
                    <form method="POST" action="{{ route('tiers.upload') }}" enctype="multipart/form-data" class="mt-6">
                        @csrf

                        <div class="mb-4">
                            <input id="tiers_csv_file" type="file"
                                class="block w-auto rounded-md border-gray-300 shadow-sm mb-2 @error('tiers_csv_file') border-red-500 @enderror"
                                name="tiers_csv_file" required>

                            <div class="flex justify-between items-center">
                                <a href="{{ route('tiers.template.download') }}"
                                    class="text-green-600 underline hover:text-green-800">
                                    {{ __('Download Tiers Template') }}
                                </a>
                                <button type="submit"
                                    class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                                    {{ __('Upload Tiers') }}
                                </button>
                            </div>
                        </div>

                        @error('tiers_csv_file')
                            <p class="text-red-500 text-sm mt-1">
                                <strong>{{ $message }}</strong>
                            </p>
                        @enderror
                    </form>
                </div>
            </div>
        </div>
    @endsection
</x-layout>

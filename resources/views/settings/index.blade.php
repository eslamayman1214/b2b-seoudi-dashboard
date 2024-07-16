<x-layout>
    @section('title', 'settings')
    @section('content')
        <div class="container mx-auto px-4 py-8">
            <h1 class="text-2xl font-bold mb-4">Settings</h1>

            @if (session('success'))
                <div class="bg-green-500 text-white px-4 py-2 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

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
                        <button type="submit" class="text-white px-4 py-2 rounded" id="save-button" disabled>Save
                            Settings</button>
                    </div>
                </fieldset>
            </form>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const loggingEnabled = @json($loggingEnabled);
                const saveButton = document.getElementById('save-button');
                const radioButtons = document.querySelectorAll('input[name="logging"]');

                const updateButtonState = () => {
                    const selectedValue = document.querySelector('input[name="logging"]:checked').value;
                    if (selectedValue == loggingEnabled) {
                        saveButton.disabled = true;
                        saveButton.classList.remove('bg-blue-500', 'hover:bg-blue-700');
                        saveButton.classList.add('bg-gray-500', 'hover:bg-gray-700');
                    } else {
                        saveButton.disabled = false;
                        saveButton.classList.remove('bg-gray-500', 'hover:bg-gray-700');
                        saveButton.classList.add('bg-blue-500', 'hover:bg-blue-700');
                    }
                };

                radioButtons.forEach(function(radio) {
                    radio.addEventListener('change', updateButtonState);
                });

                updateButtonState(); // Initial state check
            });
        </script>
    @endsection

</x-layout>

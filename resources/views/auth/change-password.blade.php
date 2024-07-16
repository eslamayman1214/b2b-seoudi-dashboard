<x-layout>
    @section('title', 'change-password')
    @section('content')
        <div class="container mx-auto mt-8">
            <div class="max-w-md mx-auto bg-white p-8 border border-gray-300">
                <h2 class="text-2xl font-bold mb-6 text-gray-800 text-center">Change Password</h2>
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="old_password" class="block text-gray-700">Old Password:</label>
                        <input id="old_password" type="password" name="old_password"
                            class="mt-1 block w-full border-2 border-gray-500 rounded-md shadow-sm @error('old_password') border-red-500 @enderror"
                            required>
                        @error('old_password')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="new_password" class="block text-gray-700">New Password:</label>
                        <input id="new_password" type="password" name="new_password"
                            class="mt-1 block w-full border-2 border-gray-500 rounded-md shadow-sm @error('new_password') border-red-500 @enderror"
                            required>
                        @error('new_password')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="new_password_confirmation" class="block text-gray-700">Confirm New Password:</label>
                        <input id="new_password_confirmation" type="password" name="new_password_confirmation"
                            class="mt-1 block w-full border-2 border-gray-500 rounded-md shadow-sm @error('new_password_confirmation') border-red-500 @enderror"
                            required>
                        @error('new_password_confirmation')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <button type="submit"
                            class="w-full bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            Change Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endsection
</x-layout>

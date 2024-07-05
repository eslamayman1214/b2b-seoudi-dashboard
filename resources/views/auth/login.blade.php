<x-layout>
    @section('title', 'login')
    <div class="container mx-auto mt-8">
        <div class="max-w-md mx-auto bg-white p-8 border border-gray-300">
            <h2 class="text-2xl font-bold mb-6 text-gray-800 text-center">Login</h2>
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-4">
                    <label for="email" class="block text-gray-700">Email:</label>
                    <input id="email" type="email" name="email"
                        class="mt-1 block w-full border-2 border-gray-400 rounded-md shadow-sm @error('email') border-red-500 @enderror"
                        value="{{ old('email') }}" required autocomplete="email" autofocus>
                    @error('email')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-4 relative">
                    <label for="password" class="block text-gray-700">Password:</label>
                    <input id="password" type="password" name="password"
                        class="mt-1 block w-full border-2 border-gray-400 rounded-md shadow-sm @error('password') border-red-500 @enderror"
                        required autocomplete="current-password">
                    @error('password')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-4 flex items-center">
                    <input type="checkbox" name="remember" id="remember"
                        class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                    <label for="remember" class="ml-2 block text-gray-900">Remember me</label>
                </div>
                <div class="mb-4">
                    <button type="submit"
                        class="w-full bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        Login
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layout>

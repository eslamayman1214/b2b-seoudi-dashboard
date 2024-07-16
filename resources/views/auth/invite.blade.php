<x-layout>
    @section('title', 'invite user')
    @section('content')
        <div class="container mx-auto mt-8">
            <div class="max-w-md mx-auto bg-white p-8 border border-gray-300 rounded-lg">
                <h2 class="text-2xl font-bold mb-6 text-gray-800 text-center">Invite New User</h2>
                <form method="POST" action="{{ route('invite.send') }}">
                    @csrf
                    <div class="mb-4">
                        <label for="name" class="block text-gray-700">Name:</label>
                        <input id="name" type="text" name="name"
                            class="mt-1 block w-full border-2 border-gray-400 rounded-md shadow-sm" required autofocus>
                    </div>
                    <div class="mb-4">
                        <label for="email" class="block text-gray-700">Email:</label>
                        <input id="email" type="email" name="email"
                            class="mt-1 block w-full border-2 border-gray-400 rounded-md shadow-sm @error('email') border-red-500 @enderror"
                            value="{{ old('email') }}" required autocomplete="email" autofocus>
                        @error('email')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="role" class="block text-gray-700">Role:</label>
                        <select id="role" name="role"
                            class="mt-1 block w-full border-2 border-gray-400 rounded-md shadow-sm" required>
                            <option value="user" selected>User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <button type="submit"
                            class="w-full bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            Send Invitation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endsection

</x-layout>

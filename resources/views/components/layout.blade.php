<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Home</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link rel="icon" href="https://www.stjegypt.com/uploads/723201832723.jpg" type="image/jpg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="bg-gray-100">
    <div class="min-h-full">
        <nav class="bg-green-600">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <img class="h-12 w-12" src="https://www.stjegypt.com/uploads/723201832723.jpg"
                                alt="Seoudi Supermarket">
                        </div>
                        @auth
                            <div class="hidden md:block">
                                <div class="ml-10 flex items-baseline space-x-4">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-gray-700"><b>Welcome back,</b></span>
                                        <span class="font-semibold text-gray-800"><b>{{ Auth::user()->name }}!</b></span>
                                    </div>
                                </div>
                            </div>
                        @endauth
                    </div>
                    <div class="hidden md:block">
                        <div class="ml-4 flex items-center md:ml-6">
                            @auth
                                <a href="/"
                                    class="{{ request()->is('/') ? 'bg-green-800 text-white' : 'text-gray-300 hover:bg-green-700 hover:text-white' }} rounded-md px-3 py-2 text-sm font-medium">
                                    Products board
                                </a>
                                @if (Auth::user()->can('upload', App\Models\Product::class))
                                    <a href="/upload-form"
                                        class="{{ request()->is('upload-form') ? 'bg-green-800 text-white' : 'text-gray-300 hover:bg-green-700 hover:text-white' }} rounded-md px-3 py-2 text-sm font-medium">Upload
                                        Excel</a>
                                @endif
                                <a href="{{ route('users.index') }}"
                                    class="{{ request()->is('users') ? 'bg-green-800 text-white' : 'text-gray-300 hover:bg-green-700 hover:text-white' }} block rounded-md px-3 py-2 text-base font-medium">Users
                                    Management</a>
                                @if (Auth::user()->role === 'super admin')
                                    <a href="/settings"
                                        class="{{ request()->is('settings') ? 'bg-green-800 text-white' : 'text-gray-300 hover:bg-green-700 hover:text-white' }} block rounded-md px-3 py-2 text-base font-medium">Settings</a>
                                @endif
                                <form method="POST" action="/logout" class="inline">
                                    @csrf
                                    <button type="submit"
                                        class="text-gray-300 hover:bg-green-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Logout</button>
                                </form>
                            @endauth
                        </div>
                    </div>
                    <div class="-mr-2 flex md:hidden">
                        <button type="button"
                            class="bg-green-600 inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-green-600 focus:ring-white"
                            aria-controls="mobile-menu" aria-expanded="false">
                            <span class="sr-only">Open main menu</span>
                            <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16m-7 6h7" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="md:hidden" id="mobile-menu">
                <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                    @auth
                        <a href="/"
                            class="{{ request()->is('/') ? 'bg-green-800 text-white' : 'text-gray-300 hover:bg-green-700 hover:text-white' }} block rounded-md px-3 py-2 text-base font-medium">Products
                            board</a>
                        <div class="flex items-center space-x-2">
                            <span class="text-gray-700"><b>Welcome back,</b></span>
                            <span class="font-semibold text-gray-800"><b>{{ Auth::user()->name }}!</b></span>
                        </div>
                        @if (Auth::user()->can('upload', App\Models\Product::class))
                            <a href="/upload-form"
                                class="{{ request()->is('upload-form') ? 'bg-green-800 text-white' : 'text-gray-300 hover:bg-green-700 hover:text-white' }} rounded-md px-3 py-2 text-sm font-medium">Upload
                                Excel</a>
                            <a href="/invite"
                                class="{{ request()->is('invite') ? 'bg-green-800 text-white' : 'text-gray-300 hover:bg-green-700 hover:text-white' }} rounded-md px-3 py-2 text-sm font-medium">Invite
                                a new member</a>
                        @endif
                        <a href="{{ route('password.edit') }}"
                            class="{{ request()->is('change-password') ? 'bg-green-800 text-white' : 'text-gray-300 hover:bg-green-700 hover:text-white' }} block rounded-md px-3 py-2 text-base font-medium">Change
                            Password</a>
                        <form method="POST" action="/logout" class="inline">
                            @csrf
                            <button type="submit"
                                class="text-gray-300 hover:bg-green-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Logout</button>
                        </form>
                    @endauth
                </div>
            </div>
        </nav>
        <main>
            <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </main>
    </div>
</body>

</html>

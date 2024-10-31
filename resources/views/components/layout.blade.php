<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <title>@yield('title', 'Home')</title>

    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-08Y4ZJ2TJX"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());
        gtag('config', 'G-08Y4ZJ2TJX');
    </script>
    <!-- End Google Analytics -->

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    <link rel="icon" href="https://www.stjegypt.com/uploads/723201832723.jpg" type="image/jpg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="bg-gray-100">
    <div class="min-h-full">
        <nav class="bg-green-600">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <img class="h-10 w-10" src="https://www.stjegypt.com/uploads/723201832723.jpg"
                                alt="Seoudi Supermarket">
                        </div>
                        @auth
                            <div class="hidden md:flex items-center space-x-4">
                                <span class="text-gray-200 font-semibold">Welcome back, {{ Auth::user()->name }}!</span>
                            </div>
                        @endauth
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden md:flex space-x-2">
                        @auth
                            <a href="/"
                                class="{{ request()->is('/') ? 'bg-green-800 text-white' : 'text-gray-300 hover:bg-green-700 hover:text-white' }} px-3 py-2 rounded-md text-sm font-medium">Products
                                Board</a>
                            @if (Auth::user()->can('upload', App\Models\Product::class))
                                <a href="/upload-form"
                                    class="{{ request()->is('upload-form') ? 'bg-green-800 text-white' : 'text-gray-300 hover:bg-green-700 hover:text-white' }} px-3 py-2 rounded-md text-sm font-medium">Upload
                                    Excel</a>
                            @endif
                            <a href="{{ route('users.index') }}"
                                class="{{ request()->is('users') ? 'bg-green-800 text-white' : 'text-gray-300 hover:bg-green-700 hover:text-white' }} px-3 py-2 rounded-md text-sm font-medium">Users
                                Management</a>
                            <a href="/tickets"
                                class="{{ request()->is('tickets') ? 'bg-green-800 text-white' : 'text-gray-300 hover:bg-green-700 hover:text-white' }} px-3 py-2 rounded-md text-sm font-medium">Tickets</a>
                            <a href="{{ route('customers.index') }}"
                                class="{{ request()->is('customers') ? 'bg-green-800 text-white' : 'text-gray-300 hover:bg-green-700 hover:text-white' }} px-3 py-2 rounded-md text-sm font-medium">Customers
                                Management</a>
                            <a href="{{ route('quotations.index') }}"
                                class="{{ request()->is('quotations') ? 'bg-green-800 text-white' : 'text-gray-300 hover:bg-green-700 hover:text-white' }} px-3 py-2 rounded-md text-sm font-medium">Quotations</a>
                            @if (Auth::user()->role === 'super admin')
                                <a href="/settings"
                                    class="{{ request()->is('settings') ? 'bg-green-800 text-white' : 'text-gray-300 hover:bg-green-700 hover:text-white' }} px-3 py-2 rounded-md text-sm font-medium">Settings</a>
                            @endif
                            <form method="POST" action="/logout" class="inline">
                                @csrf
                                <button type="submit"
                                    class="text-gray-300 hover:bg-green-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Logout</button>
                            </form>
                        @endauth
                    </div>

                    <!-- Mobile Menu Button -->
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

            <!-- Mobile Menu -->
            <div class="md:hidden" id="mobile-menu">
                <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                    @auth
                        <a href="/"
                            class="{{ request()->is('/') ? 'bg-green-800 text-white' : 'text-gray-300 hover:bg-green-700 hover:text-white' }} block px-3 py-2 rounded-md text-base font-medium">Products
                            Board</a>
                        @if (Auth::user()->can('upload', App\Models\Product::class))
                            <a href="/upload-form"
                                class="{{ request()->is('upload-form') ? 'bg-green-800 text-white' : 'text-gray-300 hover:bg-green-700 hover:text-white' }} block px-3 py-2 rounded-md text-base font-medium">Upload
                                Excel</a>
                        @endif
                        <a href="{{ route('users.index') }}"
                            class="{{ request()->is('users') ? 'bg-green-800 text-white' : 'text-gray-300 hover:bg-green-700 hover:text-white' }} block px-3 py-2 rounded-md text-base font-medium">Users
                            Management</a>
                        <a href="/tickets"
                            class="{{ request()->is('tickets') ? 'bg-green-800 text-white' : 'text-gray-300 hover:bg-green-700 hover:text-white' }} block px-3 py-2 rounded-md text-base font-medium">Tickets</a>
                        <a href="{{ route('customers.index') }}"
                            class="{{ request()->is('customers') ? 'bg-green-800 text-white' : 'text-gray-300 hover:bg-green-700 hover:text-white' }} block px-3 py-2 rounded-md text-base font-medium">Customers
                            Management</a>
                        <a href="{{ route('quotations.index') }}"
                            class="{{ request()->is('quotations') ? 'bg-green-800 text-white' : 'text-gray-300 hover:bg-green-700 hover:text-white' }} block px-3 py-2 rounded-md text-base font-medium">Quotations</a>
                        @if (Auth::user()->role === 'super admin')
                            <a href="/settings"
                                class="{{ request()->is('settings') ? 'bg-green-800 text-white' : 'text-gray-300 hover:bg-green-700 hover:text-white' }} block px-3 py-2 rounded-md text-base font-medium">Settings</a>
                        @endif
                        <form method="POST" action="/logout" class="inline">
                            @csrf
                            <button type="submit"
                                class="text-gray-300 hover:bg-green-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Logout</button>
                        </form>
                    @endauth
                </div>
            </div>
        </nav>
        <main>
            <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
                @yield('content')
            </div>
        </main>
    </div>
</body>

</html>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Home</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link rel="icon" href="https://www.stjegypt.com/uploads/723201832723.jpg" type="image/jpg">
</head>

<body class="bg-gray-100">
    <div class="min-h-full">
        <nav class="bg-green-600">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <img class="h-12 w-12" src="https://www.stjegypt.com/uploads/723201832723.jpg" alt="Seoudi Supermarket">
                        </div>
                        <div class="hidden md:block">
                            <div class="ml-10 flex items-baseline space-x-4">
                                <a href="/" class="{{ request()->is('/') ? 'bg-green-800 text-white' : 'text-gray-300 hover:bg-green-700 hover:text-white' }} rounded-md px-3 py-2 text-sm font-medium">Products board</a>
                         </div>
                        </div>
                    </div>
                    <div class="hidden md:block">
                        <div class="ml-4 flex items-center md:ml-6">
                            @guest
                                <a href="/upload-form" class="{{ request()->is('upload-file') ? 'bg-green-800 text-white' : 'text-gray-300 hover:bg-green-700 hover:text-white' }} rounded-md px-3 py-2 text-sm font-medium">Upload Excel </a>
                                <a href="/register" class="{{ request()->is('register') ? 'bg-green-800 text-white' : 'text-gray-300 hover:bg-green-700 hover:text-white' }} rounded-md px-3 py-2 text-sm font-medium">Invite a new User</a>
                            @endguest

                            @auth
                                <form method="POST" action="/logout" class="inline">
                                    @csrf
                                    <button type="submit" class="text-gray-300 hover:bg-green-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Logout</button>
                                </form>
                            @endauth
                        </div>
                    </div>
                    <div class="-mr-2 flex md:hidden">
                        <button type="button" class="bg-green-600 inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-green-600 focus:ring-white" aria-controls="mobile-menu" aria-expanded="false">
                            <span class="sr-only">Open main menu</span>
                            <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="md:hidden" id="mobile-menu">
                <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                    <a href="/" class="{{ request()->is('/') ? 'bg-green-800 text-white' : 'text-gray-300 hover:bg-green-700 hover:text-white' }} block rounded-md px-3 py-2 text-base font-medium">Products board</a>
                           @guest
                        <a href="/upload-form" class="{{ request()->is('register') ? 'bg-green-800 text-white' : 'text-gray-300 hover:bg-green-700 hover:text-white' }} block rounded-md px-3 py-2 text-base font-medium">Uplaod Excel Sheet</a>
                        <a href="/register" class="{{ request()->is('register') ? 'bg-green-800 text-white' : 'text-gray-300 hover:bg-green-700 hover:text-white' }} rounded-md px-3 py-2 text-sm font-medium">Invite a new User</a>
                    @endguest
                    @auth
                        <form method="POST" action="/logout" class="inline">
                            @csrf
                            <button type="submit" class="w-full text-left text-gray-300 hover:bg-green-700 hover:text-white block rounded-md px-3 py-2 text-base font-medium">Logout</button>
                        </form>
                    @endauth
                </div>
            </div>
        </nav>

        @auth
        <header class="bg-white shadow">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 sm:flex sm:justify-between">
                <h1 class="text-3xl font-bold tracking-tight text-gray-900">{{ $heading }}</h1>
                <a href="/jobs/create" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-white bg-green-500 border border-transparent rounded-md leading-5 shadow-sm hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9 9a1 1 0 011-1h1V7a1 1 0 112 0v1h1a1 1 0 110 2h-1v1a1 1 0 11-2 0V9H10a1 1 0 01-1-1z" clip-rule="evenodd" />
                    </svg>
                    Add Job
                </a>
            </div>
        </header>
        @endauth

        <main>
            <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </main>
    </div>
</body>

</html>

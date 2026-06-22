<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk - ClassSync</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="h-full bg-[#F5F4F0] dark:bg-gray-900 transition-colors duration-300">
    <div class="flex min-h-screen flex-1">
        
        <div class="flex flex-1 flex-col justify-center px-4 py-12 sm:px-6 lg:flex-none lg:px-20 xl:px-28">
            <div class="mx-auto w-full max-w-sm lg:w-96">
                
                <div>
                    <h1 class="text-gray-900 dark:text-white font-bold text-xl">Welcome to ClassSync</h1>
                    <h2 class="mt-1 text-2xl font-semibold leading-9 tracking-tight text-gray-900 dark:text-white">Masuk ke akun Anda</h2>
                    <p class="mt-1 text-sm leading-6 text-gray-500 dark:text-gray-400">
                        Belum punya akun?
                        <a href="{{ route('register') }}" class="font-medium text-[#4F46E5] dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 transition-colors">Daftar sekarang gratis</a>
                    </p>
                </div>

                <div class="mt-8">
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf

                        <div>
                            <label for="email" class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-200">Email address</label>
                            <div class="mt-2">
                                <input id="email" name="email" type="email" autocomplete="email" required autofocus
                                    class="block w-full rounded-md border-0 py-2.5 px-3 bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-700 placeholder:text-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-inset focus:ring-[#4F46E5] dark:focus:ring-indigo-500 sm:text-sm sm:leading-6 transition-all"
                                    placeholder="mahasiswa@kampus.ac.id">
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-200">Password</label>
                            <div class="mt-2">
                                <input id="password" name="password" type="password" autocomplete="current-password" required
                                    class="block w-full rounded-md border-0 py-2.5 px-3 bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-700 placeholder:text-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-inset focus:ring-[#4F46E5] dark:focus:ring-indigo-500 sm:text-sm sm:leading-6 transition-all"
                                    placeholder="••••••••">
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <input id="remember_me" name="remember" type="checkbox"
                                    class="h-4 w-4 rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-[#4F46E5] dark:text-indigo-500 focus:ring-[#4F46E5] dark:focus:ring-indigo-500">
                                <label for="remember_me" class="ml-2 block text-sm leading-6 text-gray-900 dark:text-gray-300">Remember me</label>
                            </div>

                            <div class="text-sm leading-6">
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="font-medium text-[#4F46E5] dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 transition-colors">Forgot password?</a>
                                @endif
                            </div>
                        </div>

                        <div>
                            <button type="submit"
                                class="flex w-full justify-center rounded-md bg-[#4F46E5] px-3 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 dark:hover:bg-indigo-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all active:scale-[0.98]">
                                Sign in
                            </button>
                        </div>
                    </form>

                    <div class="mt-8 relative">
                        <div class="absolute inset-0 flex items-center" aria-hidden="true">
                            <div class="w-full border-t border-gray-200 dark:border-gray-700 transition-colors duration-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm font-medium leading-6">
                            <span class="bg-[#F5F4F0] dark:bg-gray-900 px-6 text-gray-500 dark:text-gray-400 transition-colors duration-300">Or continue with</span>
                        </div>
                    </div>

                    <div class="mt-6">
                        <a href="/auth/google/redirect"
                            class="flex w-full items-center justify-center gap-3 rounded-md bg-white dark:bg-gray-800 px-3 py-2.5 text-sm font-semibold text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all active:scale-[0.98]">
                            <svg class="h-5 w-5" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                            </svg>
                            <span class="text-sm font-medium leading-6">Google</span>
                        </a>
                    </div>

                </div>
            </div>
        </div>

        <div class="relative hidden w-0 flex-1 lg:block bg-cover bg-center bg-no-repeat" 
             style="background-image: url('https://images.pexels.com/photos/8085269/pexels-photo-8085269.jpeg');">
            
            <div class="absolute inset-0 bg-gray-900/10 dark:bg-gray-900/60 mix-blend-multiply transition-colors duration-300"></div>
            
        </div>
    
    </div>

</body>
</html>
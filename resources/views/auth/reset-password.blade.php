<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-white">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Buat Sandi Baru - ClassSync</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="h-full bg-white">

    <div class="flex min-h-screen flex-1">
        
        <div class="flex flex-1 flex-col justify-center px-4 py-12 sm:px-6 lg:flex-none lg:px-20 xl:px-28">
            <div class="mx-auto w-full max-w-sm lg:w-96">
                
                <div>
                    <div class="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center shadow-sm mb-6">
                        <i class="fa-solid fa-lock text-white text-sm"></i>
                    </div>
                    <h2 class="text-2xl font-semibold leading-9 tracking-tight text-gray-900">Buat Sandi Baru</h2>
                    <p class="mt-2 text-sm leading-6 text-gray-500">
                        Amankan akunmu. Silakan masukkan kata sandi baru untuk akun ClassSync Anda.
                    </p>
                </div>

                <div class="mt-8">
                    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
                        @csrf

                        <input type="hidden" name="token" value="{{ $request->route('token') }}">

                        <div>
                            <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email address</label>
                            <div class="mt-2">
                                <input id="email" name="email" type="email" value="{{ old('email', $request->email) }}" required autofocus
                                    class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-[#4F46E5] sm:text-sm sm:leading-6 bg-gray-50" readonly>
                                <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-xs" />
                            </div>
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Sandi Baru</label>
                            <div class="mt-2">
                                <input id="password" name="password" type="password" required
                                    class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-[#4F46E5] sm:text-sm sm:leading-6 transition-all"
                                    placeholder="••••••••">
                                <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500 text-xs" />
                            </div>
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium leading-6 text-gray-900">Konfirmasi Sandi Baru</label>
                            <div class="mt-2">
                                <input id="password_confirmation" name="password_confirmation" type="password" required
                                    class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-[#4F46E5] sm:text-sm sm:leading-6 transition-all"
                                    placeholder="••••••••">
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-500 text-xs" />
                            </div>
                        </div>

                        <div class="pt-2">
                            <button type="submit"
                                class="flex w-full justify-center rounded-md bg-[#4F46E5] px-3 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all active:scale-[0.98]">
                                Simpan & Masuk
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="relative hidden w-0 flex-1 lg:block bg-cover bg-center bg-no-repeat" 
             style="background-image: url('https://images.unsplash.com/photo-1555421689-491a97ff2040?ixlib=rb-1.2.1&auto=format&fit=crop&w=1908&q=80');">
            <div class="absolute inset-0 bg-gray-900/20 mix-blend-multiply"></div>
        </div>
        
    </div>

</body>
</html>
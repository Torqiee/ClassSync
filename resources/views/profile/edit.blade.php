<x-app-layout>
<div class="min-h-screen bg-[#F5F4F0] font-sans pb-12">

    {{-- Header --}}
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-6">
        <div>
            <p class="text-xs text-gray-400 uppercase tracking-widest mb-1">Pengaturan</p>
            <h1 class="text-2xl sm:text-3xl font-medium text-gray-900 tracking-tight">Profil Pengguna</h1>
            <p class="mt-1 text-sm text-gray-400">Kelola informasi pribadi dan keamanan akunmu.</p>
        </div>

        {{-- Flash Messages --}}
        <div class="mt-5 space-y-2">
            @if (session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-2.5 rounded-lg flex items-center gap-2.5 text-sm font-medium shadow-sm">
                <i class="fa-solid fa-circle-check text-emerald-600 shrink-0"></i>
                {{ session('success') }}
            </div>
            @endif
            @if (session('success_password'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-2.5 rounded-lg flex items-center gap-2.5 text-sm font-medium shadow-sm">
                <i class="fa-solid fa-circle-check text-emerald-600 shrink-0"></i>
                {{ session('success_password') }}
            </div>
            @endif
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- CARD 1: Informasi Profil -->
        <div class="bg-white border border-gray-200 rounded-xl p-6 sm:p-8 shadow-sm">
            <header class="border-b border-gray-100 pb-4 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <i class="fa-regular fa-id-badge text-gray-400"></i>
                    Informasi Dasar
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Perbarui nama tampilan dan alamat email akunmu.
                </p>
            </header>

            <form method="post" action="{{ route('profile.update') }}" class="space-y-6 max-w-xl">
                @csrf
                @method('patch')

                <!-- Input Nama -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm px-4 py-2.5 bg-white transition-colors">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1.5 font-medium"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                <!-- Input Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm px-4 py-2.5 transition-colors {{ $user->google_id ? 'bg-gray-50 text-gray-500 border-gray-200' : 'bg-white' }}"
                        {{ $user->google_id ? 'readonly' : '' }}>
                    
                    @error('email')
                        <p class="text-red-500 text-xs mt-1.5 font-medium"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                    @enderror

                    @if($user->google_id)
                        <div class="mt-3 flex items-start gap-2 bg-amber-50 text-amber-700 p-3 rounded-lg border border-amber-100">
                            <i class="fa-brands fa-google text-amber-600 mt-0.5"></i>
                            <p class="text-xs font-medium leading-relaxed">
                                Akun ini terhubung dengan Google. Alamat email diatur secara otomatis oleh Google dan tidak dapat diubah di sini.
                            </p>
                        </div>
                    @endif
                </div>

                <div class="pt-4 flex items-center">
                    <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-all shadow-sm shadow-purple-200 flex items-center gap-2 active:scale-[0.98]">
                        <i class="fa-solid fa-floppy-disk text-xs"></i>
                        Simpan Profil
                    </button>
                </div>
            </form>
        </div>

        <!-- CARD 2: Ganti Password -->
        @if(!$user->google_id)
        <div class="bg-white border border-gray-200 rounded-xl p-6 sm:p-8 shadow-sm">
            <header class="border-b border-gray-100 pb-4 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <i class="fa-solid fa-shield-halved text-gray-400"></i>
                    Ubah Kata Sandi
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Pastikan akunmu menggunakan kata sandi yang panjang dan acak agar tetap aman.
                </p>
            </header>

            <form method="post" action="{{ route('password.update') }}" class="space-y-6 max-w-xl">
                @csrf
                @method('put')

                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1.5">Kata Sandi Saat Ini</label>
                    <input id="current_password" name="current_password" type="password" required
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm px-4 py-2.5 bg-white transition-colors">
                    @error('current_password')
                        <p class="text-red-500 text-xs mt-1.5 font-medium"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Kata Sandi Baru</label>
                    <input id="password" name="password" type="password" required
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm px-4 py-2.5 bg-white transition-colors">
                    @error('password')
                        <p class="text-red-500 text-xs mt-1.5 font-medium"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">Konfirmasi Kata Sandi Baru</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm px-4 py-2.5 bg-white transition-colors">
                </div>

                <div class="pt-4 flex items-center">
                    <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-all shadow-sm flex items-center gap-2 active:scale-[0.98]">
                        <i class="fa-solid fa-key text-xs"></i>
                        Perbarui Sandi
                    </button>
                </div>
            </form>
        </div>
        @endif

    </div>

</div>
</x-app-layout>
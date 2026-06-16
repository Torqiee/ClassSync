<x-app-layout>
    <!-- Bungkus seluruh halaman dengan Form -->
    <form action="{{ route('activities.store') }}" method="POST" x-data="{ tipe: 'tetap' }">
        @csrf
        <div class="min-h-screen bg-[#F5F4F0] font-sans pb-12">
            
            <!-- Header Section (Senada dengan Dashboard) -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-6">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4">
                    <div class="flex items-center gap-4">
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-widest mb-1">Manajemen Jadwal</p>
                            <h1 class="text-2xl sm:text-3xl font-medium text-gray-900 tracking-tight">Tambah Aktivitas</h1>
                            <p class="mt-1 text-sm text-gray-400">Masukkan jadwal baru atau tugas yang perlu diselesaikan.</p>
                        </div>
                    </div>
                </div>
                {{-- Flash Messages --}}
                <div class="mt-2 space-y-2">
                    @if (session('success'))
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-2.5 rounded-lg flex items-center gap-2.5 text-sm font-medium">
                        <i class="fa-solid fa-circle-check text-emerald-600 shrink-0"></i>
                        {{ session('success') }}
                    </div>
                    @endif
                    @if (session('info'))
                    <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-2.5 rounded-lg flex items-center gap-2.5 text-sm font-medium">
                        <i class="fa-solid fa-circle-info text-blue-600 shrink-0"></i>
                        {{ session('info') }}
                    </div>
                    @endif
                    @if (session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-2.5 rounded-lg flex items-center gap-2.5 text-sm font-medium">
                        <i class="fa-solid fa-triangle-exclamation text-red-600 shrink-0"></i>
                        {{ session('error') }}
                    </div>
                    @endif
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

                <!-- Card 1: Informasi Dasar -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 bg-white">
                        <h2 class="text-base font-semibold text-gray-900">Informasi Dasar</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Detail utama dari kegiatan atau tugasmu.</p>
                    </div>
                    
                    <div class="p-5 sm:p-6 space-y-6">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Nama Kegiatan</label>
                            <input type="text" name="nama_kegiatan" required placeholder="Contoh: Kuliah KKA / Nugas Figma" 
                                class="w-full bg-white border border-gray-200 px-4 py-2.5 text-gray-900 text-sm rounded-lg focus:ring-gray-900 focus:border-gray-900 transition-colors placeholder-gray-300 shadow-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Kategori</label>
                            <select name="kategori" class="w-full bg-white border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-gray-900 focus:border-gray-900 block p-2.5 shadow-sm">
                                <option value="Kuliah">🎓 Kuliah</option>
                                <option value="Organisasi">💼 Organisasi</option>
                                <option value="UKM">🤖 UKM</option>
                                <option value="Tugas">📝 Tugas</option>
                                <option value="Lainnya">📌 Lainnya</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Pengaturan Waktu & AI -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 bg-white">
                        <h2 class="text-base font-semibold text-gray-900">Pengaturan Waktu</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Pilih apakah ini jadwal tetap atau tugas yang dicarikan waktunya oleh Sistem.</p>
                    </div>

                    <!-- Tipe Selector (Segmented Control ala iOS) -->
                    <div class="p-5 sm:p-6 border-b border-gray-100 bg-gray-50/30">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Sifat Jadwal</label>
                        <div class="flex p-1 bg-gray-100/80 rounded-xl flex-col sm:flex-row gap-1 border border-gray-200/60">
                            <button type="button" @click="tipe = 'tetap'" 
                                :class="tipe === 'tetap' ? 'bg-white shadow-sm ring-1 ring-black/5 text-gray-900 font-medium' : 'text-gray-500 hover:text-gray-700 font-medium'" 
                                class="flex-1 py-2 text-sm rounded-lg transition-all flex items-center justify-center gap-2">
                                <i class="fa-solid fa-thumbtack" :class="tipe === 'tetap' ? 'text-violet-500' : ''"></i> Jadwal Tetap
                            </button>
                            <button type="button" @click="tipe = 'fleksibel'" 
                                :class="tipe === 'fleksibel' ? 'bg-white shadow-sm ring-1 ring-black/5 text-gray-900 font-medium' : 'text-gray-500 hover:text-gray-700 font-medium'" 
                                class="flex-1 py-2 text-sm rounded-lg transition-all flex items-center justify-center gap-2">
                                <i class="fa-solid fa-robot" :class="tipe === 'fleksibel' ? 'text-amber-500' : ''"></i> Tugas Fleksibel
                            </button>
                        </div>
                        <!-- Input hidden untuk dikirim ke backend -->
                        <input type="hidden" name="tipe" x-model="tipe">
                    </div>

                    <!-- Input Fields: Jadwal Tetap -->
                    <div x-show="tipe === 'tetap'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="p-5 sm:p-6 space-y-6">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Hari Pelaksanaan</label>
                            <select name="hari" class="w-full bg-white border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-gray-900 focus:border-gray-900 block p-2.5 shadow-sm">
                                <option value="Senin">Senin</option>
                                <option value="Selasa">Selasa</option>
                                <option value="Rabu">Rabu</option>
                                <option value="Kamis">Kamis</option>
                                <option value="Jumat">Jumat</option>
                            </select>
                        </div>
                        <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                            <div class="flex-1">
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Jam Mulai</label>
                                <input type="time" name="jam_mulai" class="w-full bg-white border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-gray-900 focus:border-gray-900 block p-2.5 shadow-sm">
                            </div>
                            <div class="flex-1">
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Jam Selesai</label>
                                <input type="time" name="jam_selesai" class="w-full bg-white border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-gray-900 focus:border-gray-900 block p-2.5 shadow-sm">
                            </div>
                        </div>
                    </div>

                    <!-- Input Fields: Jadwal Fleksibel -->
                    <div x-cloak x-show="tipe === 'fleksibel'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="p-5 sm:p-6 space-y-6">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Batas Akhir (Deadline)</label>
                            <select name="deadline" class="w-full bg-white border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-gray-900 focus:border-gray-900 block p-2.5 shadow-sm">
                                <option value="Senin">Senin</option>
                                <option value="Selasa">Selasa</option>
                                <option value="Rabu">Rabu</option>
                                <option value="Kamis">Kamis</option>
                                <option value="Jumat">Jumat</option>
                            </select>
                            <p class="text-[11px] text-amber-700 mt-2 font-medium inline-flex items-center">
                                <i class="fa-solid fa-circle-info mr-2"></i> Sistem akan mencarikan waktu kosong sebelum hari ini berakhir.
                            </p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Estimasi Durasi (Jam)</label>
                            <input type="number" min="1" max="10" name="durasi" placeholder="Misal: 2" class="w-full bg-white border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-gray-900 focus:border-gray-900 block p-2.5 shadow-sm">
                        </div>
                    </div>
                </div>
                    <button type="submit" class="bg-gray-900 hover:bg-gray-800 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 shadow-sm active:scale-[0.98]">
                        <i class="fa-solid fa-floppy-disk text-xs"></i>
                        Simpan Aktivitas
                    </button>
            </div>
        </div>
    </form>
</x-app-layout>
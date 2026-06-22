<x-app-layout>
    <form action="{{ route('activities.store') }}" method="POST" x-data="{ tipe: 'tetap' }">
        @csrf
        <div class="min-h-screen bg-[#F5F4F0] dark:bg-gray-900 font-sans pb-12 transition-colors duration-300">
            
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-6">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4">
                    <div class="flex items-center gap-4">
                        <div>
                            <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1">Manajemen Jadwal</p>
                            <h1 class="text-2xl sm:text-3xl font-medium text-gray-900 dark:text-white tracking-tight">Tambah Aktivitas</h1>
                            <p class="mt-1 text-sm text-gray-400 dark:text-gray-400">Masukkan jadwal baru atau tugas yang perlu diselesaikan.</p>
                        </div>
                    </div>
                </div>
                {{-- Flash Messages --}}
                <div class="mt-2 space-y-2">
                    @if (session('success'))
                    <div class="bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800/50 text-emerald-800 dark:text-emerald-400 px-4 py-2.5 rounded-lg flex items-center gap-2.5 text-sm font-medium transition-colors">
                        <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400 shrink-0"></i>
                        {{ session('success') }}
                    </div>
                    @endif
                    @if (session('info'))
                    <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800/50 text-blue-800 dark:text-blue-400 px-4 py-2.5 rounded-lg flex items-center gap-2.5 text-sm font-medium transition-colors">
                        <i class="fa-solid fa-circle-info text-blue-600 dark:text-blue-400 shrink-0"></i>
                        {{ session('info') }}
                    </div>
                    @endif
                    @if (session('error'))
                    <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800/50 text-red-800 dark:text-red-400 px-4 py-2.5 rounded-lg flex items-center gap-2.5 text-sm font-medium transition-colors">
                        <i class="fa-solid fa-triangle-exclamation text-red-600 dark:text-red-400 shrink-0"></i>
                        {{ session('error') }}
                    </div>
                    @endif
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm overflow-hidden transition-colors duration-300">
                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 transition-colors">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Informasi Dasar</h2>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Detail utama dari kegiatan atau tugasmu.</p>
                    </div>
                    
                    <div class="p-5 sm:p-6 space-y-6">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Nama Kegiatan</label>
                            <input type="text" name="nama_kegiatan" required placeholder="Contoh: Kuliah KKA / Nugas Figma" 
                                class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 px-4 py-2.5 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition-colors placeholder-gray-300 dark:placeholder-gray-600 shadow-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Kategori</label>
                            <select name="kategori" class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 shadow-sm transition-colors">
                                <option value="Kuliah">🎓 Kuliah</option>
                                <option value="Organisasi">💼 Organisasi</option>
                                <option value="UKM">🤖 UKM</option>
                                <option value="Tugas">📝 Tugas</option>
                                <option value="Lainnya">📌 Lainnya</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm overflow-hidden transition-colors duration-300">
                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 transition-colors">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Pengaturan Waktu</h2>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Pilih apakah ini jadwal tetap atau tugas yang dicarikan waktunya oleh Sistem.</p>
                    </div>

                    <div class="p-5 sm:p-6 border-b border-gray-100 dark:border-gray-700 bg-gray-50/30 dark:bg-gray-800/50 transition-colors">
                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Sifat Jadwal</label>
                        <div class="flex p-1 bg-gray-100/80 dark:bg-gray-900 rounded-xl flex-col sm:flex-row gap-1 border border-gray-200/60 dark:border-gray-700">
                            <button type="button" @click="tipe = 'tetap'" 
                                :class="tipe === 'tetap' ? 'bg-white dark:bg-gray-700 shadow-sm ring-1 ring-black/5 dark:ring-white/5 text-gray-900 dark:text-white font-medium' : 'text-gray-500 dark:text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-medium'" 
                                class="flex-1 py-2 text-sm rounded-lg transition-all flex items-center justify-center gap-2">
                                <i class="fa-solid fa-thumbtack" :class="tipe === 'tetap' ? 'text-violet-500 dark:text-violet-400' : ''"></i> Jadwal Tetap
                            </button>
                            <button type="button" @click="tipe = 'fleksibel'" 
                                :class="tipe === 'fleksibel' ? 'bg-white dark:bg-gray-700 shadow-sm ring-1 ring-black/5 dark:ring-white/5 text-gray-900 dark:text-white font-medium' : 'text-gray-500 dark:text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-medium'" 
                                class="flex-1 py-2 text-sm rounded-lg transition-all flex items-center justify-center gap-2">
                                <i class="fa-solid fa-robot" :class="tipe === 'fleksibel' ? 'text-amber-500 dark:text-amber-400' : ''"></i> Tugas Fleksibel
                            </button>
                        </div>
                        <input type="hidden" name="tipe" x-model="tipe">
                    </div>

                    <div x-show="tipe === 'tetap'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="p-5 sm:p-6 space-y-6">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Hari Pelaksanaan</label>
                            <select name="hari" class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 shadow-sm transition-colors">
                                <option value="Senin">Senin</option>
                                <option value="Selasa">Selasa</option>
                                <option value="Rabu">Rabu</option>
                                <option value="Kamis">Kamis</option>
                                <option value="Jumat">Jumat</option>
                            </select>
                        </div>
                        <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                            <div class="flex-1">
                                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Jam Mulai</label>
                                <input type="time" name="jam_mulai" class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 shadow-sm transition-colors">
                            </div>
                            <div class="flex-1">
                                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Jam Selesai</label>
                                <input type="time" name="jam_selesai" class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 shadow-sm transition-colors">
                            </div>
                        </div>
                    </div>

                    <div x-cloak x-show="tipe === 'fleksibel'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="p-5 sm:p-6 space-y-6">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Batas Akhir (Deadline)</label>
                            <select name="deadline" class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 shadow-sm transition-colors">
                                <option value="Senin">Senin</option>
                                <option value="Selasa">Selasa</option>
                                <option value="Rabu">Rabu</option>
                                <option value="Kamis">Kamis</option>
                                <option value="Jumat">Jumat</option>
                            </select>
                            <p class="text-[11px] text-amber-700 dark:text-amber-500 mt-2 font-medium inline-flex items-center">
                                <i class="fa-solid fa-circle-info mr-2"></i> Sistem akan mencarikan waktu kosong sebelum hari ini berakhir.
                            </p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Estimasi Durasi (Jam)</label>
                            <input type="number" min="1" max="10" name="durasi" placeholder="Misal: 2" class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 shadow-sm transition-colors placeholder-gray-300 dark:placeholder-gray-600">
                        </div>
                    </div>
                </div>
                <button type="submit" class="bg-gray-900 dark:bg-white hover:bg-gray-800 dark:hover:bg-gray-100 text-white dark:text-gray-900 px-5 py-2.5 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 shadow-sm active:scale-[0.98]">
                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                    Simpan Aktivitas
                </button>
            </div>
        </div>
    </form>
</x-app-layout>
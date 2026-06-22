<x-app-layout>
    <form action="{{ route('settings.update') }}" method="POST">
        @csrf
        <div class="min-h-screen bg-[#F5F4F0] dark:bg-gray-900 font-sans pb-12 transition-colors duration-300">
            
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-6">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4">
                    <div>
                        <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1">Pengaturan</p>
                        <h1 class="text-2xl sm:text-3xl font-medium text-gray-900 dark:text-white tracking-tight">Aturan & Preferensi AI</h1>
                        <p class="mt-1 text-sm text-gray-400 dark:text-gray-400">Kustomisasi bagaimana algoritma menyusun jadwalmu.</p>
                    </div>
                </div>

                <div class="mt-4">
                    @if (session('success'))
                        <div class="bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800/50 text-emerald-800 dark:text-emerald-400 px-4 py-2.5 rounded-lg flex items-center gap-2.5 text-sm font-medium shadow-sm transition-colors duration-300">
                            <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400"></i>
                            {{ session('success') }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm overflow-hidden transition-colors duration-300">
                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 transition-colors">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Constraint Utama</h2>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Aturan mutlak yang harus dipatuhi oleh algoritma AI saat menyusun kalender.</p>
                    </div>
                    
                    <div class="flex flex-col divide-y divide-gray-100 dark:divide-gray-700">
                        
                        <label class="flex items-center justify-between p-5 sm:p-6 hover:bg-gray-50/50 dark:hover:bg-gray-700/50 cursor-pointer transition-colors w-full group">
                            <div class="flex items-center pr-4">
                                <div class="w-10 h-10 rounded-full bg-purple-50 dark:bg-purple-900/30 flex items-center justify-center mr-4 shrink-0 group-hover:bg-purple-100 dark:group-hover:bg-purple-900/50 transition-colors">
                                    <i class="fa-solid fa-graduation-cap text-purple-600 dark:text-purple-400"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">Tidak boleh bentrok dengan kuliah</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Kegiatan tidak boleh memiliki waktu yang sama dengan jadwal kuliah.</div>
                                </div>
                            </div>
                            <div class="flex items-center shrink-0 pr-2">
                                <input type="checkbox" name="no_overlap_kuliah" class="w-5 h-5 text-purple-600 dark:text-purple-500 bg-white dark:bg-gray-900 border-gray-300 dark:border-gray-600 rounded focus:ring-purple-600 dark:focus:ring-purple-500 focus:ring-2 cursor-pointer transition-all shadow-sm" {{ $setting->no_overlap_kuliah ? 'checked' : '' }}>
                            </div>
                        </label>

                        <label class="flex items-center justify-between p-5 sm:p-6 hover:bg-gray-50/50 dark:hover:bg-gray-700/50 cursor-pointer transition-colors w-full group">
                            <div class="flex items-center pr-4">
                                <div class="w-10 h-10 rounded-full bg-purple-50 dark:bg-purple-900/30 flex items-center justify-center mr-4 shrink-0 group-hover:bg-purple-100 dark:group-hover:bg-purple-900/50 transition-colors">
                                    <i class="fa-solid fa-layer-group text-purple-600 dark:text-purple-400"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">Tidak boleh ada dua kegiatan pada waktu sama</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Hanya satu kegiatan yang boleh berlangsung pada satu waktu.</div>
                                </div>
                            </div>
                            <div class="flex items-center shrink-0 pr-2">
                                <input type="checkbox" name="no_overlap_all" class="w-5 h-5 text-purple-600 dark:text-purple-500 bg-white dark:bg-gray-900 border-gray-300 dark:border-gray-600 rounded focus:ring-purple-600 dark:focus:ring-purple-500 focus:ring-2 cursor-pointer transition-all shadow-sm" {{ $setting->no_overlap_all ? 'checked' : '' }}>
                            </div>
                        </label>

                        <label class="flex items-center justify-between p-5 sm:p-6 hover:bg-gray-50/50 dark:hover:bg-gray-700/50 cursor-pointer transition-colors w-full group">
                            <div class="flex items-center pr-4">
                                <div class="w-10 h-10 rounded-full bg-purple-50 dark:bg-purple-900/30 flex items-center justify-center mr-4 shrink-0 group-hover:bg-purple-100 dark:group-hover:bg-purple-900/50 transition-colors">
                                    <i class="fa-solid fa-mug-hot text-purple-600 dark:text-purple-400"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">Istirahat minimal 1 jam</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Setelah setiap kegiatan, wajib ada istirahat 1 jam sebelum kegiatan berikutnya.</div>
                                </div>
                            </div>
                            <div class="flex items-center shrink-0 pr-2">
                                <input type="checkbox" name="istirahat_1_jam" class="w-5 h-5 text-purple-600 dark:text-purple-500 bg-white dark:bg-gray-900 border-gray-300 dark:border-gray-600 rounded focus:ring-purple-600 dark:focus:ring-purple-500 focus:ring-2 cursor-pointer transition-all shadow-sm" {{ $setting->istirahat_1_jam ? 'checked' : '' }}>
                            </div>
                        </label>

                        <label class="flex items-center justify-between p-5 sm:p-6 hover:bg-gray-50/50 dark:hover:bg-gray-700/50 cursor-pointer transition-colors w-full group">
                            <div class="flex items-center pr-4">
                                <div class="w-10 h-10 rounded-full bg-purple-50 dark:bg-purple-900/30 flex items-center justify-center mr-4 shrink-0 group-hover:bg-purple-100 dark:group-hover:bg-purple-900/50 transition-colors">
                                    <i class="fa-solid fa-flag-checkered text-purple-600 dark:text-purple-400"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">Tugas harus selesai sebelum deadline</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Semua tugas harus dijadwalkan dan selesai sebelum batas akhir yang ditentukan.</div>
                                </div>
                            </div>
                            <div class="flex items-center shrink-0 pr-2">
                                <input type="checkbox" name="strict_deadline" class="w-5 h-5 text-purple-600 dark:text-purple-500 bg-white dark:bg-gray-900 border-gray-300 dark:border-gray-600 rounded focus:ring-purple-600 dark:focus:ring-purple-500 focus:ring-2 cursor-pointer transition-all shadow-sm" {{ $setting->strict_deadline ? 'checked' : '' }}>
                            </div>
                        </label>

                        <label class="flex items-center justify-between p-5 sm:p-6 hover:bg-gray-50/50 dark:hover:bg-gray-700/50 cursor-pointer transition-colors w-full group">
                            <div class="flex items-center pr-4">
                                <div class="w-10 h-10 rounded-full bg-purple-50 dark:bg-purple-900/30 flex items-center justify-center mr-4 shrink-0 group-hover:bg-purple-100 dark:group-hover:bg-purple-900/50 transition-colors">
                                    <i class="fa-solid fa-ranking-star text-purple-600 dark:text-purple-400"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">Kuliah memiliki prioritas tertinggi</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Kuliah tidak boleh dipindahkan dan menjadi prioritas utama dalam penjadwalan.</div>
                                </div>
                            </div>
                            <div class="flex items-center shrink-0 pr-2">
                                <input type="checkbox" name="prioritas_kuliah" class="w-5 h-5 text-purple-600 dark:text-purple-500 bg-white dark:bg-gray-900 border-gray-300 dark:border-gray-600 rounded focus:ring-purple-600 dark:focus:ring-purple-500 focus:ring-2 cursor-pointer transition-all shadow-sm" {{ $setting->prioritas_kuliah ? 'checked' : '' }}>
                            </div>
                        </label>

                        <label class="flex items-center justify-between p-5 sm:p-6 hover:bg-gray-50/50 dark:hover:bg-gray-700/50 cursor-pointer transition-colors w-full group">
                            <div class="flex items-center pr-4">
                                <div class="w-10 h-10 rounded-full bg-purple-50 dark:bg-purple-900/30 flex items-center justify-center mr-4 shrink-0 group-hover:bg-purple-100 dark:group-hover:bg-purple-900/50 transition-colors">
                                    <i class="fa-solid fa-chart-simple text-purple-600 dark:text-purple-400"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">Maksimal 3 kegiatan per hari</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Total kegiatan dalam satu hari tidak boleh lebih dari 3 aktivitas.</div>
                                </div>
                            </div>
                            <div class="flex items-center shrink-0 pr-2">
                                <input type="checkbox" name="maks_3_kegiatan" class="w-5 h-5 text-purple-600 dark:text-purple-500 bg-white dark:bg-gray-900 border-gray-300 dark:border-gray-600 rounded focus:ring-purple-600 dark:focus:ring-purple-500 focus:ring-2 cursor-pointer transition-all shadow-sm" {{ $setting->maks_3_kegiatan ? 'checked' : '' }}>
                            </div>
                        </label>

                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm overflow-hidden transition-colors duration-300">
                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 transition-colors">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Preferensi Tambahan</h2>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Penyesuaian gaya jadwal untuk mencocokkan dengan kebiasaan produktifmu.</p>
                    </div>
                    
                    <div class="flex flex-col">
                        <label class="flex items-center justify-between p-5 sm:p-6 hover:bg-gray-50/50 dark:hover:bg-gray-700/50 cursor-pointer transition-colors w-full group">
                            <div class="flex items-center pr-4">
                                <div class="w-10 h-10 rounded-full bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center mr-4 shrink-0 group-hover:bg-amber-100 dark:group-hover:bg-amber-900/50 transition-colors">
                                    <i class="fa-solid fa-sun text-amber-500 dark:text-amber-400"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">Preferensi waktu produktif</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">AI hanya menjadwalkan tugas dari jam 08:00 sampai 17:00 (Matikan jika bersedia lembur).</div>
                                </div>
                            </div>
                            <div class="flex items-center shrink-0 pr-2">
                                <input type="checkbox" name="waktu_produktif" class="w-5 h-5 text-purple-600 dark:text-purple-500 bg-white dark:bg-gray-900 border-gray-300 dark:border-gray-600 rounded focus:ring-purple-600 dark:focus:ring-purple-500 focus:ring-2 cursor-pointer transition-all shadow-sm" {{ $setting->waktu_produktif ? 'checked' : '' }}>
                            </div>
                        </label>
                    </div>
                </div>

                <div>
                    <button type="submit" class="bg-purple-600 hover:bg-purple-700 dark:bg-purple-500 dark:hover:bg-purple-600 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-all shadow-sm shadow-purple-200 dark:shadow-none flex items-center gap-2 active:scale-[0.98]">
                        <i class="fa-solid fa-check text-xs"></i>
                        Simpan Pengaturan
                    </button>
                </div>
            </div>
        </div>
    </form>
</x-app-layout>
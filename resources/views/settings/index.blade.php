<x-app-layout>
    <form action="{{ route('settings.update') }}" method="POST">
        @csrf
        <div class="min-h-screen bg-[#F5F4F0] font-sans pb-12">
            
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-6">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-widest mb-1">Pengaturan</p>
                        <h1 class="text-2xl sm:text-3xl font-medium text-gray-900 tracking-tight">Aturan & Preferensi AI</h1>
                        <p class="mt-1 text-sm text-gray-400">Kustomisasi bagaimana algoritma menyusun jadwalmu.</p>
                    </div>
                </div>

                <div class="mt-4">
                    @if (session('success'))
                        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-2.5 rounded-lg flex items-center gap-2.5 text-sm font-medium shadow-sm">
                            <i class="fa-solid fa-circle-check text-emerald-600"></i>
                            {{ session('success') }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 bg-white">
                        <h2 class="text-base font-semibold text-gray-900">Constraint Utama</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Aturan mutlak yang harus dipatuhi oleh algoritma AI saat menyusun kalender.</p>
                    </div>
                    
                    <div class="flex flex-col divide-y divide-gray-100">
                        <label class="flex items-center justify-between p-5 sm:p-6 hover:bg-gray-50/50 cursor-pointer transition-colors w-full group">
                            <div class="flex items-center pr-4">
                                <div class="w-10 h-10 rounded-full bg-purple-50 flex items-center justify-center mr-4 shrink-0 group-hover:bg-purple-100 transition-colors">
                                    <i class="fa-solid fa-clock text-purple-600"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900">Istirahat minimal 1 jam</div>
                                    <div class="text-xs text-gray-500 mt-0.5">Wajib ada jeda kosong 1 jam setelah setiap kegiatan.</div>
                                </div>
                            </div>
                            
                            <div class="flex items-center shrink-0 pr-2">
                                <input type="checkbox" name="istirahat_1_jam" class="w-5 h-5 text-purple-600 bg-white border-gray-300 rounded focus:ring-purple-600 focus:ring-2 cursor-pointer transition-all shadow-sm" {{ $setting->istirahat_1_jam ? 'checked' : '' }}>
                            </div>
                        </label>

                        <label class="flex items-center justify-between p-5 sm:p-6 hover:bg-gray-50/50 cursor-pointer transition-colors w-full group">
                            <div class="flex items-center pr-4">
                                <div class="w-10 h-10 rounded-full bg-purple-50 flex items-center justify-center mr-4 shrink-0 group-hover:bg-purple-100 transition-colors">
                                    <i class="fa-solid fa-chart-simple text-purple-600"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900">Maksimal 3 kegiatan per hari</div>
                                    <div class="text-xs text-gray-500 mt-0.5">AI akan memindahkan tugas ke hari lain jika hari ini sudah penuh.</div>
                                </div>
                            </div>
                            
                            <div class="flex items-center shrink-0 pr-2">
                                <input type="checkbox" name="maks_3_kegiatan" class="w-5 h-5 text-purple-600 bg-white border-gray-300 rounded focus:ring-purple-600 focus:ring-2 cursor-pointer transition-all shadow-sm" {{ $setting->maks_3_kegiatan ? 'checked' : '' }}>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 bg-white">
                        <h2 class="text-base font-semibold text-gray-900">Preferensi Tambahan</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Penyesuaian gaya jadwal untuk mencocokkan dengan kebiasaan produktifmu.</p>
                    </div>
                    
                    <div class="flex flex-col">
                        <label class="flex items-center justify-between p-5 sm:p-6 hover:bg-gray-50/50 cursor-pointer transition-colors w-full group">
                            <div class="flex items-center pr-4">
                                <div class="w-10 h-10 rounded-full bg-amber-50 flex items-center justify-center mr-4 shrink-0 group-hover:bg-amber-100 transition-colors">
                                    <i class="fa-solid fa-sun text-amber-500"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900">Preferensi waktu produktif</div>
                                    <div class="text-xs text-gray-500 mt-0.5">AI hanya menjadwalkan tugas dari jam 08:00 sampai 17:00 (Matikan jika bersedia lembur).</div>
                                </div>
                            </div>
                            
                            <div class="flex items-center shrink-0 pr-2">
                                <input type="checkbox" name="waktu_produktif" class="w-5 h-5 text-purple-600 bg-white border-gray-300 rounded focus:ring-purple-600 focus:ring-2 cursor-pointer transition-all shadow-sm" {{ $setting->waktu_produktif ? 'checked' : '' }}>
                            </div>
                        </label>
                    </div>
                </div>
                <div>
                    <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-all shadow-sm shadow-purple-200 flex items-center gap-2 active:scale-[0.98]">
                        <i class="fa-solid fa-check text-xs"></i>
                        Simpan Pengaturan
                    </button>
                </div>
            </div>
        </div>
    </form>
</x-app-layout>
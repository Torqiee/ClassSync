<x-app-layout>
    <form action="{{ route('activities.update', $activity->id) }}" method="POST" x-data="{ tipe: '{{ $activity->tipe }}' }">
        @csrf
        @method('PUT')
        
        <div class="min-h-screen bg-[#F5F4F0] dark:bg-gray-900 font-sans pb-12 transition-colors duration-300">
            
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-6">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4">
                    <div class="flex items-center gap-4">
                        <div>
                            <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1">Manajemen Jadwal</p>
                            <h1 class="text-2xl sm:text-3xl font-medium text-gray-900 dark:text-white tracking-tight">Edit Aktivitas</h1>
                            <p class="mt-1 text-sm text-gray-400 dark:text-gray-400">Perbarui detail jadwal atau tugasmu.</p>
                        </div>
                    </div>
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
                            <input type="text" name="nama_kegiatan" value="{{ $activity->nama_kegiatan }}" required 
                                class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 px-4 py-2.5 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition-colors placeholder-gray-300 dark:placeholder-gray-600 shadow-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Kategori</label>
                            <select name="kategori" class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 shadow-sm transition-colors">
                                <option value="Kuliah" {{ $activity->kategori == 'Kuliah' ? 'selected' : '' }}>🎓 Kuliah</option>
                                <option value="Organisasi" {{ $activity->kategori == 'Organisasi' ? 'selected' : '' }}>💼 Organisasi</option>
                                <option value="UKM" {{ $activity->kategori == 'UKM' ? 'selected' : '' }}>🤖 UKM</option>
                                <option value="Tugas" {{ $activity->kategori == 'Tugas' ? 'selected' : '' }}>📝 Tugas</option>
                                <option value="Lainnya" {{ $activity->kategori == 'Lainnya' ? 'selected' : '' }}>📌 Lainnya</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm overflow-hidden transition-colors duration-300">
                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 transition-colors">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Pengaturan Waktu</h2>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Penyesuaian waktu pelaksanaan aktivitas.</p>
                    </div>

                    <div class="p-5 sm:p-6 border-b border-gray-100 dark:border-gray-700 bg-gray-50/30 dark:bg-gray-800/50 transition-colors">
                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Sifat Jadwal (Tidak bisa diubah)</label>
                        <div class="inline-flex items-center px-4 py-2 bg-gray-100/80 dark:bg-gray-900 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium border border-gray-200/60 dark:border-gray-700 cursor-not-allowed shadow-sm">
                            @if($activity->tipe === 'tetap')
                                <i class="fa-solid fa-thumbtack text-violet-500 dark:text-violet-400 mr-2.5"></i> Jadwal Tetap
                            @else
                                <i class="fa-solid fa-robot text-amber-500 dark:text-amber-400 mr-2.5"></i> Tugas Fleksibel
                            @endif
                        </div>
                    </div>

                    <div x-show="tipe === 'tetap'" class="p-5 sm:p-6 space-y-6">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Hari Pelaksanaan</label>
                            <select name="hari" class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 shadow-sm transition-colors">
                                @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $hari)
                                    <option value="{{ $hari }}" {{ $activity->hari == $hari ? 'selected' : '' }}>{{ $hari }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                            <div class="flex-1">
                                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Jam Mulai</label>
                                <input type="time" name="jam_mulai" value="{{ substr($activity->jam_mulai, 0, 5) }}" class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 shadow-sm transition-colors">
                            </div>
                            <div class="flex-1">
                                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Jam Selesai</label>
                                <input type="time" name="jam_selesai" value="{{ substr($activity->jam_selesai, 0, 5) }}" class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 shadow-sm transition-colors">
                            </div>
                        </div>
                    </div>

                    <div x-show="tipe === 'fleksibel'" class="p-5 sm:p-6 space-y-6">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Batas Akhir (Deadline)</label>
                            <select name="deadline" class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 shadow-sm transition-colors">
                                @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $hari)
                                    <option value="{{ $hari }}" {{ $activity->deadline == $hari ? 'selected' : '' }}>{{ $hari }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Estimasi Durasi (Jam)</label>
                            <input type="number" min="1" max="10" name="durasi" value="{{ $activity->durasi }}" class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 shadow-sm transition-colors">
                        </div>
                    </div>
                </div>
                <button type="submit" class="bg-gray-900 dark:bg-white hover:bg-gray-800 dark:hover:bg-gray-100 text-white dark:text-gray-900 px-5 py-2.5 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 shadow-sm active:scale-[0.98]">
                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                    Perbarui Aktivitas
                </button>
            </div>
        </div>
    </form>
</x-app-layout>
<x-app-layout>
    <!-- Bungkus seluruh halaman dengan Form agar tombol Submit di header bekerja -->
    <form action="{{ route('activities.update', $activity->id) }}" method="POST" x-data="{ tipe: '{{ $activity->tipe }}' }">
        @csrf
        @method('PUT')
        
        <div class="min-h-screen bg-[#F5F4F0] font-sans pb-12">
            
            <!-- Header Section (Senada dengan Dashboard) -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-6">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4">
                    <div class="flex items-center gap-4">
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-widest mb-1">Manajemen Jadwal</p>
                            <h1 class="text-2xl sm:text-3xl font-medium text-gray-900 tracking-tight">Edit Aktivitas</h1>
                            <p class="mt-1 text-sm text-gray-400">Perbarui detail jadwal atau tugasmu.</p>
                        </div>
                    </div>
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
                            <input type="text" name="nama_kegiatan" value="{{ $activity->nama_kegiatan }}" required 
                                class="w-full bg-white border border-gray-200 px-4 py-2.5 text-gray-900 text-sm rounded-lg focus:ring-gray-900 focus:border-gray-900 transition-colors placeholder-gray-300 shadow-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Kategori</label>
                            <select name="kategori" class="w-full bg-white border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-gray-900 focus:border-gray-900 block p-2.5 shadow-sm">
                                <option value="Kuliah" {{ $activity->kategori == 'Kuliah' ? 'selected' : '' }}>🎓 Kuliah</option>
                                <option value="Organisasi" {{ $activity->kategori == 'Organisasi' ? 'selected' : '' }}>💼 Organisasi</option>
                                <option value="UKM" {{ $activity->kategori == 'UKM' ? 'selected' : '' }}>🤖 UKM</option>
                                <option value="Tugas" {{ $activity->kategori == 'Tugas' ? 'selected' : '' }}>📝 Tugas</option>
                                <option value="Lainnya" {{ $activity->kategori == 'Lainnya' ? 'selected' : '' }}>📌 Lainnya</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Pengaturan Waktu & AI -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 bg-white">
                        <h2 class="text-base font-semibold text-gray-900">Pengaturan Waktu</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Penyesuaian waktu pelaksanaan aktivitas.</p>
                    </div>

                    <!-- Sifat Jadwal (Read-only mode) -->
                    <div class="p-5 sm:p-6 border-b border-gray-100 bg-gray-50/30">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Sifat Jadwal (Tidak bisa diubah)</label>
                        <div class="inline-flex items-center px-4 py-2 bg-gray-100/80 text-gray-700 rounded-lg text-sm font-medium border border-gray-200/60 cursor-not-allowed shadow-sm">
                            @if($activity->tipe === 'tetap')
                                <i class="fa-solid fa-thumbtack text-violet-500 mr-2.5"></i> Jadwal Tetap
                            @else
                                <i class="fa-solid fa-robot text-amber-500 mr-2.5"></i> Tugas Fleksibel
                            @endif
                        </div>
                    </div>

                    <!-- Input Fields: Jadwal Tetap -->
                    <div x-show="tipe === 'tetap'" class="p-5 sm:p-6 space-y-6">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Hari Pelaksanaan</label>
                            <select name="hari" class="w-full bg-white border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-gray-900 focus:border-gray-900 block p-2.5 shadow-sm">
                                @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $hari)
                                    <option value="{{ $hari }}" {{ $activity->hari == $hari ? 'selected' : '' }}>{{ $hari }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                            <div class="flex-1">
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Jam Mulai</label>
                                <input type="time" name="jam_mulai" value="{{ substr($activity->jam_mulai, 0, 5) }}" class="w-full bg-white border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-gray-900 focus:border-gray-900 block p-2.5 shadow-sm">
                            </div>
                            <div class="flex-1">
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Jam Selesai</label>
                                <input type="time" name="jam_selesai" value="{{ substr($activity->jam_selesai, 0, 5) }}" class="w-full bg-white border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-gray-900 focus:border-gray-900 block p-2.5 shadow-sm">
                            </div>
                        </div>
                    </div>

                    <!-- Input Fields: Jadwal Fleksibel -->
                    <div x-show="tipe === 'fleksibel'" class="p-5 sm:p-6 space-y-6">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Batas Akhir (Deadline)</label>
                            <select name="deadline" class="w-full bg-white border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-gray-900 focus:border-gray-900 block p-2.5 shadow-sm">
                                @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $hari)
                                    <option value="{{ $hari }}" {{ $activity->deadline == $hari ? 'selected' : '' }}>{{ $hari }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Estimasi Durasi (Jam)</label>
                            <input type="number" min="1" max="10" name="durasi" value="{{ $activity->durasi }}" class="w-full bg-white border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-gray-900 focus:border-gray-900 block p-2.5 shadow-sm">
                        </div>
                    </div>
                </div>
                <button type="submit" class="bg-gray-900 hover:bg-gray-800 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 shadow-sm active:scale-[0.98]">
                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                    Perbarui Aktivitas
                </button>
            </div>
        </div>
    </form>
</x-app-layout>
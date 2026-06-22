<x-app-layout>
<div class="min-h-screen bg-[#F5F4F0] dark:bg-gray-900 font-sans pb-12 transition-colors duration-300">
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-6">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4">
            <div>
                <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1">Pencapaian</p>
                <h1 class="text-2xl sm:text-3xl font-medium text-gray-900 dark:text-white tracking-tight">Riwayat Kegiatan</h1>
                <p class="mt-1 text-sm text-gray-400 dark:text-gray-400">Jejak produktivitas dan tugas yang telah kamu selesaikan.</p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm overflow-hidden transition-colors duration-300">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 transition-colors">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">Arsip Tugas Fleksibel</h2>
            </div>
            
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($historyActivities as $activity)
                    <div class="p-5 sm:p-6 hover:bg-gray-50/50 dark:hover:bg-gray-700/50 transition-colors flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center shrink-0 transition-colors">
                                <i class="fa-solid fa-check text-emerald-500 dark:text-emerald-400"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $activity->nama_kegiatan }}</h3>
                                <div class="flex items-center gap-3 mt-1">
                                    <span class="text-xs text-gray-500 dark:text-gray-300 font-medium bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded transition-colors">{{ $activity->kategori }}</span>
                                    <span class="text-xs text-gray-400 dark:text-gray-500"><i class="fa-regular fa-calendar-check mr-1"></i> Diselesaikan: {{ \Carbon\Carbon::parse($activity->diselesaikan_pada)->translatedFormat('d M Y') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="hidden sm:block text-right">
                            <span class="text-xs font-semibold text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-900/30 px-2.5 py-1 rounded-lg ring-1 ring-purple-200/50 dark:ring-purple-700/50 transition-colors">
                                Durasi: {{ $activity->durasi }} Jam
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="p-10 text-center flex flex-col items-center">
                        <div class="w-16 h-16 bg-gray-50 dark:bg-gray-800/50 rounded-full flex items-center justify-center mb-3 transition-colors">
                            <i class="fa-solid fa-box-open text-2xl text-gray-300 dark:text-gray-600"></i>
                        </div>
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white">Belum ada riwayat</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Tugas yang kamu selesaikan akan muncul di sini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>
</x-app-layout>
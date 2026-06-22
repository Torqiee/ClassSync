<x-app-layout>
<div class="min-h-screen bg-[#F5F4F0] dark:bg-gray-900 font-sans pb-12 transition-colors duration-300" x-data="{ showDeleteModal: false, deleteUrl: '' }">

    {{-- Header --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-6">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4">
            <div>
                <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1">Minggu ini</p>
                <h1 class="text-2xl sm:text-3xl font-medium text-gray-900 dark:text-white tracking-tight">Halo, {{ Auth::user()->name }}!</h1>
                <p class="mt-1 text-sm text-gray-400 dark:text-gray-400">Kelola jadwal produktifmu</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <form action="/activities/generate-ai" method="POST">
                    @csrf
                    <button type="submit" class="bg-purple-600 hover:bg-purple-700 dark:bg-purple-600 dark:hover:bg-purple-500 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-all shadow-sm shadow-purple-200 dark:shadow-none flex items-center gap-2 active:scale-[0.98]">
                        <i class="fa-solid fa-wand-magic-sparkles text-xs"></i>
                        Generate Jadwal
                    </button>
                </form>
                <form action="{{ route('activities.archive') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-emerald-50 dark:bg-gray-800 hover:bg-emerald-100 dark:hover:bg-gray-700 text-emerald-700 dark:text-emerald-400 px-4 py-2.5 rounded-lg text-sm font-medium border border-emerald-200 dark:border-emerald-800 transition-colors inline-flex items-center gap-1.5 shadow-sm active:scale-[0.98]">
                        <i class="fa-solid fa-calendar-check text-xs"></i> Akhiri Minggu Ini
                    </button>
                </form>
                <a href="{{ route('activities.reset') }}" class="bg-white dark:bg-gray-800 hover:bg-red-50 dark:hover:bg-gray-700 text-red-600 dark:text-red-400 px-4 py-2.5 rounded-lg text-sm font-medium border border-gray-200 dark:border-red-900 transition-colors inline-flex items-center gap-1.5 shadow-sm">
                    <i class="fa-solid fa-rotate-left text-xs"></i> Reset AI
                </a>
            </div>
        </div>

        {{-- Flash Messages --}}
        <div class="mt-4 space-y-2">
            @if (session('success'))
            <div class="bg-emerald-50 dark:bg-gray-800 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-400 px-4 py-2.5 rounded-lg flex items-center gap-2.5 text-sm font-medium shadow-sm">
                <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400 shrink-0"></i>
                {{ session('success') }}
            </div>
            @endif
            @if (session('info'))
            <div class="bg-blue-50 dark:bg-gray-800 border border-blue-200 dark:border-blue-800 text-blue-800 dark:text-blue-400 px-4 py-2.5 rounded-lg flex items-center gap-2.5 text-sm font-medium shadow-sm">
                <i class="fa-solid fa-circle-info text-blue-600 dark:text-blue-400 shrink-0"></i>
                {{ session('info') }}
            </div>
            @endif
            @if (session('error'))
            <div class="bg-red-50 dark:bg-gray-800 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-400 px-4 py-2.5 rounded-lg flex items-center gap-2.5 text-sm font-medium shadow-sm">
                <i class="fa-solid fa-triangle-exclamation text-red-600 dark:text-red-400 shrink-0"></i>
                {{ session('error') }}
            </div>
            @endif
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

        {{-- Statistik Section --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 shadow-sm transition-colors duration-300">
                <div class="text-gray-500 dark:text-gray-400 text-xs font-medium uppercase tracking-wider mb-1">Total Aktivitas</div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalAktivitas }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 shadow-sm transition-colors duration-300">
                <div class="text-gray-500 dark:text-gray-400 text-xs font-medium uppercase tracking-wider mb-1">Jam Terpakai</div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $jamTerpakai }}<span class="text-sm font-normal text-gray-500 dark:text-gray-400 ml-1">jam</span></div>
            </div>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 shadow-sm transition-colors duration-300">
                <div class="text-gray-500 dark:text-gray-400 text-xs font-medium uppercase tracking-wider mb-1">Status Bentrok</div>
                <div class="text-2xl font-bold {{ str_contains($warnaBentrok, 'red') ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' }}">{{ $statusBentrok }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 shadow-sm transition-colors duration-300">
                <div class="text-gray-500 dark:text-gray-400 text-xs font-medium uppercase tracking-wider mb-1">Status</div>
                <div class="text-2xl font-bold {{ str_contains($warnaAI, 'red') ? 'text-red-600 dark:text-red-400' : (str_contains($warnaAI, 'amber') ? 'text-amber-500 dark:text-amber-400' : (str_contains($warnaAI, 'blue') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500')) }}">{{ $efisiensiAI }}</div>
            </div>
        </div>

        {{-- Tugas Belum Terjadwal --}}
        @if($unscheduledTasks->count() > 0)
        <div class="bg-amber-50 dark:bg-gray-800 border border-amber-200 dark:border-amber-800 rounded-xl p-5 shadow-sm transition-colors duration-300">
            <div class="flex items-start gap-3 mb-4">
                <div class="w-9 h-9 rounded-lg bg-amber-100 dark:bg-gray-700 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-triangle-exclamation text-amber-600 dark:text-amber-400"></i>
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-amber-900 dark:text-amber-400">{{ $unscheduledTasks->count() }} Tugas Belum Terjadwal</h2>
                    <p class="text-xs text-amber-700/80 dark:text-gray-400 mt-0.5">Tugas ini bentrok atau belum berhasil ditempatkan AI di kalender. Edit detailnya (durasi/deadline) atau hapus jika sudah tidak relevan, lalu klik "Generate Jadwal" lagi.</p>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($unscheduledTasks as $tugas)
                <div class="group relative bg-white dark:bg-gray-700 border border-amber-200 dark:border-gray-600 rounded-xl p-3.5 shadow-sm transition-colors duration-300">
                    <div class="absolute top-2 right-2 flex gap-1 z-10">
                        <a href="{{ route('activities.edit', $tugas->id) }}" class="w-6 h-6 bg-white dark:bg-gray-600 border border-amber-100 dark:border-gray-500 rounded-md flex items-center justify-center hover:bg-amber-50 dark:hover:bg-gray-500 transition-colors shadow-sm">
                            <i class="fa-solid fa-pen-to-square text-[10px] text-amber-600 dark:text-amber-400"></i>
                        </a>
                        <button type="button" @click.prevent="showDeleteModal = true; deleteUrl = '{{ route('activities.destroy', $tugas->id) }}'" class="w-6 h-6 bg-white dark:bg-gray-600 border border-gray-200 dark:border-gray-500 rounded-md flex items-center justify-center hover:bg-red-50 dark:hover:bg-red-900/50 transition-colors shadow-sm">
                            <i class="fa-solid fa-trash text-[10px] text-red-500 dark:text-red-400"></i>
                        </button>
                    </div>
                    <div class="text-xs text-gray-900 dark:text-white font-semibold pr-12 leading-snug break-words">{{ $tugas->nama_kegiatan }}</div>
                    <div class="flex items-center gap-1.5 mt-2">
                        <i class="fa-regular fa-flag text-[9px] text-amber-500 dark:text-amber-400 shrink-0"></i>
                        <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider truncate">Deadline: {{ $tugas->deadline }} · {{ $tugas->durasi }} jam</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Weekly Calendar --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden flex flex-col shadow-sm transition-colors duration-300">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center shrink-0">
                <div class="flex items-center gap-4">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Jadwal Mingguan</h2>
                    <div class="hidden sm:flex items-center gap-4 pl-4 border-l border-gray-200 dark:border-gray-700">
                        <span class="flex items-center gap-1.5 text-xs font-medium text-gray-500 dark:text-gray-400">
                            <span class="w-2.5 h-2.5 rounded-full bg-purple-500 inline-block shrink-0 shadow-sm"></span> Tetap
                        </span>
                        <span class="flex items-center gap-1.5 text-xs font-medium text-gray-500 dark:text-gray-400">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-400 inline-block shrink-0 shadow-sm"></span> Fleksibel
                        </span>
                    </div>
                </div>
                <a href="{{ route('activities.create') }}" class="text-sm text-purple-700 dark:text-purple-400 bg-purple-50 dark:bg-gray-600 hover:bg-purple-100 dark:hover:bg-gray-700 ring-1 ring-inset ring-purple-200 dark:ring-purple-800 px-3 py-1.5 rounded-lg font-medium transition-colors flex items-center gap-1.5 shrink-0">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span class="hidden sm:inline">Tambah Aktivitas</span>
                </a>
            </div>

            <div class="w-full overflow-x-auto p-3 sm:p-4 pb-6">
                <table class="min-w-full table-fixed border-collapse" style="min-width: 800px;">
                    <thead>
                        <tr>
                            @php $hari_list = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat']; @endphp
                            @foreach($hari_list as $hari)
                            <th scope="col" class="w-1/5 text-center pb-3 text-xs font-medium text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-700">
                                {{ $hari }}
                            </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-x divide-gray-100 dark:divide-gray-700">
                        <tr>
                            @foreach($hari_list as $hari)
                            <td class="align-top px-1.5 sm:px-2 pt-4 space-y-2.5">
                                @if(isset($activities[$hari]) && $activities[$hari]->count() > 0)
                                    @foreach($activities[$hari] as $kegiatan)

                                        @if($kegiatan->tipe === 'tetap')
                                        <div class="group relative bg-purple-50 dark:bg-gray-800 border border-purple-200 dark:border-purple-800 rounded-xl p-3 transition-all hover:shadow-md hover:border-purple-300 dark:hover:border-purple-500 hover:-translate-y-0.5">
                                            <div class="absolute top-2 right-2 hidden group-hover:flex gap-1 z-10">
                                                <a href="{{ route('activities.edit', $kegiatan->id) }}" class="w-6 h-6 bg-white dark:bg-gray-700 border border-purple-100 dark:border-gray-600 rounded-md flex items-center justify-center hover:bg-purple-50 dark:hover:bg-gray-600 transition-colors shadow-sm">
                                                    <i class="fa-solid fa-pen-to-square text-[10px] text-purple-600 dark:text-purple-400"></i>
                                                </a>
                                                <button type="button" @click.prevent="showDeleteModal = true; deleteUrl = '{{ route('activities.destroy', $kegiatan->id) }}'" class="w-6 h-6 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-md flex items-center justify-center hover:bg-red-50 dark:hover:bg-red-900/50 transition-colors shadow-sm">
                                                    <i class="fa-solid fa-trash text-[10px] text-red-500 dark:text-red-400"></i>
                                                </button>
                                            </div>
                                            <div class="text-[11px] font-semibold text-purple-600 dark:text-purple-400 mb-1 flex items-center gap-1.5">
                                                <i class="fa-regular fa-clock text-[10px]"></i>
                                                {{ substr($kegiatan->jam_mulai, 0, 5) }}–{{ substr($kegiatan->jam_selesai, 0, 5) }}
                                            </div>
                                            <div class="text-xs text-gray-900 dark:text-gray-100 font-semibold pr-5 leading-snug break-words">{{ $kegiatan->nama_kegiatan }}</div>
                                            <div class="text-[10px] text-purple-500 dark:text-purple-400 mt-2 font-bold uppercase tracking-wider truncate">{{ $kegiatan->kategori }}</div>
                                        </div>

                                        @else
                                        <div class="group relative bg-amber-50 dark:bg-gray-800 border border-amber-200 dark:border-amber-700 rounded-xl p-3 transition-all hover:shadow-md hover:border-amber-300 dark:hover:border-amber-500 hover:-translate-y-0.5">
                                            <div class="absolute top-2 right-2 hidden group-hover:flex gap-1 z-10">
                                                <a href="{{ route('activities.edit', $kegiatan->id) }}" class="w-6 h-6 bg-white dark:bg-gray-700 border border-amber-100 dark:border-gray-600 rounded-md flex items-center justify-center hover:bg-amber-50 dark:hover:bg-gray-600 transition-colors shadow-sm">
                                                    <i class="fa-solid fa-pen-to-square text-[10px] text-amber-600 dark:text-amber-400"></i>
                                                </a>
                                                <button type="button" @click.prevent="showDeleteModal = true; deleteUrl = '{{ route('activities.destroy', $kegiatan->id) }}'" class="w-6 h-6 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-md flex items-center justify-center hover:bg-red-50 dark:hover:bg-red-900/50 transition-colors shadow-sm">
                                                    <i class="fa-solid fa-trash text-[10px] text-red-500 dark:text-red-400"></i>
                                                </button>
                                            </div>
                                            <div class="text-[11px] font-semibold text-amber-700 dark:text-amber-400 mb-1 flex items-center gap-1.5">
                                                <i class="fa-solid fa-clock text-[10px] text-amber-500 dark:text-amber-400"></i>
                                                {{ substr($kegiatan->jam_mulai, 0, 5) }}–{{ substr($kegiatan->jam_selesai, 0, 5) }}
                                            </div>
                                            <div class="text-xs text-gray-900 dark:text-gray-100 font-semibold pr-5 leading-snug break-words">{{ $kegiatan->nama_kegiatan }}</div>
                                            <div class="flex items-center gap-1.5 mt-2">
                                                <i class="fa-regular fa-flag text-[9px] text-amber-500 dark:text-amber-400 shrink-0"></i>
                                                <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider truncate">Deadline: {{ $kegiatan->deadline }}</span>
                                            </div>
                                        </div>
                                        @endif

                                    @endforeach
                                @else
                                <div class="h-16 border border-dashed border-gray-200 dark:border-gray-700 rounded-xl flex items-center justify-center bg-gray-50/50 dark:bg-gray-800/50">
                                    <span class="text-[11px] font-medium text-gray-400 dark:text-gray-600">Kosong</span>
                                </div>
                                @endif
                            </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- Delete Modal --}}
    <div x-cloak x-show="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true">
        <div x-show="showDeleteModal"
            x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="absolute inset-0 bg-gray-900/40 dark:bg-black/60 backdrop-blur-sm"
            @click="showDeleteModal = false"></div>
        <div x-show="showDeleteModal"
            x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
            class="relative bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-6 w-full max-w-sm shadow-xl">
            <div class="w-10 h-10 bg-red-50 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4 border dark:border-gray-600">
                <i class="fa-solid fa-triangle-exclamation text-red-600 dark:text-red-400"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Hapus aktivitas?</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Tindakan ini tidak dapat dibatalkan. Jadwal ini akan dihapus secara permanen dari kalender Anda.</p>
            <div class="flex gap-3 justify-end">
                <button type="button" @click="showDeleteModal = false"
                    class="px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 rounded-lg transition-colors">
                    Batal
                </button>
                <form :action="deleteUrl" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="px-4 py-2.5 text-sm font-medium text-white bg-red-600 dark:bg-red-600 hover:bg-red-700 dark:hover:bg-red-500 rounded-lg transition-colors shadow-sm active:scale-[0.98]">
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
</x-app-layout>
<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 font-sans pb-12">
        {{-- Header --}}
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-6">
            <div class="flex flex-col gap-2">
                <p class="text-xs text-slate-400 uppercase tracking-widest">Fitur Baru</p>
                <h1 class="text-3xl sm:text-4xl font-bold text-white tracking-tight">Visualisasi Backtracking</h1>
                <p class="text-sm text-slate-400 mt-2">Lihat proses pencarian solusi secara real-time dengan engine trace log</p>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Control Panel --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-slate-800 border border-slate-700 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-slate-400 text-sm">Status</p>
                            <p class="text-white text-lg font-semibold mt-1">Aktif</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-500/10">
                            <span class="inline-block w-3 h-3 rounded-full bg-green-500 animate-pulse"></span>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-800 border border-slate-700 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-slate-400 text-sm">Total Node</p>
                            <p class="text-white text-lg font-semibold mt-1">{{ count($backtrackingData['nodes']) }}</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-500/10">
                            <span class="text-blue-400 text-lg">⊛</span>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-800 border border-slate-700 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-slate-400 text-sm">Waktu Eksekusi</p>
                            <p class="text-white text-lg font-semibold mt-1">2.45s</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-amber-500/10">
                            <span class="text-amber-400 text-lg">⏱</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Visualization Card --}}
            <div class="bg-slate-800 border border-slate-700 rounded-xl shadow-2xl overflow-hidden">
                {{-- Title Section --}}
                <div class="px-8 py-6 border-b border-slate-700 bg-slate-800/50">
                    <h2 class="text-xl font-bold text-white">{{ $backtrackingData['title'] }}</h2>
                </div>

                {{-- Visualization Area --}}
                <div class="px-8 py-12 overflow-x-auto">
                    <div class="min-h-[600px] flex items-center justify-center">
                        <div class="space-y-8 w-full">
                            {{-- SVG Tree Visualization --}}
                            <svg class="w-full h-auto" style="min-height: 400px;">
                                <defs>
                                    <marker id="arrowhead" markerWidth="10" markerHeight="10" refX="9" refY="3" orient="auto">
                                        <polygon points="0 0, 10 3, 0 6" fill="#64748B" />
                                    </marker>
                                </defs>
                                
                                {{-- Connector Lines --}}
                                <g stroke="#64748B" stroke-width="2" stroke-dasharray="5,5" fill="none" marker-end="url(#arrowhead)">
                                    <line x1="50%" y1="150" x2="50%" y2="230" />
                                    <line x1="50%" y1="400" x2="50%" y2="480" />
                                </g>

                                {{-- Node Container --}}
                                <g>
                                    {{-- START Node --}}
                                    <g transform="translate(50%, 80)" text-anchor="middle">
                                        <rect x="-180" y="-30" width="360" height="60" rx="30" fill="#6366F1" opacity="0.9" stroke="#818CF8" stroke-width="2"/>
                                        <circle cx="-150" cy="0" r="6" fill="#22C55E"/>
                                        <text x="-20" y="8" font-size="16" font-weight="bold" fill="white" font-family="system-ui">
                                            START: HITUNG BOBOT MATA KULIAH
                                        </text>
                                    </g>

                                    {{-- PROSES 1 Node --}}
                                    <g transform="translate(50%, 260)">
                                        <rect x="-280" y="-50" width="560" height="100" rx="8" fill="#374151" opacity="0.8" stroke="#4B5563" stroke-width="2"/>
                                        <text x="-280" y="-25" font-size="12" font-weight="bold" fill="#9CA3AF" font-family="system-ui" letter-spacing="2">
                                            PROSES 1
                                        </text>
                                        <text x="-280" y="5" font-size="15" font-weight="600" fill="#FFFFFF" font-family="system-ui">
                                            Sortir Mata Kuliah berdasarkan Bobot Tertinggi & Buat Tabel
                                        </text>
                                        <text x="-280" y="30" font-size="15" font-weight="600" fill="#FFFFFF" font-family="system-ui">
                                            Hubungan Tetangga
                                        </text>
                                    </g>

                                    {{-- ERROR Node --}}
                                    <g transform="translate(50%, 540)">
                                        <rect x="-320" y="-35" width="640" height="70" rx="6" fill="#475569" opacity="0.7" stroke="#64748B" stroke-width="1" stroke-dasharray="3,3"/>
                                        <text x="-320" y="10" font-size="13" font-weight="600" fill="#94A3B8" font-family="system-ui" font-style="italic">
                                            Belum ada variabel kegiatan/tugas dalam antrian domain.
                                        </text>
                                    </g>
                                </g>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Footer Info --}}
                <div class="px-8 py-4 border-t border-slate-700 bg-slate-800/30 flex items-center justify-between text-sm flex-wrap gap-4">
                    <div class="text-slate-400">
                        Process ID: <span class="text-slate-300 font-mono text-xs">{{ $backtrackingData['processId'] }}</span>
                    </div>
                    <div class="text-slate-400">
                        <span class="inline-flex items-center gap-2">
                            <span class="inline-block w-2 h-2 rounded-full bg-green-500"></span>
                            <span class="font-mono text-xs">{{ now()->format('Y-m-d H:i:s') }}</span>
                        </span>
                    </div>
                </div>
            </div>

            {{-- Legend --}}
            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-slate-800 border border-slate-700 rounded-lg p-4 hover:border-slate-600 transition-colors">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-500/20">
                                <span class="text-indigo-400 text-lg">◆</span>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-white font-semibold text-sm">Node Awal</h3>
                            <p class="text-slate-400 text-xs mt-1">Start dari proses backtracking dengan status active indicator (hijau)</p>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-800 border border-slate-700 rounded-lg p-4 hover:border-slate-600 transition-colors">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-500/20">
                                <span class="text-slate-300 text-lg">□</span>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-white font-semibold text-sm">Node Proses</h3>
                            <p class="text-slate-400 text-xs mt-1">Tahap proses yang sedang/sudah dijalankan dengan deskripsi lengkap</p>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-800 border border-slate-700 rounded-lg p-4 hover:border-slate-600 transition-colors">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-500/20">
                                <span class="text-slate-400 text-lg">⚠</span>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-white font-semibold text-sm">Error/Warning</h3>
                            <p class="text-slate-400 text-xs mt-1">Kondisi error atau warning dalam proses dengan format dashed border</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Debug Info (Development Only) --}}
            @if(config('app.debug'))
            <div class="mt-8 bg-slate-800 border border-slate-700 rounded-lg p-4">
                <h3 class="text-white font-semibold text-sm mb-3">Debug Info</h3>
                <pre class="text-slate-400 text-xs overflow-x-auto bg-slate-900 p-3 rounded border border-slate-700">{{ json_encode($backtrackingData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>

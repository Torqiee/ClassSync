<x-app-layout>
<div
    class="min-h-screen bg-[#F5F4F0] dark:bg-gray-900 font-sans flex flex-col transition-colors duration-300"
    x-data="traceRunner({{ \Illuminate\Support\Js::from($trace) }})"
    x-init="autoplay()"
>
    <div class="flex-1">
    {{-- Header --}}
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-6">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4">
            <div>
                <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1">Proses AI</p>
                <h1 class="text-2xl sm:text-3xl font-medium text-gray-900 dark:text-white tracking-tight">Visualisasi Penjadwalan</h1>
                <p class="mt-1 text-sm text-gray-400 dark:text-gray-400">Rekaman langkah algoritma backtracking saat menyusun jadwalmu.</p>
            </div>
            <a href="{{ route('dashboard') }}" class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300 px-4 py-2.5 rounded-lg text-sm font-medium border border-gray-200 dark:border-gray-700 transition-colors inline-flex items-center gap-1.5 shadow-sm shrink-0">
                <i class="fa-solid fa-arrow-left text-xs"></i> Kembali ke Dashboard
            </a>
        </div>

        {{-- Status banner --}}
        @if($message)
        <div class="mt-4
            @if($status === 'success') bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800/50 text-emerald-800 dark:text-emerald-400
            @elseif($status === 'error') bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800/50 text-red-800 dark:text-red-400
            @else bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800/50 text-blue-800 dark:text-blue-400
            @endif
            px-4 py-2.5 rounded-lg flex items-center gap-2.5 text-sm font-medium shadow-sm transition-colors duration-300">
            <i class="fa-solid {{ $status === 'success' ? 'fa-circle-check text-emerald-600 dark:text-emerald-400' : ($status === 'error' ? 'fa-triangle-exclamation text-red-600 dark:text-red-400' : 'fa-circle-info text-blue-600 dark:text-blue-400') }} shrink-0"></i>
            {{ $message }}
        </div>
        @endif
    </div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

        {{-- Live tally --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 shadow-sm transition-colors duration-300">
                <div class="text-gray-500 dark:text-gray-400 text-xs font-medium uppercase tracking-wider mb-1">Langkah</div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white" x-text="(idx + 1) + ' / ' + trace.length"></div>
            </div>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 shadow-sm transition-colors duration-300">
                <div class="text-gray-500 dark:text-gray-400 text-xs font-medium uppercase tracking-wider mb-1">Ditempatkan</div>
                <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400" x-text="tally.assign"></div>
            </div>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 shadow-sm transition-colors duration-300">
                <div class="text-gray-500 dark:text-gray-400 text-xs font-medium uppercase tracking-wider mb-1">Backtrack</div>
                <div class="text-2xl font-bold text-amber-500 dark:text-amber-400" x-text="tally.backtrack"></div>
            </div>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 shadow-sm transition-colors duration-300">
                <div class="text-gray-500 dark:text-gray-400 text-xs font-medium uppercase tracking-wider mb-1">Slot Dicek</div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white" x-text="tally.check"></div>
            </div>
        </div>

        {{-- Trace panel --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden shadow-sm transition-colors duration-300">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center shrink-0">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">Jejak Proses Backtracking</h2>
                <div class="hidden sm:flex items-center gap-4 pl-4 border-l border-gray-200 dark:border-gray-700">
                    <span class="flex items-center gap-1.5 text-xs font-medium text-gray-500 dark:text-gray-400">
                        <span class="w-2.5 h-2.5 rounded-full bg-purple-500 inline-block shrink-0 shadow-sm"></span> Penempatan
                    </span>
                    <span class="flex items-center gap-1.5 text-xs font-medium text-gray-500 dark:text-gray-400">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-400 inline-block shrink-0 shadow-sm"></span> Backtrack
                    </span>
                </div>
            </div>

            <div x-ref="scroller" class="px-4 sm:px-6 py-6 max-h-[60vh] overflow-y-auto scroll-smooth">
                <template x-for="(step, i) in visible.filter((s, idx, arr) => idx === 0 || JSON.stringify(s) !== JSON.stringify(arr[idx-1]))" :key="i">
                    <div>
                        {{-- connector arrow before major nodes --}}
                        <div x-show="isMajor(step.type) && i > 0 && isMajor(visible[i-1] ? visible[i-1].type : null)" class="flex justify-center py-1">
                            <i class="fa-solid fa-arrow-down text-gray-300 dark:text-gray-600 text-xs"></i>
                        </div>

                        {{-- START --}}
                        <template x-if="step.type === 'start'">
                            <div class="flex justify-center py-3">
                                <div class="inline-flex items-center gap-2.5 bg-purple-600 dark:bg-purple-500 px-6 py-3 rounded-full shadow-sm shadow-purple-200 dark:shadow-none">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 shrink-0"></span>
                                    <span class="text-white font-bold text-sm uppercase tracking-wide" x-text="step.title"></span>
                                </div>
                            </div>
                        </template>

                        {{-- PROCESS --}}
                        <template x-if="step.type === 'process'">
                            <div class="bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl px-5 py-4 my-2 transition-colors">
                                <div class="text-[10px] font-bold text-purple-600 dark:text-purple-400 uppercase tracking-widest mb-1.5">Proses</div>
                                <div class="text-sm font-semibold text-gray-900 dark:text-white mb-1" x-text="step.title"></div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed" x-text="step.detail" x-show="step.detail"></div>
                            </div>
                        </template>

                        {{-- VARIABLE --}}
                        <template x-if="step.type === 'variable'">
                            <div class="bg-purple-50/60 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800/50 rounded-xl px-5 py-4 my-2 transition-colors">
                                <div class="text-[10px] font-bold text-purple-600 dark:text-purple-400 uppercase tracking-widest mb-1.5">Variabel Berikutnya</div>
                                <div class="text-sm font-semibold text-gray-900 dark:text-white mb-1" x-text="step.title"></div>
                                <div class="text-xs text-purple-500 dark:text-purple-400" x-text="step.detail" x-show="step.detail"></div>
                            </div>
                        </template>

                        {{-- CHECK (compact log line) --}}
                        <template x-if="step.type === 'check'">
                            <div class="flex items-start gap-2.5 py-1 pl-2 border-l-2 ml-2 transition-colors"
                                 :class="step.status === 'pass' ? 'border-emerald-300 dark:border-emerald-600' : 'border-gray-200 dark:border-gray-700'">
                                <i :class="step.status === 'pass' ? 'fa-solid fa-check text-emerald-500' : 'fa-solid fa-xmark text-gray-300 dark:text-gray-600'" class="text-[10px] mt-1 shrink-0"></i>
                                <div class="text-xs leading-relaxed">
                                    <span :class="step.status === 'pass' ? 'text-emerald-700 dark:text-emerald-400 font-medium' : 'text-gray-400 dark:text-gray-500'" x-text="step.title"></span>
                                    <span class="text-gray-300 dark:text-gray-600" x-show="step.detail"> — </span>
                                    <span :class="step.status === 'pass' ? 'text-emerald-500' : 'text-gray-400 dark:text-gray-500'" x-text="step.detail"></span>
                                </div>
                            </div>
                        </template>

                        {{-- ASSIGN --}}
                        <template x-if="step.type === 'assign'">
                            <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800/50 rounded-xl px-5 py-3.5 my-2 flex items-center gap-3 transition-colors">
                                <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400"></i>
                                <span class="text-sm font-semibold text-emerald-800 dark:text-emerald-400" x-text="step.title"></span>
                            </div>
                        </template>

                        {{-- BACKTRACK --}}
                        <template x-if="step.type === 'backtrack'">
                            <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/50 rounded-xl px-5 py-3.5 my-2 transition-colors">
                                <div class="flex items-center gap-3">
                                    <i class="fa-solid fa-rotate-left text-amber-500 dark:text-amber-400"></i>
                                    <span class="text-sm font-semibold text-amber-700 dark:text-amber-400" x-text="step.title"></span>
                                </div>
                                <div class="text-xs text-amber-600/80 dark:text-amber-500/80 mt-1 pl-7" x-text="step.detail" x-show="step.detail"></div>
                            </div>
                        </template>

                        {{-- DEAD END --}}
                        <template x-if="step.type === 'deadend'">
                            <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800/50 rounded-xl px-5 py-3.5 my-2 transition-colors">
                                <div class="flex items-center gap-3">
                                    <i class="fa-solid fa-triangle-exclamation text-orange-500 dark:text-orange-400"></i>
                                    <span class="text-sm font-semibold text-orange-700 dark:text-orange-400" x-text="step.title"></span>
                                </div>
                                <div class="text-xs text-orange-600/80 dark:text-orange-500/80 mt-1 pl-7" x-text="step.detail" x-show="step.detail"></div>
                            </div>
                        </template>

                        {{-- SUCCESS --}}
                        <template x-if="step.type === 'success'">
                            <div class="flex justify-center py-4">
                                <div class="inline-flex items-center gap-3 bg-emerald-600 dark:bg-emerald-500 px-7 py-4 rounded-2xl shadow-sm shadow-emerald-200 dark:shadow-none transition-colors">
                                    <i class="fa-solid fa-flag-checkered text-white"></i>
                                    <span class="text-white font-bold text-sm" x-text="step.title"></span>
                                </div>
                            </div>
                        </template>

                        {{-- FAIL --}}
                        <template x-if="step.type === 'fail'">
                            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/50 rounded-xl px-5 py-4 my-2 transition-colors">
                                <div class="flex items-center gap-3">
                                    <i class="fa-solid fa-circle-xmark text-red-600 dark:text-red-400"></i>
                                    <span class="text-sm font-bold text-red-800 dark:text-red-400" x-text="step.title"></span>
                                </div>
                                <div class="text-xs text-red-600/80 dark:text-red-500/80 mt-1 pl-7" x-text="step.detail" x-show="step.detail"></div>
                            </div>
                        </template>

                        {{-- EMPTY --}}
                        <template x-if="step.type === 'empty'">
                            <div class="border border-dashed border-gray-200 dark:border-gray-700 rounded-xl px-5 py-6 my-2 text-center bg-gray-50/50 dark:bg-gray-800/50 transition-colors">
                                <span class="text-sm italic text-gray-400 dark:text-gray-500" x-text="step.title"></span>
                            </div>
                        </template>
                    </div>
                </template>

                {{-- end-of-log cursor --}}
                <div class="flex items-center gap-2 pl-2 mt-2" x-show="idx < trace.length - 1">
                    <span class="w-1.5 h-4 bg-purple-400 dark:bg-purple-500 inline-block animate-pulse"></span>
                    <span class="text-[11px] text-gray-400 dark:text-gray-500">menjalankan...</span>
                </div>
            </div>
        </div>
    </div>
    </div>

    {{-- Transport bar (signature scrubber) --}}
    <div class="sticky bottom-0 inset-x-0 z-30 w-full">
        <div class="bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 shadow-[0_-4px_16px_rgba(0,0,0,0.04)] dark:shadow-none transition-colors duration-300">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-3">

                {{-- Scrubber ticks --}}
                <div class="flex w-full h-6 gap-[1.5px] mb-3 cursor-pointer rounded overflow-hidden bg-gray-100 dark:bg-gray-800 transition-colors">
                    <template x-for="(step, i) in trace" :key="'tick-'+i">
                        <div
                            @click="jumpTo(i)"
                            class="flex-1 rounded-[1px] transition-opacity"
                            :class="[tickColor(step.type, step.status), i <= idx ? 'opacity-100' : 'opacity-25']"
                            :title="step.title"
                        ></div>
                    </template>
                </div>

                <div class="flex items-center justify-between gap-3 flex-wrap">
                    <div class="flex items-center gap-2">
                        <button @click="restart()" class="w-9 h-9 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-500 dark:text-gray-400 flex items-center justify-center transition-colors shadow-sm" title="Ulang dari awal">
                            <i class="fa-solid fa-backward-step text-xs"></i>
                        </button>
                        <button @click="togglePlay()" class="w-10 h-10 rounded-lg bg-purple-600 hover:bg-purple-700 dark:bg-purple-500 dark:hover:bg-purple-600 text-white flex items-center justify-center transition-colors shadow-sm shadow-purple-200 dark:shadow-none active:scale-[0.98]">
                            <i class="fa-solid" :class="playing ? 'fa-pause' : 'fa-play'"></i>
                        </button>
                        <button @click="skipToEnd()" class="w-9 h-9 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-500 dark:text-gray-400 flex items-center justify-center transition-colors shadow-sm" title="Lompat ke akhir">
                            <i class="fa-solid fa-forward-step text-xs"></i>
                        </button>
                    </div>

                    <div class="flex items-center gap-1.5">
                        <span class="text-[11px] text-gray-400 dark:text-gray-500 mr-1">Speed</span>
                        <template x-for="s in [0.5, 1, 2, 4]" :key="s">
                            <button
                                @click="speed = s"
                                class="px-2.5 py-1 rounded-md text-[11px] font-semibold transition-colors"
                                :class="speed === s ? 'bg-purple-50 dark:bg-purple-900/40 text-purple-700 dark:text-purple-400 ring-1 ring-inset ring-purple-200 dark:ring-purple-700/50' : 'bg-white dark:bg-gray-800 text-gray-400 dark:text-gray-500 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700'"
                                x-text="s + 'x'"
                            ></button>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function traceRunner(trace) {
    return {
        trace: trace,
        idx: -1,
        playing: false,
        speed: 1,
        timer: null,
        tally: { assign: 0, backtrack: 0, check: 0 },

        get visible() {
            return this.trace.slice(0, this.idx + 1);
        },

        isMajor(type) {
            return type && type !== 'check';
        },

        tickColor(type, status) {
            switch (type) {
                case 'start': return 'bg-purple-500 dark:bg-purple-600';
                case 'process': return 'bg-purple-300 dark:bg-purple-800';
                case 'variable': return 'bg-purple-400 dark:bg-purple-700';
                case 'check': return status === 'pass' ? 'bg-emerald-400 dark:bg-emerald-600' : 'bg-gray-300 dark:bg-gray-600';
                case 'assign': return 'bg-emerald-500 dark:bg-emerald-500';
                case 'backtrack': return 'bg-amber-400 dark:bg-amber-500';
                case 'deadend': return 'bg-orange-500 dark:bg-orange-600';
                case 'success': return 'bg-emerald-600 dark:bg-emerald-500';
                case 'fail': return 'bg-red-500 dark:bg-red-600';
                default: return 'bg-gray-300 dark:bg-gray-600';
            }
        },

        recalcTally() {
            const t = { assign: 0, backtrack: 0, check: 0 };
            for (let i = 0; i <= this.idx; i++) {
                const type = this.trace[i].type;
                if (type === 'assign') t.assign++;
                if (type === 'backtrack') t.backtrack++;
                if (type === 'check') t.check++;
            }
            this.tally = t;
        },

        baseDelay(type) {
            switch (type) {
                case 'check': return 70;
                case 'assign':
                case 'backtrack':
                case 'deadend': return 420;
                case 'start':
                case 'success':
                case 'fail':
                case 'empty': return 700;
                default: return 550;
            }
        },

        step() {
            if (this.idx >= this.trace.length - 1) {
                this.playing = false;
                clearTimeout(this.timer);
                return;
            }
            this.idx++;
            this.recalcTally();
            this.$nextTick(() => {
                if (this.$refs.scroller) {
                    this.$refs.scroller.scrollTop = this.$refs.scroller.scrollHeight;
                }
            });
            if (this.playing) {
                const delay = this.baseDelay(this.trace[this.idx].type) / this.speed;
                this.timer = setTimeout(() => this.step(), delay);
            }
        },

        togglePlay() {
            if (this.playing) {
                this.playing = false;
                clearTimeout(this.timer);
            } else {
                this.playing = true;
                this.step();
            }
        },

        autoplay() {
            this.playing = true;
            this.step();
        },

        restart() {
            clearTimeout(this.timer);
            this.idx = -1;
            this.tally = { assign: 0, backtrack: 0, check: 0 };
            this.playing = true;
            this.step();
        },

        skipToEnd() {
            clearTimeout(this.timer);
            this.playing = false;
            this.idx = this.trace.length - 1;
            this.recalcTally();
            this.$nextTick(() => {
                if (this.$refs.scroller) {
                    this.$refs.scroller.scrollTop = this.$refs.scroller.scrollHeight;
                }
            });
        },

        jumpTo(i) {
            clearTimeout(this.timer);
            this.playing = false;
            this.idx = i;
            this.recalcTally();
            this.$nextTick(() => {
                if (this.$refs.scroller) {
                    this.$refs.scroller.scrollTop = this.$refs.scroller.scrollHeight;
                }
            });
        },
    }
}
</script>
</x-app-layout>
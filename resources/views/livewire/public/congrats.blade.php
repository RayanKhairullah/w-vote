<section class="container mx-auto py-10 px-4">
    <div class="max-w-4xl mx-auto">
        <div class="text-center mb-6">
            <flux:heading size="xl" class="mb-1">Ucapan Selamat</flux:heading>
            <p class="text-gray-600 dark:text-zinc-300">Terima kasih telah menggunakan aplikasi W‑Vote</p>
        </div>

        @if(!$election)
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-gray-200 dark:border-zinc-700/50 p-8 text-center">
                <p class="text-gray-700 dark:text-zinc-200">Belum ada pemilihan aktif saat ini.</p>
                <div class="mt-4">
                    <a href="{{ route('results') }}" wire:navigate>
                        <flux:button>Ke Halaman Hasil</flux:button>
                    </a>
                </div>
            </div>
        @else
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-gray-200 dark:border-zinc-700/50 p-4 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                    @if($elections->count() > 1)
                    <div class="sm:w-64">
                        <flux:select wire:model.live="electionId">
                            @foreach ($elections as $opt)
                            <option value="{{ $opt->id }}">{{ $opt->year }} - {{ $opt->name }}</option>
                            @endforeach
                        </flux:select>
                    </div>
                    @else
                    <div class="text-sm text-gray-600 dark:text-zinc-300">
                        {{ $election->year }} — {{ $election->name }}
                    </div>
                    @endif
                    
                    <div class="flex items-center gap-2">
                        <a href="{{ route('results', ['electionId' => $election->id]) }}" wire:navigate>
                            <flux:button variant="ghost">Lihat Hasil</flux:button>
                        </a>
                        <flux:button id="btn-download" icon="arrow-down-tray">Unduh Twibbon</flux:button>
                    </div>
                </div>

                @if($winner)
                    <!-- Twibbon Canvas -->
                    <div class="flex justify-center">
                        <div id="twibbon-canvas" class="relative w-full max-w-xl aspect-square rounded-2xl overflow-hidden shadow-lg">
                            <!-- Background gradient -->
                            <div class="absolute inset-0 bg-gradient-to-br from-indigo-500 via-blue-500 to-emerald-400"></div>

                            <!-- Decorative shapes -->
                            <div class="absolute -top-10 -left-10 w-40 h-40 rounded-full bg-white/10 blur-2xl"></div>
                            <div class="absolute -bottom-16 -right-10 w-56 h-56 rounded-full bg-black/10 blur-2xl"></div>

                            <!-- Content Layer -->
                            <div class="absolute inset-0 p-6 flex flex-col items-center justify-between text-center">
                                <div class="w-full mb-4">
                                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 text-white text-xs font-medium shadow">
                                        <img src="{{ asset('images/logo-w_vote.png') }}" alt="Logo Aplikasi" class="h-4 w-4 rounded-sm object-contain bg-white/70 p-[1px]" />
                                        @php $schoolLogo = public_path('images/logosmea.png'); @endphp
                                        @if (file_exists($schoolLogo))
                                            <img src="{{ asset('images/logosmea.png') }}" alt="Logo Sekolah" class="h-4 w-4 rounded-sm object-contain bg-white/70 p-[1px]" />
                                        @else
                                            <!-- Fallback placeholder icon for school logo -->
                                            <svg class="h-4 w-4 text-white/80" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3l9 5-9 5-9-5 9-5z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10l9 5 9-5"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 13v5a2 2 0 002 2h6a2 2 0 002-2v-5"/>
                                            </svg>
                                        @endif
                                        <span>Pemenang Pemilihan</span>
                                        <span class="opacity-80">•</span>
                                        <span>{{ $election->year }}</span>
                                    </div>
                                </div>

                                <div class="flex flex-col items-center">
                                    <div class="relative w-40 h-40 sm:w-48 sm:h-48 rounded-2xl overflow-hidden ring-4 ring-white/40 shadow-xl bg-white/20 backdrop-blur">
                                        @if($winner->photo_path)
                                            <img src="{{ Storage::url($winner->photo_path) }}" alt="{{ $winner->leader_name }}" class="absolute inset-0 w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full grid place-items-center text-white/80">Tidak ada foto</div>
                                        @endif
                                    </div>

                                    <div class="mt-4 text-center">
                                        <div class="text-white/90 text-sm mb-2">
                                            Nomor Urut {{ $winner->ballot_number ?? '-' }}
                                        </div>
                                        <div class="text-white font-extrabold text-lg sm:text-xl tracking-wide drop-shadow leading-snug text-center break-words max-w-xs mx-auto">
                                            {{ $winner->leader_name }} &amp; {{ $winner->deputy_name }}
                                        </div>
                                    </div>
                                </div>

                                <div class="w-full">
                                    <div class="mx-auto max-w-sm text-white/95">
                                        <div class="font-semibold text-base">Selamat!</div>
                                        <p class="text-sm">Terima kasih kepada seluruh pemilih yang telah berpartisipasi dan menggunakan aplikasi W‑Vote.</p>
                                        <div class="mt-2 text-[10px] opacity-80">#WVOTE #PemilihanOSIS #Transparan #Aman</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Frame Overlay -->
                            <div class="absolute inset-0 pointer-events-none">
                                <svg viewBox="0 0 100 100" class="w-full h-full">
                                    <defs>
                                        <linearGradient id="frameGrad" x1="0" y1="0" x2="1" y2="1">
                                            <stop offset="0%" stop-color="#ffffff" stop-opacity=".85" />
                                            <stop offset="100%" stop-color="#ffffff" stop-opacity=".35" />
                                        </linearGradient>
                                    </defs>
                                    <!-- Corner ribbons -->
                                    <rect x="0" y="0" width="100" height="100" fill="none" stroke="url(#frameGrad)" stroke-width="2" rx="4" ry="4" />
                                    <path d="M0,16 L16,0 M84,100 L100,84" stroke="url(#frameGrad)" stroke-width="4" stroke-linecap="round" />
                                </svg>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-12 text-gray-700 dark:text-zinc-300">
                        Belum ada perolehan suara untuk menentukan pemenang.
                    </div>
                @endif
            </div>
        @endif
    </div>

    <script src="https://unpkg.com/html-to-image@1.11.11/dist/html-to-image.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const btn = document.getElementById('btn-download');
            const target = document.getElementById('twibbon-canvas');
            if (!btn || !target) return;

            btn.addEventListener('click', async () => {
                try {
                    const dataUrl = await window.htmlToImage.toPng(target, {
                        cacheBust: true,
                        pixelRatio: 2,
                        backgroundColor: 'transparent',
                        width: target.offsetWidth,
                        height: target.offsetHeight,
                    });
                    const link = document.createElement('a');
                    link.download = 'wvote-twibbon-{{ $election?->year }}.png';
                    link.href = dataUrl;
                    link.click();
                } catch (e) {
                    console.error(e);
                    window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: 'Gagal mengunduh gambar.' } }));
                }
            });
        });
    </script>
</section>

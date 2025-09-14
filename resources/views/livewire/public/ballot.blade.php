<div class="max-w-6xl mx-auto px-4">
    <flux:heading size="xl" class="mb-2 text-center">Pilih Kandidat</flux:heading>

    <div class="mb-6 flex items-center justify-center">
        <span class="inline-flex items-center gap-2 rounded-full bg-gray-100 text-gray-700 dark:bg-zinc-800 dark:text-zinc-200 px-3 py-1 text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M6 2a1 1 0 00-1 1v.341C3.67 4.165 2.5 5.97 2.5 8s1.17 3.835 2.5 4.659V17a1 1 0 001.447.894l3.724-1.862a.5.5 0 01.447 0L14.342 17A1 1 0 0015.79 16v-3.341C17.12 11.835 18.29 10.03 18.29 8s-1.17-3.835-2.5-4.659V3a1 1 0 00-1-1H6z"/></svg>
            {{ $election->year }} — {{ $election->name }}
        </span>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach ($candidates as $c)
            <div
                @class([
                    'relative bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg shadow-sm p-4 flex flex-col gap-3 cursor-pointer transition hover:shadow-md',
                    'ring-2 ring-[#1b5fa0] hover:ring-[#174f86]' => $selected_candidate_id === $c->id,
                    'hover:ring-2 hover:ring-[#1b5fa0]/40' => $selected_candidate_id !== $c->id,
                ])
                wire:click="choose({{ $c->id }})"
                role="button"
                aria-label="Pilih pasangan nomor {{ $c->ballot_number }}"
            >
                <div class="flex items-center justify-between text-sm text-gray-500">
                    <div class="inline-flex items-center gap-2">
                        <span class="text-gray-600 dark:text-zinc-300">Nomor Urut</span>
                        <span class="inline-flex h-7 items-center gap-1 rounded-full bg-[#1b5fa0]/10 text-[#1b5fa0] border border-[#1b5fa0]/30 px-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3 5a2 2 0 012-2h10a2 2 0 012 2v1a1 1 0 01-1 1h-1.382l-1.447 7.236A2 2 0 0111.206 16H8.794a2 2 0 01-1.965-1.764L5.382 7H4a1 1 0 01-1-1V5zm5.382 2l1.2 6h1.036l1.2-6H8.382z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-semibold">{{ $c->ballot_number }}</span>
                        </span>
                    </div>
                </div>

                @if ($c->photo_path)
                    <div class="rounded-md w-full h-56 bg-white flex items-center justify-center">
                        <img src="{{ Storage::url($c->photo_path) }}" alt="{{ $c->leader_name }}" class="max-h-full max-w-full object-contain">
                    </div>
                @endif

                <div class="text-center">
                    <div class="text-base font-semibold text-gray-900 dark:text-white">
                        {{ $c->leader_name }} &amp; {{ $c->deputy_name }}
                    </div>
                </div>

                @if ($selected_candidate_id === $c->id)
                    <div class="absolute top-2 right-2 inline-flex items-center gap-1 rounded-full bg-[#1b5fa0] text-white px-2 py-0.5 text-xs">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        Dipilih
                    </div>
                @endif

                <div class="mt-auto"></div>
            </div>
        @endforeach
    </div>

    <div class="mt-6 h-20"></div>

    <!-- Sticky Action Bar -->
    <div class="fixed bottom-0 left-0 right-0 z-40 bg-white/80 dark:bg-zinc-900/80 backdrop-blur border-t border-gray-200 dark:border-zinc-800">
        <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between gap-3">
            <div class="text-sm text-gray-600 dark:text-zinc-300">
                @if ($selected_candidate_id)
                    Kandidat dipilih. Klik Kirim Suara untuk melanjutkan.
                @else
                    Pilih salah satu kandidat terlebih dahulu.
                @endif
            </div>
            <div>
                @if ($this->alreadyVoted)
                    <a href="{{ route('verify') }}" wire:navigate>
                        <flux:button> Kembali ke Verifikasi </flux:button>
                    </a>
                @else
                    <flux:button wire:click="openConfirm" :disabled="!$selected_candidate_id" style="background-color:#1b5fa0; color:white;">
                        Kirim Suara
                    </flux:button>
                @endif
            </div>
        </div>
    </div>

    @if (session('error'))
        <div class="mt-3">
            <flux:alert>{{ session('error') }}</flux:alert>
        </div>
    @endif

    <!-- Confirm Modal -->
    @if ($confirming)
        @php $selected = $candidates->firstWhere('id', $selected_candidate_id); @endphp
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" role="dialog" aria-modal="true" aria-labelledby="confirmTitle" aria-describedby="confirmDesc">
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-xl w-full max-w-md p-6">
                <div class="flex items-start gap-3 mb-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-[#1b5fa0]/10 text-[#1b5fa0]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M10.75 10.75v-4.5a.75.75 0 10-1.5 0v4.5a.75.75 0 001.5 0zM10 14a1 1 0 100-2 1 1 0 000 2z"/></svg>
                    </div>
                    <div>
                        <flux:heading id="confirmTitle" size="lg">Kirim Suara?</flux:heading>
                        <p id="confirmDesc" class="text-sm text-gray-600 dark:text-zinc-300 mt-1">Pastikan pilihan Anda sudah benar sebelum mengirim.</p>
                    </div>
                </div>

                @if($selected)
                    <div class="mb-4 rounded-md border border-gray-200 dark:border-zinc-800 p-3 flex items-center gap-3">
                        @if ($selected->photo_path)
                            <img src="{{ Storage::url($selected->photo_path) }}" alt="{{ $selected->leader_name }}" class="h-12 w-12 rounded object-cover">
                        @else
                            <div class="h-12 w-12 rounded bg-gray-100 dark:bg-zinc-800" aria-hidden="true"></div>
                        @endif
                        <div class="min-w-0">
                            <div class="text-sm text-gray-500">Nomor Urut <span class="font-semibold text-gray-900 dark:text-white">{{ $selected->ballot_number }}</span></div>
                            <div class="text-base font-semibold text-gray-900 dark:text-white truncate">{{ $selected->leader_name }} &amp; {{ $selected->deputy_name }}</div>
                        </div>
                    </div>
                @endif

                <div class="flex flex-col sm:flex-row sm:justify-end gap-2">
                    <flux:button variant="ghost" class="sm:min-w-[120px]" wire:click="closeConfirm">Batal</flux:button>
                    <flux:button variant="primary" class="sm:min-w-[140px]" wire:click="submit" style="background-color:#1b5fa0;">
                        Ya, Kirim
                    </flux:button>
                </div>
            </div>
        </div>
    @endif
</div>
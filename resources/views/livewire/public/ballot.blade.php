<div class="max-w-6xl mx-auto">
    <flux:heading size="lg" class="mb-4 text-center">Pilih Kandidat</flux:heading>

    <div class="text-center text-gray-600 dark:text-zinc-300 mb-4">
        {{ $election->year }} — {{ $election->name }}
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($candidates as $c)
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg shadow-sm p-4 flex flex-col gap-3">
                <div class="flex items-center justify-between text-sm text-gray-500">
                    <div>No Urut: <span class="font-semibold">{{ $c->ballot_number }}</span></div>
                </div>

                <div class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $c->leader_name }} &amp; {{ $c->deputy_name }}
                </div>

                @if ($c->photo_path)
                    <img src="{{ Storage::url($c->photo_path) }}" alt="{{ $c->leader_name }}" class="rounded-md w-full h-40 object-cover">
                @endif

                @if ($c->vision)
                    <div class="text-sm">
                        <div class="font-medium">Visi</div>
                        <div class="text-gray-600 dark:text-zinc-300">{!! nl2br(e($c->vision)) !!}</div>
                    </div>
                @endif

                @if ($c->mission)
                    <div class="text-sm">
                        <div class="font-medium">Misi</div>
                        <div class="text-gray-600 dark:text-zinc-300">{!! nl2br(e($c->mission)) !!}</div>
                    </div>
                @endif

                <div class="mt-auto">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="radio" wire:model="selected_candidate_id" value="{{ $c->id }}">
                        <span>Pilih pasangan ini</span>
                    </label>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6 flex items-center justify-center">
        @if ($this->alreadyVoted)
            <a href="{{ route('verify') }}" wire:navigate>
                <flux:button> Kembali ke Verifikasi </flux:button>
            </a>
        @else
            <flux:button wire:click="submit">Kirim Suara</flux:button>
        @endif
    </div>

    @if (session('error'))
        <div class="mt-3">
            <flux:alert>{{ session('error') }}</flux:alert>
        </div>
    @endif
</div>
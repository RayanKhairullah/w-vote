<div>
    <flux:heading size="lg" class="mb-4">Pilih Kandidat</flux:heading>

    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg p-4 shadow-sm">
        <div class="mb-4 text-gray-600">{{ $election->year }} - {{ $election->name }}</div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach ($candidates as $c)
                <div class="border rounded-md p-4 flex flex-col gap-3">
                    <div class="text-sm text-gray-500">No Urut: <span class="font-semibold">{{ $c->ballot_number }}</span></div>
                    <div class="text-lg font-semibold">{{ $c->leader_name }} & {{ $c->deputy_name }}</div>
                    @if ($c->photo_path)
                        <img src="{{ Storage::url($c->photo_path) }}" alt="{{ $c->leader_name }}" class="rounded-md">
                    @endif
                    <div class="text-sm">
                        <div class="font-medium">Visi</div>
                        <div class="text-gray-600">{!! nl2br(e($c->vision)) !!}</div>
                    </div>
                    <div class="text-sm">
                        <div class="font-medium">Misi</div>
                        <div class="text-gray-600">{!! nl2br(e($c->mission)) !!}</div>
                    </div>
                    <div>
                        <label class="inline-flex items-center gap-2">
                            <input type="radio" wire:model="selected_candidate_id" value="{{ $c->id }}">
                            <span>Pilih pasangan ini</span>
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-6">
            @if ($this->alreadyVoted)
                <a href="{{ route('verify') }}" wire:navigate>
                    <flux:button>Kembali ke Verifikasi</flux:button>
                </a>
            @else
                <flux:button wire:click="submit">Kirim Suara</flux:button>
            @endif
            @if (session('error'))
                <flux:alert class="mt-2">{{ session('error') }}</flux:alert>
            @endif
        </div>
    </div>
</div>

<div>
    <flux:heading size="lg" class="mb-4 text-center">Verifikasi Pemilih</flux:heading>

    <p class="max-w-3xl mx-auto mb-4 text-sm text-gray-600 text-center">
        Masukkan tahun pemilihan, identitas (NISN untuk siswa, NIP untuk staff), dan token yang Anda terima.
    </p>

    <div class="max-w-3xl mx-auto bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg p-6 shadow-sm">
        @if ($alreadyVoted)
            <div class="space-y-4 text-center">
                <flux:text class="block text-lg font-semibold">Anda sudah memberikan suara.</flux:text>
                <flux:alert>Terima kasih telah berpartisipasi. Anda dapat keluar dari halaman ini.</flux:alert>
                <div>
                    <flux:button wire:click="logoutVoter">Logout</flux:button>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 gap-6">
                <div class="space-y-4">
                    <flux:input wire:model.defer="year" type="number" label="Tahun Pemilihan" placeholder="2025" />
                    <flux:input wire:model.defer="identifier" label="NISN / NIP" placeholder="Masukkan identitas" />
                    <flux:input wire:model.defer="token" type="password" label="Token" placeholder="Masukkan token" />
                    <div class="flex gap-2">
                        <flux:button wire:click="submit">Lanjut</flux:button>
                    </div>
                    @if ($error)
                        <flux:alert class="mt-2">{{ $error }}</flux:alert>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

<div class="max-w-xl mx-auto">
    <flux:card class="p-6 text-center">
        <flux:heading size="lg" class="mb-2">{{ $title ?? 'Terjadi Kesalahan' }}</flux:heading>
        <p class="text-gray-600 dark:text-zinc-300 mb-4">
            {{ $message ?? 'Maaf, terjadi kendala saat memuat halaman.' }}
        </p>
        <div class="flex items-center justify-center gap-2">
            <a href="{{ $cta ?? route('verify') }}" wire:navigate>
                <flux:button> Kembali ke Verifikasi </flux:button>
            </a>
        </div>
    </flux:card>
</div>

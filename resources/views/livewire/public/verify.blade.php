<div class="h-full flex items-center justify-center px-6">
    <div class="w-full max-w-lg space-y-6">
        <!-- Logo and Title -->
        <div class="text-center">
            <div class="flex justify-center mb-4">
                <img src="{{ asset('images/logo-w_vote.png') }}" alt="W-Vote" class="h-16 w-16">
            </div>
            <flux:heading size="xl" class="mb-2">W-Vote</flux:heading>
            <flux:subheading>Verifikasi identitas untuk memulai pemilihan</flux:subheading>
        </div>

        <!-- Form Card -->
        <flux:card class="p-6">
            @if ($alreadyVoted)
                <div class="text-center space-y-4">
                    <flux:icon.check-circle variant="solid" size="xl" class="text-green-600 mx-auto" />
                    <flux:heading size="lg">Suara Anda Sudah Tercatat</flux:heading>
                    <flux:subheading>Terima kasih telah berpartisipasi dalam pemilihan ini.</flux:subheading>
                    <flux:button wire:click="logoutVoter" variant="filled" class="w-full h-11 text-base">
                        Keluar
                    </flux:button>
                </div>
            @else
                <div class="space-y-4">
                    <flux:input 
                        wire:model.defer="year" 
                        type="number" 
                        label="Tahun Pemilihan" 
                        placeholder="2025" 
                        size="md"
                    />
                    
                    <flux:input 
                        wire:model.defer="identifier" 
                        label="NISN / NIP" 
                        placeholder="Masukkan identitas Anda" 
                        size="md"
                    />
                    
                    <flux:input
                        wire:model.defer="token"
                        :label="__('Token')"
                        type="password"
                        required
                        autocomplete="current-password"
                        :placeholder="__('Token')"
                        viewable
                        size="md"
                    />

                    @if ($error)
                        <flux:alert variant="danger" class="text-sm">{{ $error }}</flux:alert>
                    @endif

                    <flux:button wire:click="submit" class="w-full h-11 text-base" style="background-color: #1b5fa0; color: white;">
                        Masuk ke Pemilihan
                    </flux:button>
                </div>
            @endif
        </flux:card>

        <!-- Footer -->
        <div class="text-center">
            <flux:subheading class="text-zinc-500">Sistem Pemilihan Elektronik</flux:subheading>
            <flux:subheading class="text-zinc-500">Develop by Oreo Team</flux:subheading>
        </div>
    </div>
</div>

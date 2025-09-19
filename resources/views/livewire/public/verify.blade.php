<div class="h-full flex items-center justify-center px-6">
    <!-- Notification Area -->
    @if(session('vote_success'))
        <div id="success-notification" class="fixed top-4 right-4 z-50 max-w-sm animate-in slide-in-from-right duration-300">
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 shadow-lg">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('vote_success') }}</p>
                    </div>
                    <div class="ml-auto pl-3">
                        <button onclick="hideNotification('success-notification')" class="inline-flex text-green-400 hover:text-green-600 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            setTimeout(() => hideNotification('success-notification'), 5000);
        </script>
    @endif

    @if(session('vote_error'))
        <div id="error-notification" class="fixed top-4 right-4 z-50 max-w-sm animate-in slide-in-from-right duration-300">
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 shadow-lg">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ session('vote_error') }}</p>
                    </div>
                    <div class="ml-auto pl-3">
                        <button onclick="hideNotification('error-notification')" class="inline-flex text-red-400 hover:text-red-600 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            setTimeout(() => hideNotification('error-notification'), 8000);
        </script>
    @endif

    <script>
        function hideNotification(id) {
            const notification = document.getElementById(id);
            if (notification) {
                notification.style.transform = 'translateX(100%)';
                notification.style.opacity = '0';
                setTimeout(() => notification.remove(), 300);
            }
        }
    </script>

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
            <flux:subheading class="text-zinc-500">Develop by Oreos Team</flux:subheading>
        </div>
    </div>
</div>

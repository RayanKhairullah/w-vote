<div x-data="{ formOpen: false }" x-on:open-election-form.window="formOpen = true" x-on:close-election-form.window="formOpen = false">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Manajemen Pemilihan</h1>
        <p class="text-gray-600 dark:text-zinc-300">Kelola semua pemilihan dalam sistem</p>
    </div>

    @if (session()->has('success'))
        <div x-data x-init="window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: $el.dataset.msg, timeout: 3000 } }))" data-msg="{{ session('success') }}"></div>
    @endif

    {{-- Modals: Open, Close, Delete --}}
    @if($confirmingOpen)
        <div class="fixed inset-0 z-[60] overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-black/50" wire:click="$set('confirmingOpen', false)"></div>
                <div class="relative bg-white dark:bg-zinc-800 rounded-lg p-6 w-full max-w-sm z-[70]">
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-emerald-100 dark:bg-emerald-900/30 mb-4">
                            <svg class="h-6 w-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Mulai Pemilihan</h3>
                        <p class="text-gray-500 dark:text-zinc-400 mb-6">Apakah Anda yakin ingin membuka pemilihan ini sekarang?</p>
                        <div class="flex justify-center gap-3">
                            <flux:button wire:click="$set('confirmingOpen', false)" variant="primary" class="px-4">Batal</flux:button>
                            <flux:button wire:click="performOpen" variant="primary" color="green" class="px-4">Ya, Mulai</flux:button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($confirmingClose)
        <div class="fixed inset-0 z-[60] overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-black/50" wire:click="$set('confirmingClose', false)"></div>
                <div class="relative bg-white dark:bg-zinc-800 rounded-lg p-6 w-full max-w-sm z-[70]">
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-amber-100 dark:bg-amber-900/30 mb-4">
                            <svg class="h-6 w-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 20c4.418 0 8-3.582 8-8s-3.582-8-8-8-8 3.582-8 8 3.582 8 8 8z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Tutup Pemilihan</h3>
                        <p class="text-gray-500 dark:text-zinc-400 mb-6">Apakah Anda yakin ingin menutup pemilihan ini?</p>
                        <div class="flex justify-center gap-3">
                            <flux:button wire:click="$set('confirmingClose', false)" variant="primary" class="px-4">Batal</flux:button>
                            <flux:button wire:click="performClose" variant="danger" class="px-4">Ya, Tutup</flux:button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($confirmingDeletion)
        <div class="fixed inset-0 z-[60] overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-black/50" wire:click="$set('confirmingDeletion', false)"></div>
                <div class="relative bg-white dark:bg-zinc-800 rounded-lg p-6 w-full max-w-sm z-[70]">
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30 mb-4">
                            <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Hapus Pemilihan</h3>
                        <p class="text-gray-500 dark:text-zinc-400 mb-6">Apakah Anda yakin ingin menghapus pemilihan ini? Tindakan ini tidak dapat dibatalkan.</p>
                        <div class="flex justify-center gap-3">
                            <flux:button wire:click="$set('confirmingDeletion', false)" variant="primary" class="px-4">Batal</flux:button>
                            <flux:button wire:click="performDelete" variant="danger" class="px-4">Ya, Hapus</flux:button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="space-y-6">
        <!-- Trigger Button -->
        <div class="flex justify-end">
            <flux:button variant="primary" icon="plus" x-on:click="$dispatch('open-election-form')" wire:click="resetForm">Tambah Pemilihan</flux:button>
        </div>

        <!-- Modal Form Pemilihan -->
        <div x-show="formOpen" x-transition.opacity class="fixed inset-0 z-[60] overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-black/50" @click="formOpen = false"></div>
                <div class="relative bg-white dark:bg-zinc-800 rounded-lg p-6 w-full max-w-xl z-[70]">
                    <div class="flex items-start justify-between mb-2">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Form Pemilihan</h3>
                            <p class="mt-1 text-xs text-gray-500 dark:text-zinc-400">Lengkapi data dasar pemilihan.</p>
                        </div>
                        <button class="p-2 rounded-md hover:bg-gray-100 dark:hover:bg-zinc-700" @click="formOpen = false" aria-label="Tutup">
                            <svg class="w-5 h-5 text-gray-500 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="h-px bg-gray-200 dark:bg-zinc-700/60 mb-4"></div>

                    <!-- Loading indicator when editing/fetching data -->
                    <div class="mb-3" wire:loading wire:target="edit">
                        <div class="inline-flex items-center gap-2 text-sm text-gray-500 dark:text-zinc-400">
                            <svg class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke-width="4"></circle>
                                <path class="opacity-75" stroke-width="4" d="M4 12a8 8 0 018-8" />
                            </svg>
                            Memuat data...
                        </div>
                    </div>

                    <div class="space-y-4" wire:loading.class="opacity-60 pointer-events-none" wire:target="save" aria-busy="false">
                        <flux:input wire:model.defer="name" label="Nama Pemilihan" placeholder="Pemilihan OSIS 2025" />
                        @error('name')
                            <p class="text-xs text-red-600 dark:text-red-400 -mt-2">{{ $message }}</p>
                        @enderror

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <flux:input wire:model.defer="year" type="number" label="Tahun" placeholder="2025" />
                                @error('year')
                                    <p class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <flux:select wire:model.defer="status" label="Status">
                                    <option value="draft">Draft</option>
                                    <option value="open">Open</option>
                                    <option value="closed">Closed</option>
                                </flux:select>
                                @error('status')
                                    <p class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <flux:input wire:model.defer="start_at" type="datetime-local" label="Mulai" />
                                @error('start_at')
                                    <p class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <flux:input wire:model.defer="end_at" type="datetime-local" label="Selesai" />
                                @error('end_at')
                                    <p class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-2">
                            <flux:button variant="ghost" icon="x-mark" @click="formOpen = false" wire:loading.attr="disabled" wire:target="save">Batal</flux:button>
                            <flux:button variant="primary" icon="check" wire:click="save" wire:loading.attr="disabled" wire:target="save">
                                <span wire:loading.remove wire:target="save">Simpan</span>
                                <span wire:loading wire:target="save">Menyimpan...</span>
                            </flux:button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg p-4 shadow-sm">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-4">
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Daftar Pemilihan</h3>
                    <p class="text-xs text-gray-500 dark:text-zinc-400">Kelola dan cari data pemilihan yang tersedia.</p>
                </div>
                <div class="w-full sm:w-80">
                    <flux:input
                        wire:model.live="q"
                        placeholder="Cari nama atau tahun..."
                        class="w-full"
                        icon="magnifying-glass" />
                </div>
            </div>

            <div class="overflow-x-auto bg-white dark:bg-zinc-800 shadow rounded-lg border border-gray-200 dark:border-zinc-700">
                <table class="w-full text-left text-sm text-gray-700 dark:text-zinc-200">
                    <thead class="bg-gray-100/80 dark:bg-zinc-700/80 text-gray-900 dark:text-zinc-100 uppercase text-xs font-semibold sticky top-0 z-10 backdrop-blur">
                        <tr>
                            <th class="px-4 py-3">Tahun</th>
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3" >Status</th>
                            <th class="px-4 py-3">Waktu</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-zinc-700">
                        @foreach ($items as $e)
                            <tr class="align-top transition duration-150 ease-in-out odd:bg-white even:bg-gray-50/60 hover:bg-gray-100/60 dark:odd:bg-zinc-800 dark:even:bg-zinc-800/60 dark:hover:bg-zinc-700/40">
                                <td class="px-4 py-3 font-medium text-gray-500 dark:text-zinc-400">{{ $e->year }}</td>
                                <td class="px-4 py-3">{{ $e->name }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center h-6 px-2 rounded-full text-xs font-semibold text-gray-800 dark:text-zinc-100
                                        {{ $e->status === 'open' ? 'bg-emerald-100 dark:bg-emerald-800/40' : ($e->status === 'closed' ? 'bg-red-100 dark:bg-red-800/40' : 'bg-gray-100 dark:bg-zinc-700') }}">
                                        {{ ucfirst($e->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-500 dark:text-zinc-400">
                                    {{ optional($e->start_at)->format('d/m/Y H:i') }} - {{ optional($e->end_at)->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-2 justify-end">
                                        <flux:button size="xs" variant="primary" icon="pencil-square" x-on:click="$dispatch('open-election-form')" wire:click="edit({{ $e->id }})">Edit</flux:button>
                                        @if($e->status !== 'open')
                                            <flux:button size="xs" variant="primary" color="green" icon="play" wire:click="confirmOpen({{ $e->id }})">Mulai</flux:button>
                                        @endif
                                        @if($e->status === 'open')
                                            <flux:button size="xs" variant="danger" icon="stop-circle" wire:click="confirmClose({{ $e->id }})">Tutup</flux:button>
                                        @endif
                                        <flux:button size="xs" variant="ghost" icon="trash" wire:click="confirmDelete({{ $e->id }})">Hapus</flux:button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if ($items->isEmpty())
                    <div class="px-4 py-8 text-center text-gray-500 dark:text-zinc-500">Belum ada pemilihan.</div>
                @endif
            </div>

            @if($items->hasPages())
            <div class="p-4 bg-gray-50 dark:bg-zinc-800 border-t border-gray-200 dark:border-zinc-700 transition-colors duration-200 mt-3 rounded-b-lg">
                <div class="flex flex-col md:flex-row items-center justify-between gap-3">
                    <p class="text-sm text-gray-600 dark:text-zinc-400">
                        Menampilkan
                        <span class="font-medium text-gray-900 dark:text-white">{{ $items->firstItem() }}</span>
                        –
                        <span class="font-medium text-gray-900 dark:text-white">{{ $items->lastItem() }}</span>
                        dari
                        <span class="font-semibold text-emerald-600 dark:text-emerald-400">{{ $items->total() }}</span>
                        data
                    </p>

                    <div class="[&>nav]:flex [&>nav]:items-center [&>nav]:gap-1">
                        {{ $items->links('components.pagination.simple-arrows') }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>


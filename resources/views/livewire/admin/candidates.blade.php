<div>
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Manajemen Kandidat</h1>
        <p class="text-gray-600 dark:text-zinc-300">Kelola semua kandidat dalam sistem</p>
    </div>

    @if (session()->has('success'))
        <div x-data x-init="window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: $el.dataset.msg, timeout: 3000 } }))" data-msg="{{ session('success') }}"></div>
    @endif
    
    {{-- Modal Form Kandidat --}}
    @if($showFormModal)
        <div class="fixed inset-0 z-[60] overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-black/50" wire:click="$set('showFormModal', false)"></div>
                <div class="relative bg-white dark:bg-zinc-800 rounded-lg p-6 w-full max-w-2xl z-[70]">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $editId ? 'Edit Kandidat' : 'Tambah Kandidat' }}</h3>
                    <p class="mt-1 text-xs text-gray-500 dark:text-zinc-400">Lengkapi informasi kandidat secara lengkap dan benar.</p>
                    <div class="mt-4 h-px bg-gray-200 dark:bg-zinc-700/60"></div>
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <flux:input wire:model.defer="leader_name" label="Nama Ketua" />
                            @error('leader_name')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <flux:input wire:model.defer="deputy_name" label="Nama Wakil" />
                            @error('deputy_name')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <flux:input wire:model.defer="ballot_number" type="number" label="Nomor Urut " />
                            @error('ballot_number')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="md:col-span-2" x-data="{ fileName: '' }">
                            <label class="block text-sm font-medium text-gray-800 dark:text-zinc-200 mb-1">Foto Kandidat</label>
                            <input x-ref="photo" id="photo" type="file" wire:model="photo" accept="image/*" class="hidden" @change="fileName = $refs.photo.files?.[0]?.name || ''" />
                            <div class="flex flex-wrap items-center gap-2">
                                <flux:button type="button" icon="photo" variant="primary" @click="$refs.photo.click()">Pilih Foto</flux:button>
                                <flux:button type="button" icon="x-mark" variant="ghost" class="text-red-600" @click="$refs.photo.value=''; fileName=''; $wire.set('photo', null)" x-show="fileName">Hapus Pilihan</flux:button>
                                <span class="text-xs text-gray-600 dark:text-zinc-400" x-text="fileName" x-show="fileName"></span>
                            </div>
                            <p class="mt-1 text-xs text-gray-500 dark:text-zinc-400">Gambar akan otomatis dikonversi ke .webp</p>
                            @error('photo')
                                <p class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                            @enderror
                            <div class="text-xs text-gray-500" wire:loading wire:target="photo">Mengunggah gambar...</div>
                            <div class="mt-3 flex items-center gap-4 flex-wrap">
                                @if ($photo)
                                    <div>
                                        <div class="text-xs text-gray-600 dark:text-zinc-400 mb-1">Preview baru:</div>
                                        <img src="{{ $photo->temporaryUrl() }}" alt="Preview baru" class="w-24 h-24 rounded-full object-cover border-2 border-gray-300 dark:border-zinc-700 shadow-sm" />
                                    </div>
                                @endif
                                @if ($photo_path)
                                    <div>
                                        <div class="text-xs text-gray-600 dark:text-zinc-400 mb-1">Foto saat ini:</div>
                                        <img src="{{ Storage::url($photo_path) }}" alt="Foto saat ini" class="w-24 h-24 rounded-full object-cover border-2 border-gray-300 dark:border-zinc-700 shadow-sm" />
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-800 dark:text-zinc-200 mb-1">Visi</label>
                            <textarea wire:model.defer="vision" rows="3"
                                class="w-full rounded-md border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-100 placeholder-gray-400 dark:placeholder-zinc-500"></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-800 dark:text-zinc-200 mb-1">Misi</label>
                            <textarea wire:model.defer="mission" rows="3"
                                class="w-full rounded-md border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-100 placeholder-gray-400 dark:placeholder-zinc-500"></textarea>
                        </div>
                    </div>
                    <div class="mt-6 flex items-center justify-end gap-2">
                        <flux:button variant="ghost" icon="x-mark" wire:click="$set('showFormModal', false)">Batal</flux:button>
                        <flux:button variant="primary" icon="check" wire:click="save" wire:loading.attr="disabled" wire:target="save,photo">
                            <span wire:loading.remove wire:target="save">Simpan</span>
                            <span wire:loading wire:target="save">Menyimpan...</span>
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Konfirmasi Hapus Kandidat --}}
    @if($confirmingDeletion)
        <div class="fixed inset-0 z-[60] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-black/50" wire:click="$set('confirmingDeletion', false)"></div>

                <!-- Modal panel -->
                <div class="relative bg-white dark:bg-zinc-800 rounded-lg p-6 w-full max-w-sm z-[70]">
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30 mb-4">
                            <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2" id="modal-title">Hapus Kandidat</h3>
                        <p class="text-gray-500 dark:text-zinc-400 mb-6">Apakah Anda yakin ingin menghapus kandidat ini? Tindakan ini tidak dapat dibatalkan.</p>
                        <div class="flex justify-center gap-3">
                            <flux:button wire:click="$set('confirmingDeletion', false)" variant="primary" class="px-4">Batal</flux:button>
                            <flux:button wire:click="performDelete" variant="danger" class="px-4">Ya, Hapus</flux:button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if (session()->has('error'))
        <div x-data x-init="window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: $el.dataset.msg, timeout: 3500 } }))" data-msg="{{ session('error') }}"></div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-3 flex justify-end">
            <flux:button icon="plus" variant="primary"  wire:click="openCreate">Tambah Kandidat</flux:button>
        </div>

        <flux:card class="lg:col-span-3">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-4">
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Daftar Kandidat</h3>
                    <p class="text-xs text-gray-500 dark:text-zinc-400">Kelola dan cari data kandidat yang tersedia.</p>
                </div>
                <div class="w-full sm:w-96">
                    <flux:input wire:model.live="q" placeholder="Cari nama ketua/wakil atau nomor urut..." class="w-full" icon="magnifying-glass" />
                </div>
            </div>

            <div class="overflow-x-auto bg-white dark:bg-zinc-800 shadow rounded-lg border border-gray-200 dark:border-zinc-700">
                <table class="w-full text-left text-sm text-gray-700 dark:text-zinc-200">
                    <thead class="bg-gray-100/80 dark:bg-zinc-700/80 text-gray-900 dark:text-zinc-100 uppercase text-xs font-semibold sticky top-0 z-10 backdrop-blur">
                        <tr>
                            <th class="px-4 py-3">No</th>
                            <th class="px-4 py-3">Gambar</th>
                            <th class="px-4 py-3">Ketua</th>
                            <th class="px-4 py-3">Wakil</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-zinc-700">
                        @foreach ($candidates as $c)
                            <tr class="align-top transition duration-150 ease-in-out odd:bg-white even:bg-gray-50/60 hover:bg-gray-100/60 dark:odd:bg-zinc-800 dark:even:bg-zinc-800/60 dark:hover:bg-zinc-700/40">
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full bg-gray-100 text-gray-700 dark:bg-zinc-700/40 dark:text-zinc-200 px-2 py-0.5 text-xs font-semibold">#{{ $c->ballot_number }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-center">
                                        @if($c->photo_path)
                                            <img src="{{ Storage::url($c->photo_path) }}" alt="{{ $c->leader_name }}" class="w-12 h-12 object-cover rounded-full border-2 border-gray-300 dark:border-gray-600">
                                        @else
                                            <div class="w-12 h-12 bg-gray-200 dark:bg-zinc-700 rounded-full flex items-center justify-center border-2 border-gray-300 dark:border-gray-600">
                                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m4-6a4 4 0 11-8 0 4 4 0 018 0zm10 6a4 4 0 11-8 0 4 4 0 018 0z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $c->leader_name }}</div>
                                </td>
                                <td class="px-4 py-3">{{ $c->deputy_name }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-2 justify-end">
                                        <flux:button size="xs" variant="primary" icon="pencil-square" wire:click="edit({{ $c->id }})">Edit</flux:button>
                                        <flux:button size="xs" variant="danger" icon="trash" wire:click="confirmDelete({{ $c->id }})">Hapus</flux:button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if ($candidates->isEmpty())
                    <div class="px-4 py-8 text-center text-gray-500 dark:text-zinc-500">Belum ada kandidat.</div>
                @endif
            </div>

            @if($candidates->hasPages())
            <div class="p-4 bg-gray-50 dark:bg-zinc-800 border-t border-gray-200 dark:border-zinc-700 transition-colors duration-200 mt-3 rounded-b-lg">
                <div class="flex flex-col md:flex-row items-center justify-between gap-3">
                    <p class="text-sm text-gray-600 dark:text-zinc-400">
                        Menampilkan
                        <span class="font-medium text-gray-900 dark:text-white">{{ $candidates->firstItem() }}</span>
                        –
                        <span class="font-medium text-gray-900 dark:text-white">{{ $candidates->lastItem() }}</span>
                        dari
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $candidates->total() }}</span>
                        data
                    </p>

                    <div class="[&>nav]:flex [&>nav]:items-center [&>nav]:gap-1">
                        {{ $candidates->links('components.pagination.simple-arrows') }}
                    </div>
                </div>
            </div>
            @endif
        </flux:card>
    </div>

    <div class="mt-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg p-4 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <flux:select wire:model="assignElectionId" label="Pilih Pemilihan" required>
                    <option value="">- Pilih -</option>
                    @foreach ($elections as $e)
                        <option value="{{ $e->id }}">{{ $e->year }} - {{ $e->name }}</option>
                    @endforeach
                </flux:select>
                @error('assignElectionId')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <flux:select wire:model="assignCandidateId" label="Pilih Kandidat" required>
                    <option value="">- Pilih -</option>
                    @foreach ($candidates as $c)
                        <option value="{{ $c->id }}">#{{ $c->ballot_number }} - {{ $c->leader_name }} & {{ $c->deputy_name }}</option>
                    @endforeach
                </flux:select>
                @error('assignCandidateId')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <flux:input wire:model="assignBallotNumber" type="number" label="Nomor Urut" required />
                @error('assignBallotNumber')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <flux:button variant="primary" wire:click="assignToElection" wire:loading.attr="disabled" wire:target="assignToElection">
                    <span wire:loading.remove wire:target="assignToElection">Assign</span>
                    <span wire:loading wire:target="assignToElection">Processing...</span>
                </flux:button>
            </div>
        </div>
    </div>
</div>

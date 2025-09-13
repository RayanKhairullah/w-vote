<div>
    <flux:heading size="lg" class="mb-4">Manajemen Kandidat</flux:heading>

    @if (session()->has('success'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-50 dark:bg-emerald-900 text-emerald-800 dark:text-emerald-100 border border-emerald-200 dark:border-emerald-700 flex items-center gap-2 shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-red-50 dark:bg-red-900 text-red-800 dark:text-red-100 border border-red-200 dark:border-red-700 flex items-center gap-2 shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg p-4 shadow-sm">
            <div class="space-y-3">
                <flux:input wire:model.defer="leader_name" label="Nama Ketua" />
                <flux:input wire:model.defer="deputy_name" label="Nama Wakil" />
                <flux:input wire:model.defer="ballot_number" type="number" label="Nomor Urut (global)" />
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Foto Kandidat</label>
                    <input type="file" wire:model="photo" accept="image/*" class="w-full rounded-md border-gray-300" />
                    <p class="mt-1 text-xs text-gray-500">Gambar akan otomatis dikonversi ke .webp</p>
                    @if ($photo_path)
                        <div class="mt-2 text-xs text-gray-600">Foto saat ini:</div>
                        <img src="{{ Storage::url($photo_path) }}" alt="Preview" class="mt-1 max-h-32 rounded" />
                    @endif
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Visi</label>
                    <textarea wire:model.defer="vision" class="w-full rounded-md border-gray-300" rows="3"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Misi</label>
                    <textarea wire:model.defer="mission" class="w-full rounded-md border-gray-300" rows="3"></textarea>
                </div>
                <div class="flex gap-2">
                    <flux:button variant="primary" icon="check" wire:click="save">Simpan</flux:button>
                    <flux:button variant="ghost" icon="x-mark" wire:click="resetForm">Reset</flux:button>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg p-4 shadow-sm">
            <div class="bg-white dark:bg-zinc-800 p-5 rounded-lg shadow border border-gray-200 dark:border-zinc-700 mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Cari Kandidat</label>
                <flux:input
                    wire:model.live="q"
                    placeholder="Cari nama ketua/wakil atau nomor urut..."
                    class="w-full"
                    icon="magnifying-glass" />
            </div>

            <div class="overflow-x-auto bg-white dark:bg-zinc-800 shadow rounded-lg border border-gray-200 dark:border-zinc-700">
                <table class="w-full text-left text-sm text-gray-700 dark:text-zinc-200">
                    <thead class="bg-gray-100 dark:bg-zinc-700 text-gray-900 dark:text-zinc-100 uppercase text-xs font-semibold">
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
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition duration-150 ease-in-out">
                                <td class="px-4 py-3 font-medium text-gray-500 dark:text-zinc-400">{{ $c->ballot_number }}</td>
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
                                        <flux:button size="xs" icon="pencil-square" wire:click="edit({{ $c->id }})">Edit</flux:button>
                                        <flux:button size="xs" icon="trash" wire:click="delete({{ $c->id }})">Hapus</flux:button>
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

            <div class="mt-3">{{ $candidates->links() }}</div>
        </div>
    </div>

    <div class="mt-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg p-4 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Pemilihan</label>
                <select wire:model="assignElectionId" class="w-full rounded-md border-gray-300">
                    <option value="">- Pilih -</option>
                    @foreach ($elections as $e)
                        <option value="{{ $e->id }}">{{ $e->year }} - {{ $e->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Kandidat</label>
                <select wire:model="assignCandidateId" class="w-full rounded-md border-gray-300">
                    <option value="">- Pilih -</option>
                    @foreach ($candidates as $c)
                        <option value="{{ $c->id }}">#{{ $c->ballot_number }} - {{ $c->leader_name }} & {{ $c->deputy_name }}</option>
                    @endforeach
                </select>
            </div>
            <flux:input wire:model="assignBallotNumber" type="number" label="Nomor Urut (untuk election)" />
            <div>
                <flux:button wire:click="assignToElection">Assign</flux:button>
            </div>
        </div>
    </div>
</div>

<div>
    <flux:heading size="lg" class="mb-4">Manajemen Kandidat</flux:heading>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg p-4 shadow-sm">
            <div class="space-y-3">
                <flux:input wire:model.defer="leader_name" label="Nama Ketua" />
                <flux:input wire:model.defer="deputy_name" label="Nama Wakil" />
                <flux:input wire:model.defer="ballot_number" type="number" label="Nomor Urut (global)" />
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Foto Kandidat (opsional)</label>
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
                    <flux:button wire:click="save">Simpan</flux:button>
                    <flux:button wire:click="resetForm">Reset</flux:button>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg p-4 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <flux:input wire:model.live="q" placeholder="Cari kandidat..." class="w-64" />
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-left text-gray-500">
                        <tr>
                            <th class="py-2 pr-4">No</th>
                            <th class="py-2 pr-4">Ketua</th>
                            <th class="py-2 pr-4">Wakil</th>
                            <th class="py-2 pr-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($candidates as $c)
                            <tr class="border-t">
                                <td class="py-2 pr-4 font-medium">{{ $c->ballot_number }}</td>
                                <td class="py-2 pr-4">{{ $c->leader_name }}</td>
                                <td class="py-2 pr-4">{{ $c->deputy_name }}</td>
                                <td class="py-2 pr-4 flex gap-2">
                                    <flux:button size="xs" wire:click="edit({{ $c->id }})">Edit</flux:button>
                                    <flux:button size="xs" wire:click="delete({{ $c->id }})">Hapus</flux:button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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

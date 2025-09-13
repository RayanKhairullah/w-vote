<div>
    <flux:heading size="lg" class="mb-4">Manajemen Pemilihan</flux:heading>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg p-4 shadow-sm">
            <div class="space-y-3">
                <flux:input wire:model.defer="name" label="Nama Pemilihan" placeholder="Pemilihan OSIS 2025" />
                <flux:input wire:model.defer="year" type="number" label="Tahun" placeholder="2025" />
                <flux:input wire:model.defer="start_at" type="datetime-local" label="Mulai" />
                <flux:input wire:model.defer="end_at" type="datetime-local" label="Selesai" />
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select wire:model.defer="status" class="w-full rounded-md border-gray-300">
                        <option value="draft">Draft</option>
                        <option value="open">Open</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <flux:button wire:click="save">Simpan</flux:button>
                    <flux:button wire:click="resetForm">Reset</flux:button>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg p-4 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <flux:input wire:model.live="q" placeholder="Cari nama/tahun..." class="w-64" />
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-left text-gray-500">
                        <tr>
                            <th class="py-2 pr-4">Tahun</th>
                            <th class="py-2 pr-4">Nama</th>
                            <th class="py-2 pr-4">Status</th>
                            <th class="py-2 pr-4">Waktu</th>
                            <th class="py-2 pr-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $e)
                            <tr class="border-t">
                                <td class="py-2 pr-4 font-medium">{{ $e->year }}</td>
                                <td class="py-2 pr-4">{{ $e->name }}</td>
                                <td class="py-2 pr-4">
                                    <span class="px-2 py-1 rounded bg-gray-100">{{ $e->status }}</span>
                                </td>
                                <td class="py-2 pr-4 text-gray-500">
                                    {{ optional($e->start_at)->format('d/m/Y H:i') }} - {{ optional($e->end_at)->format('d/m/Y H:i') }}
                                </td>
                                <td class="py-2 pr-4 flex gap-2">
                                    <flux:button size="xs" wire:click="edit({{ $e->id }})">Edit</flux:button>
                                    <flux:button size="xs" wire:click="openElection({{ $e->id }})">Open</flux:button>
                                    <flux:button size="xs" wire:click="closeElection({{ $e->id }})">Close</flux:button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $items->links() }}
            </div>
        </div>
    </div>
</div>

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
                    <select wire:model.defer="status" class="w-full rounded-md border-gray-300 bg-white dark:bg-zinc-800">
                        <option value="draft">Draft</option>
                        <option value="open">Open</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <flux:button variant="primary" icon="check" wire:click="save">Simpan</flux:button>
                    <flux:button variant="ghost" icon="x-mark" wire:click="resetForm">Reset</flux:button>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg p-4 shadow-sm">
            <div class="bg-white dark:bg-zinc-800 p-5 rounded-lg shadow border border-gray-200 dark:border-zinc-700 mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Cari Pemilihan</label>
                <flux:input
                    wire:model.live="q"
                    placeholder="Cari nama atau tahun..."
                    class="w-full"
                    icon="magnifying-glass" />
            </div>

            <div class="overflow-x-auto bg-white dark:bg-zinc-800 shadow rounded-lg border border-gray-200 dark:border-zinc-700">
                <table class="w-full text-left text-sm text-gray-700 dark:text-zinc-200">
                    <thead class="bg-gray-100 dark:bg-zinc-700 text-gray-900 dark:text-zinc-100 uppercase text-xs font-semibold">
                        <tr>
                            <th class="px-4 py-3">Tahun</th>
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Waktu</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-zinc-700">
                        @foreach ($items as $e)
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition duration-150 ease-in-out">
                                <td class="px-4 py-3 font-medium text-gray-500 dark:text-zinc-400">{{ $e->year }}</td>
                                <td class="px-4 py-3">{{ $e->name }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-full text-xs
                                        {{ $e->status === 'open' ? 'bg-emerald-100 dark:bg-emerald-800/40 text-emerald-800 dark:text-emerald-300' : ($e->status === 'closed' ? 'bg-red-100 dark:bg-red-800/40 text-red-800 dark:text-red-300' : 'bg-gray-100 dark:bg-zinc-700 text-gray-700 dark:text-zinc-200') }}">
                                        {{ $e->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-500">
                                    {{ optional($e->start_at)->format('d/m/Y H:i') }} - {{ optional($e->end_at)->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-2 justify-end">
                                        <flux:button size="xs" variant="primary" icon="pencil-square" wire:click="edit({{ $e->id }})">Edit</flux:button>
                                        <flux:button size="xs" variant="primary" icon="play" wire:click="openElection({{ $e->id }})">Open</flux:button>
                                        <flux:button size="xs" variant="danger" icon="stop-circle" wire:click="closeElection({{ $e->id }})">Close</flux:button>
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

            <div class="mt-3">
                {{ $items->links() }}
            </div>
        </div>
    </div>
</div>


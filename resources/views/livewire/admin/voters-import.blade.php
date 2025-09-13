<div>
    <flux:heading size="lg" class="mb-4">Import Data Pemilih</flux:heading>

    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg p-4 shadow-sm">
        <div class="space-y-4">
            <flux:input wire:model.defer="year" type="number" label="Tahun" placeholder="2025" />
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">File CSV</label>
                <input type="file" wire:model="file" class="w-full rounded-md border-gray-300" />
                <p class="mt-1 text-xs text-gray-500">
                    Format kolom (siswa): <code>type,identifier,name,class,major,token</code>
                    — (staff): <code>type,identifier,name,position,token</code>
                    — (gabungan): <code>type,identifier,name,class,major,position,token</code>
                </p>
            </div>
            <div class="flex gap-2">
                <flux:button wire:click="import">Mulai Import</flux:button>
            </div>
            @if (session('success'))
                <flux:alert>{{ session('success') }}</flux:alert>
            @endif
            @if (session('error'))
                <flux:alert>{{ session('error') }}</flux:alert>
            @endif
        </div>
    </div>

    <div class="mt-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg p-4 shadow-sm">
        <div class="bg-white dark:bg-zinc-800 p-5 rounded-lg shadow border border-gray-200 dark:border-zinc-700 mb-4">
            <div class="flex items-end gap-3 flex-wrap">
                <div class="flex-1 min-w-[220px]">
                    <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Cari Pemilih</label>
                    <flux:input wire:model.live="q" placeholder="Cari identifier / nama / tipe..." class="w-full" icon="magnifying-glass" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Filter Tahun</label>
                    <flux:input wire:model.defer="year" type="number" placeholder="2025" class="w-40" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Filter Tipe</label>
                    <select class="w-40 rounded-md border-gray-300 bg-white dark:bg-zinc-800" wire:model="filterType">
                        <option value="">Semua</option>
                        <option value="student">student</option>
                        <option value="staff">staff</option>
                    </select>
                </div>
                <div class="ms-auto text-sm text-gray-500">Menampilkan {{ $voters->total() }} data</div>
            </div>
        </div>

        <div class="overflow-x-auto bg-white dark:bg-zinc-800 shadow rounded-lg border border-gray-200 dark:border-zinc-700">
            <table class="w-full text-left text-sm text-gray-700 dark:text-zinc-200">
                <thead class="bg-gray-100 dark:bg-zinc-700 text-gray-900 dark:text-zinc-100 uppercase text-xs font-semibold">
                    <tr>
                        <th class="px-4 py-3">ID</th>
                        <th class="px-4 py-3">Tahun</th>
                        <th class="px-4 py-3">Tipe</th>
                        <th class="px-4 py-3">Identifier</th>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Kelas</th>
                        <th class="px-4 py-3">Jurusan</th>
                        <th class="px-4 py-3">Jabatan</th>
                        <th class="px-4 py-3">Token</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-zinc-700">
                    @forelse ($voters as $v)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition duration-150 ease-in-out align-top">
                            <td class="px-4 py-3 font-medium text-gray-500 dark:text-zinc-400">{{ $v->id }}</td>
                            <td class="px-4 py-3">{{ $v->year }}</td>
                            <td class="px-4 py-3">{{ $v->type }}</td>
                            <td class="px-4 py-3 font-mono">{{ $v->identifier }}</td>
                            <td class="px-4 py-3">{{ $v->name }}</td>
                            <td class="px-4 py-3">{{ $v->class }}</td>
                            <td class="px-4 py-3">{{ $v->major }}</td>
                            <td class="px-4 py-3">{{ $v->position }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    @if (isset($recentTokens[$v->id]))
                                        <code class="text-xs px-2 py-1 rounded bg-zinc-100 dark:bg-zinc-800">{{ $recentTokens[$v->id] }}</code>
                                    @else
                                        <span class="text-xs text-gray-500">—</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2 justify-end">
                                    <flux:button size="xs" variant="primary" icon="pencil-square" wire:click="editVoter({{ $v->id }})">Edit</flux:button>
                                    <flux:button size="xs" variant="danger" icon="trash" wire:click="deleteVoter({{ $v->id }})">Hapus</flux:button>
                                </div>
                            </td>
                        </tr>

                        @if ($editId === $v->id)
                            <tr class="border-t bg-zinc-50/50 dark:bg-zinc-800/30">
                                <td colspan="10" class="p-3">
                                    <div class="grid grid-cols-1 md:grid-cols-6 gap-3">
                                        <div>
                                            <label class="block text-sm mb-1">Tipe</label>
                                            <select class="w-full rounded-md border-gray-300" wire:model="e_type">
                                                <option value="student">student</option>
                                                <option value="staff">staff</option>
                                            </select>
                                        </div>
                                        <flux:input wire:model.defer="e_identifier" label="Identifier" />
                                        <flux:input wire:model.defer="e_name" label="Nama" />
                                        <flux:input wire:model.defer="e_class" label="Kelas" />
                                        <flux:input wire:model.defer="e_major" label="Jurusan" />
                                        <flux:input wire:model.defer="e_position" label="Jabatan" />
                                    </div>
                                    <div class="mt-3 flex gap-2">
                                        <flux:button wire:click="updateVoter">Simpan</flux:button>
                                        <flux:button wire:click="cancelEdit">Batal</flux:button>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="10" class="py-6 text-center text-gray-500">Belum ada data.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">{{ $voters->links() }}</div>
    </div>
</div>

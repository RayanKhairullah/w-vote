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
        <div class="flex items-center justify-between mb-3 gap-3">
            <div class="flex items-center gap-3">
                <flux:input wire:model.live="q" placeholder="Cari identifier/nama/tipe..." />
                <flux:input wire:model.defer="year" type="number" placeholder="Filter Tahun" />
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Filter Tipe</label>
                    <select class="w-40 rounded-md border-gray-300" wire:model="filterType">
                        <option value="">Semua</option>
                        <option value="student">student</option>
                        <option value="staff">staff</option>
                    </select>
                </div>
            </div>
            <div class="text-sm text-gray-500">Menampilkan {{ $voters->total() }} data</div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="text-left text-gray-500">
                    <tr>
                        <th class="py-2 pr-4">ID</th>
                        <th class="py-2 pr-4">Tahun</th>
                        <th class="py-2 pr-4">Tipe</th>
                        <th class="py-2 pr-4">Identifier</th>
                        <th class="py-2 pr-4">Nama</th>
                        <th class="py-2 pr-4">Kelas</th>
                        <th class="py-2 pr-4">Jurusan</th>
                        <th class="py-2 pr-4">Jabatan</th>
                        <th class="py-2 pr-4">Token</th>
                        <th class="py-2 pr-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($voters as $v)
                        <tr class="border-t align-top">
                            <td class="py-2 pr-4">{{ $v->id }}</td>
                            <td class="py-2 pr-4">{{ $v->year }}</td>
                            <td class="py-2 pr-4">{{ $v->type }}</td>
                            <td class="py-2 pr-4 font-mono">{{ $v->identifier }}</td>
                            <td class="py-2 pr-4">{{ $v->name }}</td>
                            <td class="py-2 pr-4">{{ $v->class }}</td>
                            <td class="py-2 pr-4">{{ $v->major }}</td>
                            <td class="py-2 pr-4">{{ $v->position }}</td>
                            <td class="py-2 pr-4">
                                <div class="flex items-center gap-2">
                                    @if (isset($recentTokens[$v->id]))
                                        <code class="text-xs px-2 py-1 rounded bg-zinc-100 dark:bg-zinc-800">{{ $recentTokens[$v->id] }}</code>
                                    @else
                                        <span class="text-xs text-gray-500">—</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-2 pr-4">
                                <div class="flex gap-2">
                                    <flux:button size="xs" wire:click="editVoter({{ $v->id }})">Edit</flux:button>
                                    <flux:button size="xs" wire:click="deleteVoter({{ $v->id }})">Hapus</flux:button>
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

<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Manajemen Data Pemilih</h1>
        <p class="text-gray-600 dark:text-zinc-300">Kelola semua pemilihan dalam sistem</p>
    </div>
    <flux:card>
        <div class="mb-3">
            <h3 class="text-sm font-medium text-gray-900 dark:text-white">Pengaturan Import</h3>
            <p class="text-xs text-gray-500 dark:text-zinc-400">Isi tahun dan pilih berkas CSV sesuai format.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <flux:input wire:model.defer="year" type="number" label="Tahun" placeholder="2025" />
                @error('year')
                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Tahun akademik/penyelenggaraan data pemilih.</p>
            </div>

            <div class="md:col-span-2 space-y-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300">File CSV</label>
                <input type="file" wire:model="file" accept=".csv,text/csv" class="w-full rounded-md border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-2.5 text-sm" />
                @error('file')
                <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror

                <div class="mt-2 rounded-md border border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-900/40 p-3">
                    <p class="text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Format kolom (wajib urut):</p>
                    <ul class="space-y-1 text-xs text-gray-600 dark:text-zinc-400">
                        <li class="flex items-start gap-2">
                            <span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-200 px-2 py-0.5 text-[11px] font-medium">Siswa</span>
                            <span>
                                <code class="rounded bg-zinc-100 px-1.5 py-0.5 dark:bg-zinc-800">type,identifier,name,class,major,token</code>
                            </span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="inline-flex items-center rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-200 px-2 py-0.5 text-[11px] font-medium">Staff</span>
                            <span>
                                <code class="rounded bg-zinc-100 px-1.5 py-0.5 dark:bg-zinc-800">type,identifier,name,position,token</code>
                            </span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="inline-flex items-center rounded-full bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-200 px-2 py-0.5 text-[11px] font-medium">Gabungan</span>
                            <span>
                                <code class="rounded bg-zinc-100 px-1.5 py-0.5 dark:bg-zinc-800">type,identifier,name,class,major,position,token</code>
                            </span>
                        </li>
                    </ul>
                    <p class="mt-2 text-[11px] text-gray-500 dark:text-zinc-400">Catatan: baris pertama harus berisi header sesuai format di atas. Token boleh dikosongkan (akan di-generate otomatis).</p>
                </div>
            </div>
        </div>
        <div class="mt-5 flex flex-col gap-2">
            <div class="flex flex-wrap items-center gap-3">
                <flux:button icon="arrow-up-tray" wire:click="import" wire:loading.attr="disabled" wire:target="import">
                    <span wire:loading.remove wire:target="import">Mulai Import</span>
                    <span wire:loading wire:target="import" class="inline-flex items-center gap-2">
                        Mengimpor...
                    </span>
                </flux:button>
                <span class="text-xs text-gray-400">Pastikan file sudah sesuai format.</span>
            </div>
            <div wire:loading wire:target="import" class="w-full">
                <div class="h-1 w-full bg-gray-200 dark:bg-zinc-700 rounded overflow-hidden">
                    <div class="h-1 w-1/3 bg-blue-500 dark:bg-blue-400 animate-pulse rounded"></div>
                </div>
            </div>
        </div>
    </flux:card>

    <flux:card class="mt-6">
        <div class="flex items-end gap-3 flex-wrap">
            <div class="flex-1 min-w-[220px]">
                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Cari Pemilih</label>
                <flux:input wire:model.live="q" placeholder="Cari identifier / nama / tipe..." class="w-full" icon="magnifying-glass" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Filter Tahun</label>
                <flux:input wire:model.live.debounce.400ms="filterYear" type="number" inputmode="numeric" step="1" min="2000" max="2100" placeholder="2025" class="w-40" />
            </div>
            <div class="w-40">
                <flux:select wire:model.live="filterType" label="Filter Tipe">
                    <option value="">Semua</option>
                    <option value="student">student</option>
                    <option value="staff">staff</option>
                </flux:select>
            </div>
            <div class="w-48">
                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Filter Kelas</label>
                <flux:input wire:model.live.debounce.400ms="filterClass" type="text" placeholder="cth: XII PPLG / XII TJKT 1" />
            </div>
            <div class="w-56">
                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Filter Jurusan</label>
                <flux:input wire:model.live.debounce.400ms="filterMajor" type="text" placeholder="cth: PPLG / TJKT" />
            </div>
            <div class="ms-auto flex items-end gap-2">
                <div class="w-44">
                    <flux:select wire:model="exportType" label="Tipe Export">
                        <option value="student">Siswa (student)</option>
                        <option value="staff">Staff (staff)</option>
                        <option value="unified">Gabungan (unified)</option>
                    </flux:select>
                </div>
                <div class="pb-0.5">
                    <label class="block text-sm font-medium text-transparent mb-1">Export</label>
                    <flux:button icon="arrow-down-tray" wire:click="export">Export CSV</flux:button>
                </div>
                <div class="pb-0.5">
                    <label class="block text-sm font-medium text-transparent mb-1">Hapus</label>
                    <flux:button icon="trash" variant="danger" wire:click="deleteSelected" :disabled="empty($selectedVoters)">Hapus Terpilih</flux:button>
                </div>
                @if (!empty($selectedVoters))
                <div class="pb-0.5 text-xs text-gray-600 dark:text-zinc-300">
                    <span class="block">Terpilih: {{ count($selectedVoters) }}</span>
                </div>
                @endif
            </div>
        </div>

        <div class="mt-4 overflow-x-auto rounded-lg border border-gray-200 dark:border-zinc-700">
            <table class="w-full text-left text-sm text-gray-700 dark:text-zinc-200">
                <thead class="bg-gray-100/80 dark:bg-zinc-700/80 text-gray-900 dark:text-zinc-100 uppercase text-xs font-semibold sticky top-0 z-10 backdrop-blur">
                    <tr>
                        <th class="px-4 py-3 w-10">
                            <input type="checkbox" wire:model.live="selectAll" class="rounded border-gray-300 dark:border-zinc-600 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-zinc-700 dark:checked:bg-blue-600">
                        </th>
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Tahun</th>
                        <th class="px-4 py-3">Tipe</th>
                        <th class="px-4 py-3">Identifier</th>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Kelas</th>
                        <th class="px-4 py-3">Jurusan</th>
                        <th class="px-4 py-3">Jabatan</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Token</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-zinc-700">
                    @forelse ($voters as $v)
                    <tr class="align-top transition duration-150 ease-in-out odd:bg-white even:bg-gray-50/60 hover:bg-gray-100/60 dark:odd:bg-zinc-800 dark:even:bg-zinc-800/60 dark:hover:bg-zinc-700/40">
                        <td class="px-4 py-3">
                            <input type="checkbox" wire:model.live="selectedVoters" value="{{ $v->id }}" class="rounded border-gray-300 dark:border-zinc-600 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-zinc-700 dark:checked:bg-blue-600">
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-500 dark:text-zinc-400">{{ $voters->firstItem() + $loop->index }}</td>
                        <td class="px-4 py-3">{{ $v->year }}</td>
                        <td class="px-4 py-3 capitalize">{{ $v->type }}</td>
                        <td class="px-4 py-3 font-mono text-[13px]">{{ $v->identifier }}</td>
                        <td class="px-4 py-3">{{ $v->name }}</td>
                        <td class="px-4 py-3">{{ $v->class ?: '-' }}</td>
                        <td class="px-4 py-3">{{ $v->major ?: '-' }}</td>
                        <td class="px-4 py-3">{{ $v->position ?: '-' }}</td>
                        <td class="px-4 py-3">
                            @if ($v->has_voted)
                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-200">Sudah memilih</span>
                            @else
                            <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600 dark:bg-zinc-800 dark:text-zinc-300">Belum memilih</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                @if (isset($recentTokens[$v->id]))
                                <code class="text-xs px-2 py-1 rounded bg-zinc-100 dark:bg-zinc-800">{{ $recentTokens[$v->id] }}</code>
                                @else
                                <span class="text-xs text-gray-400">-</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex gap-2 justify-end">
                                <flux:button size="xs" variant="primary" icon="pencil-square" wire:click="editVoter({{ $v->id }})">Edit</flux:button>
                            </div>
                        </td>
                    </tr>

                    @if ($editId === $v->id)
                    <tr class="border-t bg-zinc-50/60 dark:bg-zinc-800/30">
                        <td colspan="12" class="p-3">
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 text-sm">
                                <div class="md:col-span-2">
                                    <flux:select wire:model="e_type" label="Tipe">
                                        <option value="student">student</option>
                                        <option value="staff">staff</option>
                                    </flux:select>
                                    @error('type')
                                    <p class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="md:col-span-2">
                                    <flux:input wire:model.defer="e_identifier" label="Identifier" class="text-sm" />
                                </div>
                                <div class="md:col-span-3">
                                    <flux:input wire:model.defer="e_name" label="Nama" class="text-sm" />
                                </div>
                                <div class="md:col-span-1">
                                    <flux:input wire:model.defer="e_class" label="Kelas" class="text-sm" />
                                </div>
                                <div class="md:col-span-2">
                                    <flux:input wire:model.defer="e_major" label="Jurusan" class="text-sm" />
                                </div>
                                <div class="md:col-span-2">
                                    <flux:input wire:model.defer="e_position" label="Jabatan" class="text-sm" />
                                </div>
                            </div>
                            <div class="mt-3 flex gap-2 justify-end">
                                <flux:button icon="check" wire:click="updateVoter">Simpan</flux:button>
                                <flux:button icon="x-mark" wire:click="cancelEdit">Batal</flux:button>
                            </div>
                        </td>
                    </tr>
                    @endif
                    @empty
                    <tr>
                        <td colspan="12" class="py-10 text-center">
                            <div class="text-sm text-gray-500 dark:text-zinc-400">
                                Tidak ada data untuk ditampilkan.
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($voters->hasPages())
        <div class="p-4 bg-gray-50 dark:bg-zinc-800 border-t border-gray-200 dark:border-zinc-700 transition-colors duration-200 mt-3 rounded-b-lg">
            <div class="flex flex-col md:flex-row items-center justify-between gap-3">
                <p class="text-sm text-gray-600 dark:text-zinc-400">
                    Menampilkan
                    <span class="font-medium text-gray-900 dark:text-white">{{ $voters->firstItem() }}</span>
                    –
                    <span class="font-medium text-gray-900 dark:text-white">{{ $voters->lastItem() }}</span>
                    dari
                    <span class="font-semibold text-emerald-600 dark:text-emerald-400">{{ $voters->total() }}</span>
                    data
                </p>

                <div class="[&>nav]:flex [&>nav]:items-center [&>nav]:gap-1">
                    {{ $voters->links('components.pagination.simple-arrows') }}
                </div>
            </div>
        </div>
        @endif
    </flux:card>

    

    
</div>
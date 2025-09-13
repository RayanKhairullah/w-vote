<?php

namespace App\Livewire\Admin;

use App\Models\ImportLog;
use App\Models\Voter;
use App\Models\VoterPlainToken;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Crypt;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class VotersImport extends Component
{
    use WithFileUploads;
    use WithPagination;

    public ?int $year = null;
    public $file; // Livewire temporary uploaded file

    // Listing & search
    public string $q = '';
    public int $perPage = 10;
    public ?string $filterType = null; // 'student' | 'staff' | null
    public ?int $filterYear = null; // listing filter: year
    public ?string $filterClass = null; // listing filter: class
    public ?string $filterMajor = null; // listing filter: major

    // Edit form
    public ?int $editId = null;
    public string $e_type = 'student';
    public string $e_identifier = '';
    public string $e_name = '';
    public ?string $e_class = null;
    public ?string $e_major = null;
    public ?string $e_position = null;

    // Recently generated tokens (plain) per voter id for display
    public array $recentTokens = [];

    // Deletion confirmation modal state
    public bool $confirmingDeletion = false;
    public ?int $voterIdToDelete = null;

    // Export
    public string $exportType = 'unified'; // 'student' | 'staff' | 'unified'

    // Import confirmation modal
    public bool $confirmingImport = false;

    public function mount(): void
    {
        abort_unless(Auth::check(), 403);
        // Default filter year to current year if not set
        $this->filterYear = $this->filterYear ?? (int) now()->year;
    }

    public function import(): void
    {
        try {
            $data = [
                'year' => $this->year,
                'file' => $this->file,
            ];
            Validator::make($data, [
                'year' => 'required|integer|min:2000|max:2100',
                'file' => 'required|file|mimes:csv,txt|max:10240',
            ])->validate();

            // Ensure directory exists and store via Storage
            if (!Storage::disk('local')->exists('imports')) {
                Storage::disk('local')->makeDirectory('imports');
            }
            $path = Storage::disk('local')->putFile('imports', $this->file);
            $full = storage_path('app/'.$path);

            $handle = Storage::disk('local')->readStream($path);
            $header = fgetcsv($handle);
        // Acceptable headers:
        // 1) student: type,identifier,name,class,major,token
        // 2) staff:   type,identifier,name,position,token
        // 3) unified: type,identifier,name,class,major,position,token
        $expectedStudent = ['type','identifier','name','class','major','token'];
        $expectedStaff = ['type','identifier','name','position','token'];
        $expectedUnified = ['type','identifier','name','class','major','position','token'];
        $total = $inserted = $updated = $failed = 0;
        $details = [];
        $generatedCount = 0;

            if (!$header) {
                session()->flash('error', 'File kosong');
                $this->dispatch('toast', message: 'File kosong', type: 'error');
                return;
            }

        $map = array_map(fn($h) => strtolower(trim($h)), $header);
        $mode = null; // 'student' | 'staff' | 'unified'
        if ($map === $expectedStudent) {
            $mode = 'student';
        } elseif ($map === $expectedStaff) {
            $mode = 'staff';
        } elseif ($map === $expectedUnified) {
            $mode = 'unified';
            } else {
                $msg = 'Header CSV tidak sesuai. Contoh siswa: '.implode(',', $expectedStudent).' | staff: '.implode(',', $expectedStaff);
                session()->flash('error', $msg);
                $this->dispatch('toast', message: $msg, type: 'error');
                return;
            }

        while (($row = fgetcsv($handle)) !== false) {
            $total++;
            try {
                if ($mode === 'student') {
                    [$type, $identifier, $name, $class, $major, $token] = $row;
                    $position = null;
                } elseif ($mode === 'staff') {
                    [$type, $identifier, $name, $position, $token] = $row;
                    $class = null; $major = null;
                } else { // unified
                    [$type, $identifier, $name, $class, $major, $position, $token] = $row;
                }
                $type = strtolower(trim($type));
                if (!in_array($type, ['student','staff'], true)) {
                    throw new \RuntimeException('type invalid');
                }
                $identifier = trim($identifier);
                $plainToken = trim((string) $token);
                $generatedThisRow = false;
                if ($plainToken === '') {
                    // Auto-generate 6-char alphanumeric uppercase token when absent
                    $plainToken = strtoupper(Str::random(6));
                    $generatedThisRow = true;
                    $generatedCount++;
                }
                $payload = [
                    'type' => $type,
                    'identifier' => $identifier,
                    'name' => trim($name),
                    'class' => trim($class) ?: null,
                    'major' => trim($major) ?: null,
                    'position' => trim($position) ?: null,
                    'token_hash' => Hash::make($plainToken),
                    'has_voted' => false,
                    'year' => $this->year,
                ];

                $existing = Voter::where('identifier', $identifier)
                    ->where('year', $this->year)
                    ->first();

                if ($existing) {
                    $existing->fill($payload)->save();
                    $voterSaved = $existing;
                    $updated++;
                } else {
                    $voterSaved = Voter::create($payload);
                    $inserted++;
                }

                if ($generatedThisRow && isset($voterSaved)) {
                    $this->recentTokens[$voterSaved->id] = $plainToken;
                    Session::put('import.generated_tokens.'.$voterSaved->id, $plainToken);
                }

                // Persist encrypted plain token for admin export/reference
                if (!empty($plainToken) && isset($voterSaved)) {
                    VoterPlainToken::updateOrCreate(
                        ['voter_id' => $voterSaved->id],
                        ['token_encrypted' => Crypt::encryptString($plainToken)]
                    );
                }
            } catch (\Throwable $e) {
                $failed++;
                $details[] = 'Row '.($total + 1).': '.$e->getMessage();
            }
        }
            fclose($handle);

        ImportLog::create([
            'admin_id' => Auth::id(),
            'filename' => basename($full),
            'total_records' => $total,
            'inserted' => $inserted,
            'updated' => $updated,
            'failed' => $failed,
            'details' => $details ? implode("\n", $details) : null,
            'created_at' => now(),
        ]);

            $msg = "Import selesai. total=$total, inserted=$inserted, updated=$updated, failed=$failed";
            if ($generatedCount > 0) {
                $msg .= ", token otomatis dibuat: $generatedCount";
            }
            session()->flash('success', $msg);
            $this->dispatch('toast', message: $msg, type: 'success');
        } catch (\Throwable $e) {
            // Generic failure toast
            $this->dispatch('toast', message: 'Gagal import: '.$e->getMessage(), type: 'error');
            throw $e;
        }
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        // Keep recent tokens across refresh by reading from session
        $this->recentTokens = Session::get('import.generated_tokens', []);

        $voters = Voter::query()
            ->when($this->filterYear, fn($q) => $q->where('year', $this->filterYear))
            ->when($this->filterType, fn($q) => $q->where('type', $this->filterType))
            ->when($this->filterClass, fn($q) => $q->where('class', 'like', '%'.trim($this->filterClass).'%'))
            ->when($this->filterMajor, fn($q) => $q->where('major', 'like', '%'.trim($this->filterMajor).'%'))
            ->when($this->q, function ($q) {
                $term = "%{$this->q}%";
                $q->where(function ($qq) use ($term) {
                    $qq->where('identifier', 'like', $term)
                       ->orWhere('name', 'like', $term)
                       ->orWhere('type', 'like', $term);
                });
            })
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        // Merge encrypted tokens from DB for the current page (fallback when session is empty)
        $ids = collect($voters->items())->pluck('id')->all();
        if ($ids) {
            $encrypted = VoterPlainToken::whereIn('voter_id', $ids)->get(['voter_id','token_encrypted']);
            foreach ($encrypted as $row) {
                try {
                    $plain = Crypt::decryptString($row->token_encrypted);
                    $this->recentTokens[$row->voter_id] = $plain;
                } catch (\Throwable $e) {
                    // ignore decrypt errors
                }
            }
        }

        return view('livewire.admin.voters-import', [
            'voters' => $voters,
        ]);
    }

    public function updatingQ()
    {
        $this->resetPage();
    }

    public function updatingFilterType()
    {
        $this->resetPage();
    }

    public function updatingFilterYear()
    {
        $this->resetPage();
    }

    public function updatingFilterClass()
    {
        $this->resetPage();
    }

    public function updatingFilterMajor()
    {
        $this->resetPage();
    }

    public function confirmImport(): void
    {
        $this->confirmingImport = true;
    }

    public function proceedImport(): void
    {
        $this->confirmingImport = false;
        $this->import();
    }

    public function confirmDelete(int $id): void
    {
        $this->voterIdToDelete = $id;
        $this->confirmingDeletion = true;
    }

    public function performDelete(): void
    {
        if (!$this->voterIdToDelete) {
            $this->confirmingDeletion = false;
            return;
        }
        $id = $this->voterIdToDelete;
        Voter::whereKey($id)->delete();
        VoterPlainToken::where('voter_id', $id)->delete();
        Session::forget('import.generated_tokens.'.$id);
        $this->confirmingDeletion = false;
        $this->voterIdToDelete = null;
        session()->flash('success', 'Data pemilih dihapus.');
        $this->dispatch('toast', message: 'Data pemilih dihapus.', type: 'success');
    }

    public function editVoter(int $id): void
    {
        $v = Voter::findOrFail($id);
        $this->editId = $v->id;
        $this->e_type = $v->type;
        $this->e_identifier = $v->identifier;
        $this->e_name = $v->name;
        $this->e_class = $v->class;
        $this->e_major = $v->major;
        $this->e_position = $v->position;
    }

    public function cancelEdit(): void
    {
        $this->editId = null;
        $this->e_type = 'student';
        $this->e_identifier = '';
        $this->e_name = '';
        $this->e_class = null;
        $this->e_major = null;
        $this->e_position = null;
    }

    public function updateVoter(): void
    {
        if (!$this->editId) return;
        $payload = [
            'type' => $this->e_type,
            'identifier' => trim($this->e_identifier),
            'name' => trim($this->e_name),
            'class' => $this->e_class ?: null,
            'major' => $this->e_major ?: null,
            'position' => $this->e_position ?: null,
        ];
        Validator::make($payload, [
            'type' => 'required|in:student,staff',
            'identifier' => 'required|string|max:50',
            'name' => 'required|string|max:200',
            'class' => 'nullable|string|max:100',
            'major' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:150',
        ])->validate();

        $v = Voter::findOrFail($this->editId);
        // Enforce unique identifier per year
        $exists = Voter::where('year', $v->year)
            ->where('identifier', $payload['identifier'])
            ->where('id', '!=', $v->id)
            ->exists();
        if ($exists) {
            session()->flash('error', 'Identifier sudah digunakan untuk tahun ini.');
            $this->dispatch('toast', message: 'Identifier sudah digunakan untuk tahun ini.', type: 'error');
            return;
        }

        $v->fill($payload)->save();
        $this->cancelEdit();
        session()->flash('success', 'Data pemilih diperbarui.');
        $this->dispatch('toast', message: 'Data pemilih diperbarui.', type: 'success');
    }

    public function deleteVoter(int $id): void
    {
        Voter::whereKey($id)->delete();
        // Remove any stored plain token for this voter from session mapping
        Session::forget('import.generated_tokens.'.$id);
        VoterPlainToken::where('voter_id', $id)->delete();
        session()->flash('success', 'Data pemilih dihapus.');
        $this->dispatch('toast', message: 'Data pemilih dihapus.', type: 'success');
    }

    // Manual regeneration removed by request; tokens are generated only during import

    public function export()
    {
        $type = in_array($this->exportType, ['student','staff','unified'], true) ? $this->exportType : 'unified';
        $year = $this->filterYear;

        $headers = match ($type) {
            'student' => ['type','identifier','name','class','major','token'],
            'staff' => ['type','identifier','name','position','token'],
            default => ['type','identifier','name','class','major','position','token'],
        };

        $query = Voter::query()
            ->when($year, fn($q) => $q->where('year', $year))
            ->when($type !== 'unified', fn($q) => $q->where('type', $type))
            ->when($this->filterClass, fn($q) => $q->where('class', 'like', '%'.trim($this->filterClass).'%'))
            ->when($this->filterMajor, fn($q) => $q->where('major', 'like', '%'.trim($this->filterMajor).'%'))
            ->orderBy('id');

        $filename = 'voters_'.($year ?: 'all')."_{$type}_".now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($query, $headers, $type) {
            $out = fopen('php://output', 'w');
            // Optional: BOM for Excel compatibility
            fprintf($out, "\xEF\xBB\xBF");
            fputcsv($out, $headers);

            $query->chunk(1000, function ($chunk) use ($out, $type) {
                foreach ($chunk as $v) {
                    // Resolve plain token from session mapping or encrypted table
                    $plainToken = session()->get('import.generated_tokens.'.$v->id, '');
                    if ($plainToken === '') {
                        $rec = VoterPlainToken::where('voter_id', $v->id)->first();
                        if ($rec) {
                            try { $plainToken = Crypt::decryptString($rec->token_encrypted); } catch (\Throwable $e) { $plainToken = ''; }
                        }
                    }
                    // Force Excel to treat as text if present
                    $tokenText = $plainToken !== '' ? '="'.str_replace('"', '""', $plainToken).'"' : '';
                    if ($type === 'student') {
                        $row = [
                            $v->type,
                            $v->identifier,
                            $v->name,
                            $v->class,
                            $v->major,
                            $tokenText, // token as text
                        ];
                    } elseif ($type === 'staff') {
                        $row = [
                            $v->type,
                            $v->identifier,
                            $v->name,
                            $v->position,
                            $tokenText, // token as text
                        ];
                    } else { // unified
                        $row = [
                            $v->type,
                            $v->identifier,
                            $v->name,
                            $v->class,
                            $v->major,
                            $v->position,
                            $tokenText, // token as text
                        ];
                    }
                    fputcsv($out, $row);
                }
            });

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}

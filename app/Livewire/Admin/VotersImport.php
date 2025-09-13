<?php

namespace App\Livewire\Admin;

use App\Models\ImportLog;
use App\Models\Voter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
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

    public function mount(): void
    {
        abort_unless(Auth::check(), 403);
    }

    public function import(): void
    {
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
            session()->flash('error', 'Header CSV tidak sesuai. Contoh siswa: '.implode(',', $expectedStudent).' | staff: '.implode(',', $expectedStaff));
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
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        // Keep recent tokens across refresh by reading from session
        $this->recentTokens = Session::get('import.generated_tokens', []);

        $voters = Voter::query()
            ->when($this->year, fn($q) => $q->where('year', $this->year))
            ->when($this->filterType, fn($q) => $q->where('type', $this->filterType))
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
            return;
        }

        $v->fill($payload)->save();
        $this->cancelEdit();
        session()->flash('success', 'Data pemilih diperbarui.');
    }

    public function deleteVoter(int $id): void
    {
        Voter::whereKey($id)->delete();
        // Remove any stored plain token for this voter from session mapping
        Session::forget('import.generated_tokens.'.$id);
        session()->flash('success', 'Data pemilih dihapus.');
    }

    // Manual regeneration removed by request; tokens are generated only during import
}

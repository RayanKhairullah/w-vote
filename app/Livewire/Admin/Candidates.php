<?php

namespace App\Livewire\Admin;

use App\Models\Candidate;
use App\Models\CandidateElection;
use App\Models\Election;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

class Candidates extends Component
{
    use WithFileUploads;
    #[Url]
    public string $q = '';

    public ?int $editId = null;
    public string $leader_name = '';
    public string $deputy_name = '';
    public ?int $ballot_number = null;
    public ?string $photo_path = null; // stored path (existing)
    public $photo = null; // uploaded file (temporary)
    public ?string $vision = null;
    public ?string $mission = null;

    public ?int $assignElectionId = null;
    public ?int $assignCandidateId = null;
    public ?int $assignBallotNumber = null;

    // Deletion confirmation modal state
    public bool $confirmingDeletion = false;
    public ?int $candidateIdToDelete = null;

    // Form modal state
    public bool $showFormModal = false;

    public function mount(): void
    {
        abort_unless(Auth::check(), 403);
    }

    public function resetForm(): void
    {
        $this->editId = null;
        $this->leader_name = '';
        $this->deputy_name = '';
        $this->ballot_number = null;
        $this->photo_path = null;
        $this->photo = null;
        $this->vision = null;
        $this->mission = null;
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showFormModal = true;
    }

    public function edit(int $id): void
    {
        $c = Candidate::findOrFail($id);
        $this->editId = $c->id;
        $this->leader_name = $c->leader_name;
        $this->deputy_name = $c->deputy_name;
        $this->ballot_number = $c->ballot_number;
        $this->photo_path = $c->photo_path;
        $this->vision = $c->vision;
        $this->mission = $c->mission;
        $this->showFormModal = true;
    }

    public function save(): void
    {
        $data = [
            'leader_name' => trim($this->leader_name),
            'deputy_name' => trim($this->deputy_name),
            'ballot_number' => $this->ballot_number,
            'vision' => $this->vision,
            'mission' => $this->mission,
        ];

        $validated = Validator::make($data, [
            'leader_name' => 'required|string|max:200',
            'deputy_name' => 'required|string|max:200',
            'ballot_number' => 'required|integer|min:1|unique:candidates,ballot_number,' . ($this->editId ?? 'NULL'),
            'vision' => 'nullable|string',
            'mission' => 'nullable|string',
        ])->validate();

        // Validate photo upload (optional)
        Validator::make(['photo' => $this->photo], [
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ])->validate();

        // Process upload to .webp if provided
        $newPhotoPath = null;
        if ($this->photo) {
            $newPhotoPath = $this->processAndStoreWebp($this->photo);
            $validated['photo_path'] = $newPhotoPath;
        }

        if ($this->editId) {
            $c = Candidate::findOrFail($this->editId);
            // If new photo provided, optionally delete old file
            if ($newPhotoPath && $c->photo_path && Storage::disk('public')->exists($c->photo_path)) {
                Storage::disk('public')->delete($c->photo_path);
            }
            $c->fill($validated)->save();
        } else {
            Candidate::create($validated);
        }

        $this->resetForm();
        $this->dispatch('toast', message: 'Kandidat disimpan', type: 'success');
        $this->showFormModal = false;
    }

    public function delete(int $id): void
    {
        Candidate::findOrFail($id)->delete();
        $this->dispatch('toast', message: 'Kandidat dihapus', type: 'success');
    }

    public function confirmDelete(int $id): void
    {
        $this->candidateIdToDelete = $id;
        $this->confirmingDeletion = true;
    }

    public function performDelete(): void
    {
        if (!$this->candidateIdToDelete) {
            $this->confirmingDeletion = false;
            return;
        }
        $id = $this->candidateIdToDelete;
        Candidate::findOrFail($id)->delete();
        $this->confirmingDeletion = false;
        $this->candidateIdToDelete = null;
        $this->dispatch('toast', message: 'Kandidat dihapus', type: 'success');
    }

    public function assignToElection(): void
    {
        $data = [
            'election_id' => $this->assignElectionId,
            'candidate_id' => $this->assignCandidateId,
            'ballot_number' => $this->assignBallotNumber,
        ];
        try {
            Validator::make($data, [
                'election_id' => 'required|exists:elections,id',
                'candidate_id' => 'required|exists:candidates,id',
                'ballot_number' => 'required|integer|min:1',
            ])->validate();
        } catch (ValidationException $e) {
            // Show a single toast notification for incomplete/invalid inputs
            $this->dispatch('toast', message: 'Lengkapi semua field dengan benar.', type: 'error');
            throw $e;
        }

        CandidateElection::updateOrCreate(
            [
                'election_id' => $this->assignElectionId,
                'candidate_id' => $this->assignCandidateId,
            ],
            ['ballot_number' => $this->assignBallotNumber]
        );

        $this->dispatch('toast', message: 'Kandidat ditambahkan ke pemilihan', type: 'success');
        $this->assignElectionId = $this->assignCandidateId = $this->assignBallotNumber = null;
    }

    public function unassign(int $candidateElectionId): void
    {
        CandidateElection::findOrFail($candidateElectionId)->delete();
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        $candidates = Candidate::query()
            ->when($this->q, fn($q) => $q->where('leader_name', 'like', "%{$this->q}%")->orWhere('deputy_name', 'like', "%{$this->q}%"))
            ->orderBy('ballot_number')
            ->paginate(10);

        $elections = Election::orderByDesc('year')->get();

        return view('livewire.admin.candidates', [
            'candidates' => $candidates,
            'elections' => $elections,
        ]);
    }

    private function processAndStoreWebp($uploadedFile): string
    {
        // Ensure directory exists
        $disk = Storage::disk('public');
        $dir = 'candidates';
        if (!$disk->exists($dir)) {
            $disk->makeDirectory($dir);
        }

        $originalName = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeName = str($originalName)->slug('_');
        $filename = $safeName . '_' . uniqid() . '.webp';

        // Read image
        $contents = file_get_contents($uploadedFile->getRealPath());
        $image = imagecreatefromstring($contents);
        if (!$image) {
            throw new \RuntimeException('Gagal memproses gambar');
        }

        // Encode to webp buffer
        ob_start();
        imagepalettetotruecolor($image);
        imagealphablending($image, true);
        imagesavealpha($image, true);
        imagewebp($image, null, 85);
        $webpData = ob_get_clean();
        imagedestroy($image);

        // Store
        $disk->put($dir . '/' . $filename, $webpData, 'public');
        return $dir . '/' . $filename;
    }
}

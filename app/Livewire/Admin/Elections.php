<?php

namespace App\Livewire\Admin;

use App\Models\Election;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

class Elections extends Component
{
    #[Url]
    public string $q = '';

    public ?int $editId = null;
    public string $name = '';
    public ?int $year = null;
    public ?string $start_at = null;
    public ?string $end_at = null;
    public string $status = 'draft';

    // Modal state
    public bool $confirmingOpen = false;
    public bool $confirmingClose = false;
    public bool $confirmingDeletion = false;
    public ?int $targetId = null;

    public function mount(): void
    {
        // Restrict to authenticated admins (users)
        abort_unless(Auth::check(), 403);
    }

    public function resetForm(): void
    {
        $this->editId = null;
        $this->name = '';
        $this->year = null;
        $this->start_at = null;
        $this->end_at = null;
        $this->status = 'draft';
    }

    public function edit(int $id): void
    {
        $e = Election::findOrFail($id);
        $this->editId = $e->id;
        $this->name = $e->name;
        $this->year = $e->year;
        $this->start_at = optional($e->start_at)?->format('Y-m-d\TH:i');
        $this->end_at = optional($e->end_at)?->format('Y-m-d\TH:i');
        $this->status = $e->status;
    }

    public function save(): void
    {
        $data = [
            'name' => trim($this->name),
            'year' => $this->year,
            'start_at' => $this->start_at ? Carbon::parse($this->start_at) : null,
            'end_at' => $this->end_at ? Carbon::parse($this->end_at) : null,
            'status' => $this->status,
        ];

        Validator::make($data, [
            'name' => 'required|string|max:200',
            'year' => 'required|integer|min:2000|max:2100|unique:elections,year,' . ($this->editId ?? 'NULL'),
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
            'status' => 'required|in:draft,open,closed',
        ])->validate();

        if ($this->editId) {
            $e = Election::findOrFail($this->editId);
            $e->fill($data)->save();
        } else {
            Election::create($data);
        }

        $this->resetForm();
        session()->flash('success', 'Election saved');
        // Close the modal on the front-end (Alpine listens for this)
        $this->dispatch('close-election-form');
    }

    // Confirmation triggers
    public function confirmOpen(int $id): void
    {
        $this->targetId = $id;
        $this->confirmingOpen = true;
    }

    public function confirmClose(int $id): void
    {
        $this->targetId = $id;
        $this->confirmingClose = true;
    }

    public function confirmDelete(int $id): void
    {
        $this->targetId = $id;
        $this->confirmingDeletion = true;
    }

    // Perform actions
    public function performOpen(): void
    {
        if (!$this->targetId) return;
        $e = Election::findOrFail($this->targetId);
        $e->status = 'open';
        if (!$e->start_at) $e->start_at = now();
        $e->save();
        $this->confirmingOpen = false;
        $this->targetId = null;
        session()->flash('success', 'Pemilihan dibuka.');
    }

    public function performClose(): void
    {
        if (!$this->targetId) return;
        $e = Election::findOrFail($this->targetId);
        $e->status = 'closed';
        if (!$e->end_at) $e->end_at = now();
        $e->save();
        $this->confirmingClose = false;
        $this->targetId = null;
        session()->flash('success', 'Pemilihan ditutup.');
    }

    public function performDelete(): void
    {
        if (!$this->targetId) return;
        $e = Election::findOrFail($this->targetId);
        $e->delete();
        $this->confirmingDeletion = false;
        $this->targetId = null;
        session()->flash('success', 'Pemilihan dihapus.');
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        $items = Election::query()
            ->when($this->q, fn($q) => $q->where('name', 'like', "%{$this->q}%")->orWhere('year', $this->q))
            ->orderByDesc('year')
            ->paginate(10);

        return view('livewire.admin.elections', [
            'items' => $items,
        ]);
    }
}

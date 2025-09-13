<?php

namespace App\Livewire\Public;

use App\Models\Election;
use App\Models\Vote;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

class Results extends Component
{
    #[Url]
    public ?int $electionId = null; // election id

    #[Layout('components.layouts.app')]
    public function render()
    {
        $election = $this->electionId
            ? Election::findOrFail($this->electionId)
            : Election::where('status', 'open')->orderByDesc('year')->first();

        $totals = collect();
        if ($election) {
            $totals = Vote::query()
                ->selectRaw('candidate_id, COUNT(*) as total')
                ->where('election_id', $election->id)
                ->groupBy('candidate_id')
                ->pluck('total', 'candidate_id');
        }

        return view('livewire.public.results', [
            'election' => $election,
            'totals' => $totals,
        ]);
    }
}

<?php

namespace App\Livewire\Public;

use App\Models\Candidate;
use App\Models\CandidateElection;
use App\Models\Election;
use App\Models\Vote;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

class Congrats extends Component
{
    #[Url]
    public ?int $electionId = null;

    public ?Election $election = null;
    public ?Candidate $winner = null;
    public int $winnerVotes = 0;

    #[Layout('components.layouts.app')]
    public function render()
    {
        $this->election = $this->resolveElection();
        if ($this->election) {
            [$this->winner, $this->winnerVotes] = $this->resolveWinner($this->election->id);
        }

        // Get all elections for dropdown
        $elections = Election::orderByDesc('year')->get();

        return view('livewire.public.congrats', [
            'election' => $this->election,
            'winner' => $this->winner,
            'winnerVotes' => $this->winnerVotes,
            'elections' => $elections,
        ]);
    }

    protected function resolveElection(): ?Election
    {
        if ($this->electionId) {
            return Election::find($this->electionId);
        }
        return Election::where('status', 'open')->orderByDesc('year')->first();
    }

    /**
     * @return array{0: ?Candidate, 1: int}
     */
    protected function resolveWinner(int $electionId): array
    {
        // Get totals for the election
        $totals = Vote::query()
            ->selectRaw('candidate_id, COUNT(*) as total')
            ->where('election_id', $electionId)
            ->groupBy('candidate_id')
            ->pluck('total', 'candidate_id');

        if ($totals->isEmpty()) {
            return [null, 0];
        }

        $maxVotes = (int) $totals->max();
        $topCandidateIds = $totals->filter(fn($v) => (int)$v === $maxVotes)->keys();

        // In case of tie, choose the one with the smallest ballot_number
        $winner = Candidate::select('candidates.*', 'candidate_election.ballot_number')
            ->join('candidate_election', 'candidate_election.candidate_id', '=', 'candidates.id')
            ->where('candidate_election.election_id', $electionId)
            ->whereIn('candidates.id', $topCandidateIds)
            ->orderBy('candidate_election.ballot_number')
            ->first();

        return [$winner, $maxVotes];
    }
}

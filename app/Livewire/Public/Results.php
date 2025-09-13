<?php

namespace App\Livewire\Public;

use App\Models\Election;
use App\Models\Vote;
use App\Models\Voter;
use App\Models\CandidateElection;
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
        $totalVotes = 0;
        $stats = [
            'totalVoters' => 0,
            'studentCount' => 0,
            'staffCount' => 0,
            'studentPct' => 0.0,
            'staffPct' => 0.0,
            'participants' => 0,
            'participationPct' => 0.0,
            'nonParticipants' => 0,
            'candidateCount' => 0,
        ];
        if ($election) {
            $totals = Vote::query()
                ->selectRaw('candidate_id, COUNT(*) as total')
                ->where('election_id', $election->id)
                ->groupBy('candidate_id')
                ->pluck('total', 'candidate_id');
            $totalVotes = (int) $totals->sum();

            // Voter population based on the election year
            $year = $election->year;
            $stats['totalVoters'] = Voter::where('year', $year)->count();
            $stats['studentCount'] = Voter::where('year', $year)->where('type', 'student')->count();
            $stats['staffCount'] = Voter::where('year', $year)->where('type', 'staff')->count();

            if ($stats['totalVoters'] > 0) {
                $stats['studentPct'] = round($stats['studentCount'] / $stats['totalVoters'] * 100, 1);
                $stats['staffPct'] = round($stats['staffCount'] / $stats['totalVoters'] * 100, 1);
            }

            // Participation based on distinct voter_id in votes for this election
            $stats['participants'] = Vote::where('election_id', $election->id)
                ->distinct('voter_id')
                ->count('voter_id');
            $stats['nonParticipants'] = max(0, $stats['totalVoters'] - $stats['participants']);
            if ($stats['totalVoters'] > 0) {
                $stats['participationPct'] = round($stats['participants'] / $stats['totalVoters'] * 100, 1);
            }

            // Number of candidates in this election
            $stats['candidateCount'] = CandidateElection::where('election_id', $election->id)->count();
        }

        // Elections list for dropdown
        $elections = Election::orderByDesc('year')->get();

        return view('livewire.public.results', [
            'election' => $election,
            'totals' => $totals,
            'stats' => $stats,
            'elections' => $elections,
            'totalVotes' => $totalVotes,
        ]);
    }
}

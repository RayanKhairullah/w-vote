<?php

namespace App\Services;

use App\Models\Election;
use App\Models\Vote;
use App\Models\Voter;
use App\Models\Candidate;
use App\Models\CandidateElection;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;
use App\Exports\ElectionResultsExport;

class ElectionExportService
{
    public function exportDetailedResults(Election $election, string $format = 'xlsx')
    {
        $data = $this->prepareDetailedData($election);
        
        $filename = "laporan-pemilihan-{$election->year}-{$election->name}-" . now()->format('Y-m-d-H-i-s') . ".{$format}";
        
        return Excel::download(new ElectionResultsExport($data), $filename);
    }

    public function prepareDetailedData(Election $election): array
    {
        // Get all votes with voter and candidate information
        $votes = Vote::with(['voter', 'candidate'])
            ->where('election_id', $election->id)
            ->orderBy('created_at', 'asc')
            ->get();

        // Get election candidates with ballot numbers
        $candidates = Candidate::select('candidates.*', 'candidate_election.ballot_number')
            ->join('candidate_election', 'candidate_election.candidate_id', '=', 'candidates.id')
            ->where('candidate_election.election_id', $election->id)
            ->orderBy('candidate_election.ballot_number')
            ->get();

        // Get all registered voters for this election year
        $allVoters = Voter::where('year', $election->year)->get();
        
        // Prepare vote details
        $voteDetails = $votes->map(function ($vote) {
            return [
                'No' => $vote->id,
                'Waktu Voting' => $vote->created_at->format('d/m/Y H:i:s'),
                'Nama Pemilih' => $vote->voter->name,
                'Tipe Pemilih' => ucfirst($vote->voter->type),
                'Identifier' => $vote->voter->identifier,
                'Kelas/Posisi' => $vote->voter->type === 'student' ? $vote->voter->class : $vote->voter->position,
                'Jurusan' => $vote->voter->major ?? '-',
                'Kandidat Dipilih' => "#{$vote->candidate->ballot_number} {$vote->candidate->leader_name} & {$vote->candidate->deputy_name}",
                'Nomor Urut' => $vote->candidate->ballot_number,
                'Nama Ketua' => $vote->candidate->leader_name,
                'Nama Wakil' => $vote->candidate->deputy_name,
            ];
        });

        // Prepare summary data
        $voteCounts = $votes->groupBy('candidate_id')->map->count();
        $totalVotes = $votes->count();
        
        $candidateSummary = $candidates->map(function ($candidate) use ($voteCounts, $totalVotes) {
            $voteCount = $voteCounts->get($candidate->id, 0);
            $percentage = $totalVotes > 0 ? round(($voteCount / $totalVotes) * 100, 2) : 0;
            
            return [
                'Nomor Urut' => $candidate->ballot_number,
                'Nama Ketua' => $candidate->leader_name,
                'Nama Wakil' => $candidate->deputy_name,
                'Jumlah Suara' => $voteCount,
                'Persentase' => $percentage . '%',
            ];
        });

        // Participation statistics
        $totalVoters = $allVoters->count();
        $participatingVoters = $votes->unique('voter_id')->count();
        $nonParticipatingVoters = $totalVoters - $participatingVoters;
        $participationRate = $totalVoters > 0 ? round(($participatingVoters / $totalVoters) * 100, 2) : 0;

        // Voter composition
        $studentVoters = $allVoters->where('type', 'student')->count();
        $staffVoters = $allVoters->where('type', 'staff')->count();
        
        $studentParticipation = $votes->whereIn('voter_id', $allVoters->where('type', 'student')->pluck('id'))->unique('voter_id')->count();
        $staffParticipation = $votes->whereIn('voter_id', $allVoters->where('type', 'staff')->pluck('id'))->unique('voter_id')->count();

        // Non-participating voters
        $participatingVoterIds = $votes->pluck('voter_id')->unique();
        $nonParticipatingVotersList = $allVoters->whereNotIn('id', $participatingVoterIds)->map(function ($voter) {
            return [
                'Nama' => $voter->name,
                'Tipe' => ucfirst($voter->type),
                'Identifier' => $voter->identifier,
                'Kelas/Posisi' => $voter->type === 'student' ? $voter->class : $voter->position,
                'Jurusan' => $voter->major ?? '-',
            ];
        });

        return [
            'election' => [
                'name' => $election->name,
                'year' => $election->year,
                'start_at' => $election->start_at?->format('d/m/Y H:i:s'),
                'end_at' => $election->end_at?->format('d/m/Y H:i:s'),
                'status' => ucfirst($election->status),
                'generated_at' => now()->format('d/m/Y H:i:s'),
            ],
            'statistics' => [
                'total_voters' => $totalVoters,
                'participating_voters' => $participatingVoters,
                'non_participating_voters' => $nonParticipatingVoters,
                'participation_rate' => $participationRate . '%',
                'total_votes' => $totalVotes,
                'student_voters' => $studentVoters,
                'staff_voters' => $staffVoters,
                'student_participation' => $studentParticipation,
                'staff_participation' => $staffParticipation,
            ],
            'candidate_summary' => $candidateSummary,
            'vote_details' => $voteDetails,
            'non_participating_voters' => $nonParticipatingVotersList,
        ];
    }
}

<?php

namespace App\Livewire\Public;

use App\Models\Candidate;
use App\Models\CandidateElection;
use App\Models\Election;
use App\Models\Vote;
use App\Models\Voter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Ballot extends Component
{
    public ?int $selected_candidate_id = null;
    public bool $alreadyVoted = false;

    public function submit(): void
    {
        $voterId = Session::get('voting.voter_id');
        $electionId = Session::get('voting.election_id');
        $tokenHashUsed = Session::get('voting.token_hash_used');

        if (!$voterId || !$electionId) {
            session()->flash('error', 'Sesi pemilihan tidak valid. Silakan verifikasi ulang.');
            redirect()->route('verify');
            return;
        }

        Validator::make(['selected_candidate_id' => $this->selected_candidate_id], [
            'selected_candidate_id' => 'required|exists:candidates,id',
        ])->validate();

        DB::transaction(function () use ($voterId, $electionId, $tokenHashUsed) {
            $voter = Voter::lockForUpdate()->findOrFail($voterId);
            if ($voter->has_voted) {
                throw new \RuntimeException('Anda sudah memberikan suara.');
            }

            Vote::create([
                'election_id' => $electionId,
                'voter_id' => $voterId,
                'candidate_id' => $this->selected_candidate_id,
                'token_hash_used' => $tokenHashUsed,
                'created_at' => now(),
            ]);

            $voter->has_voted = true;
            $voter->last_voted_at = now();
            $voter->save();
        });

        // Clear session and go back to verify so next voter can proceed
        Session::forget(['voting.voter_id', 'voting.election_id', 'voting.token_hash_used']);
        redirect()->route('verify');
    }

    #[Layout('components.layouts.public')]
    public function render()
    {
        try {
            $electionId = Session::get('voting.election_id');
            if (!$electionId) {
                return redirect()->route('verify');
            }

            $voterId = Session::get('voting.voter_id');
            if ($voterId) {
                $voter = Voter::find($voterId);
                $this->alreadyVoted = (bool) ($voter?->has_voted);
            } else {
                $this->alreadyVoted = false;
            }

            $election = Election::findOrFail($electionId);
            $candidates = Candidate::select('candidates.*', 'candidate_election.ballot_number')
                ->join('candidate_election', 'candidate_election.candidate_id', '=', 'candidates.id')
                ->where('candidate_election.election_id', $electionId)
                ->orderBy('candidate_election.ballot_number')
                ->get();

            return view('livewire.public.ballot', [
                'election' => $election,
                'candidates' => $candidates,
            ]);
        } catch (\BadMethodCallException $e) {
            return view('livewire.public.error', [
                'title' => 'Terjadi Kesalahan',
                'message' => 'Terjadi kendala saat memuat halaman. Silakan kembali ke halaman verifikasi dan coba lagi.',
                'cta' => route('verify'),
            ]);
        }
    }
}

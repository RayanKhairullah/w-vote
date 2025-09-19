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
    public bool $confirming = false;

    public function choose(int $candidateId): void
    {
        $this->selected_candidate_id = $candidateId;
    }

    public function openConfirm(): void
    {
        if (!$this->selected_candidate_id) {
            session()->flash('error', 'Pilih kandidat terlebih dahulu.');
            return;
        }
        $this->confirming = true;
    }

    public function closeConfirm(): void
    {
        $this->confirming = false;
    }

    public function submit(): void
    {
        // Close confirm modal if open
        $this->confirming = false;

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

        try {
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
            
            // Clear session and add success notification
            Session::forget(['voting.voter_id', 'voting.election_id', 'voting.token_hash_used']);
            session()->flash('vote_success', 'Suara Anda berhasil dikirim! Terima kasih telah berpartisipasi dalam pemilihan.');
            
        } catch (\Exception $e) {
            // Add error notification
            session()->flash('vote_error', 'Terjadi kesalahan saat mengirim suara: ' . $e->getMessage());
        }
        
        redirect()->route('verify');
    }

    #[Layout('components.layouts.public')]
    public function render()
    {
        $electionId = Session::get('voting.election_id');
        $voterId = Session::get('voting.voter_id');

        // If session is missing, show a friendly page instead of returning a Redirector from render()
        if (!$electionId || !$voterId) {
            return view('livewire.public.error', [
                'title' => 'Login diperlukan',
                'message' => 'Login terlebih dahulu untuk mengakses halaman pemilihan',
                'cta' => route('verify'),
            ]);
        }

        $voter = Voter::find($voterId);
        $this->alreadyVoted = (bool) ($voter?->has_voted);

        $election = Election::findOrFail($electionId);
        $candidates = Candidate::select('candidates.*', 'candidate_election.ballot_number')
            ->join('candidate_election', 'candidate_election.candidate_id', '=', 'candidates.id')
            ->where('candidate_election.election_id', $electionId)
            ->orderBy('candidate_election.ballot_number')
            ->get();

        return view('livewire.public.ballot', [
            'election' => $election,
            'candidates' => $candidates,
            'voter' => $voter,
        ]);
    }
}

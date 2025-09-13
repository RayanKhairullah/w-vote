<?php

namespace App\Livewire\Voting;

use App\Models\Election;
use App\Models\Voter;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Verify extends Component
{
    public string $identifier = '';
    public string $token = '';
    public ?int $year = null;

    public ?string $error = null;

    public function submit(): void
    {
        $data = [
            'identifier' => trim($this->identifier),
            'token' => $this->token,
            'year' => $this->year,
        ];
        Validator::make($data, [
            'identifier' => 'required|string|max:50',
            'token' => 'required|string|max:255',
            'year' => 'required|integer|min:2000|max:2100',
        ])->validate();

        $election = Election::where('year', $this->year)->first();
        if (!$election || $election->status !== 'open') {
            $this->error = 'Pemilihan untuk tahun ini belum dibuka.';
            return;
        }

        $voter = Voter::where('identifier', $data['identifier'])
            ->where('year', $data['year'])
            ->first();
        if (!$voter || !Hash::check($data['token'], $voter->token_hash)) {
            $this->error = 'Identitas atau token tidak valid.';
            return;
        }
        if ($voter->has_voted) {
            $this->error = 'Anda sudah memberikan suara.';
            return;
        }

        Session::put('voting.voter_id', $voter->id);
        Session::put('voting.election_id', $election->id);
        Session::put('voting.token_hash_used', $voter->token_hash);

        redirect()->route('ballot');
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.public.verify');
    }
}

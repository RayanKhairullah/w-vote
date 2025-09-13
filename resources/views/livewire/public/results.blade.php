<div>
    <flux:heading size="lg" class="mb-4">Hasil Sementara</flux:heading>

    <flux:card>
        @if (!$election)
            <div class="text-gray-600">Belum ada pemilihan aktif.</div>
        @else
            <div class="mb-4 text-gray-600">{{ $election->year }} - {{ $election->name }}</div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-left text-gray-500">
                        <tr>
                            <th class="py-2 pr-4">Kandidat</th>
                            <th class="py-2 pr-4">Suara</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $candidates = \App\Models\Candidate::select('candidates.*', 'candidate_election.ballot_number')
                                ->join('candidate_election', 'candidate_election.candidate_id', '=', 'candidates.id')
                                ->where('candidate_election.election_id', $election->id)
                                ->orderBy('candidate_election.ballot_number')
                                ->get();
                        @endphp
                        @foreach ($candidates as $c)
                            <tr class="border-t">
                                <td class="py-2 pr-4">#{{ $c->ballot_number }} - {{ $c->leader_name }} & {{ $c->deputy_name }}</td>
                                <td class="py-2 pr-4 font-semibold">{{ $totals[$c->id] ?? 0 }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </flux:card>
</div>

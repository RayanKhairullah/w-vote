<div @if($autoRefresh) wire:poll.5s @endif>
<section class="container mx-auto py-6 px-4" x-data>
    <!-- Notification Area -->
    <div x-data="{ show: false, message: '', type: 'success' }" 
         @notify.window="show = true; message = $event.detail.message; type = $event.detail.type; setTimeout(() => show = false, 5000)"
         x-show="show" 
         x-transition
         class="fixed top-4 right-4 z-50 max-w-sm">
        <div class="rounded-lg p-4 shadow-lg border" 
             :class="{
                 'bg-green-50 border-green-200 text-green-800': type === 'success',
                 'bg-red-50 border-red-200 text-red-800': type === 'error',
                 'bg-blue-50 border-blue-200 text-blue-800': type === 'info'
             }">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg x-show="type === 'success'" class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <svg x-show="type === 'error'" class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium" x-text="message"></p>
                </div>
                <div class="ml-auto pl-3">
                    <button @click="show = false" class="inline-flex text-gray-400 hover:text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white">
                Hasil Sementara
            </h1>
            <p class="text-gray-600 dark:text-zinc-300 mt-1">
                Pemilihan OSIS Real-time
            </p>
            <div class="flex items-center gap-2 mt-2 text-xs text-gray-500 dark:text-gray-400">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Terakhir diperbarui: {{ $lastUpdated->format('d/m/Y H:i:s') }}</span>
                @if($autoRefresh)
                    <span class="text-green-600">• Live Update</span>
                @endif
            </div>
            <div class="flex items-center gap-2 mt-2">
                <svg class="w-4 h-4 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    @if($election) {{ $election->year }} - {{ $election->name }} @endif
                </span>
            </div>
        </div>

        @if($election)
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="md:w-64">
                <flux:select wire:model.live="electionId" label="Pilih Pemilihan">
                    @foreach ($elections as $opt)
                    <option value="{{ $opt->id }}">{{ $opt->year }} - {{ $opt->name }}</option>
                    @endforeach
                </flux:select>
            </div>
            
            <!-- Auto-refresh Toggle -->
            <div class="flex items-end">
                <flux:button 
                    variant="ghost" 
                    size="sm"
                    wire:click="toggleAutoRefresh"
                    class="mr-3"
                >
                    @if($autoRefresh)
                        <svg class="w-4 h-4 text-green-600 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <span class="ml-1 text-green-600">Live</span>
                    @else
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="ml-1 text-gray-400">Paused</span>
                    @endif
                </flux:button>
            </div>
            
            <!-- Export Button -->
            <div class="flex items-end">
                <div class="relative" x-data="{ open: false }">
                    <flux:button 
                        variant="primary" 
                        icon="arrow-down-tray"
                        @click="open = !open"
                        class="flex items-center gap-2"
                        wire:loading.attr="disabled" wire:target="exportResults"
                    >
                        <span wire:loading.remove wire:target="exportResults">Export Laporan</span>
                        <span wire:loading wire:target="exportResults" class="flex items-center gap-2">
                            <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Mengekspor...
                        </span>
                        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24" wire:loading.remove wire:target="exportResults">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </flux:button>
                    
                    <!-- Dropdown Menu -->
                    <div 
                        x-show="open" 
                        x-transition
                        @click.away="open = false"
                        class="absolute right-0 mt-2 w-48 bg-white dark:bg-zinc-800 rounded-lg shadow-lg border border-gray-200 dark:border-zinc-700 z-50"
                    >
                        <div class="py-2">
                            <button 
                                wire:click="exportResults('xlsx')"
                                @click="open = false"
                                class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-zinc-700 flex items-center gap-2"
                            >
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Excel (.xlsx)
                            </button>
                            <button 
                                wire:click="exportResults('csv')"
                                @click="open = false"
                                class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-zinc-700 flex items-center gap-2"
                            >
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                CSV (.csv)
                            </button>
                        </div>
                        
                        <!-- Export Info -->
                        <div class="border-t border-gray-200 dark:border-zinc-700 px-4 py-3">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                                <strong>Laporan berisi:</strong>
                            </p>
                            <ul class="text-xs text-gray-500 dark:text-gray-400 space-y-1">
                                <li>• Detail siapa yang memilih</li>
                                <li>• Kandidat yang dipilih</li>
                                <li>• Waktu voting</li>
                                <li>• Statistik partisipasi</li>
                                <li>• Daftar yang belum voting</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
    @if (!$election)
    <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-gray-200 dark:border-zinc-700/50 p-12 text-center">
        <div class="bg-gray-100 dark:bg-zinc-800 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
        </div>
        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Belum Ada Pemilihan Aktif</h3>
        <p class="text-gray-600 dark:text-gray-400">Silakan tunggu hingga pemilihan OSIS dimulai.</p>
    </div>
    @else

    <!-- Export Quick Stats Card -->
    @if($election)
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl border border-blue-200 dark:border-blue-800 p-5 mb-6">
        <div class="flex items-start justify-between">
            <div>
                <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-2">Export Ready</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div>
                        <div class="text-blue-600 dark:text-blue-400 font-medium">{{ number_format($stats['participants']) }}</div>
                        <div class="text-blue-700 dark:text-blue-300">Voting Records</div>
                    </div>
                    <div>
                        <div class="text-blue-600 dark:text-blue-400 font-medium">{{ number_format($stats['candidateCount']) }}</div>
                        <div class="text-blue-700 dark:text-blue-300">Candidates</div>
                    </div>
                    <div>
                        <div class="text-blue-600 dark:text-blue-400 font-medium">{{ number_format($stats['nonParticipants']) }}</div>
                        <div class="text-blue-700 dark:text-blue-300">Not Voted</div>
                    </div>
                    <div>
                        <div class="text-blue-600 dark:text-blue-400 font-medium">4</div>
                        <div class="text-blue-700 dark:text-blue-300">Excel Sheets</div>
                    </div>
                </div>
            </div>
            <div class="bg-blue-100 dark:bg-blue-800/50 rounded-lg p-2">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
        </div>
    </div>
    @endif

    <!-- Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">
        <!-- Total Pemilih -->
        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-gray-200 dark:border-zinc-700/50 p-5 transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
            <div class="flex items-start justify-between">
                <div>
                    <flux:text class="text-sm text-gray-600 dark:text-zinc-400">Total Pemilih</flux:text>
                    <flux:heading size="xl" class="mb-1">
                        {{ number_format($stats['totalVoters']) }}
                    </flux:heading>
                    <div class="text-xs text-gray-500 dark:text-zinc-500">Tahun {{ $election->year }}</div>
                </div>
                <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-lg p-3">
                    <svg class="w-6 h-6 text-zinc-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Partisipasi -->
        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-gray-200 dark:border-zinc-700/50 p-5 transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
            <div class="flex items-start justify-between">
                <div>
                    <flux:text class="text-sm text-gray-600 dark:text-zinc-400">Partisipasi</flux:text>
                    <flux:heading size="xl" class="mb-1">
                        {{ number_format($stats['participationPct'], 1) }}%
                    </flux:heading>
                    <div class="text-xs text-gray-500 dark:text-zinc-500">{{ number_format($stats['participants']) }} dari {{ number_format($stats['totalVoters']) }} orang</div>
                </div>
                <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-lg p-3">
                    <svg class="w-6 h-6 text-zinc-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Kandidat -->
        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-gray-200 dark:border-zinc-700/50 p-5 transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
            <div class="flex items-start justify-between">
                <div>
                    <flux:text class="text-sm text-gray-600 dark:text-zinc-400">Total Kandidat</flux:text>
                    <flux:heading size="xl" class="mb-1">
                        {{ number_format($stats['candidateCount']) }}
                    </flux:heading>
                    <div class="text-xs text-gray-500 dark:text-zinc-500">Terdaftar pada pemilihan ini</div>
                </div>
                <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-lg p-3">
                    <flux:icon.users class="w-6 h-6 text-zinc-600 dark:text-zinc-400" />
                </div>
            </div>
        </div>
    </div>

    <!-- Candidate Chart Section -->
    <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-gray-200 dark:border-zinc-700/50 p-5 transition-all duration-300 hover:shadow-md mb-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="p-2 rounded-lg bg-zinc-100 dark:bg-zinc-800/50 text-zinc-600 dark:text-zinc-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                </svg>
            </div>
            <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Grafik Perolehan Suara</h4>
        </div>
        
        @php
        $candidates = \App\Models\Candidate::select('candidates.*', 'candidate_election.ballot_number')
            ->join('candidate_election', 'candidate_election.candidate_id', '=', 'candidates.id')
            ->where('candidate_election.election_id', $election->id)
            ->orderBy('candidate_election.ballot_number')
            ->get();
        $maxVotes = $totals->max() ?: 1;
        @endphp
        
        <!-- Chart Container -->
        <div class="relative">
            <!-- Y-axis labels (candidate names) -->
            <div class="flex">
                <div class="w-48 pr-4">
                    @foreach ($candidates as $c)
                    @php
                    $votes = (int)($totals[$c->id] ?? 0);
                    @endphp
                    <div class="h-16 flex items-center justify-end border-b border-gray-100 dark:border-zinc-700 last:border-b-0">
                        <div class="text-right">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                #{{ $c->ballot_number }} {{ $c->leader_name }}
                            </div>
                            <div class="text-xs text-gray-600 dark:text-gray-400">
                                & {{ $c->deputy_name }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <!-- Chart area -->
                <div class="flex-1 relative">
                    <!-- Grid lines -->
                    <div class="absolute inset-0 flex">
                        @for ($i = 0; $i <= 5; $i++)
                        <div class="flex-1 border-l border-gray-200 dark:border-zinc-700 {{ $i === 0 ? 'border-l-2 border-gray-400 dark:border-zinc-500' : '' }}"></div>
                        @endfor
                    </div>
                    
                    <!-- Bars -->
                    <div class="relative z-10">
                        @foreach ($candidates as $c)
                        @php
                        $votes = (int)($totals[$c->id] ?? 0);
                        $percentage = $maxVotes > 0 ? ($votes / $maxVotes * 100) : 0;
                        $votePercentage = $totalVotes > 0 ? ($votes / $totalVotes * 100) : 0;
                        $colors = ['from-blue-500 to-blue-600', 'from-emerald-500 to-emerald-600', 'from-purple-500 to-purple-600', 'from-orange-500 to-orange-600', 'from-red-500 to-red-600', 'from-teal-500 to-teal-600'];
                        $colorIndex = $loop->index % count($colors);
                        @endphp
                        <div class="h-16 flex items-center border-b border-gray-100 dark:border-zinc-700 last:border-b-0">
                            <div class="relative w-full h-8">
                                <div class="absolute left-0 top-0 h-full bg-gradient-to-r {{ $colors[$colorIndex] }} rounded-r-md shadow-sm transition-all duration-700 ease-out flex items-center justify-end pr-2" 
                                     style="width: {{ $percentage }}%">
                                    @if($percentage > 15)
                                    <span class="text-white text-xs font-medium">{{ number_format($votes) }}</span>
                                    @endif
                                </div>
                                @if($percentage <= 15 && $votes > 0)
                                <span class="absolute left-full ml-2 top-1/2 transform -translate-y-1/2 text-xs font-medium text-gray-700 dark:text-gray-300">{{ number_format($votes) }}</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <!-- X-axis labels -->
            <div class="flex mt-2">
                <div class="w-48"></div>
                <div class="flex-1 flex justify-between text-xs text-gray-600 dark:text-gray-400">
                    <span>0</span>
                    @if($maxVotes > 0)
                    <span>{{ number_format($maxVotes * 0.2) }}</span>
                    <span>{{ number_format($maxVotes * 0.4) }}</span>
                    <span>{{ number_format($maxVotes * 0.6) }}</span>
                    <span>{{ number_format($maxVotes * 0.8) }}</span>
                    <span>{{ number_format($maxVotes) }}</span>
                    @endif
                </div>
            </div>
            
            <!-- X-axis title -->
            <div class="text-center mt-2">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah Suara</span>
            </div>
        </div>
    </div>

    <!-- Participation Overview Card -->
    <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-gray-200 dark:border-zinc-700/50 p-6 mb-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="p-2 rounded-lg bg-zinc-100 dark:bg-zinc-800/50 text-zinc-600 dark:text-zinc-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Ringkasan Partisipasi</h3>
        </div>

        @php
        $pp = max(0, (float)($stats['participationPct'] ?? 0));
        $np = 100 - $pp;
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
            <!-- Donut Chart Placeholder -->
            <div class="flex justify-center">
                <div class="relative w-48 h-48">
                    <svg class="w-48 h-48 transform -rotate-90" viewBox="0 0 100 100">
                        <!-- Background circle -->
                        <circle cx="50" cy="50" r="40" stroke="#e5e7eb" stroke-width="8" fill="none" class="dark:stroke-zinc-700" />
                        <!-- Progress circle -->
                        <circle cx="50" cy="50" r="40" stroke="url(#gradient)" stroke-width="8" fill="none"
                            stroke-dasharray="{{ $pp * 2.51 }} 251.2"
                            stroke-linecap="round"
                            class="transition-all duration-1000 ease-out" />
                        <defs>
                            <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" style="stop-color:#6366f1" />
                                <stop offset="100%" style="stop-color:#8b5cf6" />
                            </linearGradient>
                        </defs>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($pp, 1) }}%</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Partisipasi</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Legend and Stats -->
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-zinc-800/50 rounded-xl">
                    <div class="flex items-center gap-3">
                        <div class="w-4 h-4 rounded-full bg-indigo-500"></div>
                        <span class="font-medium text-gray-900 dark:text-white">Sudah Memilih</span>
                    </div>
                    <div class="text-right">
                        <div class="font-bold text-gray-900 dark:text-white">{{ number_format($stats['participants']) }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">{{ number_format($pp, 1) }}%</div>
                    </div>
                </div>

                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-zinc-800/50 rounded-xl">
                    <div class="flex items-center gap-3">
                        <div class="w-4 h-4 rounded-full bg-gray-400"></div>
                        <span class="font-medium text-gray-900 dark:text-white">Belum Memilih</span>
                    </div>
                    <div class="text-right">
                        <div class="font-bold text-gray-900 dark:text-white">{{ number_format($stats['nonParticipants']) }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">{{ number_format($np, 1) }}%</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Voter Composition Section -->
    <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-gray-200 dark:border-zinc-700/50 p-5 transition-all duration-300 hover:shadow-md mb-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="p-2 rounded-lg bg-zinc-100 dark:bg-zinc-800/50 text-zinc-600 dark:text-zinc-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Komposisi Pemilih</h4>
        </div>
        
        @php
        $studentCount = (int)($stats['studentCount'] ?? 0);
        $staffCount = (int)($stats['staffCount'] ?? 0);
        $totalVoters = $studentCount + $staffCount;
        $studentPct = $totalVoters > 0 ? ($studentCount / $totalVoters * 100) : 0;
        $staffPct = $totalVoters > 0 ? ($staffCount / $totalVoters * 100) : 0;
        @endphp
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Student Bar -->
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded bg-blue-500"></div>
                        <span class="font-medium text-gray-900 dark:text-white">Siswa</span>
                    </div>
                    <div class="text-right">
                        <div class="font-bold text-gray-900 dark:text-white">{{ number_format($studentCount) }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">{{ number_format($studentPct, 1) }}%</div>
                    </div>
                </div>
                <div class="w-full bg-gray-200 dark:bg-zinc-700 rounded-full h-4">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-4 rounded-full transition-all duration-500" style="width: {{ $studentPct }}%"></div>
                </div>
            </div>
            
            <!-- Staff Bar -->
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded bg-emerald-500"></div>
                        <span class="font-medium text-gray-900 dark:text-white">Staff</span>
                    </div>
                    <div class="text-right">
                        <div class="font-bold text-gray-900 dark:text-white">{{ number_format($staffCount) }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">{{ number_format($staffPct, 1) }}%</div>
                    </div>
                </div>
                <div class="w-full bg-gray-200 dark:bg-zinc-700 rounded-full h-4">
                    <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 h-4 rounded-full transition-all duration-500" style="width: {{ $staffPct }}%"></div>
                </div>
            </div>
        </div>
        
        <div class="mt-6 p-4 bg-gray-50 dark:bg-zinc-800/50 rounded-lg">
            <div class="text-center">
                <div class="text-sm text-gray-600 dark:text-gray-400">Total Pemilih Terdaftar</div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($totalVoters) }}</div>
            </div>
        </div>
    </div>

    <!-- Candidate Results Table -->
    <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-gray-200 dark:border-zinc-700/50 overflow-hidden mb-8">
        <div class="p-6 border-b border-gray-200 dark:border-zinc-700/50">
            <div class="flex items-center gap-3">
                <div class="p-2 rounded-lg bg-zinc-100 dark:bg-zinc-800/50 text-zinc-600 dark:text-zinc-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Hasil Perolehan Suara</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">Total suara masuk: <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($totalVotes) }}</span></p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            @php
            $candidates = \App\Models\Candidate::select('candidates.*', 'candidate_election.ballot_number')
            ->join('candidate_election', 'candidate_election.candidate_id', '=', 'candidates.id')
            ->where('candidate_election.election_id', $election->id)
            ->orderBy('candidate_election.ballot_number')
            ->get();
            @endphp

            <div class="divide-y divide-gray-200 dark:divide-zinc-700">
                @foreach ($candidates as $index => $c)
                @php
                $v = (int)($totals[$c->id] ?? 0);
                $pct = ($totalVotes > 0) ? round($v / $totalVotes * 100, 1) : 0;
                @endphp
                <div class="p-6 hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-all duration-300 {{ $index % 2 === 0 ? 'bg-white dark:bg-zinc-900' : 'bg-gray-50/50 dark:bg-zinc-800/30' }}">
                    <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                        <!-- Candidate Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 px-3 py-1 rounded-full text-sm font-bold">
                                    #{{ $c->ballot_number }}
                                </div>
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white truncate">
                                    {{ $c->leader_name }} & {{ $c->deputy_name }}
                                </h4>
                            </div>
                        </div>

                        <!-- Vote Count -->
                        <div class="flex items-center gap-6">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($v) }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">suara</div>
                            </div>

                            <!-- Progress Bar -->
                            <div class="flex items-center gap-4 min-w-0 flex-1 lg:w-80">
                                <div class="relative flex-1 h-3 bg-gray-200 dark:bg-zinc-700 rounded-full overflow-hidden">
                                    <div class="absolute inset-0 bg-emerald-500 rounded-full transition-all duration-1000 ease-out"
                                        style="width: {{ min(100, max(0, $pct)) }}%"></div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-lg font-bold text-gray-900 dark:text-white tabular-nums">{{ number_format($pct, 1) }}%</span>
                                    @if($pct > 0)
                                    <div class="bg-emerald-500 text-white px-2 py-1 rounded-md text-xs font-medium">
                                        {{ number_format($pct, 1) }}%
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</section>
</div>

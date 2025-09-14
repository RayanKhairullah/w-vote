<section class="container mx-auto py-6 px-4">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white">
                Hasil Sementara
            </h1>
            <p class="text-gray-600 dark:text-zinc-300 mt-1">
                Pemilihan OSIS Real-time
            </p>
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
        <div class="md:w-64">
            <flux:select wire:model.live="electionId" label="Pilih Pemilihan">
                @foreach ($elections as $opt)
                <option value="{{ $opt->id }}">{{ $opt->year }} - {{ $opt->name }}</option>
                @endforeach
            </flux:select>
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
        <div class="flex items-center gap-3 mb-4">
            <div class="p-2 rounded-lg bg-zinc-100 dark:bg-zinc-800/50 text-zinc-600 dark:text-zinc-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                </svg>
            </div>
            <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Grafik Perolehan Suara</h4>
        </div>
        <div class="h-64">
            <canvas id="candChart" class="w-full h-full"></canvas>
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

    <!-- Chart Section -->
    <div class="grid grid-cols-1 gap-6 mb-8">
        <!-- Komposisi Pemilih Chart -->
        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-gray-200 dark:border-zinc-700/50 p-5 transition-all duration-300 hover:shadow-md">
            <div class="flex items-center gap-3 mb-4">
                <div class="p-2 rounded-lg bg-zinc-100 dark:bg-zinc-800/50 text-zinc-600 dark:text-zinc-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Komposisi Pemilih</h4>
            </div>
            <div class="h-64">
                <canvas id="compChart" class="w-full h-full"></canvas>
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

@php
$candidatesForChart = \App\Models\Candidate::select('candidates.*', 'candidate_election.ballot_number')
->join('candidate_election', 'candidate_election.candidate_id', '=', 'candidates.id')
->where('candidate_election.election_id', optional($election)->id)
->orderBy('candidate_election.ballot_number')
->get();
$candLabels = $candidatesForChart->map(fn($c) => '#' . $c->ballot_number . ' - ' . $c->leader_name)->values();
$candVotes = $candidatesForChart->map(fn($c) => (int)($totals[$c->id] ?? 0))->values();
@endphp

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    const chartStore = {
        comp: null,
        part: null,
        cand: null
    };

    // Modern color palettes
    const modernColors = {
        gradients: [{
                start: '#3b82f6',
                end: '#8b5cf6'
            }, // blue to purple
            {
                start: '#10b981',
                end: '#06b6d4'
            }, // emerald to cyan
            {
                start: '#f59e0b',
                end: '#ef4444'
            }, // amber to red
            {
                start: '#ec4899',
                end: '#f43f5e'
            }, // pink to rose
            {
                start: '#6366f1',
                end: '#3b82f6'
            }, // indigo to blue
            {
                start: '#eab308',
                end: '#f59e0b'
            } // yellow to amber
        ],
        solid: ['#3b82f6', '#10b981', '#f59e0b', '#ec4899', '#6366f1', '#eab308']
    };

    function createGradient(ctx, color1, color2, direction = 'vertical') {
        const gradient = direction === 'vertical' ?
            ctx.createLinearGradient(0, 0, 0, 400) :
            ctx.createLinearGradient(0, 0, 400, 0);
        gradient.addColorStop(0, color1);
        gradient.addColorStop(1, color2);
        return gradient;
    }

    function renderCharts() {
        if (!window.Chart) return;
        const compCtx = document.getElementById('compChart');
        const partCtx = document.getElementById('partChart');
        const candCtx = document.getElementById('candChart');

        // Destroy previous charts
        Object.keys(chartStore).forEach(k => {
            if (chartStore[k]) {
                chartStore[k].destroy();
                chartStore[k] = null;
            }
        });

        // Common chart options
        const commonOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: '#374151',
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: true,
                    padding: 12
                }
            },
            animation: {
                duration: 1500,
                easing: 'easeOutQuart'
            }
        };

        // Composition Chart (Bar)
        const studentCount = {{ (int)($stats['studentCount'] ?? 0) }};
        const staffCount   = {{ (int)($stats['staffCount'] ?? 0) }};
        if (compCtx) {
            chartStore.comp = new Chart(compCtx, {
                type: 'bar',
                data: {
                    labels: ['Siswa', 'Staff'],
                    datasets: [{
                        label: 'Jumlah Pemilih',
                        data: [studentCount, staffCount],
                        backgroundColor: [
                            createGradient(compCtx.getContext('2d'), '#3b82f6', '#8b5cf6'),
                            createGradient(compCtx.getContext('2d'), '#10b981', '#06b6d4')
                        ],
                        borderRadius: 8,
                        borderSkipped: false,
                    }]
                },
                options: {
                    ...commonOptions,
                    plugins: {
                        ...commonOptions.plugins,
                        legend: {
                            display: false
                        },
                        tooltip: {
                            ...commonOptions.plugins.tooltip,
                            callbacks: {
                                label: function(context) {
                                    return `${context.label}: ${context.parsed.y.toLocaleString()} orang`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(156, 163, 175, 0.1)',
                                drawBorder: false
                            },
                            ticks: {
                                color: '#6b7280',
                                font: {
                                    size: 12
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#6b7280',
                                font: {
                                    size: 12,
                                    weight: 'bold'
                                }
                            }
                        }
                    }
                }
            });
        }

        // Participation Chart (Doughnut)
        const participants       = {{ (int)($stats['participants'] ?? 0) }};
        const nonParticipants    = {{ (int)($stats['nonParticipants'] ?? 0) }};
        const participationPct   = {{ (float)($stats['participationPct'] ?? 0) }};

        if (partCtx) chartStore.part = new Chart(partCtx, {
            type: 'doughnut',
            data: {
                labels: ['Sudah Memilih', 'Belum Memilih'],
                datasets: [{
                    data: [participants, nonParticipants],
                    backgroundColor: [
                        createGradient(partCtx.getContext('2d'), '#6366f1', '#8b5cf6'),
                        createGradient(partCtx.getContext('2d'), '#9ca3af', '#6b7280')
                    ],
                    borderWidth: 0,
                    cutout: '70%'
                }]
            },
            options: {
                ...commonOptions,
                plugins: {
                    ...commonOptions.plugins,
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: {
                                size: 12,
                                weight: 'bold'
                            },
                            color: '#374151'
                        }
                    },
                    tooltip: {
                        ...commonOptions.plugins.tooltip,
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return `${context.label}: ${context.parsed.toLocaleString()} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Candidate Chart (Horizontal Bar)
        const candLabels = @json($candLabels);
        const candVotes = @json($candVotes);
        const candidateColors = candLabels.map((_, index) => {
            const colorPair = modernColors.gradients[index % modernColors.gradients.length];
            return createGradient(candCtx.getContext('2d'), colorPair.start, colorPair.end, 'horizontal');
        });

        if (candCtx) chartStore.cand = new Chart(candCtx, {
            type: 'bar',
            data: {
                labels: candLabels,
                datasets: [{
                    label: 'Perolehan Suara',
                    data: candVotes,
                    backgroundColor: candidateColors,
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: {
                ...commonOptions,
                indexAxis: 'y',
                plugins: {
                    ...commonOptions.plugins,
                    legend: {
                        display: false
                    },
                    tooltip: {
                        ...commonOptions.plugins.tooltip,
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((context.parsed.x / total) * 100).toFixed(1) : 0;
                                return `Suara: ${context.parsed.x.toLocaleString()} (${percentage}%)`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(156, 163, 175, 0.1)',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#6b7280',
                            font: {
                                size: 12
                            }
                        }
                    },
                    y: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#6b7280',
                            font: {
                                size: 11
                            },
                            maxRotation: 0,
                            callback: function(value, index) {
                                const label = this.getLabelForValue(value);
                                return label.length > 25 ? label.substring(0, 25) + '...' : label;
                            }
                        }
                    }
                }
            }
        });
    }

    // Initialize charts with smooth loading
    document.addEventListener('DOMContentLoaded', () => {
        setTimeout(renderCharts, 100);
    });

    // Re-render after Livewire updates
    document.addEventListener('livewire:init', () => {
        Livewire.hook('message.processed', () => {
            setTimeout(renderCharts, 100);
        });
    });

    // Handle window resize
    window.addEventListener('resize', () => {
        Object.values(chartStore).forEach(chart => {
            if (chart) chart.resize();
        });
    });
</script>
</div>
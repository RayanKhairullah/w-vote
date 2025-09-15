<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Admin\Elections as AdminElections;
use App\Livewire\Admin\Candidates as AdminCandidates;
use App\Livewire\Admin\VotersImport as AdminVotersImport;
use App\Livewire\Public\Verify as PublicVerify;
use App\Livewire\Public\Ballot as PublicBallot;
use App\Livewire\Public\Results as PublicResults;
use App\Livewire\Public\Congrats as PublicCongrats;
use Illuminate\Support\Facades\Route;

Route::get('/', PublicVerify::class)->name('home');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

    // Admin: Elections, Candidates, Voters Import
    Route::get('admin/elections', AdminElections::class)->name('admin.elections');
    Route::get('admin/candidates', AdminCandidates::class)->name('admin.candidates');
    Route::get('admin/voters/import', AdminVotersImport::class)->name('admin.voters.import');

    // Results page: only for authenticated admin users
    Route::get('results', PublicResults::class)->name('results');
});

// Public voting flow
Route::get('verify', PublicVerify::class)->name('verify');
Route::get('ballot', PublicBallot::class)->name('ballot');
Route::get('congrats', PublicCongrats::class)->name('congrats');

require __DIR__.'/auth.php';


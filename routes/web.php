<?php

use App\Http\Controllers\VerifyEmailController;
use App\Http\Middleware\EnsureReviewerFillMandatoryFields;
use App\Http\Middleware\EnsureUserCanSubmitProposals;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\RoutePath;
use Laravel\Socialite\Facades\Socialite;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('test', [\App\Http\Controllers\TestController::class,'index'])->name('test');

Route::get('/', function () {
    // return view('welcome');
    return redirect('/catalogue');
});


Route::get('/admin', function () {
    return redirect('/dashboard');
})->name('dashboard');

Route::get('/catalogue', App\Livewire\Catalogue::class)->name('catalogue');
Route::get('/cart', App\Livewire\Cart::class)->name('cart');
Route::middleware('auth:sanctum')->get('/favourites', App\Livewire\Favourite::class)->name('favourites');
Route::middleware('auth:sanctum', EnsureUserCanSubmitProposals::class)->get('/proposal', App\Livewire\CreateProposal::class)->name('proposal');
// Route::middleware('auth:sanctum')->middleware(config('filament.middleware.base'))->get('/wizard', App\Filament\Pages\WizardProfile::class);
Route::middleware('auth:sanctum')->get('/wizard', App\Filament\Pages\WizardProfile::class)->name('wizard');
Route::middleware('auth:sanctum')
    ->get('/reviewer-terms-and-conditions', App\Filament\Pages\ReviewerAdditionalFields::class)
    ->name('reviewer-terms-and-conditions')
    ->withoutMiddleware(\App\Http\Middleware\EnsureReviewerFillMandatoryFields::class);

Route::middleware('auth:sanctum')->get('/proposal/inserted/{proposal_id}', function ($proposal_id) {
    return view('livewire.proposals.proposal-inserted')->with('proposal_id', $proposal_id);
})->name('proposal_success');

/*Route::middleware('auth:sanctum')->get('/dashboard/tool/{tool_id}', function ($tool_id) {
    return view('livewire.tool')->with('tool_id', $tool_id);
})->name('tool');
*/
// Route::middleware('auth:sanctum')->get('/dashboard/service/{service_id}', function($service_id){
Route::get('/organization/{id}', App\Livewire\OrganizationItem::class)->name('organization');
Route::get('/service/{id}', App\Livewire\ServiceItem::class)->name('service');
Route::get('/tool/{service_id}/{id}', App\Livewire\ToolItem::class)->name('tool');
Route::get('/method/{service_id}/{id}', App\Livewire\MethodItem::class)->name('tool');

Route::get('/confidentiality-policy', [\App\Http\Controllers\ConfidentialityPolicyController::class, 'show'])
    ->name('confidentiality.show')
    ->withoutMiddleware(EnsureReviewerFillMandatoryFields::class);
Route::get('/terms-of-services', [\App\Http\Controllers\TermsOfServicesController::class, 'show'])
    ->name('terms.show')
    ->withoutMiddleware(EnsureReviewerFillMandatoryFields::class);

Route::get('/terms-of-services-reviewer', [\App\Http\Controllers\TermsOfServicesController::class, 'showReviewerTerms'])
    ->name('terms.showReviewerTerms')
    ->withoutMiddleware(EnsureReviewerFillMandatoryFields::class);
// Route::middleware([
//     'auth:sanctum',
//     config('jetstream.auth_session'),
//     'verified'
// ])->group(function () {
//     Route::get('/dashboard', function () {
//         return view('dashboard');
//     })->name('dashboard');
// });


// Route::get('/oauth/orcid', function () {
Route::get('/login/orcid', function () {
    return Socialite::driver('orcid')->setScopes(['/authenticate', 'openid'])->redirect();
})->name('orcid_login');

// Route::get('/auth/orcid', function () {
//     $user = Socialite::driver('orcid')->setScopes(['/authenticate', 'openid'])->user();
// });

Route::withoutMiddleware('custom-verified')->group(function (){
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');
    Route::post(RoutePath::for('verification.send', '/email/verification-notification'),
        [\Laravel\Fortify\Http\Controllers\EmailVerificationNotificationController::class, 'store'])->name('verification.send');
    Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
});

// Route::get('/login', function () {
//     return view('login');
// })->middleware(['guest:'.config('fortify.guard')])->name('login');

Route::get('/verified', function () {
    return view('account_verified');
})->middleware([config('fortify.auth_middleware', 'auth') . ':' . config('fortify.guard')])->name('account_verified');

Route::get('/post-access-report/{id}/pdf', [\App\Http\Controllers\PostAccessReportPdfController::class, 'downloadPdf'])
    ->name('post-access-report.pdf');
Route::get('/proposals/{id}/pdf', [\App\Http\Controllers\PDFController::class, 'downloadPdf'])->name('proposal.pdf');
// Route::get('/forgot-password', function () {
//     return view('forgot_password');
// })->middleware(['guest:'.config('fortify.guard')])->name('forgot_password');


/*Route::get('catalogue', Catalogue::class);
Route::get('cart', Cart::class);
Route::get('favourites', Favourite::class)->middleware('auth');*/

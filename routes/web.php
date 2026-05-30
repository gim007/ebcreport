<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ForgotUsernameController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Instructor\InstructorAccountController;
use App\Http\Controllers\Instructor\InstructorCourseController;
use App\Http\Controllers\Instructor\InstructorDashboardController;
use App\Http\Controllers\Instructor\InstructorRegistrationController;
use App\Http\Controllers\Instructor\InstructorRosterController;
use App\Http\Controllers\Instructor\InstructorVerificationController;
use App\Http\Controllers\Participant\ParticipantAccountController;
use App\Http\Controllers\Participant\PaymentController;
use App\Http\Controllers\Participant\PrepaidRegistrationController;
use App\Http\Controllers\Participant\RegisterParticipantController;
use App\Http\Controllers\Participant\ReportController;
use App\Http\Controllers\Participant\SelectionController;
use App\Http\Controllers\Participant\TermsController;
use App\Http\Controllers\Participant\TestController;
use Illuminate\Support\Facades\Route;

// ── Public landing ──────────────────────────────────────────────────────────
// Authenticated users land directly on their dashboard instead of the marketing
// page; `guest.redirect` middleware sends them to participant.account or
// instructor.dashboard based on user_type.
Route::middleware('guest.redirect')->get('/', fn () => view('welcome'));

// ── Auth — email/password login (with legacy MD5 → bcrypt upgrade) ─────────
Route::middleware('guest.redirect')->group(function () {
    Route::get('/login',   [LoginController::class, 'show'])->name('login');
    Route::post('/login',  [LoginController::class, 'login'])->name('login.attempt');
});
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// ── SSO — R-30 ──────────────────────────────────────────────────────────────
Route::get('/auth/{provider}',          [SocialAuthController::class, 'redirect'])
    ->where('provider', 'google|facebook|apple');
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])
    ->where('provider', 'google|facebook|apple');

// ── Forgot Username — R-32 ──────────────────────────────────────────────────
Route::middleware('guest.redirect')->group(function () {
    Route::get('/forgot-username',  [ForgotUsernameController::class, 'show'])->name('forgot-username');
    Route::post('/forgot-username', [ForgotUsernameController::class, 'send'])->name('forgot-username.send');
});

// ── Forgot Password — Laravel signed-token broker, legacy parity ────────────
// Route names match Laravel's defaults (`password.request`, `password.email`,
// `password.reset`, `password.update`) so framework helpers + the
// ResetPassword notification work out of the box.
Route::middleware(['guest.redirect', 'throttle:6,1'])->group(function () {
    Route::get('/forgot-password',         [ForgotPasswordController::class, 'show'])->name('password.request');
    Route::post('/forgot-password',        [ForgotPasswordController::class, 'send'])->name('password.email');
    Route::get('/reset-password/{token}',  [ResetPasswordController::class, 'show'])->name('password.reset');
    Route::post('/reset-password',         [ResetPasswordController::class, 'reset'])->name('password.update');
});

// ── Terms of Service gate (legacy parity with student_reg_terms.php) ───────
// Logged-in users don't need to re-accept terms or pick a new course — redirect
// them to their account.
Route::middleware('guest.redirect')->group(function () {
    Route::get('/register',         [TermsController::class, 'show'])->name('participant.terms');
    Route::post('/register/terms',  [TermsController::class, 'accept'])->name('participant.terms.accept');
});

// ── Course selection (gated by Terms acceptance for new participants) ──────
Route::middleware('participant.terms')->group(function () {
    Route::get('/organizations',                      [SelectionController::class, 'organizations'])->name('participant.organizations');
    Route::get('/organizations/{orgId}/instructors',  [SelectionController::class, 'instructors'])->name('participant.instructors');
    Route::get('/instructors/{instructorId}/courses', [SelectionController::class, 'courses'])->name('participant.courses');
});

// ── Registration (public — creates account) ──────────────────────────────────
Route::get('/courses/{courseId}/register',  [RegisterParticipantController::class, 'show'])->name('participant.register');
Route::post('/courses/{courseId}/register', [RegisterParticipantController::class, 'store'])->name('participant.register.store');

// ── Authenticated participant area ───────────────────────────────────────────
Route::middleware(['auth'])->name('participant.')->group(function () {
    // Account / profile self-service (legacy parity)
    Route::get('/account',           [ParticipantAccountController::class, 'show'])->name('account');
    Route::put('/account',           [ParticipantAccountController::class, 'update'])->name('account.update');
    Route::post('/account/password', [ParticipantAccountController::class, 'updatePassword'])->name('account.password');

    // Prepaid / scholarship codes (R-33)
    Route::get('/redeem',  [PrepaidRegistrationController::class, 'show'])->name('prepaid');
    Route::post('/redeem', [PrepaidRegistrationController::class, 'redeem'])->name('prepaid.redeem');

    // Payment method choice — legacy parity (student_registration_payment.php).
    Route::get('/courses/{courseId}/payment-method', [PaymentController::class, 'choose'])->name('payment.choose');

    // Payment (Phase 9 — Stripe)
    Route::get('/courses/{courseId}/payment',         [PaymentController::class, 'show'])->name('payment');
    Route::post('/courses/{courseId}/payment/charge', [PaymentController::class, 'charge'])->name('payment.charge');
    Route::get('/courses/{courseId}/payment/success', [PaymentController::class, 'success'])->name('payment.success');

    // Assessment
    Route::get('/test',    [TestController::class, 'show'])->name('test');
    Route::post('/test',   [TestController::class, 'submit'])->name('test.submit');

    // Report
    Route::get('/reports/{resultId}',          [ReportController::class, 'show'])->name('report.show');
    Route::get('/reports/{resultId}/download', [ReportController::class, 'download'])->name('report.download');
});

// ── Instructor registration (public; guest-only) ───────────────────────────
Route::middleware('guest.redirect')->group(function () {
    Route::get('/instructor/register',  [InstructorRegistrationController::class, 'show'])->name('instructor.register');
    Route::post('/instructor/register', [InstructorRegistrationController::class, 'store'])->name('instructor.register.store');
});

// ── Instructor email verification (signed URLs, R-26 / legacy parity) ──────
// The signed-URL consumer route must be named `verification.verify` because
// Laravel's built-in `VerifyEmail` notification hardcodes that name when
// building the signed link.
Route::middleware('auth')->group(function () {
    Route::get('/instructor/email/verify',             [InstructorVerificationController::class, 'notice'])
        ->name('instructor.verify.notice');
    Route::get('/instructor/email/verify/{id}/{hash}', [InstructorVerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/instructor/email/verify/resend',     [InstructorVerificationController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('instructor.verify.resend');
});

// ── Authenticated instructor area ────────────────────────────────────────────
Route::middleware(['auth', 'instructor'])->prefix('instructor')->name('instructor.')->group(function () {
    Route::get('/dashboard', [InstructorDashboardController::class, 'index'])->name('dashboard');

    // Account / profile self-service (legacy parity with instructor_account.php)
    Route::get('/account',           [InstructorAccountController::class, 'show'])->name('account');
    Route::put('/account',           [InstructorAccountController::class, 'update'])->name('account.update');
    Route::post('/account/password', [InstructorAccountController::class, 'updatePassword'])->name('account.password');

    // Courses CRUD
    Route::get('/courses',                  [InstructorCourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/create',           [InstructorCourseController::class, 'create'])->name('courses.create');
    Route::post('/courses',                 [InstructorCourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{courseId}/edit',  [InstructorCourseController::class, 'edit'])->name('courses.edit');
    Route::put('/courses/{courseId}',       [InstructorCourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{courseId}',    [InstructorCourseController::class, 'destroy'])->name('courses.destroy');

    // Roster & participant reports
    Route::get('/courses/{courseId}/roster',                        [InstructorRosterController::class, 'show'])->name('courses.roster');
    Route::get('/courses/{courseId}/roster/{resultId}',             [InstructorRosterController::class, 'reportRedirect'])->name('courses.roster.report');
});

// ── Authenticated participant area (legacy dashboard) ────────────────────────
Route::middleware(['auth'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', fn () => view('dashboard'))->name('index');
});

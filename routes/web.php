<?php

use App\Http\Controllers\Auth\ForgotUsernameController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Instructor\InstructorCourseController;
use App\Http\Controllers\Instructor\InstructorDashboardController;
use App\Http\Controllers\Instructor\InstructorRegistrationController;
use App\Http\Controllers\Instructor\InstructorRosterController;
use App\Http\Controllers\Participant\ParticipantAccountController;
use App\Http\Controllers\Participant\PaymentController;
use App\Http\Controllers\Participant\PrepaidRegistrationController;
use App\Http\Controllers\Participant\RegisterParticipantController;
use App\Http\Controllers\Participant\ReportController;
use App\Http\Controllers\Participant\SelectionController;
use App\Http\Controllers\Participant\TestController;
use Illuminate\Support\Facades\Route;

// ── Public landing ──────────────────────────────────────────────────────────
Route::get('/', fn () => view('welcome'));

// ── Auth — email/password login (with legacy MD5 → bcrypt upgrade) ─────────
Route::get('/login',   [LoginController::class, 'show'])->name('login');
Route::post('/login',  [LoginController::class, 'login'])->name('login.attempt');
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// ── SSO — R-30 ──────────────────────────────────────────────────────────────
Route::get('/auth/{provider}',          [SocialAuthController::class, 'redirect'])
    ->where('provider', 'google|facebook|apple');
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])
    ->where('provider', 'google|facebook|apple');

// ── Forgot Username — R-32 ──────────────────────────────────────────────────
Route::get('/forgot-username',  [ForgotUsernameController::class, 'show'])->name('forgot-username');
Route::post('/forgot-username', [ForgotUsernameController::class, 'send'])->name('forgot-username.send');

// ── Course selection (public) ────────────────────────────────────────────────
Route::get('/organizations',                      [SelectionController::class, 'organizations'])->name('participant.organizations');
Route::get('/organizations/{orgId}/instructors',  [SelectionController::class, 'instructors'])->name('participant.instructors');
Route::get('/instructors/{instructorId}/courses', [SelectionController::class, 'courses'])->name('participant.courses');

// ── Registration (public — creates account) ──────────────────────────────────
Route::get('/courses/{courseId}/register',  [RegisterParticipantController::class, 'show'])->name('participant.register');
Route::post('/courses/{courseId}/register', [RegisterParticipantController::class, 'store'])->name('participant.register.store');

// ── Authenticated participant area ───────────────────────────────────────────
Route::middleware(['auth'])->name('participant.')->group(function () {
    // Account
    Route::get('/account',          [ParticipantAccountController::class, 'show'])->name('account');
    Route::post('/account/password', [ParticipantAccountController::class, 'updatePassword'])->name('account.password');

    // Prepaid / scholarship codes (R-33)
    Route::get('/redeem',  [PrepaidRegistrationController::class, 'show'])->name('prepaid');
    Route::post('/redeem', [PrepaidRegistrationController::class, 'redeem'])->name('prepaid.redeem');

    // Payment (Phase 9 — Stripe)
    Route::get('/courses/{courseId}/payment',        [PaymentController::class, 'show'])->name('payment');
    Route::post('/courses/{courseId}/payment/charge', [PaymentController::class, 'charge'])->name('payment.charge');
    Route::get('/courses/{courseId}/payment/success', [PaymentController::class, 'success'])->name('payment.success');

    // Assessment
    Route::get('/test',    [TestController::class, 'show'])->name('test');
    Route::post('/test',   [TestController::class, 'submit'])->name('test.submit');

    // Report
    Route::get('/reports/{resultId}',          [ReportController::class, 'show'])->name('report.show');
    Route::get('/reports/{resultId}/download', [ReportController::class, 'download'])->name('report.download');
});

// ── Instructor registration (public) ────────────────────────────────────────
Route::get('/instructor/register',  [InstructorRegistrationController::class, 'show'])->name('instructor.register');
Route::post('/instructor/register', [InstructorRegistrationController::class, 'store'])->name('instructor.register.store');

// ── Authenticated instructor area ────────────────────────────────────────────
Route::middleware(['auth', 'instructor'])->prefix('instructor')->name('instructor.')->group(function () {
    Route::get('/dashboard', [InstructorDashboardController::class, 'index'])->name('dashboard');

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

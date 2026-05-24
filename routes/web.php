<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Student\StudentController;
use App\Http\Controllers\AiChatController;
use App\Http\Controllers\PremiumController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AiMonitorController;
use App\Http\Controllers\Admin\AdminPackageController;
use App\Http\Controllers\Admin\AdminSubjectController;
use App\Http\Controllers\Admin\AdminTopicController;
use App\Http\Controllers\Student\PackageController;
use App\Http\Controllers\Student\SessionController;
use App\Http\Controllers\Student\ReportController;
use App\Http\Controllers\Student\PembahasanController;
use App\Http\Controllers\Student\LeaderboardController;
use App\Http\Controllers\Student\AccountController;
use App\Http\Controllers\Student\ResultController;
use App\Http\Controllers\Student\TryoutController;

// ── Public ──────────────────────────────────────────
Route::get('/', fn() => view('landing.index'))->name('home');
Route::view('/syarat-ketentuan', 'pages.terms')->name('terms');
Route::view('/kebijakan-privasi', 'pages.privacy')->name('privacy');

Route::middleware('guest')->group(function () {
    Route::get('/login',                  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',                 [AuthController::class, 'login'])->name('login.post');
    Route::get('/register',               [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',              [AuthController::class, 'register'])->name('register.post');

    Route::get('/forgot-password',        [AuthController::class, 'showForgot'])->name('password.request');
    Route::post('/forgot-password',       [AuthController::class, 'sendReset'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showReset'])->name('password.reset');
    Route::post('/reset-password',        [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')->middleware('auth');

// ── Student ──────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/dashboard',             [StudentController::class, 'dashboard'])->name('dashboard');
    Route::get('/premium',               [PremiumController::class, 'index'])->name('premium');
    Route::get('/checkout/{plan}',       [PaymentController::class, 'checkout'])->name('checkout');
    Route::post('/payment/process',      [PaymentController::class, 'process'])->name('payment.process');
    Route::get('/payment/status/{id}',   [PaymentController::class, 'status'])->name('payment.status');

    Route::prefix('ai')->name('ai.')->group(function () {
        Route::get('/tutor',                    [AiChatController::class, 'index'])->name('tutor');
        Route::post('/chat/send',               [AiChatController::class, 'send'])->name('send')->middleware('throttle:60,1');
        Route::post('/chat/{message}/feedback', [AiChatController::class, 'feedback'])->name('feedback');
        Route::post('/chat/{message}/star',     [AiChatController::class, 'star'])->name('star');
        Route::get('/sessions',                 [AiChatController::class, 'sessions'])->name('sessions');
        Route::get('/sessions/{session}',       [AiChatController::class, 'sessionMessages'])->name('session.messages');
    });

    // Latihan Soal
    Route::get('/latihan',                 [PackageController::class, 'index'])->name('latihan.index');
    Route::get('/latihan/{package}',        [PackageController::class, 'show'])->name('latihan.show');
    Route::get('/latihan/{package}/mulai', [SessionController::class, 'start'])->name('latihan.start');
    Route::get('/latihan/sesi/{session}', [SessionController::class, 'show'])->name('latihan.mulai');
    Route::post('/latihan/sesi/{session}/jawab', [SessionController::class, 'submitAnswer'])->name('latihan.submit');
    Route::post('/latihan/sesi/{session}/selesai', [SessionController::class, 'finish'])->name('latihan.finish');
    Route::get('/latihan/sesi/{session}/hasil', [ResultController::class, 'show'])->name('latihan.hasil');

    // Try Out
    Route::get('/tryout', [TryoutController::class, 'index'])->name('tryout.index');

    // Laporan
    Route::get('/laporan', [ReportController::class, 'index'])->name('laporan.index');

    // Pembahasan
    Route::get('/pembahasan',              [PembahasanController::class, 'index'])->name('pembahasan.index');

    // Peringkat
    Route::get('/peringkat',               [LeaderboardController::class, 'index'])->name('peringkat.index');

    // Pengaturan Akun
    Route::get('/akun',                    [AccountController::class, 'show'])->name('akun.show');
    Route::post('/akun/update',            [AccountController::class, 'updateProfile'])->name('akun.update-profile');
    Route::post('/akun/update-password',   [AccountController::class, 'updatePassword'])->name('akun.update-password');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Soal
    Route::get('/soal', [QuestionController::class, 'index'])->name('soal.index');
    Route::get('/soal/tambah', [QuestionController::class, 'create'])->name('soal.create');
    Route::post('/soal', [QuestionController::class, 'store'])->name('soal.store');
    Route::post('/soal/import', [QuestionController::class, 'importExcel'])->name('soal.import');
    Route::get('/soal/{question}/edit', [QuestionController::class, 'edit'])->name('soal.edit');
    Route::put('/soal/{question}', [QuestionController::class, 'update'])->name('soal.update');
    Route::delete('/soal/{question}', [QuestionController::class, 'destroy'])->name('soal.destroy');

    // Paket
    Route::get('/paket', [AdminPackageController::class, 'index'])->name('paket.index');
    Route::get('/paket/tambah', [AdminPackageController::class, 'create'])->name('paket.create');
    Route::post('/paket', [AdminPackageController::class, 'store'])->name('paket.store');
    Route::get('/paket/{package}/edit', [AdminPackageController::class, 'edit'])->name('paket.edit');
    Route::put('/paket/{package}', [AdminPackageController::class, 'update'])->name('paket.update');
    Route::delete('/paket/{package}', [AdminPackageController::class, 'destroy'])->name('paket.destroy');
    
    // Paket Import via Excel
    Route::get('/paket/import/template', [AdminPackageController::class, 'downloadTemplate'])->name('paket.import.template');
    Route::get('/paket/import', [AdminPackageController::class, 'importForm'])->name('paket.import');
    Route::post('/paket/import', [AdminPackageController::class, 'importProcess'])->name('paket.import.process');

    // Pengguna
    Route::get('/pengguna', [AdminUserController::class, 'index'])->name('pengguna.index');
    Route::get('/pengguna/tambah', [AdminUserController::class, 'create'])->name('pengguna.create');
    Route::post('/pengguna', [AdminUserController::class, 'store'])->name('pengguna.store');
    Route::get('/pengguna/{user}', [AdminUserController::class, 'show'])->name('pengguna.show');
    Route::get('/pengguna/{user}/edit', [AdminUserController::class, 'edit'])->name('pengguna.edit');
    Route::put('/pengguna/{user}', [AdminUserController::class, 'update'])->name('pengguna.update');
    Route::delete('/pengguna/{user}', [AdminUserController::class, 'destroy'])->name('pengguna.destroy');
    Route::post('/pengguna/{user}/suspend', [AdminUserController::class, 'suspend'])->name('pengguna.suspend');
    Route::post('/pengguna/{user}/upgrade', [AdminUserController::class, 'upgrade'])->name('pengguna.upgrade');

    // Mata Pelajaran
    Route::resource('mata-pelajaran', AdminSubjectController::class)->parameters(['mata-pelajaran' => 'mata_pelajaran']);

    // Topik / Bab
    Route::resource('topik', AdminTopicController::class)->parameters(['topik' => 'topik']);

    // AI Monitor
    Route::get('/ai-monitor', [AiMonitorController::class, 'index'])->name('ai-monitor.index');
    Route::put('/settings/ai-prompt', [AiMonitorController::class, 'updatePrompt'])->name('settings.ai-prompt');
});

// ── Payment Webhook ──────────────────────────────────
Route::post('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');
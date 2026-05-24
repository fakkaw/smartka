<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\TestPackage;
use App\Models\UserSession;
use App\Models\UserAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SessionController extends Controller
{
    /**
     * Start a new test session.
     */
    public function start(TestPackage $package)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Authorization check
        if ($user->class_level !== $package->class_level || $package->status !== 'published') {
            abort(403, 'Akses ditolak.');
        }

        // If premium package, check if user is premium
        if ($package->type === 'premium' && !$user->isPremium()) {
            return redirect()->route('premium')->with('error', 'Paket ini hanya untuk pengguna Premium.');
        }

        // Prevent starting if there's an ongoing session for this package
        $ongoingSession = $user->sessions()->where('test_package_id', $package->id)
                                          ->where('status', 'ongoing')
                                          ->first();
        if ($ongoingSession) {
            // Resume existing session
            return redirect()->route('latihan.mulai', $ongoingSession->id);
        }

        // Create a new session
        $session = UserSession::create([
            'user_id'         => $user->id,
            'test_package_id' => $package->id,
            'started_at'      => now(),
            'status'          => 'ongoing',
            'time_spent_seconds' => 0, // Initialize
        ]);

        // Get questions for the package, potentially randomized
        $questions = $package->questions()->orderBy('order_number')->get()->shuffle();

        // Store questions order in session (if randomized)
        if ($package->is_randomized) {
            // This could be stored in a session_questions pivot or JSON column on UserSession
            // For simplicity, let's just use the current order for now and assume client handles next/prev
        }

        // Redirect to the exercise view with the session ID
        return redirect()->route('latihan.mulai', $session->id);
    }

    /**
     * Display the exercise session interface.
     */
    public function show(UserSession $session)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Authorization check: ensure the session belongs to the current user
        if ($session->user_id !== $user->id) {
            abort(403, 'Akses ditolak.');
        }

        // Ensure session is ongoing
        if ($session->status !== 'ongoing') {
            return redirect()->route('latihan.hasil', $session->id)->with('info', 'Sesi ini sudah selesai.');
        }

        $package = $session->testPackage;
        $questions = $package->questions()->orderBy('order_number')->get(); // Get questions in defined order

        // Load user answers for this session
        $userAnswers = $session->answers->keyBy('question_id');

        return view('latihan.mulai', compact('session', 'package', 'questions', 'userAnswers'));
    }

    /**
     * Submit an answer for a question.
     */
    public function submitAnswer(Request $request, UserSession $session)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Authorization check
        if ($session->user_id !== $user->id || $session->status !== 'ongoing') {
            abort(403, 'Akses ditolak atau sesi tidak aktif.');
        }

        $request->validate([
            'question_id'     => 'required|exists:questions,id',
            'selected_answer' => 'nullable|string',
            'time_spent'      => 'required|integer|min:0',
            'is_marked'       => 'nullable|boolean',
            'hint_used'       => 'nullable|boolean',
        ]);

        $question = $session->testPackage->questions()->where('questions.id', $request->question_id)->firstOrFail();

        $isCorrect = null;
        if ($request->selected_answer !== null) {
            if ($question->type === 'multiple_choice') {
                $isCorrect = ($request->selected_answer === $question->correct_answer);
            } else {
                // Trim dan case-insensitive untuk isian singkat / benar-salah
                $isCorrect = (trim(strtolower($request->selected_answer)) === trim(strtolower($question->correct_answer)));
                // Untuk soal essay (short_answer)
                $studentAns = preg_replace('/\s+/', ' ', strtolower(trim($request->selected_answer)));
                $correctAns = preg_replace('/\s+/', ' ', strtolower(trim($question->correct_answer)));
                $isCorrect = ($studentAns === $correctAns);
            }
        }

        UserAnswer::updateOrCreate(
            [
                'session_id'  => $session->id,
                'question_id' => $request->question_id,
            ],
            [
                'selected_answer'    => $request->selected_answer,
                'is_correct'         => $isCorrect ?? false,
                'is_marked'          => $request->is_marked ?? false,
                'hint_used'          => $request->hint_used ?? false,
                'time_spent_seconds' => $request->time_spent,
            ]
        );

        // Update session total time spent (optional: sometimes better to sum answers at finish)
        // $session->increment('time_spent_seconds', $request->time_spent);

        return response()->json(['message' => 'Jawaban berhasil disimpan.'], 200);
    }

    /**
     * Finish the test session.
     */
    public function finish(UserSession $session)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Authorization check
        if ($session->user_id !== $user->id || $session->status !== 'ongoing') {
            abort(403, 'Akses ditolak atau sesi sudah selesai.');
        }

        // Update session status and finished_at timestamp
        $session->update([
            'status'      => 'completed',
            'finished_at' => now(),
        ]);

        // TODO: Trigger scoring service here (next step)
        // For now, redirect to a placeholder results page
        return redirect()->route('latihan.hasil', $session->id)->with('success', 'Latihan selesai!');
    }
}

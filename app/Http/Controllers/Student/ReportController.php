<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\UserSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        $period = $request->query('period', 'all');
        
        $query = UserSession::where('user_id', $user->id)
            ->where('status', 'completed')
            ->with(['result', 'testPackage']);
            
        $dateLimit = match($period) {
            'week'     => Carbon::now()->subDays(7),
            'month'    => Carbon::now()->subDays(30),
            'semester' => Carbon::now()->subMonths(6),
            default    => null,
        };
        
        if ($dateLimit) {
            $query->where('finished_at', '>=', $dateLimit);
        }
        
        $sessions = $query->orderBy('finished_at', 'asc')->get();
        
        $totalSessions = $sessions->count();
        $avgScore = $totalSessions > 0
            ? round($sessions->map(fn($s) => optional($s->result)->total_score ?? 0)->avg(), 1)
            : 0;
        
        $totalCorrect = $sessions->map(fn($s) => optional($s->result)->correct_count ?? 0)->sum();
        $totalWrong   = $sessions->map(fn($s) => optional($s->result)->wrong_count ?? 0)->sum();
        $totalEmpty   = $sessions->map(fn($s) => optional($s->result)->empty_count ?? 0)->sum();
        $totalTime    = $sessions->sum('time_spent_seconds');
        
        $hours = floor($totalTime / 3600);
        $minutes = floor(($totalTime / 60) % 60);
        $timeSpentLabel = $hours > 0 ? "{$hours}j {$minutes}m" : "{$minutes}m";

        $averageScore = $user->getAverageScore();
        $totalExercises = $totalSessions;
        $totalAnswered = $user->getTotalAnswered();
        $weakTopics = $user->getWeakTopics();

        $trendLabels = [];
        $trendScores = [];
        foreach ($sessions as $s) {
            $trendLabels[] = $s->finished_at->format('d/m') . ' - ' . mb_substr($s->testPackage->name ?? 'Latihan', 0, 12) . '...';
            $trendScores[] = optional($s->result)->total_score ?? 0;
        }
        
        $subjectScores = [];
        foreach ($sessions as $s) {
            $scores = optional($s->result)->score_per_subject;
            if (is_array($scores)) {
                foreach ($scores as $subName => $val) {
                    $subjectScores[$subName][] = $val;
                }
            }
        }
        
        $subjectAverages = [];
        foreach ($subjectScores as $subName => $vals) {
            $subjectAverages[$subName] = round(array_sum($vals) / count($vals), 1);
        }

        $resultsHistory = $user->results()
            ->with(['session', 'session.testPackage'])
            ->latest()
            ->get();

        return view('laporan.index', compact(
            'period',
            'resultsHistory',
            'averageScore',
            'totalExercises',
            'totalSessions',
            'totalAnswered',
            'weakTopics',
            'subjectAverages'
        ));
    }
}

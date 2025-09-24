<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LearningSession;
use Illuminate\Http\Request;

class SheikhController extends Controller
{
    /**
     * POST /sheikhs/sessions
     * Create a new session using Calendly
     */
    public function createSession(Request $request)
{
    $validated = $request->validate([
        'sheikh_id' => 'required|exists:users,id',
        'date' => 'required|date',
        'duration' => 'required|integer|min:1',
        'calendly_id' => 'required|string' // بدل calendly_event_id
    ]);

    $end = date('Y-m-d H:i:s', strtotime($validated['date'] . " +{$validated['duration']} minutes"));

    $session = LearningSession::create([
        'sheikh_id'   => $validated['sheikh_id'],
        'start_time'  => $validated['date'],
        'end_time'    => $end,
        'status'      => 'upcoming',
        'calendly_id' => $validated['calendly_id'] // بدل calendly_event_id
    ]);

    return response()->json([
        'session_id' => (string) $session->id,
        'message'    => 'Session created successfully'
    ], 201);
}


    /**
     * GET /sheikhs/sessions/{session_id}
     * View session details
     */
    public function getSession($session_id)
    {
        $session = LearningSession::with('students')->find($session_id);

        if (!$session) {
            return response()->json(['message' => 'Session not found'], 404);
        }

        return response()->json([
            'session_id'        => (string) $session->id,
            'date'              => $session->start_time,
            'duration'          => round((strtotime($session->end_time) - strtotime($session->start_time)) / 60),
            'student_ids'       => $session->students->pluck('id')->map(fn($id) => (string) $id)->toArray(),
            'calendly_id' => $session->calendly_id

        ], 200);
    }

    /**
     * GET /sheikhs/sessions/{sheikh_id}?period=weekly|daily|monthly
     * Display all sessions for a Sheikh
     */
    public function getSheikhSessions(Request $request, $sheikh_id)
    {
        $period = $request->query('period'); // optional filter
        $query  = LearningSession::with('students')->where('sheikh_id', $sheikh_id);

        if ($period) {
            $now = now();
            if ($period === 'daily') {
                $query->whereDate('start_time', $now->toDateString());
            } elseif ($period === 'weekly') {
                $query->whereBetween('start_time', [$now->startOfWeek(), $now->endOfWeek()]);
            } elseif ($period === 'monthly') {
                $query->whereMonth('start_time', $now->month)
                      ->whereYear('start_time', $now->year);
            }
        }

        $sessions = $query->get();

        $formatted = $sessions->map(function ($s) {
            return [
                'session_id'  => (string) $s->id,
                'date'        => $s->start_time,
                'duration'    => round((strtotime($s->end_time) - strtotime($s->start_time)) / 60),
                'student_ids' => $s->students->pluck('id')->map(fn($id) => (string)$id)->toArray(),
            ];
        });

        return response()->json($formatted, 200);
    }

    /**
     * GET /sheikhs/students/{sheikh_id}
     * List all students paired with a Sheikh
     */
    public function getSheikhStudents($sheikh_id)
    {
        $sheikh = User::find($sheikh_id);

        if (!$sheikh) {
            return response()->json(['message' => 'Sheikh not found'], 404);
        }

        $studentIds = LearningSession::with('students')
            ->where('sheikh_id', $sheikh_id)
            ->get()
            ->pluck('students')
            ->flatten()
            ->unique('id');

        $students = $studentIds->map(fn($student) => [
            'student_id' => (string) $student->id,
            'username'   => $student->username,
            'name'       => $student->name,
        ])->values();

        return response()->json($students, 200);
    }
}

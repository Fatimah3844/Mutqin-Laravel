<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\LearningSession;

class StudentController extends Controller
{
    public function updateProfile(Request $request)
    {
        $user = User::find($request->user_id);
        if (!$user) {
            return response()->json(['message' => 'Invalid user ID'], 400);
        }

        $user->update($request->only(['email','username','phone'])); 
        return response()->json(['message' => 'Profile updated successfully'], 200);
    }

    public function deleteProfile(Request $request)
    {
        $user = User::find($request->user_id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();
        return response()->json(['message' => 'Profile deleted successfully'], 200);
    }

    public function viewProfile($username)
    {
        $user = User::where('username', $username)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
return response()->json([
    'id' => $user->id,
    'username' => $user->username,
    'email' => $user->email,
    'role' => $user->role
], 200);

    }

    public function searchProfiles(Request $request)
    {
        $query = $request->query('query');
        $users = User::where('username','like',"%$query%")
                     ->get(['id','username']);
        return response()->json($users,200);
    }
    //still need computational logic(crud op) this is just for testing
    public function progress(Request $request)
{
    $validated = $request->validate([
        'student_id' => 'required|integer|exists:users,id'
    ]);

    $student = User::find($validated['student_id']);

    $sessionsAttended = $student->studentSessions()
        ->wherePivot('attended', true)
        ->count();

    $pagesLearned = $student->studentSessions()->sum('pivot.pages_learned');
    $points = $student->studentSessions()->sum('pivot.points');

    return response()->json([
        'sessions_attended' => $sessionsAttended,
        'quizzes_completed' => $student->quizzes()->count(), // لو عندك جدول اختبارات
        'pages_learned'     => $pagesLearned,
        'points'            => $points,
        'period'            => $request->query('period', 'weekly'),
    ], 200);
}

    public function progressAll()
{
    $sessions = LearningSession::with('students')->get();

    $data = $sessions->map(function ($session) {
        return [
            'date'              => $session->start_time,
            'sessions_attended' => $session->students->where('pivot.attended', true)->count(),
            'quizzes_completed' => 0, // future
            'pages_learned'     => $session->students->sum('pivot.pages_learned')
        ];
    });

    return response()->json($data, 200);
}


public function getStudents($sheikh_id)
{
    $sessions = LearningSession::with('students')
        ->where('sheikh_id', $sheikh_id)
        ->get();

    $students = $sessions->pluck('students')
        ->flatten()
        ->unique('id')
        ->map(fn($s) => [
            'student_id' => (string) $s->id,
            'username'   => $s->username,
            'name'       => $s->name,
        ])->values();

    return response()->json($students, 200);
}

    public function assignPoints(Request $request, $student_id)
{
    $validated = $request->validate([
        'session_id' => 'required|exists:learning_sessions,id',
        'points'     => 'required|integer|min:1'
    ]);

    $student = User::findOrFail($student_id);

    
    $student->studentSessions()->updateExistingPivot(
        $validated['session_id'],
        ['points' => $validated['points']],
        false
    );

    return response()->json(['message' => 'Points assigned successfully'], 200);
}

   public function updatePoints(Request $request, $student_id)
{
    $validated = $request->validate([
        'session_id' => 'required|exists:learning_sessions,id',
        'points'     => 'required|integer|min:0'
    ]);

    $student = User::findOrFail($student_id);

    $student->studentSessions()->updateExistingPivot(
        $validated['session_id'],
        ['points' => $validated['points']]
    );

    return response()->json(['message' => 'Points updated successfully'], 200);
}


    public function recordPages(Request $request, $student_id)
{
    $validated = $request->validate([
        'session_id'    => 'required|exists:learning_sessions,id',
        'pages_learned' => 'required|integer|min:1'
    ]);

    $student = User::findOrFail($student_id);

    $student->studentSessions()->updateExistingPivot(
        $validated['session_id'],
        ['pages_learned' => $validated['pages_learned']],
        false
    );

    return response()->json(['message' => 'Pages recorded successfully'], 200);
}


    public function getStudentProgress($student_id)
{
    $student = User::find($student_id);
    if (!$student || $student->role !== 'student') {
        return response()->json(['message' => 'Student not found'], 404);
    }

    $sessionsAttended = $student->studentSessions()
        ->wherePivot('attended', true)
        ->count();

    $pagesLearned = $student->studentSessions()->sum('pivot.pages_learned');
    $points = $student->studentSessions()->sum('pivot.points');

    return response()->json([
        'student_id'        => (string) $student_id,
        'sessions_attended' => $sessionsAttended,
        'quizzes_completed' => $student->quizzes()->count(),
        'pages_learned'     => $pagesLearned,
        'points'            => $points,
    ], 200);
}


}

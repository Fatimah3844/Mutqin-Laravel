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
    $validated = $request->validate(['student_id' => 'required|integer']);

    // احسب عدد الجلسات اللي حضرها الطالب
    $sessionsAttended = LearningSession::whereHas('students', function($q) use ($validated) {
        $q->where('student_id', $validated['student_id'])
          ->where('attended', true);
    })->count();

    // لاحقاً تضيفي الـ quizzes والصفحات من جداولها
    return response()->json([
        'sessions_attended' => $sessionsAttended,
        'quizzes_completed' => 0, // Placeholder
        'pages_learned' => 0,     // Placeholder
        'period' => $request->query('period', 'weekly'),
    ], 200);
}
    public function progressAll()
{
    $sessions = LearningSession::with('students')->get();

    $data = $sessions->map(function ($session) {
        return [
            'date' => $session->start_time,
            'sessions_attended' => $session->students->where('pivot.attended', true)->count(),
            'quizzes_completed' => 0, // Placeholder
            'pages_learned' => 0      // Placeholder
        ];
    });

    return response()->json($data, 200);
}

}

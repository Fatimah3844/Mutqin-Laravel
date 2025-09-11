<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LearningSession;

class SessionController extends Controller
{
    public function bookSession(Request $request)
    {
        $validated = $request->validate([
            'session_id' => 'required|string',
            'student_id' => 'required|string'
        ]);

        // 
        return response()->json([
            'session_id' => $validated['session_id'],
            'message' => 'Session booked successfully'
        ],201);
    }

    public function attendSession(Request $request)
    {
        if (!$request->session_id) {
            return response()->json(['message' => 'Session not found'],404);
        }

        return response()->json([
            'session_url' => 'https://calendly.com/join/'.$request->session_id,
            'message' => 'Session joined'
        ],200);
    }

    public function listSessions(Request $request)
    {
        return response()->json([
            [
                'session_id' => '123',
                'status' => $request->query('status','upcoming'),
                'date' => now()->toISOString(),
                'sheikh_id' => '45'
            ]
        ],200);
    }

    public function cancelSession(Request $request)
    {
        if (!$request->session_id) {
            return response()->json(['message' => 'Session not found'],404);
        }

        return response()->json(['message' => 'Session cancelled successfully'],200);
    }
}

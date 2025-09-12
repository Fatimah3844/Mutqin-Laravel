<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LearningSession;

class SessionController extends Controller
{
    public function bookSession(Request $request)
{
    $validated = $request->validate([
        'sheikh_id' => 'required|integer',
        'start_time' => 'required|date',
        'end_time' => 'required|date',
    ]);

    $session = LearningSession::create([
        'sheikh_id' => $validated['sheikh_id'],
        'start_time' => $validated['start_time'],
        'end_time' => $validated['end_time'],
        'status' => 'upcoming',
    ]);

    return response()->json([
        'message' => 'Session booked successfully',
        'session' => $session
    ], 201);
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
    $status = $request->query('status', 'upcoming');
    $sessions = LearningSession::where('status', $status)->get();
    return response()->json($sessions, 200);
}
    public function cancelSession(Request $request)
{
    $validated = $request->validate(['session_id' => 'required|integer']);
    $session = LearningSession::find($validated['session_id']);

    if (!$session) {
        return response()->json(['message' => 'Session not found'], 404);
    }

    $session->status = 'cancelled';
    $session->save();

    return response()->json(['message' => 'Session cancelled successfully'], 200);
}

}

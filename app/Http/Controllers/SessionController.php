<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LearningSession;

class SessionController extends Controller
{
    
    public function bookSession(Request $request)
    {
        $validated = $request->validate([
            'session_id' => 'required|integer',
        ]);

        $session = LearningSession::find($validated['session_id']);

        if (!$session) {
            return response()->json(['message' => 'Session not found'], 404);
        }

        // س
        $student = $request->user(); 
        if ($session->students()->where('student_id', $student->id)->exists()) {
            return response()->json(['message' => 'You already booked this session'], 400);
        }

        $session->students()->attach($student->id, ['attended' => false]);

        return response()->json([
            'message' => 'Session booked successfully',
            'session_id' => $session->id,
        ], 201);
    }

    
    public function attendSession(Request $request)
    {
        $validated = $request->validate([
            'session_id' => 'required|integer',
        ]);

        $session = LearningSession::find($validated['session_id']);

        if (!$session) {
            return response()->json(['message' => 'Session not found'], 404);
        }

        $student = $request->user();

        
        if (!$session->students()->where('student_id', $student->id)->exists()) {
            return response()->json(['message' => 'You did not book this session'], 403);
        }

        
        return response()->json([
            'session_url' => 'https://calendly.com/join/' . $session->id,
            'message' => 'Session joined'
        ], 200);
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

        $student = $request->user();

        
        if (!$session->students()->where('student_id', $student->id)->exists()) {
            return response()->json(['message' => 'You did not book this session'], 403);
        }

       
        $session->students()->detach($student->id);

        return response()->json(['message' => 'Session cancelled successfully'], 200);
    }
}

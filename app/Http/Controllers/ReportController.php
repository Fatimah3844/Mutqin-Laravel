<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;

class ReportController extends Controller
{
    /**
     * POST /sheikhs/reports
     * Send a report to a parent
     */
    public function sendReport(Request $request)
    {
        
        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'parent_id'  => 'required|exists:users,id',
            'content'    => 'required|string',
            'period'     => 'required|in:weekly,monthly',
        ]);

        $report = Report::create([
            'student_id' => $validated['student_id'],
            'parent_id'  => $validated['parent_id'],
            'content'    => $validated['content'],
            'period'     => $validated['period'],
        ]);

        
        return response()->json([
            'report_id' => (string)$report->id,
            'message'   => 'Report sent successfully',
        ], 200);
    }
}

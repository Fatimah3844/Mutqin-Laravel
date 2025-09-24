<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Badge;
use App\Models\User;

class BadgeController extends Controller
{
    /**
     * GET /sheikhs/badges
     *  */
    public function getAllBadges()
    {
        $badges = Badge::all(['id as badge_id', 'name', 'description']);
        return response()->json($badges, 200);
    }

    /**
     * POST /sheikhs/badges
     
     */
    public function createBadge(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'icon'        => 'nullable|string|max:255',
        ]);

        $badge = Badge::create($validated);

        return response()->json([
            'badge_id' => $badge->id,
            'message'  => 'Badge created successfully',
        ], 201);
    }

    /**
     * GET /sheikhs/badges/{badge_id}
    
     */
    public function getBadge($badge_id)
    {
        $badge = Badge::find($badge_id);

        if (!$badge) {
            return response()->json(['message' => 'Badge not found'], 404);
        }

        return response()->json([
            'badge_id'    => $badge->id,
            'name'        => $badge->name,
            'description' => $badge->description,
            'icon'        => $badge->icon,
        ], 200);
    }

    /**
     * POST /sheikhs/badges/assign
     */
    public function assignBadge(Request $request)
    {
        $validated = $request->validate([
            'badge_id'  => 'required|exists:badges,id',
            'student_id'=> 'required|exists:users,id',
        ]);

        $badge  = Badge::find($validated['badge_id']);
        $student= User::find($validated['student_id']);

        
        if ($student->badges()->where('badge_id', $badge->id)->exists()) {
            return response()->json(['message' => 'Badge already assigned to this student'], 400);
        }

        $student->badges()->attach($badge->id);

        return response()->json([
            'message'  => 'Badge assigned successfully',
            'badge_id' => $badge->id,
            'student_id' => $student->id
        ], 200);
    }
}

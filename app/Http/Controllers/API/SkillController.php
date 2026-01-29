<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Skill;

class SkillController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'skills' => Skill::select('id', 'name')->orderBy('name')->get()
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $skill = Skill::firstOrCreate([
            'name' => strtolower(trim($request->name))
        ]);

        return response()->json([
            'success' => true,
            'skill' => $skill
        ], 201);
    }

    public function resolveIdToName(Request $request)
    {
        $request->validate([
            'skill_ids' => 'required|array|min:1',
            'skill_ids.*' => 'exists:skills,id',
        ]);

        $skills = Skill::whereIn('id', $request->skill_ids)
            ->pluck('name')
            ->toArray();

        return response()->json([
            'success' => true,
            'skills' => $skills, // ["plumber", "electrician"]
        ], 200);
    }

    public function resolveNameToId(Request $request)
    {
        $request->validate([
            'skills' => 'required|array|min:1',
            'skills.*' => 'exists:skills,name',
        ]);

        $skillIds = Skill::whereIn('name', $request->skills)
            ->pluck('id')
            ->toArray();

        return response()->json([
            'success' => true,
            'skill_ids' => $skillIds, // [1, 3, 5]
        ], 200);
    }

}

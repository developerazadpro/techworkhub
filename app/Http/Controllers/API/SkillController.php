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

}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use App\Models\WorkJob;
use App\Services\JobMatchingService;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::with('roles')->where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->roles->first()?->name,
            ],
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|string|in:client,technician',
        ]);

        // 1. Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 2. Assign role manually
        $role = Role::where('name', $request->role)->first();
        if ($role) {
            $user->roles()->attach($role->id);
        }

        // 3. Create API token
        $token = $user->createToken('api-token')->plainTextToken;

        // 4. Return response
        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $role?->name,
            ],
        ], 201);
    }

    public function me(Request $request)
    {
        $user = $request->user()->load('roles');

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->roles->first()?->name,
        ];
    }

    public function getSkills(Request $request)
    {
        $user = $request->user();

        if (!$user->hasRole('technician')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return response()->json([
            'success' => true,
            'skills' => $user->skills()->select('skills.id', 'skills.name')->get()->makeHidden('pivot')
        ], 200);
    }


    public function updateSkills(Request $request)
    {
        $user = $request->user();

        if (!$user->hasRole('technician')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'skill_ids' => 'array',
            'skill_ids.*' => 'exists:skills,id'
        ]);

        $user->skills()->sync($request->skill_ids);

        // Update recommended_technician in work_jobs
        $openJobs = WorkJob::where('status', 'open')->get();

        foreach ($openJobs as $job) {
            try {
                app(JobMatchingService::class)->run($job);
            } catch (\Throwable $e) {
                logger()->error('Matching failed after skill update', [
                    'job_id' => $job->id,
                    'tech_id' => $user->id,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Skills updated successfully'
        ], 200);
    }

    
}

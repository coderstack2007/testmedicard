<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\UserRole;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\DoctorProfile;
use App\Models\PatientProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AuthController
{
    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        // Create user
        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => $validated['password'],
            'role'     => UserRole::from($validated['role']),
        ]);

        // Create profile based on role
        if ($user->isPatient()) {
            PatientProfile::create([
                'user_id'   => $user->id,
                'branch_id' => 1, // Default branch, moderator will change later
            ]);
        } elseif ($user->isDoctor()) {
            DoctorProfile::create([
                'user_id'       => $user->id,
                'branch_id'     => 1,
                'department_id' => 1,
            ]);
        }

        // Create token
        $token = $user->createToken('medicard')->plainTextToken;

        return response()->json([
            'user'  => $user,
            'token' => $token,
        ], 201);
    }

    public function login(LoginRequest $request)
    {

        $credentials = $request->validated();

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        if (!$user->is_active) {
            return response()->json(['error' => 'User account is inactive'], 403);
        }

        $token = $user->createToken('medicard')->plainTextToken;

        return response()->json([
            'user'  => $user,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function me(Request $request)
    {
        $user = $request->user()
            ->load(['doctorProfile.branch', 'doctorProfile.department', 'patientProfile.branch']);

        return response()->json($user);
    }
}

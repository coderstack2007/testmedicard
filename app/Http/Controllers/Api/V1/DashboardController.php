<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\UserRole;
use App\Models\Branch;
use App\Models\Chat;
use App\Models\Diagnosis;
use App\Models\DoctorProfile;
use App\Models\PatientProfile;
use Illuminate\Http\Request;

class DashboardController
{
    public function index(Request $request)
    {
        $user = $request->user();

        return match ($user->role) {
            UserRole::Patient => $this->patientDashboard($user),
            UserRole::Doctor => $this->doctorDashboard($user),
            UserRole::Moderator => $this->moderatorDashboard($user),
            UserRole::AbsoluteAdmin => $this->adminDashboard($user),
        };
    }

    private function patientDashboard($user)
    {
        return response()->json([
            'role'       => 'patient',
            'user'       => $user->append('patient_profile'),
            'diagnoses'  => Diagnosis::where('patient_id', $user->id)
                ->with('doctor')
                ->latest()
                ->take(5)
                ->get(),
            'chats'      => Chat::where('patient_id', $user->id)
                ->with('doctor')
                ->latest()
                ->get(),
            'branch'     => $user->patientProfile?->branch,
        ]);
    }

    private function doctorDashboard($user)
    {
        return response()->json([
            'role'       => 'doctor',
            'user'       => $user->append('doctor_profile'),
            'department' => $user->doctorProfile?->department,
            'chats'      => Chat::where('doctor_id', $user->id)
                ->with('patient')
                ->latest()
                ->get(),
            'stats'      => [
                'total_chats'     => Chat::where('doctor_id', $user->id)->count(),
                'total_diagnoses' => Diagnosis::where('doctor_id', $user->id)->count(),
            ],
        ]);
    }

    private function moderatorDashboard($user)
    {
        $branch = Branch::where('moderator_id', $user->id)->with('departments')->first();

        return response()->json([
            'role'           => 'moderator',
            'user'           => $user,
            'branch'         => $branch,
            'patients_count' => PatientProfile::whereHas('branch', fn($q) => $q->where('moderator_id', $user->id))->count(),
            'doctors_count'  => DoctorProfile::whereHas('branch', fn($q) => $q->where('moderator_id', $user->id))->count(),
        ]);
    }

    private function adminDashboard($user)
    {
        return response()->json([
            'role'           => 'absolute_admin',
            'user'           => $user,
            'branches_count' => Branch::count(),
            'doctors_count'  => DoctorProfile::count(),
            'patients_count' => PatientProfile::count(),
            'branches'       => Branch::with('departments')->get(),
        ]);
    }
}

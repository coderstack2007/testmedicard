<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\StorePatientRequest;
use App\Http\Resources\PatientProfileResource;
use App\Models\PatientProfile;
use App\Models\User;
use App\Traits\BranchScoped;
use Illuminate\Http\Request;

class PatientController
{
    use BranchScoped;

    public function index(Request $request)
    {
        $branchId = $this->userBranchId();

        $patients = PatientProfile::when($branchId, function ($query) use ($branchId) {
                return $query->where('branch_id', $branchId);
            })
            ->with('user', 'branch')
            ->paginate(20);

        return PatientProfileResource::collection($patients);
    }

    public function show(Request $request, int $id)
    {
        $patient = User::findOrFail($id);

        if ($patient->isPatient()) {
            $this->assertBranchAccess($patient->patientProfile->branch_id);
        }

        return response()->json($patient->load('patientProfile.branch'));
    }

    public function store(StorePatientRequest $request)
    {
        $this->assertBranchAccess($request->validated()['branch_id']);

        $patient = PatientProfile::create($request->validated());

        return response()->json(new PatientProfileResource($patient->load('user', 'branch')), 201);
    }

    public function destroy(Request $request, int $id)
    {
        $patient = User::findOrFail($id);

        if ($patient->isPatient()) {
            $this->assertBranchAccess($patient->patientProfile->branch_id);
        }

        $patient->delete();

        return response()->json(['message' => 'Patient deleted']);
    }
}


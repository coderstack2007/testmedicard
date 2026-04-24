<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\StoreDiagnosisRequest;
use App\Http\Requests\UpdateDiagnosisRequest;
use App\Http\Resources\DiagnosisResource;
use App\Models\Diagnosis;
use App\Models\User;
use App\Traits\BranchScoped;
use Illuminate\Http\Request;

class DiagnosisController
{
    use BranchScoped;

    public function index(Request $request, int $patientId)
    {
        $patient = User::findOrFail($patientId);
        
        if ($patient->isPatient()) {
            $this->assertBranchAccess($patient->patientProfile->branch_id);
        }

        $diagnoses = Diagnosis::where('patient_id', $patientId)
            ->with('patient', 'doctor')
            ->paginate(20);

        return DiagnosisResource::collection($diagnoses);
    }

    public function store(StoreDiagnosisRequest $request, int $patientId)
    {
        $patient = User::findOrFail($patientId);
        
        if ($patient->isPatient()) {
            $this->assertBranchAccess($patient->patientProfile->branch_id);
        }

        $diagnosis = Diagnosis::create([
            ...$request->validated(),
            'patient_id' => $patientId,
            'doctor_id'  => $request->user()->id,
            'status'     => $request->validated()['status'] ?? 'active',
        ]);

        return response()->json(new DiagnosisResource($diagnosis->load('patient', 'doctor')), 201);
    }

    public function show(Request $request, int $id)
    {
        $diagnosis = Diagnosis::with('patient', 'doctor')->findOrFail($id);
        
        if ($diagnosis->patient->isPatient()) {
            $this->assertBranchAccess($diagnosis->patient->patientProfile->branch_id);
        }

        return response()->json($diagnosis);
    }

    public function update(UpdateDiagnosisRequest $request, int $id)
    {
        $diagnosis = Diagnosis::findOrFail($id);
        
        if ($diagnosis->patient->isPatient()) {
            $this->assertBranchAccess($diagnosis->patient->patientProfile->branch_id);
        }

        $diagnosis->update($request->validated());

        return response()->json(new DiagnosisResource($diagnosis->load('patient', 'doctor')));
    }

    public function destroy(Request $request, int $id)
    {
        $diagnosis = Diagnosis::findOrFail($id);
        
        if ($diagnosis->patient->isPatient()) {
            $this->assertBranchAccess($diagnosis->patient->patientProfile->branch_id);
        }

        $diagnosis->delete();

        return response()->json(['message' => 'Diagnosis deleted']);
    }
}

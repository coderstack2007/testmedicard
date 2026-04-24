<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\StoreDoctorRequest;
use App\Http\Requests\UpdateDoctorRequest;
use App\Http\Resources\DoctorProfileResource;
use App\Models\DoctorProfile;
use App\Models\User;
use App\Traits\BranchScoped;
use Illuminate\Http\Request;

class DoctorController
{
    use BranchScoped;

    public function index(Request $request, int $branchId)
    {
        $this->assertBranchAccess($branchId);

        $doctors = DoctorProfile::where('branch_id', $branchId)
            ->with('user', 'branch', 'department')
            ->paginate(20);

        return DoctorProfileResource::collection($doctors);
    }

    public function store(StoreDoctorRequest $request)
    {
        $this->assertBranchAccess($request->validated()['branch_id']);

        $doctor = DoctorProfile::create($request->validated());

        return response()->json(
            new DoctorProfileResource($doctor->load('user', 'branch', 'department')),
            201
        );
    }

    public function show(Request $request, int $id)
    {
        $doctor = DoctorProfile::findOrFail($id);
        $this->assertBranchAccess($doctor->branch_id);

        return response()->json(new DoctorProfileResource($doctor->load('user', 'branch', 'department')));
    }

    public function update(UpdateDoctorRequest $request, int $id)
    {
        $doctor = DoctorProfile::findOrFail($id);
        $this->assertBranchAccess($doctor->branch_id);

        $doctor->update($request->validated());

        return response()->json(new DoctorProfileResource($doctor->load('user', 'branch', 'department')));
    }

    public function destroy(Request $request, int $id)
    {
        $doctor = DoctorProfile::findOrFail($id);
        $this->assertBranchAccess($doctor->branch_id);

        $doctor->delete();

        return response()->json(['message' => 'Doctor deleted']);
    }
}


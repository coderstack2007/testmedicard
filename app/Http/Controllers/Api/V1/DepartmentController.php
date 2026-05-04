<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Http\Resources\DepartmentResource;
use App\Models\Department;
use App\Traits\BranchScoped;
use Illuminate\Http\Request;

class DepartmentController
{
    use BranchScoped;

    public function index(Request $request, int $branchId)
    {
        $this->assertBranchAccess($branchId);

        $departments = Department::where('branch_id', $branchId)
            ->with('branch')
            ->paginate(8);

        return DepartmentResource::collection($departments);
    }

    public function store(StoreDepartmentRequest $request, int $branchId)
    {
        $this->assertBranchAccess($branchId);

        $department = Department::create([
            ...$request->validated(),
            'branch_id' => $branchId,
        ]);

        return response()->json(new DepartmentResource($department), 201);
    }

    public function show(Request $request, int $id)
    {
        $department = Department::with('branch')->findOrFail($id); 
        $this->assertBranchAccess($department->branch_id);

        return response()->json(new DepartmentResource($department));
    }

    public function update(UpdateDepartmentRequest $request, int $id)
    {
        $department = Department::findOrFail($id);
        $this->assertBranchAccess($department->branch_id);

        $department->update($request->validated());

        return response()->json(new DepartmentResource($department));
    }

public function destroy(Request $request, int $id)
{
    $department = Department::withCount('doctorProfiles')->findOrFail($id);
    $this->assertBranchAccess($department->branch_id);

    if ($department->doctor_profiles_count > 0) {
        return response()->json(['message' => 'Cannot delete: department has doctors'], 422);
    }

    $department->delete();
    return response()->json(['message' => 'Department deleted']);
}
}

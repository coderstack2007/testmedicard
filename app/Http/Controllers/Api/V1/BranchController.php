<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\StoreBranchRequest;
use App\Http\Requests\UpdateBranchRequest;
use App\Http\Resources\BranchResource;
use App\Models\Branch;
use App\Traits\BranchScoped;
use Illuminate\Http\Request;

class BranchController
{
    use BranchScoped;

    public function index(Request $request)
    {
        $branches = Branch::with('departments', 'moderator')
            ->when(!$request->user()->isAdmin(), function ($query) {
                return $query->where('id', $this->userBranchId());
            })
            ->paginate(20);

        return BranchResource::collection($branches);
    }

    public function store(StoreBranchRequest $request)
    {
        $branch = Branch::create($request->validated());

        return response()->json(new BranchResource($branch->load('moderator', 'departments')), 201);
    }

    public function show(Request $request, int $id)
    {
        $this->assertBranchAccess($id);

        $branch = Branch::with('departments', 'moderator')->findOrFail($id);

        return response()->json(new BranchResource($branch));
    }

    public function update(UpdateBranchRequest $request, int $id)
    {
        $this->assertBranchAccess($id);

        $branch = Branch::findOrFail($id);
        $branch->update($request->validated());

        return response()->json(new BranchResource($branch->load('moderator', 'departments')));
    }

    public function destroy(int $id)
    {
        $branch = Branch::withCount('departments')->findOrFail($id);
        
        if ($branch->departments_count > 0) {
            return response()->json([
                'message' => 'Cannot delete: branch has departments. Remove all departments first.'
            ], 422);
        }
        
        $branch->delete();
        return response()->json(['message' => 'Branch deleted']);
    }
}
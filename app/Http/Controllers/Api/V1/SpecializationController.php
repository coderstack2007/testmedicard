<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Specialization;
use Illuminate\Http\JsonResponse;


class SpecializationController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Specialization::orderBy('name')->get());
    }
}

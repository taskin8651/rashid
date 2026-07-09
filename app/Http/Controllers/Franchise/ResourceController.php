<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\FranchiseResource;
use Symfony\Component\HttpFoundation\Response;

class ResourceController extends Controller
{
    public function index()
    {
        $resources = FranchiseResource::latest()->get();

        return view('franchise.resources', compact('resources'));
    }

    public function download(FranchiseResource $resource): Response
    {
        return response()->download(storage_path('app/' . $resource->file_path), $resource->original_name);
    }
}

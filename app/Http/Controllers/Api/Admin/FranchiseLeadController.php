<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\FranchiseLead;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FranchiseLeadController extends Controller
{
    public function index()
    {
        $leads = FranchiseLead::latest()->get()->map(fn (FranchiseLead $l) => [
            'id' => $l->id,
            'name' => $l->name,
            'phone' => $l->phone,
            'email' => $l->email,
            'city' => $l->city,
            'budget_range' => $l->budget_range,
            'message' => $l->message,
            'status' => $l->status,
            'created_at' => $l->created_at->format('d M Y'),
        ]);

        return response()->json(['leads' => $leads]);
    }

    public function update(Request $request, FranchiseLead $lead)
    {
        $request->validate(['status' => ['required', Rule::in(['new', 'contacted', 'converted', 'rejected'])]]);
        $lead->update(['status' => $request->status]);

        return response()->json(['message' => 'Lead status updated.']);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeamMemberController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');

        $members = TeamMember::with(['franchiseBooking', 'creator'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $pendingCount = TeamMember::where('status', 'pending')->count();

        return view('admin.team-members.index', compact('members', 'status', 'pendingCount'));
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        if ($request->hasFile('photo')) {
            $validated['photo_path'] = $request->file('photo')->store('team', 'public');
        }
        unset($validated['photo']);

        $validated['created_by'] = $request->user()->id;
        $validated['status'] = 'approved';

        TeamMember::create($validated);

        return back()->with('status', 'Team member added.');
    }

    public function update(Request $request, TeamMember $teamMember)
    {
        $validated = $this->validated($request);

        if ($request->hasFile('photo')) {
            if ($teamMember->photo_path) {
                Storage::disk('public')->delete($teamMember->photo_path);
            }
            $validated['photo_path'] = $request->file('photo')->store('team', 'public');
        }
        unset($validated['photo']);

        $teamMember->update($validated);

        return back()->with('status', 'Team member updated.');
    }

    public function approve(TeamMember $teamMember)
    {
        $teamMember->update(['status' => 'approved']);

        return back()->with('status', 'Team member approved.');
    }

    public function reject(TeamMember $teamMember)
    {
        $teamMember->update(['status' => 'rejected']);

        return back()->with('status', 'Team member rejected.');
    }

    public function destroy(TeamMember $teamMember)
    {
        if ($teamMember->photo_path) {
            Storage::disk('public')->delete($teamMember->photo_path);
        }

        $teamMember->delete();

        return back()->with('status', 'Team member removed.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'designation' => ['required', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'photo' => ['nullable', 'image', 'max:5120'],
        ]);
    }
}

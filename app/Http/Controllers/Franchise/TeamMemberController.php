<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Concerns\AuthorizesFranchiseAccess;
use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class TeamMemberController extends Controller
{
    use AuthorizesFranchiseAccess;

    public function index(Request $request)
    {
        $this->authorizeAnyFranchisePermission($request, 'manage-team-members');

        $bookings = $request->user()->accessibleFranchiseBookingsQuery()->where('status', 'paid')->get(['id', 'city']);

        $members = TeamMember::whereIn('franchise_booking_id', $bookings->pluck('id'))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('franchise.team-members.index', compact('members', 'bookings'));
    }

    public function store(Request $request)
    {
        $bookingIds = $request->user()->accessibleFranchiseBookingsQuery()->where('status', 'paid')->pluck('id');

        $validated = $request->validate([
            'franchise_booking_id' => ['required', 'integer', Rule::in($bookingIds)],
            'name' => ['required', 'string', 'max:255'],
            'designation' => ['required', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'photo' => ['nullable', 'image', 'max:5120'],
        ]);

        $this->authorizeFranchisePermission($request, (int) $validated['franchise_booking_id'], 'manage-team-members');

        if ($request->hasFile('photo')) {
            $validated['photo_path'] = $request->file('photo')->store('team', 'public');
        }
        unset($validated['photo']);

        $validated['created_by'] = $request->user()->id;
        $validated['status'] = 'pending';

        TeamMember::create($validated);

        return back()->with('status', 'Team member submitted for approval.');
    }

    public function update(Request $request, TeamMember $teamMember)
    {
        $bookingIds = $request->user()->accessibleFranchiseBookingsQuery()->pluck('id');
        abort_unless($bookingIds->contains($teamMember->franchise_booking_id), 403);
        $this->authorizeFranchisePermission($request, $teamMember->franchise_booking_id, 'manage-team-members');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'designation' => ['required', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'photo' => ['nullable', 'image', 'max:5120'],
        ]);

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

    public function destroy(Request $request, TeamMember $teamMember)
    {
        $bookingIds = $request->user()->accessibleFranchiseBookingsQuery()->pluck('id');
        abort_unless($bookingIds->contains($teamMember->franchise_booking_id), 403);
        $this->authorizeFranchisePermission($request, $teamMember->franchise_booking_id, 'manage-team-members');

        if ($teamMember->photo_path) {
            Storage::disk('public')->delete($teamMember->photo_path);
        }

        $teamMember->delete();

        return back()->with('status', 'Team member removed.');
    }
}

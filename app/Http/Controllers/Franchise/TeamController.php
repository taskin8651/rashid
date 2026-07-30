<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\FranchiseRole;
use App\Models\FranchiseTeamMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class TeamController extends Controller
{
    public function index(Request $request)
    {
        // Team management is an owner-only capability — team members never
        // get delegated the right to manage other team members.
        abort_unless($request->user()->franchiseBookings()->exists(), 403);

        $bookings = $request->user()->franchiseBookings()->where('status', 'paid')->get();

        $roles = FranchiseRole::whereIn('franchise_booking_id', $bookings->pluck('id'))
            ->withCount('teamMembers')
            ->with('franchiseBooking')
            ->get();

        $members = FranchiseTeamMember::whereIn('franchise_booking_id', $bookings->pluck('id'))
            ->with(['user', 'role', 'franchiseBooking'])
            ->get();

        return view('franchise.team.index', [
            'bookings' => $bookings,
            'roles' => $roles,
            'members' => $members,
            'availablePermissions' => FranchiseRole::PERMISSIONS,
        ]);
    }

    protected function ownedBookingOrFail(Request $request, int $bookingId)
    {
        return $request->user()->franchiseBookings()->where('status', 'paid')->findOrFail($bookingId);
    }

    public function storeRole(Request $request)
    {
        $validated = $request->validate([
            'franchise_booking_id' => ['required', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'permissions' => ['array'],
            'permissions.*' => [Rule::in(FranchiseRole::PERMISSIONS)],
        ]);

        $booking = $this->ownedBookingOrFail($request, $validated['franchise_booking_id']);

        FranchiseRole::create([
            'franchise_booking_id' => $booking->id,
            'name' => $validated['name'],
            'permissions' => $validated['permissions'] ?? [],
        ]);

        return back()->with('status', 'Role created.');
    }

    public function updateRole(Request $request, FranchiseRole $role)
    {
        $this->ownedBookingOrFail($request, $role->franchise_booking_id);

        $validated = $request->validate([
            'permissions' => ['array'],
            'permissions.*' => [Rule::in(FranchiseRole::PERMISSIONS)],
        ]);

        $role->update(['permissions' => $validated['permissions'] ?? []]);

        return back()->with('status', 'Role updated.');
    }

    public function destroyRole(Request $request, FranchiseRole $role)
    {
        $this->ownedBookingOrFail($request, $role->franchise_booking_id);

        abort_if($role->teamMembers()->count() > 0, 422, 'Reassign team members off this role before deleting it.');

        $role->delete();

        return back()->with('status', 'Role deleted.');
    }

    public function storeMember(Request $request)
    {
        $validated = $request->validate([
            'franchise_booking_id' => ['required', 'integer'],
            'franchise_role_id' => ['required', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $booking = $this->ownedBookingOrFail($request, $validated['franchise_booking_id']);
        $role = FranchiseRole::where('franchise_booking_id', $booking->id)->findOrFail($validated['franchise_role_id']);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);
        $user->assignRole('franchisee');

        FranchiseTeamMember::create([
            'franchise_booking_id' => $booking->id,
            'user_id' => $user->id,
            'franchise_role_id' => $role->id,
        ]);

        return back()->with('status', 'Team member added.');
    }

    public function updateMember(Request $request, FranchiseTeamMember $member)
    {
        $this->ownedBookingOrFail($request, $member->franchise_booking_id);

        $validated = $request->validate([
            'franchise_role_id' => ['required', 'integer'],
        ]);

        $role = FranchiseRole::where('franchise_booking_id', $member->franchise_booking_id)->findOrFail($validated['franchise_role_id']);

        $member->update(['franchise_role_id' => $role->id]);

        return back()->with('status', 'Team member updated.');
    }

    public function destroyMember(Request $request, FranchiseTeamMember $member)
    {
        $this->ownedBookingOrFail($request, $member->franchise_booking_id);

        $user = $member->user;
        $member->delete();

        // If this was their only franchise access (no owned bookings, no
        // other memberships), the franchisee role no longer means anything
        // for them — drop it so they fall back to a plain student account.
        if (!$user->franchiseBookings()->exists() && !$user->franchiseTeamMemberships()->exists()) {
            $user->removeRole('franchisee');
        }

        return back()->with('status', 'Team member removed.');
    }
}

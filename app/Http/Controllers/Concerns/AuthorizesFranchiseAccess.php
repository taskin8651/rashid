<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;

trait AuthorizesFranchiseAccess
{
    /**
     * A franchise owner always has full access to their own booking(s); a
     * team member only has access if their assigned FranchiseRole includes
     * this specific permission.
     */
    protected function authorizeFranchisePermission(Request $request, int $franchiseBookingId, string $permission): void
    {
        abort_unless($request->user()->hasFranchisePermission($franchiseBookingId, $permission), 403);
    }

    /**
     * For listing/index pages that aren't scoped to one specific booking yet
     * — blocks the page entirely if the user has this permission on none of
     * their accessible bookings, so direct URL visits can't bypass what the
     * sidebar already hides from them.
     */
    protected function authorizeAnyFranchisePermission(Request $request, string $permission): void
    {
        abort_unless($request->user()->hasAnyFranchisePermission($permission), 403);
    }

    /**
     * Same as authorizeFranchisePermission, but passes if the user holds ANY
     * one of several permissions on that booking — e.g. a lighter follow-up
     * permission alongside the full manage permission it's a subset of.
     */
    protected function authorizeFranchiseAnyOf(Request $request, int $franchiseBookingId, array $permissions): void
    {
        $user = $request->user();
        abort_unless(collect($permissions)->contains(fn ($p) => $user->hasFranchisePermission($franchiseBookingId, $p)), 403);
    }

    /**
     * Same as authorizeAnyFranchisePermission, but passes if the user holds
     * ANY one of several permissions on ANY of their accessible bookings.
     */
    protected function authorizeAnyFranchisePermissions(Request $request, array $permissions): void
    {
        $user = $request->user();
        abort_unless(collect($permissions)->contains(fn ($p) => $user->hasAnyFranchisePermission($p)), 403);
    }
}

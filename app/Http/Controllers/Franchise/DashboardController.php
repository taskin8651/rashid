<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\FranchiseBooking;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $bookings = $request->user()->franchiseBookings()->latest()->get();
        $stages = FranchiseBooking::STAGES;

        return view('franchise.dashboard', compact('bookings', 'stages'));
    }
}

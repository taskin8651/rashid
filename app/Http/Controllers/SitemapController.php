<?php

namespace App\Http\Controllers;

use App\Models\FranchiseBooking;
use Illuminate\Support\Facades\Response;

class SitemapController extends Controller
{
    public function index()
    {
        $urls = [
            ['loc' => route('home'), 'priority' => '1.0'],
            ['loc' => route('courses'), 'priority' => '0.9'],
            ['loc' => route('free-demo'), 'priority' => '0.7'],
            ['loc' => route('why-rtech'), 'priority' => '0.6'],
            ['loc' => route('contact'), 'priority' => '0.5'],
            ['loc' => route('franchise'), 'priority' => '0.8'],
        ];

        foreach (FranchiseBooking::where('status', 'paid')->get() as $booking) {
            $urls[] = ['loc' => route('franchises.show', $booking), 'priority' => '0.6'];
        }

        $xml = view('sitemap', compact('urls'))->render();

        return Response::make($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\FranchiseBooking;
use App\Models\Post;
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
            ['loc' => route('gallery'), 'priority' => '0.5'],
            ['loc' => route('blog'), 'priority' => '0.6'],
            ['loc' => route('contact'), 'priority' => '0.5'],
            ['loc' => route('franchise'), 'priority' => '0.8'],
            ['loc' => route('certificates.verify'), 'priority' => '0.4'],
            ['loc' => route('privacy-policy'), 'priority' => '0.3'],
            ['loc' => route('terms'), 'priority' => '0.3'],
            ['loc' => route('refund-policy'), 'priority' => '0.3'],
        ];

        foreach (Course::where('status', 'active')->get() as $course) {
            $urls[] = ['loc' => route('courses.show', $course), 'priority' => '0.8'];
        }

        foreach (FranchiseBooking::where('status', 'paid')->get() as $booking) {
            $urls[] = ['loc' => route('franchises.show', $booking), 'priority' => '0.6'];
        }

        foreach (Post::where('status', 'published')->where('published_at', '<=', now())->get() as $post) {
            $urls[] = ['loc' => route('blog.show', $post), 'priority' => '0.5'];
        }

        $xml = view('sitemap', compact('urls'))->render();

        return Response::make($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}

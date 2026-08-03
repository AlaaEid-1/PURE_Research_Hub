<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Show the application home page.
     */
    public function __invoke(): View
    {
        $stats = [
            'total_publications' => '25,000+',
            'total_researchers' => '12,500+',
            'institutions' => '850+',
            'citations' => '180,000+',
        ];

        return view('pages.home', compact('stats'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Research;
use App\Models\ResearchCategory;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Generate dynamic XML sitemap for SEO search engine indexing.
     */
    public function sitemap(): Response
    {
        $researches = Research::where('status', 'published')->latest()->get();
        $categories = ResearchCategory::all();

        $xml = view('seo.sitemap', compact('researches', 'categories'))->render();

        return response($xml, 200, ['Content-Type' => 'text/xml']);
    }

    /**
     * Generate robots.txt file.
     */
    public function robots(): Response
    {
        $content = "User-agent: *\nAllow: /\n\nSitemap: ".url('/sitemap.xml')."\n";

        return response($content, 200, ['Content-Type' => 'text/plain']);
    }
}

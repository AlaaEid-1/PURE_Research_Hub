<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactFormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * Show the About page.
     */
    public function about(): View
    {
        return view('pages.about');
    }

    /**
     * Show the Contact page.
     */
    public function contact(): View
    {
        return view('pages.contact');
    }

    /**
     * Handle contact form submission.
     */
    public function submitContact(ContactFormRequest $request): RedirectResponse
    {
        // Contact form submission logic / email dispatch ready
        return back()->with('success', 'Thank you for reaching out! Our academic support team will respond shortly.');
    }
}

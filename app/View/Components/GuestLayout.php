<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class GuestLayout extends Component
{
    public function __construct(
        public string $title = 'PURE Research Hub - Modern Academic Publication Platform',
        public string $metaDescription = 'Discover, publish, and showcase academic research papers on PURE Research Hub.',
        public ?string $ogImage = null,
    ) {}

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.guest');
    }
}

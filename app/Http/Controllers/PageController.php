<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class PageController extends Controller
{
    /**
     * Show the About / Maison story page.
     */
    public function about(): View
    {
        return view('about');
    }

    /**
     * Show the Contact page.
     *
     * The enquiry form is not yet wired to a backend route — it still
     * only shows a client-side confirmation toast (public/js/main.js
     * initForms()). Real submission handling + validation lands in
     * Phase 6 alongside the checkout / order flow work.
     */
    public function contact(): View
    {
        return view('contact');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Laravel\Jetstream\Jetstream;

class TermsOfServicesController extends Controller
{
    /**
     * Show the privacy policy for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function show(Request $request)
    {
        $policyFile = Jetstream::localizedMarkdownPath('terms.md');

        return view('terms', [
            'terms' => Str::markdown(file_get_contents($policyFile)),
        ]);
    }

    public function showReviewerTerms(Request $request)
    {
        $policyFile = Jetstream::localizedMarkdownPath('terms-reviewer.md');

        return view('terms', [
            'terms' => Str::markdown(file_get_contents($policyFile)),
        ]);
    }
}

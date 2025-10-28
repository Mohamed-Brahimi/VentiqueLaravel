<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class LocalizationController extends Controller
{
    public function index($locale)
    {
        Log::info('LocalizationController called with locale: ' . $locale);

        // Validate locale
        if (!in_array($locale, ['en', 'fr', 'es'])) {
            $locale = 'en';
        }

        // Store in session
        Session::put('locale', $locale);
        Session::save(); // Force save

        Log::info('Session locale saved: ' . Session::get('locale'));

        // Set app locale for this request
        App::setLocale($locale);

        Log::info('App locale set in controller: ' . App::getLocale());

        return redirect()->back();
    }
}
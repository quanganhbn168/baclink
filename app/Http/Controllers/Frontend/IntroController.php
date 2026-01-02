<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Intro;

class IntroController extends Controller
{
    public function index()
    {
        // Get all intros for sidebar
        $intros = Intro::where('status', 1)->get();
        // Default to first one or search for 'gioi-thieu-chung'
        $intro = $intros->firstWhere('slug', 'gioi-thieu-chung') ?? $intros->first();
        
        return view('frontend.intro.index', compact('intro', 'intros'));
    }

    public function getBySlug(Intro $intro)
    {
        $intros = Intro::where('status', 1)->get();
        return view('frontend.intro.index', compact('intro', 'intros'));
    }
}

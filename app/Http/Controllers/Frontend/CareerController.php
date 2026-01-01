<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Career;

class CareerController extends Controller
{
    public function index()
    {
        $careers = Career::where('status', true)
            ->where(fn($q) => $q->whereNull('deadline')->orWhere('deadline', '>=', now()))
            ->latest('id')
            ->paginate(10);
            
        return view('frontend.careers.index', compact('careers'));
    }

    public function show(Career $career)
    {
        if (!$career->status) {
            abort(404);
        }
        return view('frontend.careers.show', compact('career'));
    }
}
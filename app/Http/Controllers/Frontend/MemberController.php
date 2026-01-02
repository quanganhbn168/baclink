<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Post;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index()
    {
        // Get members with profiles, paginated
        $members = User::whereHas('dealerProfile')
            ->with('dealerProfile')
            ->paginate(12);

        // Fetch trending posts for sidebar
        $trendingPosts = Post::where('status', 1)
            ->latest('updated_at')
            ->take(5)
            ->get();

        return view('frontend.member.index', compact('members', 'trendingPosts'));
    }
}

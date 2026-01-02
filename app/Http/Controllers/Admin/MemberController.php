<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DealerProfile;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = DealerProfile::with('user');

        if ($request->has('keyword') && $request->keyword) {
            $keyword = $request->keyword;
            $query->where(function($q) use ($keyword) {
                $q->where('company_name', 'like', "%{$keyword}%")
                  ->orWhere('representative_name', 'like', "%{$keyword}%")
                  ->orWhere('phone', 'like', "%{$keyword}%");
            });
        }

        $members = $query->latest()->paginate(10);

        return view('admin.members.index', compact('members'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $member = DealerProfile::with('user')->findOrFail($id);
        return view('admin.members.show', compact('member'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $member = DealerProfile::findOrFail($id);
        // Optional: delete associated User? 
        // For now, just delete the profile or user. 
        // Usually if we delete profile, user might remain as normal user or be deleted.
        // Let's delete user if they are just a member.
        
        $user = $member->user;
        $member->delete();
        if($user) $user->delete();

        return redirect()->route('admin.members.index')->with('success', 'Đã xóa hội viên thành công.');
    }
}

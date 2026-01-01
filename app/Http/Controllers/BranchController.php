<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $branches = Branch::latest()->paginate(10);
        return view('admin.branches.index', compact('branches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.branches.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|unique:branches,phone',
            'email' => 'nullable|email|unique:branches,email',
            'map_url' => 'nullable',
            'status' => 'required|boolean',
        ]);

        Branch::create($validated);

        return redirect()->route('admin.branches.index')->with('success', 'Thêm chi nhánh thành công.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Branch $branch)
    {
        return view('admin.branches.show', compact('branch'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Branch $branch)
    {
        return view('admin.branches.edit', compact('branch'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|unique:branches,phone,' . $branch->id,
            'email' => 'nullable|email|unique:branches,email,' . $branch->id,
            'map_url' => 'nullable',
            'status' => 'required|boolean',
        ]);

        $branch->update($validated);

        return redirect()->route('admin.branches.index')->with('success', 'Cập nhật chi nhánh thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Branch $branch)
    {
        $branch->delete();
        return back()->with('success', 'Xóa chi nhánh thành công.');
    }
}
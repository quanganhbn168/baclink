<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;
use App\Services\TeamService;

class TeamController extends Controller
{
    public function __construct(protected TeamService $teamService) {}

    public function index()
    {
        $teams = $this->teamService->getAll();
        return view('admin.teams.index', compact('teams'));
    }

    public function create()
    {
        return view('admin.teams.create');
    }

    public function store(Request $request)
    {
        $this->teamService->create($request);

        $route = $request->has('save_new')
            ? route('admin.teams.create')
            : route('admin.teams.index');

        return redirect($route)->with('success', 'Thêm giảng viên thành công.');
    }

    public function edit(Team $team)
    {
        return view('admin.teams.edit', compact('team'));
    }

    public function update(Request $request, Team $team)
    {
        $this->teamService->update($request, $team);
        return redirect()->route('admin.teams.index')->with('success', 'Cập nhật giảng viên thành công.');
    }

    public function destroy(Team $team)
    {
        $this->teamService->delete($team);
        return redirect()->route('admin.teams.index')->with('success', 'Xoá giảng viên thành công.');
    }
}

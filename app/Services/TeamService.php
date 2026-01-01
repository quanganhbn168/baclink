<?php

namespace App\Services;

use App\Models\Team;
use Illuminate\Http\Request;
use App\Traits\UploadImageTrait;

class TeamService
{
    use UploadImageTrait;

    protected string $uploadPath = 'uploads/teams';

    public function getAll()
    {
        return Team::latest()->paginate(20);
    }

    public function create(Request $request): Team
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'position'    => 'nullable|string|max:255',
            'level'   => 'nullable|string|max:10',
            'experience'  => 'nullable|integer|min:0|max:50',
            'bio'         => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data['image'] = $this->handleUploadImage($request);

        return Team::create($data);
    }

    public function update(Request $request, Team $team): Team
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'position'    => 'nullable|string|max:255',
            'level'   => 'nullable|string|max:10',
            'experience'  => 'nullable|integer|min:0|max:50',
            'bio'         => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $this->deleteImage($team->image);
            $data['image'] = $this->handleUploadImage($request);
        }

        $team->update($data);
        return $team;
    }

    public function delete(Team $team): bool
    {
        $this->deleteImage($team->image);
        return $team->delete();
    }

    protected function handleUploadImage(Request $request): ?string
    {
        if ($request->hasFile('image')) {
            return $this->uploadImage(
                $request->file('image'),
                $this->uploadPath,
                500,
                500,
                false,
                '',
                false
            );
        }
        return null;
    }
}

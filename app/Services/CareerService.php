<?php

namespace App\Services;

use App\Http\Requests\CareerRequest;
use App\Models\Career;
use App\Traits\UploadImageTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CareerService
{
    use UploadImageTrait;

    public function getAll() { return Career::latest('id')->get(); }

    public function store(CareerRequest $request): Career
    {
        return DB::transaction(fn() => $this->saveData(new Career(), $request));
    }

    public function update(CareerRequest $request, Career $career): Career
    {
        return DB::transaction(fn() => $this->saveData($career, $request));
    }

    public function destroy(Career $career): bool
    {
        return DB::transaction(function () use ($career) {
            if ($career->image) $this->deleteImage($career->image);
            return $career->delete();
        });
    }

    private function saveData(Career $career, CareerRequest $request): Career
    {
        $data = $request->validated();

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        if ($request->hasFile('image')) {
            if ($career->image) $this->deleteImage($career->image);
            $data['image'] = $this->uploadImage(file: $request->file('image'), folder: 'careers', resizeWidth: 800);
        }

        $career->fill($data)->save();
        return $career;
    }
}
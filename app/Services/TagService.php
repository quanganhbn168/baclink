<?php

namespace App\Services;

use App\Models\Tag;
use Illuminate\Support\Str;

class TagService
{
    public function getAll()
    {
        return Tag::latest()->paginate(20);
    }

    public function create(array $data): Tag
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        return Tag::create($data);
    }

    public function update(Tag $tag, array $data): bool
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        return $tag->update($data);
    }

    public function delete(Tag $tag): ?bool
    {
        return $tag->delete();
    }

    public function searchAjax(string $query)
    {
        return Tag::where('name', 'like', "%{$query}%")
            ->limit(10)
            ->get(['id', 'name']);
    }
}

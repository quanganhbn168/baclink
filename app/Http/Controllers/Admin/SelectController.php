<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attribute;
use App\Models\Category;
class SelectController extends Controller
{
    /**
     * Tìm kiếm và trả về danh sách thuộc tính dưới dạng JSON.
     */
    public function attributes(Request $request)
    {
        $term = $request->input('q'); 
        $results = Attribute::where('name', 'LIKE', '%'.$term.'%')
                            ->limit(10)
                            ->get(['id', 'name']); 

        
        return response()->json($results);
    }

    public function categoriesByType(Request $request)
    {
        $request->validate(['type' => 'required|string']);
        
        $type = $request->input('type');
        $idToExclude = $request->input('exclude'); 

        $query = Category::where('cate_type', $type);

        if ($idToExclude) {
            $query->where('id', '!=', $idToExclude);
        }

        $categories = $query->get(['id', 'name', 'parent_id']);
        
        
        return response()->json($categories);
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AttributeValueRequest;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Services\AttributeService;

class AttributeValueController extends Controller
{
    protected $attributeService;

    public function __construct(AttributeService $attributeService)
    {
        $this->attributeService = $attributeService;
    }

    public function store(AttributeValueRequest $request, Attribute $attribute)
    {
        $this->attributeService->createAttributeValue($attribute, $request->validated());
        return back()->with('success', 'Thêm giá trị thành công!');
    }

    public function destroy(AttributeValue $value)
    {
        $this->attributeService->deleteAttributeValue($value);
        return back()->with('success', 'Xóa giá trị thành công!');
    }
}
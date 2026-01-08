@props([
    'categories',
    'selectedId' => null,
    'excludeId' => null,
    'level' => 0
])

@foreach ($categories as $category)
    {{-- Loại trừ một danh mục cụ thể (thường là chính nó trong form edit) --}}
    @if ($excludeId && $category->id == $excludeId)
        @continue
    @endif

    <option value="{{ $category->id }}" 
            {{-- Kiểm tra và selected option phù hợp --}}
            @if ($selectedId == $category->id) selected @endif>
        
        {{-- Tạo khoảng trống thụt đầu dòng để thể hiện cấp độ --}}
        {!! str_repeat('&nbsp;&nbsp;&nbsp;', $level) !!}
        
        {{-- Thêm ký tự phân cấp cho trực quan --}}
        @if($level > 0) &vdash; @endif

        {{ $category->name }}
    </option>

    {{-- Nếu danh mục này có con, gọi lại component này (đệ quy) --}}
    @if ($category->children->isNotEmpty())
        <x-form.category-tree
            :categories="$category->children"
            :selectedId="$selectedId"
            :excludeId="$excludeId"
            :level="$level + 1"
        />
    @endif
@endforeach

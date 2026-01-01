<div class="btn-group" role="group">
    <a href="{{ $editUrl }}" class="btn btn-warning btn-sm">
        <i class="far fa-edit"></i>
    </a>
    <form action="{{ $deleteUrl }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa không?');" style="display:inline-block;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger btn-sm">
            <i class="far fa-trash-alt"></i>
        </button>
    </form>
</div>
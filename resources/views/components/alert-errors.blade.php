{{-- File: resources/views/components/alert-errors.blade.php --}}

@if ($errors->any())
    <div class="alert alert-danger" role="alert">
        <h6 class="alert-heading">Có lỗi xảy ra, vui lòng kiểm tra lại!</h6>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

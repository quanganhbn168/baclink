@props(['dataTable'])
<div>
    <div class="card-header">
        <h3 class="card-title"></h3>
    </div>
    <div class="card-body">
        {!! $dataTable->table() !!}
    </div>
</div>

@push('js')
    {!! $dataTable->scripts() !!}
@endpush
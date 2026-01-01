<ol class="breadcrumb float-sm-right">
    @foreach($crumbs as $crumb)
        @if($loop->last)
            <li class="breadcrumb-item active" aria-current="page">{{ $crumb['label'] }}</li>
        @else
            <li class="breadcrumb-item"><a href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a></li>
        @endif
    @endforeach
</ol>
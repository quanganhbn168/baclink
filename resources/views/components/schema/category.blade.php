@props(['category', 'products'])

@push('jsonld')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "CollectionPage",
  "name": "{{ $category->name }}",
  "description": "{{ strip_tags($category->description ?? '') }}",
  "url": "{{ request()->url() }}",
  "image": "{{ asset($category->banner ?? 'uploads/default-banner.webp') }}",
  "isPartOf": {
    "@type": "WebSite",
    "@id": "{{ url('/') }}"
  },
  "mainEntity": {
    "@type": "ItemList",
    "itemListElement": [
      @foreach($products as $index => $product)
      {
        "@type": "ListItem",
        "position": {{ $index + 1 }},
        "url": "{{ route('frontend.product.show', $product->slug) }}",
        "name": "{{ $product->name }}"
      }@if(!$loop->last),@endif
      @endforeach
    ]
  }
}
</script>
@endpush

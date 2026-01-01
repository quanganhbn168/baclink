@props(['product'])



@props(['product'])

@push('jsonld')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Product",
  "name": "{{ $product->name }}",
  "image": [
    "{{ asset($product->image) }}"
  ],
  "description": "{{ e(strip_tags($product->description)) }}",
  "sku": "{{ $product->id }}",
  "mpn": "{{ $product->sku ?? 'SAMAN-' . $product->id }}",
  "brand": {
    "@type": "Brand",
    "name": "{{ $setting->name ?? 'WebApp Bắc Ninh' }}"
  },
  @if($product->category)
  "category": {
    "@type": "Thing",
    "name": "{{ $product->category->name }}"
  },
  @endif
  "offers": {
    "@type": "Offer",
    "url": "{{ request()->url() }}",
    "priceCurrency": "VND",
    "price": "{{ $product->price_discount > 0 ? $product->price_discount : $product->price }}",
    "itemCondition": "https://schema.org/NewCondition",
    "availability": "https://schema.org/InStock",
    "priceValidUntil": "{{ now()->addYear()->toDateString() }}"
  }
}
</script>
@endpush

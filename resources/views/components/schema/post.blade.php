@props(['post'])

@push('jsonld')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "NewsArticle",
  "headline": "{{ $post->title }}",
  "description": "{{ Str::limit(strip_tags($post->description), 150) }}",
  "image": "{{ asset($post->image) }}",
  "author": {
    "@type": "Person",
    "name": "Admin"
  },
  "publisher": {
    "@type": "Organization",
    "name": "{{ $setting->name ?? 'WebApp Bắc Ninh' }}",
    "logo": {
      "@type": "ImageObject",
      "url": "{{ asset($setting->logo ?? 'images/logo.png') }}"
    }
  },
  "datePublished": "{{ $post->created_at->toAtomString() }}",
  "dateModified": "{{ $post->updated_at->toAtomString() }}",
  "mainEntityOfPage": {
    "@type": "WebPage",
    "@id": "{{ request()->url() }}"
  }
}
</script>
@endpush

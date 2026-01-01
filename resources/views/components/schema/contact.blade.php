@props(['setting'])

@push('jsonld')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "ContactPage",
  "name": "Liên hệ {{ $setting->name ?? 'WebApp Bắc Ninh' }}",
  "url": "{{ request()->url() }}",
  "mainEntity": {
    "@type": "Organization",
    "name": "{{ $setting->name ?? 'WebApp Bắc Ninh' }}",
    "url": "{{ url('/') }}",
    "logo": "{{ asset($setting->logo ?? 'images/logo.png') }}",
    "contactPoint": {
      "@type": "ContactPoint",
      "telephone": "+84{{ $setting->phone ?? '965625210' }}",
      "contactType": "Customer Service",
      "availableLanguage": ["Vietnamese"]
    }
  }
}
</script>
@endpush

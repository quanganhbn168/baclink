@props([
    'speed' => '30s', // tốc độ di chuyển (CSS animation-duration)
])

<div class="marquee-container">
    <div class="marquee-content" style="animation-duration: {{ $speed }}">
        {{ $slot }}
    </div>
</div>

@once
    @push('css')
        <style>
            .marquee-container {
                overflow: hidden;
                position: relative;
                width: 100%;
                height: auto;
            }

            .marquee-content {
                display: inline-block;
                white-space: nowrap;
                will-change: transform;
                animation: marquee-left linear infinite;
            }

            @keyframes marquee-left {
                0% {
                    transform: translateX(100%);
                }
                100% {
                    transform: translateX(-100%);
                }
            }
        </style>
    @endpush
@endonce

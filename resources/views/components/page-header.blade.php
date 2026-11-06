@props(['title', 'subtitle' => null])
<div {{ $attributes->class('flex flex-col sm:flex-row sm:items-center justify-between gap-4') }}>
    <div class="min-w-0">
        <h2 class="text-2xl font-bold text-[#191c1e]">{{ $title }}</h2>
        @if($subtitle)
            <p class="text-xs md:text-sm text-[#434655] mt-1">{{ $subtitle }}</p>
        @endif
    </div>
    @isset($actions)
        <div class="flex flex-wrap items-center gap-2">{{ $actions }}</div>
    @endisset
</div>

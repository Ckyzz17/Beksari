@props(['label', 'value', 'icon', 'tone' => 'blue', 'hint' => null, 'href' => null])
@php
    $tones = [
        'blue'    => 'bg-blue-50 text-[#004ac6]',
        'emerald' => 'bg-emerald-50 text-emerald-700',
        'amber'   => 'bg-amber-50 text-amber-700',
        'violet'  => 'bg-violet-50 text-violet-700',
        'rose'    => 'bg-rose-50 text-rose-700',
        'slate'   => 'bg-slate-100 text-slate-600',
    ];
    $tag = $href ? 'a' : 'div';
@endphp
<{{ $tag }} @if($href) href="{{ $href }}" @endif {{ $attributes->class([
    'flex items-center gap-4 p-5 rounded-2xl bg-white border border-[#c3c6d7] shadow-sm min-w-0',
    'hover:border-[#2563eb] transition-colors' => $href,
]) }}>
    <span class="w-11 h-11 shrink-0 rounded-xl flex items-center justify-center {{ $tones[$tone] ?? $tones['blue'] }}">
        <span class="material-symbols-outlined">{{ $icon }}</span>
    </span>
    <div class="min-w-0">
        <p class="text-xs font-semibold text-[#434655]">{{ $label }}</p>
        <p class="text-2xl font-extrabold leading-tight text-[#191c1e] truncate">{{ $value }}</p>
        @if($hint)
            <p class="text-[11px] text-[#737686] mt-0.5">{{ $hint }}</p>
        @endif
    </div>
</{{ $tag }}>

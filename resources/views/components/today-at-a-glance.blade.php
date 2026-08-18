@props(['completed', 'total', 'focusMins'])

<div class="py-6 border-t border-[var(--color-border)] mt-8">
    <div class="meta-text font-semibold uppercase tracking-wider mb-4">Today at a glance</div>
    
    <div class="flex justify-between items-end mb-2">
        <div class="text-sm"><span class="font-bold text-[var(--color-text)]">{{ $completed }} / {{ $total }}</span> activities done</div>
        <div class="text-sm"><span class="font-bold text-[var(--color-text)]">{{ $focusMins }} min</span> focus</div>
    </div>
    
    <!-- Progress Indicator -->
    <div class="h-1.5 w-full bg-[#E8E6E1] rounded-full overflow-hidden">
        <div class="h-full bg-[var(--color-text)] rounded-full transition-all duration-500" style="width: {{ ($completed / max($total, 1)) * 100 }}%;"></div>
    </div>
</div>
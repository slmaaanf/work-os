@props([
    'title',
    'desc' => null,
    'plannedMins' => null,
    'actualMins' => null,
    'category' => 'work',
    'project' => null,
    'status' => 'planned', // planned, in_progress, done_today
])

@php
    $accentClass = match ($category) {
        'work' => 'border-work',
        'life' => 'border-life',
        'learn' => 'border-learn',
        default => 'border-work',
    };

    $btnClass = match ($category) {
        'work' => 'text-[var(--color-work)] bg-blue-50 hover:bg-blue-100',
        'life' => 'text-[var(--color-life)] bg-green-50 hover:bg-green-100',
        'learn' => 'text-[var(--color-learn)] bg-amber-50 hover:bg-amber-100',
        default => 'text-[var(--color-work)] bg-gray-50 hover:bg-gray-100',
    };

    // Dimcard visually if done for today
    $cardOpacity = $status === 'done_today' ? 'opacity-60 bg-gray-50' : 'bg-[var(--color-surface)]';
@endphp

<div class="group {{ $cardOpacity }} border border-[var(--color-border)] border-l-4 {{ $accentClass }} rounded-[var(--radius-md)] p-4 hover:shadow-[var(--shadow-hover)] transition-all flex items-start gap-3">
    
    <!-- DPA Status Icon -->
    <div class="mt-1 text-[var(--color-text-muted)] cursor-default font-bold">
        @if($status === 'done_today')
            ✓
        @elseif($status === 'in_progress')
            ◐
        @else
            ○
        @endif
    </div>

    <div class="flex-1 min-w-0">
        <div class="activity-title {{ $status === 'done_today' ? 'line-through text-[var(--color-text-muted)]' : '' }}">
            {{ $title }}
        </div>

        @if($desc)
            <div class="activity-desc mt-0.5">{{ $desc }}</div>
        @endif

        <div class="flex items-center justify-between gap-4 mt-4">
            
            <div class="meta-text flex items-center gap-2 min-w-0">
                @if($status === 'done_today' && $actualMins)
                    <span class="whitespace-nowrap font-medium">{{ $actualMins }} min focused</span>
                @elseif($plannedMins)
                    <span class="whitespace-nowrap">🎯 {{ $plannedMins }} min</span>
                @endif

                @if($project)
                    <span class="opacity-30">•</span>
                    <span class="truncate">{{ $project }}</span>
                @endif
            </div>

            @if($status === 'done_today')
                <span class="text-sm font-medium text-[var(--color-text-muted)]">Done</span>
            @else
                <button type="button" class="md:opacity-0 md:group-hover:opacity-100 flex-shrink-0 flex items-center gap-1.5 text-sm font-medium px-3 py-1.5 rounded-[var(--radius-sm)] transition-all {{ $btnClass }}">
                    <span>▶</span> Start
                </button>
            @endif

        </div>
    </div>
</div>
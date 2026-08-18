@props(['category', 'title', 'icon', 'count' => 0])

<section>
    <div class="flex items-center justify-between mb-3">
        <h2 class="section-title flex items-center gap-2">
            <span>{{ $icon }}</span> {{ $title }}
        </h2>
        <span class="meta-text">{{ $count }} activities</span>
    </div>

    <div class="space-y-3">
        {{ $slot }}
    </div>
</section>
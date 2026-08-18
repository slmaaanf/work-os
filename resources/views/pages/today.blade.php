@extends('layouts.app')

@section('content')

@php
    // --- Mocking Daily Plan Activities (DPA) Status ---
    $workDPA = [
        ['title' => 'Perbaiki Jurnal', 'desc' => 'Perbaiki bagian metodologi', 'planned_mins' => 30, 'actual_mins' => null, 'category' => 'work', 'project' => 'Thesis', 'status' => 'planned'],
        ['title' => 'Documentation API Design', 'desc' => null, 'planned_mins' => 45, 'actual_mins' => 25, 'category' => 'work', 'project' => 'Work OS', 'status' => 'in_progress'],
    ];

    $lifeDPA = [
        ['title' => 'Journaling', 'desc' => null, 'planned_mins' => 15, 'actual_mins' => 15, 'category' => 'life', 'project' => null, 'status' => 'done_today'],
    ];

    $learnDPA = [
        ['title' => 'Baca Buku (Bab 4)', 'desc' => null, 'planned_mins' => 20, 'actual_mins' => null, 'category' => 'learn', 'project' => null, 'status' => 'planned'],
    ];
@endphp

    <x-today-header />

    <div class="mt-8 space-y-8">
        
        <x-carry-over-banner />

        <x-quick-add />

        <x-activity-section category="work" title="Work" icon="💼" :count="count($workDPA)">
            @foreach($workDPA as $dpa)
                <x-activity-card 
                    :title="$dpa['title']"
                    :desc="$dpa['desc']"
                    :planned-mins="$dpa['planned_mins']"
                    :actual-mins="$dpa['actual_mins']"
                    :category="$dpa['category']"
                    :project="$dpa['project']"
                    :status="$dpa['status']"
                />
            @endforeach
        </x-activity-section>

        <x-activity-section category="life" title="Life" icon="🌱" :count="count($lifeDPA)">
            @foreach($lifeDPA as $dpa)
                <x-activity-card 
                    :title="$dpa['title']"
                    :desc="$dpa['desc']"
                    :planned-mins="$dpa['planned_mins']"
                    :actual-mins="$dpa['actual_mins']"
                    :category="$dpa['category']"
                    :project="$dpa['project']"
                    :status="$dpa['status']"
                />
            @endforeach
        </x-activity-section>

        <x-activity-section category="learn" title="Learn" icon="📚" :count="count($learnDPA)">
            @foreach($learnDPA as $dpa)
                <x-activity-card 
                    :title="$dpa['title']"
                    :desc="$dpa['desc']"
                    :planned-mins="$dpa['planned_mins']"
                    :actual-mins="$dpa['actual_mins']"
                    :category="$dpa['category']"
                    :project="$dpa['project']"
                    :status="$dpa['status']"
                />
            @endforeach
        </x-activity-section>

        <!-- Metrics independent of activity count -->
        <x-today-at-a-glance completed="1" total="4" focus-mins="40" />

        <div class="pb-16 text-center">
            <button class="px-6 py-3 bg-[var(--color-surface)] border border-[var(--color-border)] text-sm font-semibold rounded-full shadow-sm hover:shadow-md transition-all text-[var(--color-text)] flex items-center justify-center gap-2 mx-auto">
                <span>🌙</span> Review Day
            </button>
        </div>

    </div>

@endsection
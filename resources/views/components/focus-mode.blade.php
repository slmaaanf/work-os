<div id="focus-overlay" class="fixed inset-0 bg-[var(--color-surface)] z-50 flex flex-col hidden opacity-0 transition-opacity duration-300">
    
    <!-- Top Bar (Minimalist) -->
    <div class="p-6 flex justify-between items-center">
        <div class="meta-text tracking-widest uppercase">Focus Session</div>
        <!-- Tombol Dokumentasi (Tanpa menghentikan timer) -->
        <button id="btn-add-doc" class="flex items-center gap-2 text-sm font-medium text-[var(--color-text-muted)] hover:text-[var(--color-text)] transition-colors px-3 py-1.5 rounded-[var(--radius-sm)] hover:bg-gray-50">
            <span>📎</span> Add Documentation
        </button>
    </div>

    <!-- Center Content (The Zone) -->
    <div class="flex-1 flex flex-col items-center justify-center p-6 text-center max-w-lg mx-auto w-full">
        
        <!-- Activity Context -->
        <div class="mb-12">
            <div id="focus-project" class="meta-text mb-2">Project Work OS</div>
            <h2 id="focus-title" class="page-title text-[28px] md:text-[36px]">Documentation API Design</h2>
        </div>

        <!-- The Timer (Not intimidating, just clean typography) -->
        <div class="mb-12 font-variant-numeric font-medium text-[64px] md:text-[80px] text-[var(--color-text)] leading-none tracking-tight">
            <span id="timer-display">25:00</span>
        </div>

        <!-- Primary Actions -->
        <div class="flex flex-col gap-4 w-full md:w-auto md:min-w-[240px]">
            <!-- Pause / Resume Toggle -->
            <button id="btn-pause-resume" class="w-full py-3 px-6 bg-gray-50 border border-[var(--color-border)] text-[var(--color-text)] font-medium rounded-full hover:bg-gray-100 transition-colors flex items-center justify-center gap-2">
                <span id="pause-icon">⏸</span> <span id="pause-text">Pause</span>
            </button>

            <!-- Finish Session (Memicu UI-03) -->
            <button id="btn-finish-session" class="w-full py-3 px-6 bg-[var(--color-text)] text-white font-medium rounded-full hover:opacity-90 transition-opacity flex items-center justify-center gap-2 shadow-md">
                <span>✓</span> Finish Session
            </button>
        </div>
        
    </div>
</div>
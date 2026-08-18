<div id="qa-container" class="bg-[var(--color-surface)] border border-[var(--color-border)] rounded-[var(--radius-md)] p-4 shadow-sm transition-all">
    
    <div id="qa-collapsed" class="flex items-center gap-3 cursor-text text-[var(--color-text-muted)]">
        <span class="text-lg">+</span>
        <span class="text-sm font-medium">What do you want to do today?</span>
    </div>

    <div id="qa-expanded" class="hidden space-y-4">
        <input type="text" id="qa-input" placeholder="Misal: Perbaiki dokumentasi API..." class="w-full bg-transparent border-none p-0 focus:ring-0 activity-title outline-none placeholder-gray-400">
        
        <div class="flex flex-wrap items-center justify-between gap-4 pt-2 border-t border-[var(--color-border)]">
            <div class="flex gap-2" id="qa-categories">
                <!-- Default state: Work active -->
                <button type="button" data-category="work" class="qa-cat-btn px-3 py-1.5 rounded-full text-xs font-medium transition-colors bg-blue-50 text-[var(--color-work)]">
                    💼 Work
                </button>
                <button type="button" data-category="life" class="qa-cat-btn px-3 py-1.5 rounded-full text-xs font-medium transition-colors text-[var(--color-text-muted)] hover:bg-gray-50">
                    🌱 Life
                </button>
                <button type="button" data-category="learn" class="qa-cat-btn px-3 py-1.5 rounded-full text-xs font-medium transition-colors text-[var(--color-text-muted)] hover:bg-gray-50">
                    📚 Learn
                </button>
            </div>
            
            <div class="flex items-center gap-3">
                <div class="meta-text flex items-center gap-1 bg-gray-50 px-3 py-1.5 rounded-[var(--radius-sm)] border border-[var(--color-border)]">
                    <span>Today</span>
                    <input type="number" id="qa-mins" placeholder="30" class="w-10 bg-transparent border-none p-0 text-center focus:ring-0 text-sm font-medium text-[var(--color-text)] outline-none">
                    <span>min</span>
                </div>
                <button type="button" id="qa-submit" class="px-4 py-1.5 bg-[var(--color-text)] text-white text-sm font-medium rounded-[var(--radius-sm)] hover:opacity-90 transition-opacity">
                    Add
                </button>
            </div>
        </div>
    </div>
</div>
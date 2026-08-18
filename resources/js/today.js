document.addEventListener('DOMContentLoaded', () => {
    
    // QUICK ADD STATE MACHINE
    const qaCollapsed = document.getElementById('qa-collapsed');
    const qaExpanded = document.getElementById('qa-expanded');
    const qaInput = document.getElementById('qa-input');
    const qaMins = document.getElementById('qa-mins');
    const qaSubmit = document.getElementById('qa-submit');
    const catButtons = document.querySelectorAll('.qa-cat-btn');

    let selectedCategory = 'work'; // Default Category

    if (qaCollapsed && qaExpanded && qaInput) {
        
        // Expand
        qaCollapsed.addEventListener('click', () => {
            qaCollapsed.classList.add('hidden');
            qaExpanded.classList.remove('hidden');
            qaInput.focus();
        });

        // Category Selection
        catButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const target = e.currentTarget;
                selectedCategory = target.getAttribute('data-category');

                // Reset all
                catButtons.forEach(b => {
                    b.className = 'qa-cat-btn px-3 py-1.5 rounded-full text-xs font-medium transition-colors text-[var(--color-text-muted)] hover:bg-gray-50';
                });

                // Set Active
                if (selectedCategory === 'work') target.className = 'qa-cat-btn px-3 py-1.5 rounded-full text-xs font-medium transition-colors bg-blue-50 text-[var(--color-work)]';
                if (selectedCategory === 'life') target.className = 'qa-cat-btn px-3 py-1.5 rounded-full text-xs font-medium transition-colors bg-green-50 text-[var(--color-life)]';
                if (selectedCategory === 'learn') target.className = 'qa-cat-btn px-3 py-1.5 rounded-full text-xs font-medium transition-colors bg-amber-50 text-[var(--color-learn)]';
            });
        });

        // Submit (Mock)
        const submitActivity = () => {
            const title = qaInput.value.trim();
            if (!title) return;

            const payload = {
                title: title,
                category: selectedCategory,
                planned_mins: qaMins.value.trim() ? parseInt(qaMins.value) : null
            };
            
            console.log('--- MOCK: Creating Activity & Daily Plan Activity ---', payload);

            // Reset UI
            qaInput.value = '';
            qaMins.value = '';
            qaCollapsed.classList.remove('hidden');
            qaExpanded.classList.add('hidden');
        };

        qaSubmit.addEventListener('click', submitActivity);
        qaInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') submitActivity();
        });
    }
});
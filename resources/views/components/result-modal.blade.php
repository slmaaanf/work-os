<div id="result-modal-backdrop" class="fixed inset-0 bg-black/20 backdrop-blur-sm z-[60] flex items-center justify-center hidden opacity-0 transition-opacity duration-200 p-4">
    
    <div id="result-modal-content" class="bg-[var(--color-surface)] w-full max-w-md rounded-[var(--radius-lg)] shadow-xl border border-[var(--color-border)] overflow-hidden scale-95 transition-transform duration-200">
        
        <!-- Header -->
        <div class="px-6 py-5 border-b border-[var(--color-border)] bg-gray-50/50 flex justify-between items-center">
            <div>
                <h3 class="font-semibold text-[var(--color-text)]">Session Finished</h3>
                <div class="meta-text mt-0.5">You focused for <span id="result-duration" class="font-medium text-[var(--color-text)]">32 min</span></div>
            </div>
            <div class="text-[24px]">🎉</div>
        </div>

        <div class="p-6">
            <!-- Result Input (Evidence) -->
            <div class="mb-6">
                <label for="session-result" class="block text-sm font-medium text-[var(--color-text)] mb-2">What did you accomplish?</label>
                <textarea id="session-result" rows="3" placeholder="Misal: Selesai membuat outline metodologi..." class="w-full bg-gray-50 border border-[var(--color-border)] rounded-[var(--radius-md)] p-3 text-sm focus:ring-0 focus:border-gray-400 outline-none resize-none placeholder-gray-400"></textarea>
            </div>

            <!-- State Machine Decisions -->
            <div>
                <label class="block text-sm font-medium text-[var(--color-text)] mb-3">Activity Status</label>
                <div class="space-y-2">
                    
                    <!-- Option 1: Continue Later (IN_PROGRESS) -->
                    <label class="flex items-center gap-3 p-3 border border-[var(--color-border)] rounded-[var(--radius-md)] cursor-pointer hover:bg-gray-50 transition-colors">
                        <input type="radio" name="next_state" value="continue_later" checked class="text-[var(--color-text)] focus:ring-0">
                        <div class="flex-1">
                            <div class="text-sm font-medium">Continue Later</div>
                            <div class="meta-text mt-0.5">Tetap di rencana hari ini</div>
                        </div>
                    </label>

                    <!-- Option 2: Done for Today (DONE_TODAY) -->
                    <label class="flex items-center gap-3 p-3 border border-[var(--color-border)] rounded-[var(--radius-md)] cursor-pointer hover:bg-gray-50 transition-colors">
                        <input type="radio" name="next_state" value="done_today" class="text-[var(--color-text)] focus:ring-0">
                        <div class="flex-1">
                            <div class="text-sm font-medium">Done for Today</div>
                            <div class="meta-text mt-0.5">Target tercapai, lanjut besok/lusa</div>
                        </div>
                    </label>

                    <!-- Option 3: Completed (COMPLETED) -->
                    <label class="flex items-center gap-3 p-3 border border-green-200 bg-green-50/50 rounded-[var(--radius-md)] cursor-pointer hover:bg-green-50 transition-colors">
                        <input type="radio" name="next_state" value="completed" class="text-green-600 focus:ring-0">
                        <div class="flex-1">
                            <div class="text-sm font-medium text-green-700">Activity Completed</div>
                            <div class="meta-text mt-0.5 text-green-600/80">Pekerjaan besar rampung 100%</div>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Footer / Action -->
        <div class="px-6 py-4 border-t border-[var(--color-border)] bg-gray-50 flex justify-end">
            <button id="btn-save-result" class="px-6 py-2 bg-[var(--color-text)] text-white text-sm font-medium rounded-full hover:opacity-90 transition-opacity shadow-sm">
                Save Result
            </button>
        </div>

    </div>
</div>
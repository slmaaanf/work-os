document.addEventListener('DOMContentLoaded', () => {
    // --- Elements ---
    const focusOverlay = document.getElementById('focus-overlay');
    const resultBackdrop = document.getElementById('result-modal-backdrop');
    const resultModal = document.getElementById('result-modal-content');
    
    const btnPauseResume = document.getElementById('btn-pause-resume');
    const btnFinish = document.getElementById('btn-finish-session');
    const btnSaveResult = document.getElementById('btn-save-result');
    
    // Timer State (Simulasi Frontend)
    let isPaused = false;
    let elapsedSeconds = 0;
    let timerInterval = null;

    // --- 1. START SESSION (Dipanggil saat tombol Start di Activity Card diklik) ---
    // Nanti ini diikat ke event listener pada class '.start-activity-btn'
    window.startFocusSession = (title, project) => {
        document.getElementById('focus-title').innerText = title;
        document.getElementById('focus-project').innerText = project || 'Personal Activity';
        
        // Tampilkan Focus Overlay
        focusOverlay.classList.remove('hidden');
        // Sedikit delay untuk efek transisi opacity Tailwind
        setTimeout(() => focusOverlay.classList.remove('opacity-0'), 10);
        
        // Reset & Mulai Timer Frontend (Mock)
        elapsedSeconds = 0;
        isPaused = false;
        document.getElementById('pause-icon').innerText = '⏸';
        document.getElementById('pause-text').innerText = 'Pause';
        
        // Simulasi POST /api/focus-sessions (Status: ACTIVE)
        console.log(`[API MOCK] POST /focus-sessions -> ACTIVE untuk '${title}'`);

        timerInterval = setInterval(() => {
            if (!isPaused) {
                elapsedSeconds++;
                // Format MM:SS
                const m = Math.floor(elapsedSeconds / 60).toString().padStart(2, '0');
                const s = (elapsedSeconds % 60).toString().padStart(2, '0');
                document.getElementById('timer-display').innerText = `${m}:${s}`;
            }
        }, 1000);
    };

    // --- 2. PAUSE / RESUME ---
    if (btnPauseResume) {
        btnPauseResume.addEventListener('click', () => {
            isPaused = !isPaused;
            if (isPaused) {
                document.getElementById('pause-icon').innerText = '▶';
                document.getElementById('pause-text').innerText = 'Resume';
                btnPauseResume.classList.replace('bg-gray-50', 'bg-amber-50');
            } else {
                document.getElementById('pause-icon').innerText = '⏸';
                document.getElementById('pause-text').innerText = 'Pause';
                btnPauseResume.classList.replace('bg-amber-50', 'bg-gray-50');
            }
        });
    }

    // --- 3. FINISH SESSION ---
    if (btnFinish) {
        btnFinish.addEventListener('click', () => {
            // Hentikan timer
            clearInterval(timerInterval);
            
            // Hitung menit (dibulatkan ke atas agar 1 detik pun terhitung 1 menit untuk MVP)
            const actualMins = Math.ceil(elapsedSeconds / 60) || 1; 
            document.getElementById('result-duration').innerText = `${actualMins} min`;

            // Tutup Focus Overlay
            focusOverlay.classList.add('opacity-0');
            setTimeout(() => focusOverlay.classList.add('hidden'), 300);

            // Tampilkan Result Modal
            resultBackdrop.classList.remove('hidden');
            setTimeout(() => {
                resultBackdrop.classList.remove('opacity-0');
                resultModal.classList.remove('scale-95');
                document.getElementById('session-result').focus();
            }, 10);
        });
    }

    // --- 4. SAVE RESULT (Matrix Decision) ---
    if (btnSaveResult) {
        btnSaveResult.addEventListener('click', () => {
            const resultText = document.getElementById('session-result').value;
            const nextState = document.querySelector('input[name="next_state"]:checked').value;

            // Simulasi eksekusi State Machine ke Backend
            console.log(`[API MOCK] PATCH /focus-sessions/finish -> Status: COMPLETED, Result: '${resultText}'`);
            
            if (nextState === 'continue_later') {
                console.log(`[API MOCK] DPA Status: Tetap IN_PROGRESS. Activity: Tetap IN_PROGRESS`);
            } else if (nextState === 'done_today') {
                console.log(`[API MOCK] DPA Status: DONE_TODAY. Activity: Tetap IN_PROGRESS`);
            } else if (nextState === 'completed') {
                console.log(`[API MOCK] DPA Status: DONE_TODAY. Activity: COMPLETED`);
            }

            // Tutup Modal & Reset
            resultBackdrop.classList.add('opacity-0');
            resultModal.classList.add('scale-95');
            setTimeout(() => {
                resultBackdrop.classList.add('hidden');
                document.getElementById('session-result').value = '';
                // (Di dunia nyata, di sini kita memanggil fungsi untuk refresh/re-render daftar aktivitas di Today)
            }, 200);
        });
    }
});
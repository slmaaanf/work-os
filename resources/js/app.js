document.addEventListener('DOMContentLoaded', () => {
    
    // ==========================================
    // 1. HEADER & TANGGAL
    // ==========================================
    const dateDisplay = document.getElementById('date-display');
    const planDateLabel = document.getElementById('plan-date-label');
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const today = new Date();
    
    if (dateDisplay) dateDisplay.innerText = today.toLocaleDateString('id-ID', options);
    if (planDateLabel) planDateLabel.innerText = today.toLocaleDateString('en-GB', options);

    const todayStr = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;

    // ==========================================
    // 2. MODE TOGGLE (WORK/PERSONAL)
    // ==========================================
    const modeToggle = document.getElementById('mode-toggle');
    const body = document.body;
    const workOnlyInput = document.getElementById('work-only-input');
    const greeting = document.getElementById('greeting');
    
    if (modeToggle) {
        modeToggle.addEventListener('change', (e) => {
            const isPersonal = e.target.checked;
            body.classList.toggle('theme-personal', isPersonal);
            body.classList.toggle('theme-work', !isPersonal);
            if(greeting) greeting.innerText = isPersonal ? 'Waktunya Me-Time! ✨' : 'Selamat Bekerja! 💼';
            if(workOnlyInput) workOnlyInput.style.display = isPersonal ? 'none' : 'block'; 
            
            // Kosongkan timer saat pindah mode
            if (typeof window.resetTimeboxPlayer === 'function') {
                window.resetTimeboxPlayer();
            }

            loadTasksFromDB(isPersonal ? 'personal' : 'work');
        });
    }

    // ==========================================
    // 3. KANBAN TAB NAVIGATION
    // ==========================================
    const tabQueue = document.getElementById('tab-queue');
    const tabDone = document.getElementById('tab-done');
    const contentQueue = document.getElementById('content-queue');
    const contentDone = document.getElementById('content-done');

    window.switchTab = function(showQueue) {
        if (!tabQueue || !tabDone) return;
        tabQueue.classList.toggle('active', showQueue); tabDone.classList.toggle('active', !showQueue);
        contentQueue.classList.toggle('active', showQueue); contentDone.classList.toggle('active', !showQueue);
    };
    if (tabQueue) tabQueue.addEventListener('click', () => switchTab(true));
    if (tabDone) tabDone.addEventListener('click', () => switchTab(false));

    function formatMins(totalMins) {
        if (!totalMins) return "0m";
        const h = Math.floor(totalMins / 60);
        const m = Math.floor(totalMins % 60);
        if (h > 0 && m > 0) return `${h}h ${m}m`;
        if (h > 0) return `${h}h`;
        return `${m}m`;
    }

    // ==========================================
    // 4. ENGINE RENDERER (Queue, Done, Analytics, Today's Plan)
    // ==========================================
    window.loadTasksFromDB = function(mode) {
        const queueList = document.getElementById('queue-list');
        const doneList = document.getElementById('done-list');
        const todaysPlanList = document.getElementById('todays-plan-list');
        
        if (queueList) queueList.innerHTML = '';
        if (doneList) doneList.innerHTML = '';
        if (todaysPlanList) todaysPlanList.innerHTML = '';

        fetch(`/api/tasks?mode=${mode}`, { headers: { 'Accept': 'application/json' } })
        .then(response => response.json())
        .then(tasks => {
            let hasTodo = false, hasDone = false, hasTodayPlan = false;
            let todayTasks = 0, todayDone = 0, todayRemain = 0, todayFocusMins = 0;
            let monthDone = 0, monthFocusMins = 0, monthTotalEst = 0, monthTotalAct = 0, monthEstTasksCount = 0;
            
            // Variabel Time Analysis
            let totalMonthActualMins = 0;
            const categoryData = {
                'Feature': { name: 'Development', actual: 0, planned: 0, projects: {} },
                'Bug': { name: 'Bug Fix', actual: 0, planned: 0, projects: {} },
                'Meeting': { name: 'Meeting', actual: 0, planned: 0, projects: {} },
                'Learning': { name: 'Learning', actual: 0, planned: 0, projects: {} },
                'Documentation': { name: 'Docs', actual: 0, planned: 0, projects: {} }
            };
            let totalPlannedToday = 0;

            const monthInput = document.getElementById('month-filter');
            const currentMonthStr = monthInput ? monthInput.value : todayStr.substring(0, 7);

            tasks.forEach(task => {
                const tDate = task.task_date; 
                const tMonth = tDate.substring(0, 7); 
                const actMins = task.actual_time || 0;
                const estMins = parseFloat(task.effort_score || 0) * 60;
                
                // Set Badge Color
                let badgeColor = "#3b82f6", dot = '🔵'; 
                if (task.task_type === 'Bug') { badgeColor = "#ef4444"; dot = '🔴'; }
                else if (task.task_type === 'Meeting') { badgeColor = "#f59e0b"; dot = '🟠'; }
                else if (task.task_type === 'Maintenance') { badgeColor = "#8b5cf6"; dot = '🟣'; }
                else if (task.task_type === 'Documentation') { badgeColor = "#10b981"; dot = '🟢'; }
                else if (task.task_type === 'Learning') { badgeColor = "#eab308"; dot = '🟡'; }

                let hierarchyHTML = "";
                if (task.project || task.milestone) {
                    hierarchyHTML = `<div style="font-size: 11px; color: #64748b; margin-bottom: 6px; font-weight: 600;">
                        ${task.project ? `📁 ${task.project}` : ''}
                        ${task.project && task.milestone ? ` <span style="margin: 0 4px; color: #cbd5e1;">/</span> ` : ''}
                        ${task.milestone ? `🚩 ${task.milestone}` : ''}
                    </div>`;
                }

                // -- Render: ANTREAN (Todo) --
                if (task.status === 'todo') {
                    // Kalkulasi Progress untuk Queue
                    let progressPercent = 0;
                    let remainingMins = (estMins > 0) ? (estMins - actMins) : 0;
                    if (remainingMins < 0) remainingMins = 0;

                    if (estMins > 0 && actMins > 0) {
                        progressPercent = Math.min(100, Math.round((actMins / estMins) * 100));
                    }

                    let progressVisual = '░'.repeat(10);
                    if (progressPercent > 0) {
                        const filledBoxes = Math.round(progressPercent / 10);
                        progressVisual = '█'.repeat(filledBoxes) + '░'.repeat(10 - filledBoxes);
                    }

                    const taskHTML = `
                        <li class="timeline-item" id="task-${task.id}" style="display: flex; justify-content: space-between; align-items: flex-start;">
                            <div class="timeline-info" style="flex-grow: 1; padding-right: 15px;">
                                ${hierarchyHTML}
                                <span class="timeline-title">
                                    <span style="font-size: 10px; background: ${badgeColor}; color: white; padding: 2px 6px; border-radius: 4px; margin-right: 5px;">${task.task_type || 'Feature'}</span>
                                    ${task.title}
                                </span>
                                
                                <div style="margin-top: 8px; font-size: 12px; color: #64748b; background: #f8fafc; padding: 10px; border-radius: 6px; border: 1px solid #f1f5f9;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                                        <span>Estimated</span> <strong>${formatMins(estMins)}</strong>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                                        <span>Total Actual</span> <strong>${formatMins(actMins)}</strong>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                        <span>Remaining</span> <strong>${formatMins(remainingMins)}</strong>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; align-items: center; color: #3b82f6; font-family: monospace;">
                                        <span>${progressVisual}</span> <span>${progressPercent}%</span>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="task-actions" style="flex-shrink: 0; display: flex; gap: 5px;">
                                <button type="button" class="btn-play-task" onclick="loadTaskToWorkLog('${task.title}', '${task.id}')" title="Mulai Kerjakan">▶️</button>
                                <button type="button" class="btn-delete-task" onclick="deleteTask('${task.id}')" title="Hapus Tugas">🗑️</button>
                            </div>
                        </li>`;
                    queueList.insertAdjacentHTML('beforeend', taskHTML);
                    hasTodo = true;

                    // -- Render: TODAY'S PLAN --
                    if (tDate === todayStr) {
                        const plannedMinsForToday = 15; // Placeholder statis, bisa didinamiskan nanti
                        totalPlannedToday += plannedMinsForToday;
                        
                        const planHTML = `
                        <li style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed #e2e8f0; padding-bottom: 12px; margin-bottom: 12px;">
                            <div style="display: flex; gap: 12px; align-items: flex-start;">
                                <span style="font-size: 14px; margin-top: 2px;">${dot}</span>
                                <div>
                                    <strong style="color: #334155; display: block; font-size: 14px; margin-bottom: 4px;">${task.title}</strong>
                                    <span style="color: #94a3b8; font-size: 12px; background: #f1f5f9; padding: 2px 8px; border-radius: 10px;">${plannedMinsForToday} min</span>
                                </div>
                            </div>
                            <button onclick="loadTaskToWorkLog('${task.title}', '${task.id}')" style="background: transparent; color: #3b82f6; border: 1px solid #bfdbfe; padding: 6px 12px; border-radius: 6px; font-size: 12px; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='#eff6ff'" onmouseout="this.style.background='transparent'">
                                ▶ Start
                            </button>
                        </li>`;
                        todaysPlanList.insertAdjacentHTML('beforeend', planHTML);
                        hasTodayPlan = true;
                    }

                // -- Render: REKAP SELESAI --
                } else if (task.status === 'done') {
                    const doneHTML = `
                        <li class="timeline-item done-item" id="task-${task.id}" style="position: relative; display: block;">
                            <div class="timeline-info" style="padding-right: 35px;">
                                ${hierarchyHTML}
                                <span class="timeline-title">
                                    <span style="font-size: 10px; background: ${badgeColor}; color: white; padding: 2px 6px; border-radius: 4px; margin-right: 5px;">${task.task_type || 'Feature'}</span>
                                    ${task.title} ✅
                                </span>
                                <div style="background: #fff; padding: 15px; border-radius: 8px; border: 1px solid #eee; margin-top: 10px; font-size: 13px; color: #333; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);">
                                    <div style="display: flex; justify-content: space-between;"><span>Estimated</span> <span>${formatMins(estMins)}</span></div>
                                    <div style="display: flex; justify-content: space-between;"><span>Total Actual</span> <span style="color:#10b981; font-weight:bold;">${formatMins(actMins)}</span></div>
                                </div>
                            </div>
                            <div class="task-actions" style="position: absolute; top: 12px; right: 12px;">
                                <button type="button" class="btn-delete-task" onclick="deleteTask('${task.id}')" title="Hapus Tugas">🗑️</button>
                            </div>
                        </li>`;
                    doneList.insertAdjacentHTML('beforeend', doneHTML);
                }

                // -- Kalkulasi Metrik HARI INI & BULAN INI --
                if (tDate === todayStr) {
                    todayTasks++;
                    if (task.status === 'done') { todayDone++; todayFocusMins += actMins; } 
                    else { todayRemain++; }
                }

                if (tMonth === currentMonthStr) {
                    if (task.status === 'done') {
                        monthDone++; monthFocusMins += actMins;
                        if (estMins > 0) { monthTotalEst += estMins; monthTotalAct += actMins; monthEstTasksCount++; }
                    }
                    
                    // Kumpulkan data untuk Time Analysis
                    const catKey = task.task_type || 'Feature';
                    const prjName = task.project || 'Other';
                    
                    if (categoryData[catKey]) {
                        categoryData[catKey].actual += actMins;
                        categoryData[catKey].planned += estMins;
                        if (actMins > 0) {
                            if (!categoryData[catKey].projects[prjName]) categoryData[catKey].projects[prjName] = 0;
                            categoryData[catKey].projects[prjName] += actMins;
                        }
                    }
                    totalMonthActualMins += actMins;
                }
            });

            // Update UI State
            if (!hasTodo) queueList.innerHTML = '<li class="empty-state">Antrean kosong.</li>';
            if (!document.querySelector('#done-list li:not(.empty-state)')) doneList.innerHTML = '<li class="empty-state">Belum ada tugas selesai.</li>';
            if (!hasTodayPlan) todaysPlanList.innerHTML = '<li style="font-size: 13px; color: #888; text-align: center;">Tidak ada plan tersisa untuk hari ini. Waktunya bersantai! 🎉</li>';

            // Tembak Angka Analitik Utama
            if(document.getElementById('stat-today-tasks')) document.getElementById('stat-today-tasks').innerText = todayTasks;
            if(document.getElementById('stat-today-focus')) document.getElementById('stat-today-focus').innerText = formatMins(todayFocusMins);
            if(document.getElementById('stat-today-done')) document.getElementById('stat-today-done').innerText = todayDone;
            if(document.getElementById('stat-today-remain')) document.getElementById('stat-today-remain').innerText = todayRemain;

            if(document.getElementById('stat-month-done')) document.getElementById('stat-month-done').innerText = monthDone;
            if(document.getElementById('stat-month-focus')) document.getElementById('stat-month-focus').innerText = formatMins(monthFocusMins);

            // Transparansi Akurasi
            const elAccVal = document.getElementById('stat-month-accuracy');
            const elAccCtx = document.getElementById('stat-month-accuracy-context');
            if (monthEstTasksCount < 3) {
                if(elAccVal) { elAccVal.innerText = '—'; elAccVal.style.color = '#94a3b8'; }
                if(elAccCtx) elAccCtx.innerHTML = `Based on ${monthEstTasksCount} tasks<br><br>Complete at least 3<br>estimated tasks`;
            } else {
                let monthAccuracy = 100;
                if (monthTotalEst > 0 && monthTotalAct > 0) monthAccuracy = Math.round((Math.min(monthTotalEst, monthTotalAct) / Math.max(monthTotalEst, monthTotalAct)) * 100);
                if(elAccVal) { elAccVal.innerText = monthAccuracy + '%'; elAccVal.style.color = '#3b82f6'; }
                if(elAccCtx) {
                    const filled = Math.round(monthAccuracy / 10);
                    const barVisual = '█'.repeat(filled) + '░'.repeat(10 - filled);
                    elAccCtx.innerHTML = `<span style="font-size: 8px; color: #cbd5e1;">${barVisual}</span><br>Based on ${monthEstTasksCount} tasks`;
                }
            }

            // Update Today's Progress Bar
            if(document.getElementById('actual-focus-text')) document.getElementById('actual-focus-text').innerText = formatMins(todayFocusMins);
            if(document.getElementById('planned-focus-text')) document.getElementById('planned-focus-text').innerText = formatMins(totalPlannedToday);
            if(document.getElementById('task-completion-text')) document.getElementById('task-completion-text').innerText = `${todayDone} / ${todayTasks} Tasks`;
            
            const progressBar = document.getElementById('focus-progress-bar');
            if (progressBar && totalPlannedToday > 0) {
                const pct = Math.min(100, (todayFocusMins / totalPlannedToday) * 100);
                progressBar.style.width = pct + '%';
            }

            // ==========================================
            // LOGIKA RENDER TIME ANALYSIS
            // ==========================================
            const renderTimeAnalysis = (categoryKey) => {
                const data = categoryData[categoryKey];
                const taDetails = document.getElementById('ta-details');
                if(!taDetails || !data) return;

                if (data.planned === 0 && data.actual === 0) {
                    taDetails.innerHTML = `<div style="text-align: center; color: #94a3b8; padding: 20px;">Belum ada alokasi waktu untuk ${data.name} bulan ini.</div>`;
                    return;
                }

                // Kalkulasi Persentase & Variance
                const pct = totalMonthActualMins > 0 ? Math.round((data.actual / totalMonthActualMins) * 100) : 0;
                const varianceMins = data.actual - data.planned;
                
                let varianceStr = "0m";
                let varianceColor = "#64748b";
                if (varianceMins > 0) { varianceStr = "+" + formatMins(varianceMins); varianceColor = "#ef4444"; } 
                else if (varianceMins < 0) { varianceStr = "-" + formatMins(Math.abs(varianceMins)); varianceColor = "#10b981"; } 

                // Susun Tabel Project
                let projectsHTML = '';
                const sortedProjects = Object.entries(data.projects).sort((a,b) => b[1] - a[1]);
                sortedProjects.forEach(([pName, pMins]) => {
                    projectsHTML += `<tr><td>${pName}</td><td>${formatMins(pMins)}</td></tr>`;
                });
                if(projectsHTML === '') projectsHTML = `<tr><td colspan="2" style="text-align:center; color:#94a3b8;">Belum ada waktu aktual yang tercatat</td></tr>`;

                // Suntik ke HTML
                taDetails.innerHTML = `
                    <div class="ta-header">
                        <div class="ta-header-title">${data.name}</div>
                        <div class="ta-header-meta">${formatMins(data.actual)}</div>
                        <div class="ta-header-sub">${pct}% of total focus time</div>
                    </div>
                    
                    <div class="ta-divider"></div>
                    
                    <table class="ta-table">
                        <thead><tr><th>Project / Task</th><th>Time</th></tr></thead>
                        <tbody>${projectsHTML}</tbody>
                    </table>
                    
                    <div class="ta-divider"></div>
                    
                    <div class="ta-summary-row"><span>Planned</span> <span>${formatMins(data.planned)}</span></div>
                    <div class="ta-summary-row"><span>Actual</span> <span>${formatMins(data.actual)}</span></div>
                    <div class="ta-summary-row"><span>Variance</span> <span style="color: ${varianceColor};">${varianceStr}</span></div>
                `;
            };

            // Pasang event listener pada Tabs Time Analysis
            const taTabs = document.querySelectorAll('.ta-tab');
            taTabs.forEach(tab => {
                const newTab = tab.cloneNode(true);
                tab.parentNode.replaceChild(newTab, tab);
                
                newTab.addEventListener('click', (e) => {
                    document.querySelectorAll('.ta-tab').forEach(t => t.classList.remove('active'));
                    e.target.classList.add('active');
                    renderTimeAnalysis(e.target.getAttribute('data-type'));
                });
            });

            // Trigger klik untuk Tab yang sedang aktif
            const activeTab = document.querySelector('.ta-tab.active');
            if(activeTab) renderTimeAnalysis(activeTab.getAttribute('data-type'));
        })
        .catch(err => console.error(err));
    };

    loadTasksFromDB('work');

    // ==========================================
    // 5. ENGINE TIMEBOX PLAYER (Jantung Aplikasi)
    // ==========================================
    let timerInterval = null;
    let currentSeconds = 0;
    let plannedMinutes = 15; 
    let currentTaskId = "";
    let currentTaskName = "";
    let isPlaying = false;

    const lblActiveTask = document.getElementById('active-task-label');
    const lblPlanned = document.getElementById('planned-time-label');
    const displayTimer = document.getElementById('timer-display');
    const btnStart = document.getElementById('start-timer');
    const btnPause = document.getElementById('pause-timer');
    const btnFinish = document.getElementById('finish-timer');

    // Fungsi Reset Timer (Dipanggil saat switch mode atau simpan session)
    window.resetTimeboxPlayer = function() {
        clearInterval(timerInterval);
        isPlaying = false;
        currentSeconds = 0;
        currentTaskId = "";
        currentTaskName = "";
        
        const focusStateTitle = document.getElementById('focus-state-title');
        if (focusStateTitle) focusStateTitle.innerText = 'NEXT FOCUS';

        if(lblActiveTask) lblActiveTask.innerText = "No task selected";
        if(lblPlanned) lblPlanned.innerText = "Pilih task dari antrean";
        if(displayTimer) displayTimer.innerText = "00:00";
        
        if(btnStart) {
            btnStart.style.display = 'block'; 
            btnStart.disabled = true; 
            btnStart.innerText = '▶ Start';
        }
        if(btnPause) btnPause.style.display = 'none';
        if(btnFinish) {
            btnFinish.disabled = true; 
            btnFinish.style.opacity = '0.5';
        }
    };

    function updateTimerDisplay() {
        const m = String(Math.floor(currentSeconds / 60)).padStart(2, '0');
        const s = String(currentSeconds % 60).padStart(2, '0');
        displayTimer.innerText = `${m}:${s}`;
    }

    window.loadTaskToWorkLog = function(title, taskId) {
        // Reset Player State
        clearInterval(timerInterval);
        isPlaying = false;
        currentSeconds = 0;
        currentTaskId = taskId;
        currentTaskName = title;
        plannedMinutes = parseInt(document.getElementById('input-planned-mins').value) || 15;

        // UI Update
        const focusStateTitle = document.getElementById('focus-state-title');
        if (focusStateTitle) focusStateTitle.innerText = 'CURRENT FOCUS';

        lblActiveTask.innerText = title;
        lblPlanned.innerText = `Alokasi Hari Ini: ${plannedMinutes} min`;
        updateTimerDisplay();
        
        btnStart.disabled = false; btnStart.style.display = 'block';
        btnPause.style.display = 'none';
        btnFinish.disabled = false; btnFinish.style.opacity = '1';
        
        // Scroll layar ke Player
        document.querySelector('.timer-box').scrollIntoView({behavior: 'smooth', block: 'center'});
    };

    btnStart.addEventListener('click', () => {
        if(!currentTaskId) return;
        isPlaying = true;
        btnStart.style.display = 'none';
        btnPause.style.display = 'block';
        
        timerInterval = setInterval(() => {
            currentSeconds++;
            updateTimerDisplay();

            // CEK TIMEBOX COMPLETE
            if (currentSeconds === plannedMinutes * 60) {
                clearInterval(timerInterval);
                isPlaying = false;
                showTimeboxCompleteAlert();
            }
        }, 1000); 
    });

    btnPause.addEventListener('click', () => {
        isPlaying = false;
        clearInterval(timerInterval);
        btnPause.style.display = 'none';
        btnStart.style.display = 'block';
        btnStart.innerText = '▶ Resume';
    });

    function showTimeboxCompleteAlert() {
        Swal.fire({
            title: '🎉 Timebox Complete',
            html: `
                <div style="font-size: 16px; font-weight: bold; color: #334155; margin-bottom: 15px;">${currentTaskName}</div>
                <div style="display: flex; justify-content: space-around; background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <div><div style="font-size:11px; color:#64748b; text-transform:uppercase;">Planned</div><strong style="color:#3b82f6; font-size:18px;">${plannedMinutes}m</strong></div>
                    <div><div style="font-size:11px; color:#64748b; text-transform:uppercase;">Actual</div><strong style="color:#10b981; font-size:18px;">${Math.round(currentSeconds/60)}m</strong></div>
                </div>
                <p style="font-size:12px; color:#64748b; margin-top:15px;">Apa yang ingin kamu lakukan selanjutnya?</p>
            `,
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: '✅ Complete Task',
            denyButtonText: '⏱️ Continue Session',
            cancelButtonText: '⏸️ Finish Session',
            confirmButtonColor: '#10b981',
            denyButtonColor: '#3b82f6',
            cancelButtonColor: '#64748b'
        }).then((result) => {
            if (result.isConfirmed) {
                saveSessionData(true); 
            } else if (result.isDenied) {
                btnStart.click(); 
            } else {
                saveSessionData(false); 
            }
        });
    }

    // Aksi Default "Finish Session" HANYA menyimpan waktu (tidak done)
    btnFinish.addEventListener('click', () => saveSessionData(false)); 

    function saveSessionData(markAsDone) {
        if(!currentTaskId) return;
        clearInterval(timerInterval);
        
        const actualMinsToSave = Math.round(currentSeconds / 60);

        fetch(`/api/tasks/${currentTaskId}/done`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ 
                actual_time: actualMinsToSave,
                status: markAsDone ? 'done' : 'todo'
            })
        }).then(() => {
            Swal.fire({ title: 'Session Disimpan!', icon: 'success', timer: 1200, showConfirmButton: false });
            
            // Reset Player & reload
            window.resetTimeboxPlayer();
            loadTasksFromDB(document.body.classList.contains('theme-personal') ? 'personal' : 'work');
        });
    }

    // ==========================================
    // 6. TOGGLE FORM (Bug Tracker dll)
    // ==========================================
    const taskTypeSelect = document.getElementById('input-task-type');
    const bugTrackerArea = document.getElementById('bug-tracker-area');
    if (taskTypeSelect && bugTrackerArea) {
        taskTypeSelect.addEventListener('change', (e) => {
            bugTrackerArea.style.display = e.target.value === 'Bug' ? 'block' : 'none';
        });
    }

    // ==========================================
    // 7. SUBMIT FORM (Simpan Data Baru)
    // ==========================================
    const activityForm = document.getElementById('activity-form');
    if (activityForm) {
        activityForm.addEventListener('submit', (e) => {
            e.preventDefault(); 
            const currentMode = document.body.classList.contains('theme-personal') ? 'personal' : 'work';
            const isDone = document.getElementById('input-is-done') ? document.getElementById('input-is-done').checked : false;

            const payload = {
                title: document.getElementById('input-task-name').value,
                deadline: document.getElementById('input-deadline').value,
                duration: document.getElementById('input-duration').value || "0",
                mode: currentMode,
                is_done: isDone,
                project: document.getElementById('input-project') ? document.getElementById('input-project').value : '',
                milestone: document.getElementById('input-milestone') ? document.getElementById('input-milestone').value : '',
                task_type: document.getElementById('input-task-type') ? document.getElementById('input-task-type').value : 'Feature',
                
                stakeholder: document.getElementById('input-stakeholder') ? document.getElementById('input-stakeholder').value : '',
                daily_win: document.getElementById('input-daily-win') ? document.getElementById('input-daily-win').value : '',
                severity: document.getElementById('input-severity') ? document.getElementById('input-severity').value : ''
            };

            fetch('/api/tasks', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify(payload)
            }).then(() => {
                loadTasksFromDB(currentMode); 
                switchTab(!isDone);
                activityForm.reset();
            });
        });
    }

    // ==========================================
    // 8. HAPUS TASK
    // ==========================================
    window.deleteTask = function(taskId) {
        Swal.fire({
            title: 'Hapus Tugas?', text: "Yakin ingin menghapus ini?", icon: 'warning',
            showCancelButton: true, confirmButtonColor: '#e63946', confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/api/tasks/${taskId}`, { method: 'DELETE', headers: { 'Accept': 'application/json' } })
                .then(() => loadTasksFromDB(document.body.classList.contains('theme-personal') ? 'personal' : 'work'));
            }
        });
    };
});
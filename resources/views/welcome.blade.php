<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Life & Work Management System</title>
    
    <!-- Wajib ada agar JS tidak crash (Hanya perlu 1x panggil) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="theme-work">
    
    <!-- HEADER -->
    <header class="topbar">
        <div>
            <h1 id="greeting">Selamat Bekerja! 💼</h1>
            <p id="date-display"></p>
        </div>
        
        <!-- Toggle Switch Mode -->
        <div class="toggle-container">
            <span class="mode-label">Work Mode</span>
            <label class="switch">
                <input type="checkbox" id="mode-toggle">
                <span class="slider round"></span>
            </label>
            <span class="mode-label">Personal Mode</span>
        </div>
    </header>

    <main class="split-layout">
        
        <!-- ========================================== -->
        <!-- KOLOM KIRI: INPUT FORM                     -->
        <!-- ========================================== -->
        <section class="card input-section">
            <h2 class="section-title">Input Kegiatan</h2>
            <form id="activity-form">
                <div class="form-group row-wins" style="grid-template-columns: 1fr 1fr; margin-bottom: 15px;">
                    <div>
                        <label>Project 📁</label>
                        <input type="text" id="input-project" placeholder="Misal: Inventory System" class="form-control" list="project-list">
                        <datalist id="project-list">
                            <option value="Inventory Management System">
                            <option value="Personal Portfolio">
                        </datalist>
                    </div>
                    <div>
                        <label>Milestone 🚩</label>
                        <input type="text" id="input-milestone" placeholder="Misal: Authentication" class="form-control" list="milestone-list">
                        <datalist id="milestone-list">
                            <option value="Authentication">
                            <option value="Stock API">
                            <option value="Reporting">
                        </datalist>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Nama Task / Kegiatan</label>
                    <input type="text" id="input-task-name" placeholder="Misal: Implement Product API" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Task Type 📌</label>
                    <select class="form-control" id="input-task-type">
                        <option value="Feature">Feature</option>
                        <option value="Bug">Bug</option>
                        <option value="Maintenance">Maintenance</option>
                        <option value="Research">Research</option>
                        <option value="Meeting">Meeting</option>
                        <option value="Documentation">Documentation</option>
                        <option value="Learning">Learning</option>
                    </select>
                </div>

                <!-- AREA BUG TRACKER (Tersembunyi secara default) -->
                <div id="bug-tracker-area" style="display: none; background: #fff5f5; border: 1px solid #fecaca; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                    <div style="font-weight: 700; color: #dc2626; margin-bottom: 10px; display: flex; align-items: center; gap: 5px;">
                        🐛 Bug Report Details
                    </div>
                    
                    <div class="form-group row-wins" style="grid-template-columns: 1fr 1fr;">
                        <div>
                            <label>Severity</label>
                            <select class="form-control" id="input-severity">
                                <option>🔴 High</option>
                                <option>🟠 Medium</option>
                                <option>🟡 Low</option>
                            </select>
                        </div>
                        <div>
                            <label>App Environment</label>
                            <select class="form-control" id="input-bug-env">
                                <option>Development</option>
                                <option>Staging</option>
                                <option>Production</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Steps to Reproduce</label>
                        <textarea id="input-steps" rows="2" class="form-control" placeholder="1. Buat transaksi... 2. Submit..."></textarea>
                    </div>
                    
                    <div class="form-group row-wins" style="grid-template-columns: 1fr 1fr;">
                        <div>
                            <label>Expected Result</label>
                            <textarea id="input-expected" rows="2" class="form-control" placeholder="Misal: Stok berkurang"></textarea>
                        </div>
                        <div>
                            <label>Actual Result</label>
                            <textarea id="input-actual" rows="2" class="form-control" placeholder="Misal: Stok tetap"></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Root Cause</label>
                        <textarea id="input-root-cause" rows="2" class="form-control" placeholder="Kenapa ini terjadi?"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Solution</label>
                        <textarea id="input-solution" rows="2" class="form-control" placeholder="Bagaimana kode diperbaiki?"></textarea>
                    </div>
                </div>
                
                <!-- Dibagi 4 Kolom untuk Planning Timeboxing -->
                <div class="form-group row-wins" style="grid-template-columns: 1fr 1fr 1fr 1fr;">
                    <div>
                        <label>Deadline 📅</label>
                        <input type="date" id="input-deadline" class="form-control" required>
                    </div>
                    <div>
                        <label>Mulai ⏰</label>
                        <input type="time" id="input-start-time" class="form-control" required>
                    </div>
                    <div>
                        <label>Est. Total (Jam) ⏳</label>
                        <input type="number" step="0.1" id="input-duration" class="form-control" placeholder="Misal: 4">
                    </div>
                    <div>
                        <label>Alokasi Hari Ini (Menit) 🎯</label>
                        <input type="number" id="input-planned-mins" class="form-control" placeholder="15" value="15">
                    </div>
                </div>

                <!-- CURRENT FOCUS / TIMEBOX PLAYER (Menggantikan Timer Lama) -->
                <div class="form-group timer-box" style="background: #2f3e56; color: #fff; border: none; padding: 20px; border-radius: 12px; margin-bottom: 20px; transition: 0.3s;">
                    <div id="focus-state-title" style="font-size: 11px; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px;">
                        NEXT FOCUS
                    </div>
                    <div id="active-task-label" style="font-size: 18px; font-weight: 700; color: #f8fafc; margin-bottom: 5px;">
                        No task selected
                    </div>
                    <div id="planned-time-label" style="font-size: 13px; color: #cbd5e1; margin-bottom: 15px;">
                        Pilih task dari Today's Plan
                    </div>
                    
                    <div class="timer-display" id="timer-display" style="font-size: 32px; font-weight: bold; margin-bottom: 15px; color: #38bdf8;">
                        00:00
                    </div>
                    
                    <div class="timer-controls" style="display: flex; gap: 10px;">
                        <button type="button" class="btn-submit" id="start-timer" disabled style="background: #3b82f6; flex: 1; border: none; color: white;">▶ Start</button>
                        <button type="button" class="btn-submit" id="pause-timer" style="background: #475569; flex: 1; display: none; border: none; color: white;">⏸ Pause</button>
                        <button type="button" class="btn-submit" id="finish-timer" disabled style="background: #10b981; flex: 1; border: none; color: white;">✓ Finish Session</button>
                    </div>
                </div>

                <div class="form-group" id="work-only-input">
                    <label>Stakeholder / Blocker 🤝</label>
                    <input type="text" id="input-stakeholder" placeholder="Misal: Backend Team (Nunggu API)" class="form-control">
                </div>

                <div class="form-group">
                    <label>Caffeine & Environment ☕</label>
                    <select class="form-control" id="input-environment">
                        <option>Office</option>
                        <option>Cafe / WFC</option>
                        <option>Rumah</option>
                    </select>
                </div>
                
                <div class="form-group row-wins">
                    <div class="win-box">
                        <label>🏆 Daily Win</label>
                        <textarea id="input-daily-win" rows="2" class="form-control" placeholder="Misal: API selesai + unit testing..."></textarea>
                    </div>
                    <div class="oops-box">
                        <label>⚠️ Oops Moment</label>
                        <textarea id="input-oops-moment" rows="2" class="form-control" placeholder="Misal: Salah mapping relationship warehouse..."></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label>💡 Lesson Learned / What I Learned</label>
                    <textarea id="input-lesson-learned" rows="2" class="form-control" placeholder="Misal: Laravel Eloquent relationship punya best practice..."></textarea>
                </div>

                <div class="form-group" style="display: flex; align-items: center; gap: 8px; margin-bottom: 15px;">
                    <input type="checkbox" id="input-is-done" style="width: 16px; height: 16px; cursor: pointer;">
                    <label for="input-is-done" style="margin: 0; cursor: pointer; font-size: 14px; color: #666;">
                        Langsung tandai Selesai (Tugas masa lalu)
                    </label>
                </div>

                <button type="submit" class="btn-submit">Simpan Task</button>
            </form>
        </section>

        <!-- ========================================== -->
        <!-- KOLOM KANAN: REKAP & KANBAN BOARD          -->
        <!-- ========================================== -->
        <section class="overview-section">
            
            <!-- DEVELOPER PRODUCTIVITY ANALYTICS -->
            <div class="card analytics-dashboard">
                <div class="dashboard-header">
                    <h3>Developer Productivity</h3>
                    <input type="month" id="month-filter" class="month-selector" value="2026-08">
                </div>

                <!-- Grid Metrik Dipecah: TODAY & THIS MONTH -->
                <div class="analytics-split">
                    <!-- SEGMEN HARI INI -->
                    <div class="analytics-section">
                        <h4 class="section-subtitle">☀️ Today</h4>
                        <div class="metrics-grid today-grid">
                            <div class="metric-box">
                                <span class="metric-title">Tasks</span>
                                <strong class="metric-value" id="stat-today-tasks">0</strong>
                            </div>
                            <div class="metric-box">
                                <span class="metric-title">Focus Time</span>
                                <strong class="metric-value" id="stat-today-focus">0h 0m</strong>
                            </div>
                            <div class="metric-box">
                                <span class="metric-title">Completed</span>
                                <strong class="metric-value" id="stat-today-done" style="color: #10b981;">0</strong>
                            </div>
                            <div class="metric-box">
                                <span class="metric-title">Remaining</span>
                                <strong class="metric-value" id="stat-today-remain" style="color: #f59e0b;">0</strong>
                            </div>
                        </div>
                    </div>

                    <!-- SEGMEN BULAN INI -->
                    <div class="analytics-section">
                        <h4 class="section-subtitle">📅 This Month</h4>
                        <div class="metrics-grid month-grid">
                            <div class="metric-box">
                                <span class="metric-title">Completed</span>
                                <strong class="metric-value" id="stat-month-done">0</strong>
                            </div>
                            <div class="metric-box">
                                <span class="metric-title">Focus Time</span>
                                <strong class="metric-value" id="stat-month-focus">0h 0m</strong>
                            </div>
                            <div class="metric-box" style="display: flex; flex-direction: column; justify-content: center;">
                                <span class="metric-title">Accuracy</span>
                                <strong class="metric-value" id="stat-month-accuracy" style="color: #3b82f6;">—</strong>
                                <div id="stat-month-accuracy-context" style="font-size: 10px; color: #94a3b8; margin-top: 6px; line-height: 1.3;">
                                    Memuat data...
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- End of Analytics Dashboard -->

            <!-- TODAY'S PLAN DASHBOARD -->
            <div class="card focus-card" style="margin-top: 30px; border-top: 4px solid #3b82f6;">
                <div class="focus-header" style="border-bottom: none; margin-bottom: 10px;">
                    <div>
                        <h3 style="font-size: 18px;">Today's Plan</h3>
                        <span style="font-size: 12px; color: #64748b;" id="plan-date-label">Memuat tanggal...</span>
                    </div>
                </div>
                
                <!-- Focus Time Progress Bar -->
                <div style="margin-bottom: 25px;">
                    <div style="display: flex; justify-content: space-between; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 8px;">
                        <span>Focus Time</span>
                        <span><span id="actual-focus-text">0m</span> / <span id="planned-focus-text">0m</span></span>
                    </div>
                    <div style="background: #e2e8f0; height: 8px; border-radius: 4px; overflow: hidden;">
                        <div id="focus-progress-bar" style="width: 0%; height: 100%; background: #3b82f6; transition: 0.3s;"></div>
                    </div>
                    <div style="font-size: 11px; color: #94a3b8; margin-top: 5px; text-align: right;" id="task-completion-text">
                        0 / 0 Tasks
                    </div>
                </div>

                <div style="border-bottom: 1px dashed #cbd5e1; margin-bottom: 15px;"></div>

                <!-- Daily Plan List -->
                <ul class="focus-list" id="todays-plan-list">
                    <li style="font-size: 13px; color: #888; text-align: center;">Memuat Timebox hari ini...</li>
                </ul>
            </div>

            <!-- TIME ANALYSIS (Pengganti Work Distribution) -->
            <div class="card" style="margin-top: 30px;">
                <div class="work-distribution">
                    <h4 class="distribution-title" style="margin-bottom: 15px;">Time Analysis</h4>
                    
                    <!-- Kategori Navigasi (Pills) -->
                    <div class="time-analysis-tabs">
                        <button class="ta-tab active" data-type="Feature">Development</button>
                        <button class="ta-tab" data-type="Bug">Bug Fix</button>
                        <button class="ta-tab" data-type="Meeting">Meeting</button>
                        <button class="ta-tab" data-type="Learning">Learning</button>
                        <button class="ta-tab" data-type="Documentation">Docs</button>
                    </div>

                    <!-- Panel Detail Dinamis -->
                    <div class="time-analysis-details" id="ta-details">
                        <!-- Konten akan dirender oleh JavaScript -->
                        <div style="text-align: center; color: #94a3b8; padding: 20px;">
                            Memuat data analisis...
                        </div>
                    </div>
                </div>
            </div>

            <!-- ACTION BOARD / KANBAN -->
            <div class="card action-board-card" style="margin-top: 30px;">
                <div class="board-tabs">
                    <button class="tab-btn active" id="tab-queue">📝 Antrean</button>
                    <button class="tab-btn" id="tab-done">✅ Rekap Selesai</button>
                </div>
                
                <div class="tab-content active" id="content-queue">
                    <ul class="timeline-list" id="queue-list">
                        <li class="empty-state">Belum ada tugas di antrean. Mulai rencanakan di samping!</li>
                    </ul>
                </div>
                
                <div class="tab-content" id="content-done">
                    <ul class="timeline-list" id="done-list">
                        <li class="empty-state">Belum ada tugas yang selesai. Semangat!</li>
                    </ul>
                </div>
            </div>

        </section>
    </main>
</body>
</html>
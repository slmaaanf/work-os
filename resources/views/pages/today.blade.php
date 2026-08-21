<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Life & Work OS</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #FAFAF9; }
        .color-transition { transition: all 0.4s ease; }
    </style>
</head>
<body class="text-gray-800 antialiased min-h-screen p-4 md:p-8">

<div class="max-w-[1400px] mx-auto">
    <!-- HEADER -->
    <div>
        <div class="flex items-center gap-3">
            <!-- Judul berubah dinamis -->
            <h1 class="text-3xl font-bold text-gray-800 tracking-tight">
                {{ (isset($isToday) && $isToday) ? "Today's Plan" : "Daily Plan" }}
            </h1>
            
            <!-- INPUT DATE PICKER AJAIB -->
            <input type="date" 
                   id="date-navigator"
                   value="{{ isset($activeDate) ? $activeDate->format('Y-m-d') : \Carbon\Carbon::today()->format('Y-m-d') }}" 
                   onchange="window.location.href='/?date='+this.value"
                   class="bg-gray-100 border-none text-gray-600 text-sm font-semibold rounded-lg p-2 cursor-pointer outline-none focus:ring-2 focus:ring-sky-300 hover:bg-gray-200 transition"
                   title="Pilih tanggal untuk melihat atau merekap masa lalu">
        </div>
        <p class="text-gray-500 mt-1 mb-4">
            {{ isset($activeDate) ? $activeDate->format('l, d F Y') : \Carbon\Carbon::today()->format('l, d F Y') }}
            @if(isset($isToday) && !$isToday) 
                <span class="ml-2 text-[10px] font-bold bg-amber-100 text-amber-600 px-2 py-0.5 rounded-full uppercase tracking-wider">Time Travel Mode</span> 
            @endif
        </p>
        
        <div class="flex gap-2">
            <a href="/goals" class="bg-white border border-gray-200 text-gray-600 hover:text-gray-900 hover:bg-gray-50 font-medium py-2 px-4 rounded-xl text-sm transition flex items-center gap-2 shadow-sm">
                🎯 Goals & Habits
            </a>
            <a href="/recap" class="bg-white border border-gray-200 text-gray-600 hover:text-gray-900 hover:bg-gray-50 font-medium py-2 px-4 rounded-xl text-sm transition flex items-center gap-2 shadow-sm">
                📊 Weekly & Monthly Recap
            </a>
        </div>
    </div>

    <!-- MAIN SPLIT LAYOUT -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start mt-8">
        
        <!-- KOLOM KIRI -->
        <div class="lg:col-span-5 space-y-6">
            
            <!-- TABS -->
            <div class="flex gap-2 border-b border-gray-200" id="mode-tabs">
                <button onclick="switchMode('cimory')" id="tab-cimory" class="mode-tab px-4 py-3 text-sm font-bold border-b-2 border-sky-400 text-sky-500 color-transition flex items-center gap-2">🏢 Cimory</button>
                <button onclick="switchMode('work')" id="tab-work" class="mode-tab px-4 py-3 text-sm font-semibold border-b-2 border-transparent text-gray-400 hover:text-gray-600 color-transition flex items-center gap-2">💻 Work</button>
                <button onclick="switchMode('personal')" id="tab-personal" class="mode-tab px-4 py-3 text-sm font-semibold border-b-2 border-transparent text-gray-400 hover:text-gray-600 color-transition flex items-center gap-2">🌱 Personal</button>
            </div>

            <!-- ADD FORM -->
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm color-transition" id="form-container">
                
                <!-- Tarik Data Goals & Projects Langsung dari Database -->
                @php
                    $currentUser = \App\Models\User::where('email', 'salma@cimory.com')->first();
                    
                    // Ambil Goals
                    $userGoals = $currentUser ? \App\Models\Goal::with('milestones')->where('user_id', $currentUser->id)->get() : collect();
                    
                    // Ambil Projects (Pastikan model Project sudah ada)
                    $userProjects = $currentUser ? \App\Models\Project::where('user_id', $currentUser->id)->get() : collect();
                @endphp

                <form action="#" method="POST" class="flex flex-col gap-3">
                    <input type="text" id="task-input" placeholder="Add a new task..." class="w-full bg-sky-50/50 border border-sky-100 text-sm rounded-xl focus:ring-sky-300 focus:border-sky-300 p-3 outline-none color-transition">
                    
                    <div id="advanced-fields" class="flex gap-2 relative">
                        <!-- DROPDOWN 1: PROJECT ASLI DARI DATABASE -->
                        <select id="project-select" class="flex-1 bg-gray-50/50 border border-gray-200 text-sm rounded-xl p-3 outline-none text-gray-600">
                            <option value="">📁 Select Project (Optional)</option>
                            @foreach($userProjects as $project)
                                <option value="{{ $project->id }}">{{ $project->title ?? $project->name }}</option>
                            @endforeach
                        </select>

                        <!-- DROPDOWN 2: GOALS CUSTOM ALA NOTION -->
                        <div class="relative flex-1" id="custom-goal-dropdown">
                            <!-- Input rahasia penampung ID untuk fungsi submitTask() -->
                            <input type="hidden" id="goal-milestone-select" value="">
                            
                            <!-- Tombol Dropdown Utama -->
                            <button type="button" onclick="toggleDropdown()" class="w-full flex justify-between items-center bg-gray-50/50 border border-gray-200 text-sm rounded-xl p-3 outline-none text-gray-600 hover:border-sky-400 transition-colors">
                                <span id="dropdown-text" class="flex items-center gap-2 truncate">🎯 Select Goal (Optional)</span>
                                <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>

                            <!-- Isi Menu Dropdown -->
                            <div id="dropdown-menu" class="hidden absolute z-50 w-full mt-2 bg-white border border-gray-100 shadow-xl rounded-xl max-h-72 overflow-y-auto py-2 text-left">
                                <div onclick="selectGoalItem('', '🎯 Select Goal (Optional)', 'text-gray-400')" class="px-4 py-2 hover:bg-gray-50 cursor-pointer text-sm text-gray-500 transition border-b border-gray-50">
                                    <span class="italic">-- No Goal (General Task) --</span>
                                </div>

                                @foreach($userGoals as $goal)
                                    <div class="pt-2 pb-1">
                                        <div class="px-4 py-1.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider bg-gray-50/50 flex items-center gap-1.5">
                                            <span class="w-2 h-2 rounded-full bg-{{ $goal->color ?? 'sky' }}-400"></span>
                                            {{ $goal->title }}
                                        </div>
                                        
                                        <div onclick="selectGoalItem('{{ $goal->id }}_null', '🎯 {{ addslashes($goal->title) }}', 'text-gray-800')" class="px-4 py-2 pl-8 hover:bg-sky-50 cursor-pointer text-sm text-gray-700 font-medium transition flex items-center gap-2">
                                            ➡️ Just Goal: {{ $goal->title }}
                                        </div>

                                        @foreach($goal->milestones as $ms)
                                            <div onclick="selectGoalItem('{{ $goal->id }}_{{ $ms->id }}', '📌 {{ addslashes($ms->title) }}', 'text-gray-600')" class="px-4 py-2 pl-8 hover:bg-gray-50 cursor-pointer text-sm text-gray-600 transition flex items-center gap-2 border-l-2 border-transparent hover:border-sky-400">
                                                ↳ Step: {{ $ms->title }}
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex gap-2 mt-1">
                        <input type="number" id="mins-input" placeholder="Mins (e.g. 30)" class="w-1/3 bg-gray-50/50 border border-gray-200 text-sm rounded-xl p-3 outline-none">
                        <button type="button" id="btn-add" onclick="submitTask()" class="flex-1 bg-sky-400 hover:bg-sky-500 text-white font-semibold py-3 px-6 rounded-xl shadow-sm color-transition">Add Task</button>
                    </div>
                </form>
            </div>

            <!-- QUEUE FOR TODAY -->
            <div class="space-y-3">
                @php
                    $plannedActivities = $activities->where('status', '!=', 'completed');
                @endphp
                <div class="flex justify-between items-center mb-2">
                    <h3 class="font-bold text-gray-500 uppercase tracking-wider text-[11px]">Queue for Today</h3>
                    <span id="queue-counter" class="text-[10px] font-bold bg-gray-200 text-gray-600 px-2 py-0.5 rounded-full">{{ $plannedActivities->count() }}</span>
                </div>
                
                @forelse ($plannedActivities as $dpa)
                    @php
                        $cat = $dpa->activity->category->value;
                        $cardClass = match($cat) { 'cimory' => 'border-l-sky-300 border-sky-50', 'work' => 'border-l-orange-300 border-orange-50', 'personal' => 'border-l-pink-300 border-pink-50', default => 'border-l-gray-300 border-gray-50' };
                        $btnClass = match($cat) { 'cimory' => 'bg-sky-50 hover:bg-sky-100 text-sky-600', 'work' => 'bg-orange-50 hover:bg-orange-100 text-orange-500', 'personal' => 'bg-pink-50 hover:bg-pink-100 text-pink-500', default => 'bg-gray-50 hover:bg-gray-100 text-gray-600' };
                        $icon = match($cat) { 'cimory' => '🏢 Cimory', 'work' => '💻 Work', 'personal' => '🌱 Personal', default => '📌' };
                    @endphp
                    
                    <div class="task-card bg-white border-l-4 {{ $cardClass }} rounded-xl p-4 shadow-sm flex justify-between items-center group" data-mode="{{ $cat }}" style="display: none;">
                        <div>
                            <h3 class="font-semibold text-gray-800 text-sm">{{ $dpa->activity->title }}</h3>
                            <p class="text-[11px] text-gray-400 mt-1">{{ $icon }} • ⏱️ {{ $dpa->planned_mins ?? 0 }} min</p>
                        </div>
                        <button onclick="startFocus({{ $dpa->id }}, '{{ addslashes($dpa->activity->title) }}', '{{ $cat }}', {{ $dpa->planned_mins ?? 0 }})" class="{{ $btnClass }} px-3 py-1.5 rounded-lg text-xs font-semibold color-transition">
                            ▶ Start
                        </button>
                    </div>
                @empty
                @endforelse
                
                <div id="empty-state" class="hidden text-center py-6 bg-white/50 rounded-xl border border-dashed border-gray-200">
                    <p class="text-gray-400 text-sm">Yeay! No tasks in this queue.</p>
                </div>
            </div>

            <!-- COMPLETED TODAY LOG -->
            <div class="mt-8 pt-6 border-t border-gray-200 space-y-3">
                <div class="flex justify-between items-center mb-2">
                    <h3 class="font-bold text-gray-500 uppercase tracking-wider text-[11px]">Completed Today</h3>
                </div>

                @php
                    $completedActivities = $activities->where('status', 'completed');
                @endphp

                @forelse ($completedActivities as $dpa)
                    @php $cat = $dpa->activity->category->value; $icon = match($cat) { 'cimory' => '🏢 Cimory', 'work' => '💻 Work', 'personal' => '🌱 Personal', default => '📌' }; @endphp
                    
                    <div class="completed-card bg-[#F8F9FA] border border-gray-200 rounded-xl p-4 opacity-80 hover:opacity-100 transition" data-mode="{{ $cat }}" style="display: none;">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-medium text-gray-700 text-sm line-through decoration-gray-400">{{ $dpa->activity->title }}</h3>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-100 text-emerald-600 uppercase">Completed</span>
                        </div>
                        <p class="text-[11px] text-gray-400 mb-3">{{ $icon }} • ⏱️ Actual: {{ $dpa->actual_mins ?? 0 }} min</p>
                        
                        <div class="bg-white p-3 rounded-lg border border-gray-100 text-xs space-y-2">
                            @if($dpa->achievements)
                            <div class="flex gap-2">
                                <span class="text-emerald-500">✅</span>
                                <span class="text-gray-600">{{ $dpa->achievements }}</span>
                            </div>
                            @endif
                            @if($dpa->blockers)
                            <div class="flex gap-2">
                                <span class="text-amber-500">⚠️</span>
                                <span class="text-gray-600">{{ $dpa->blockers }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-gray-400 italic">Belum ada tugas yang diselesaikan hari ini.</p>
                @endforelse
            </div>
        </div>

        <!-- KOLOM KANAN: EKSEKUSI & ANALISIS -->
        <div class="lg:col-span-7 space-y-6">

            <!-- ATTENDANCE WIDGET -->
            <div id="attendance-widget" class="bg-white p-5 rounded-2xl border border-sky-100 shadow-sm flex flex-col md:flex-row justify-between items-center gap-4 color-transition">
                <div>
                    <h3 class="text-xs font-bold text-sky-400 uppercase tracking-wider mb-2">Attendance Log</h3>
                    <div class="flex items-center gap-2 font-mono text-gray-700">
                        <input type="time" id="input-clock-in" value="{{ $dailyPlan?->clock_in_at ? $dailyPlan->clock_in_at->format('H:i') : '' }}" class="bg-gray-50 border border-gray-200 rounded-lg p-2 text-lg font-bold w-28 outline-none focus:border-sky-300">
                        <span class="text-gray-400 text-sm">to</span>
                        <input type="time" id="input-clock-out" value="{{ $dailyPlan?->clock_out_at ? $dailyPlan->clock_out_at->format('H:i') : '' }}" class="bg-gray-50 border border-gray-200 rounded-lg p-2 text-lg font-bold w-28 outline-none focus:border-sky-300">
                    </div>
                </div>
                <div class="text-xs text-gray-400 text-right md:max-w-xs">
                    🕒 Waktu kehadiran akan <b class="text-sky-500">otomatis tersimpan</b> saat kamu menambahkan Task baru.
                </div>
            </div>

            <!-- POMODORO PLAYER -->
            <div id="timer-player" class="bg-gradient-to-br from-[#2A3B4C] to-[#1A2530] p-8 rounded-3xl shadow-lg text-center relative overflow-hidden transition-colors duration-500">
                <div id="timer-accent" class="absolute -top-10 -right-10 w-40 h-40 opacity-20 rounded-full blur-2xl color-transition bg-gray-500"></div>
                <h3 class="text-gray-400 font-semibold tracking-widest uppercase text-[10px] mb-2">Currently Focusing On</h3>
                <h2 id="running-task-name" class="text-2xl font-bold text-white mb-6">No Active Task</h2>
                <div id="timer-display" class="text-7xl font-bold text-white font-mono tracking-widest mb-8 drop-shadow-md">00:00</div>
                <div class="flex justify-center gap-3">
                    <button id="btn-pause" onclick="togglePause()" class="bg-white/10 hover:bg-white/20 text-white font-medium py-3 px-8 rounded-xl flex items-center gap-2 backdrop-blur-sm color-transition">⏸ Pause</button>
                    <button id="btn-finish-session" onclick="finishSession()" class="bg-gray-600 hover:bg-gray-500 text-white font-semibold py-3 px-8 rounded-xl flex items-center gap-2 color-transition">⏹ Finish Session</button>
                </div>
            </div>

            <!-- FORM TASK ANALYSIS -->
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <div class="border-b border-gray-100 pb-4 mb-4 flex justify-between items-center">
                    <div>
                        <h2 class="text-lg font-bold text-gray-700">Task Analysis & Result</h2>
                        <p class="text-sm text-gray-400" id="analysis-task-label">Waiting for session to finish...</p>
                    </div>
                </div>

                <div class="flex items-center gap-6 mb-6 p-4 bg-[#FAFAF9] rounded-xl border border-gray-100">
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wide">Planned</p>
                        <p class="text-lg font-bold text-gray-600 font-mono mt-1" id="planned-time-value">0 Mins</p>
                    </div>
                    <div class="h-8 border-r border-gray-200"></div>
                    <div>
                        <p class="text-[10px] text-sky-500 font-bold uppercase tracking-wide" id="actual-time-label">Actual Time</p>
                        <p class="text-lg font-bold text-sky-500 font-mono mt-1" id="actual-time-value">0 Mins</p>
                    </div>
                </div>

                <form action="#" class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">✅ Apa yang sudah selesai?</label>
                        <textarea id="input-achievements" rows="2" class="w-full bg-[#FAFAF9] border border-gray-200 text-sm rounded-xl focus:ring-sky-300 focus:border-sky-300 p-3 outline-none" placeholder="Contoh: Berhasil merekap data..."></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">⚠️ Kendala atau Masalah (Blockers)</label>
                        <textarea id="input-blockers" rows="2" class="w-full bg-[#FFF5F5] border border-red-100 text-sm rounded-xl focus:ring-red-300 focus:border-red-300 p-3 outline-none" placeholder="Contoh: Server sempat down..."></textarea>
                    </div>
                    <div class="pt-2 flex gap-3">
                        <button type="button" id="btn-submit-analysis" onclick="submitAnalysis()" class="flex-1 bg-emerald-400 hover:bg-emerald-500 text-white font-medium py-3 px-4 rounded-xl shadow-sm color-transition">
                            Mark as Completed
                        </button>
                        <button type="button" id="btn-done-today" onclick="doneForToday()" class="flex-1 bg-amber-400 hover:bg-amber-500 text-white font-medium py-3 px-4 rounded-xl shadow-sm color-transition">
                            Done for Today
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    let currentMode = 'cimory'; 

    // --- FUNGSI CUSTOM DROPDOWN GOAL ---
    function toggleDropdown() {
        document.getElementById('dropdown-menu').classList.toggle('hidden');
    }

    function selectGoalItem(value, text, textColorClass) {
        document.getElementById('goal-milestone-select').value = value;
        const textSpan = document.getElementById('dropdown-text');
        textSpan.className = `flex items-center gap-2 truncate font-medium ${textColorClass}`;
        textSpan.innerHTML = text;
        document.getElementById('dropdown-menu').classList.add('hidden');
    }

    // Menutup dropdown jika user klik di luarnya
    document.addEventListener('click', function(event) {
        const container = document.getElementById('custom-goal-dropdown');
        if (container && !container.contains(event.target)) {
            document.getElementById('dropdown-menu').classList.add('hidden');
        }
    });
    // ------------------------------------

    function switchMode(mode) {
        currentMode = mode; 
        localStorage.setItem('activeOSMode', mode);

        const tabs = document.querySelectorAll('.mode-tab');
        tabs.forEach(tab => { tab.className = 'mode-tab px-4 py-3 text-sm font-semibold border-b-2 border-transparent text-gray-400 hover:text-gray-600 color-transition flex items-center gap-2'; });

        const activeTab = document.getElementById(`tab-${mode}`);
        const inputField = document.getElementById('task-input');
        const btnAdd = document.getElementById('btn-add');
        const advancedFields = document.getElementById('advanced-fields');
        const projectSelect = document.getElementById('project-select'); 
        const attendanceWidget = document.getElementById('attendance-widget');

        if (mode === 'cimory') {
            activeTab.className = 'mode-tab px-4 py-3 text-sm font-bold border-b-2 border-sky-400 text-sky-500 color-transition flex items-center gap-2';
            inputField.className = "w-full bg-sky-50/50 border border-sky-100 text-sm rounded-xl focus:ring-sky-300 focus:border-sky-300 p-3 outline-none color-transition";
            btnAdd.className = "flex-1 bg-sky-400 hover:bg-sky-500 text-white font-semibold py-3 px-6 rounded-xl shadow-sm color-transition";
            advancedFields.style.display = 'flex';
            projectSelect.style.display = 'block'; 
            attendanceWidget.style.display = 'flex';
        } 
        else if (mode === 'work') {
            activeTab.className = 'mode-tab px-4 py-3 text-sm font-bold border-b-2 border-orange-400 text-orange-500 color-transition flex items-center gap-2';
            inputField.className = "w-full bg-orange-50/50 border border-orange-100 text-sm rounded-xl focus:ring-orange-300 focus:border-orange-300 p-3 outline-none color-transition";
            btnAdd.className = "flex-1 bg-orange-400 hover:bg-orange-500 text-white font-semibold py-3 px-6 rounded-xl shadow-sm color-transition";
            advancedFields.style.display = 'flex';
            projectSelect.style.display = 'block'; 
            attendanceWidget.style.display = 'none';
        } 
        else if (mode === 'personal') {
            activeTab.className = 'mode-tab px-4 py-3 text-sm font-bold border-b-2 border-pink-400 text-pink-500 color-transition flex items-center gap-2';
            inputField.className = "w-full bg-pink-50/50 border border-pink-100 text-sm rounded-xl focus:ring-pink-300 focus:border-pink-300 p-3 outline-none color-transition";
            btnAdd.className = "flex-1 bg-pink-400 hover:bg-pink-500 text-white font-semibold py-3 px-6 rounded-xl shadow-sm color-transition";
            advancedFields.style.display = 'flex'; 
            projectSelect.style.display = 'none'; 
            projectSelect.value = ""; 
            attendanceWidget.style.display = 'none';
        }

        const taskCards = document.querySelectorAll('.task-card');
        let visibleCount = 0;
        taskCards.forEach(card => {
            if(card.dataset.mode === mode) { card.style.display = 'flex'; visibleCount++; } 
            else { card.style.display = 'none'; }
        });
        document.getElementById('queue-counter').innerText = visibleCount;
        document.getElementById('empty-state').style.display = (visibleCount === 0) ? 'block' : 'none';

        const completedCards = document.querySelectorAll('.completed-card');
        completedCards.forEach(card => {
            if(card.dataset.mode === mode) { card.style.display = 'block'; } 
            else { card.style.display = 'none'; }
        });
    }

    let activeTimerMode = null;
    let activeDpaId = null; 
    let reviewDpaId = null; 
    let reviewActualMins = 0; 
    
    let timerInterval = null;
    let elapsedSeconds = 0;
    let isPaused = false;

    function updateTimerDisplay() {
        const minutes = Math.floor(elapsedSeconds / 60);
        const seconds = elapsedSeconds % 60;
        document.getElementById('timer-display').innerText = String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
    }

    function togglePause() {
        if (activeTimerMode === null) return; 
        const btnPause = document.getElementById('btn-pause');
        isPaused = !isPaused; 
        if (isPaused) {
            btnPause.innerText = "▶ Resume";
            btnPause.classList.add('bg-yellow-500', 'text-white'); 
        } else {
            btnPause.innerText = "⏸ Pause";
            btnPause.classList.remove('bg-yellow-500');
        }
    }

    function startFocus(id, taskName, mode, plannedMins) {
        if (activeTimerMode !== null) {
            alert(`⚠️ Selesaikan task saat ini sebelum memulai yang lain.`);
            return; 
        }

        activeTimerMode = mode;
        activeDpaId = id; 
        document.getElementById('running-task-name').innerText = taskName;
        document.getElementById('planned-time-value').innerText = plannedMins + " Mins"; 

        clearInterval(timerInterval);
        elapsedSeconds = 0;
        isPaused = false;
        updateTimerDisplay();
        document.getElementById('btn-pause').innerText = "⏸ Pause";
        document.getElementById('btn-pause').classList.remove('bg-yellow-500');

        timerInterval = setInterval(() => {
            if (!isPaused) { elapsedSeconds++; updateTimerDisplay(); }
        }, 1000);

        const timerAccent = document.getElementById('timer-accent');
        const finishBtn = document.getElementById('btn-finish-session');
        const actualTimeLabel = document.getElementById('actual-time-label');
        const actualTimeValue = document.getElementById('actual-time-value');

        timerAccent.className = "absolute -top-10 -right-10 w-40 h-40 opacity-20 rounded-full blur-2xl color-transition";
        finishBtn.className = "text-white font-semibold py-3 px-8 rounded-xl flex items-center gap-2 shadow-lg color-transition";

        if (mode === 'cimory') {
            timerAccent.classList.add('bg-sky-400');
            finishBtn.classList.add('bg-sky-400', 'hover:bg-sky-500', 'shadow-sky-400/30');
            actualTimeLabel.className = "text-[10px] font-bold uppercase tracking-wide text-sky-500";
            actualTimeValue.className = "text-lg font-bold font-mono mt-1 text-sky-500";
        } else if (mode === 'work') {
            timerAccent.classList.add('bg-orange-400');
            finishBtn.classList.add('bg-orange-400', 'hover:bg-orange-500', 'shadow-orange-400/30');
            actualTimeLabel.className = "text-[10px] font-bold uppercase tracking-wide text-orange-500";
            actualTimeValue.className = "text-lg font-bold font-mono mt-1 text-orange-500";
        } else if (mode === 'personal') {
            timerAccent.classList.add('bg-pink-400');
            finishBtn.classList.add('bg-pink-400', 'hover:bg-pink-500', 'shadow-pink-400/30');
            actualTimeLabel.className = "text-[10px] font-bold uppercase tracking-wide text-pink-500";
            actualTimeValue.className = "text-lg font-bold font-mono mt-1 text-pink-500";
        }
    }

    function finishSession() {
        if(activeTimerMode === null) return;
        
        clearInterval(timerInterval);
        
        reviewDpaId = activeDpaId;
        reviewActualMins = Math.floor(elapsedSeconds / 60);
        document.getElementById('actual-time-value').innerText = reviewActualMins + " Mins";
        document.getElementById('analysis-task-label').innerText = "Reviewing: " + document.getElementById('running-task-name').innerText;
        document.getElementById('analysis-task-label').classList.replace('text-gray-400', 'text-sky-500');
        document.getElementById('analysis-task-label').classList.add('font-semibold');
        
        activeTimerMode = null;
        activeDpaId = null;
        document.getElementById('running-task-name').innerText = "No Active Task";
        document.getElementById('timer-display').innerText = "00:00";
        
        const timerAccent = document.getElementById('timer-accent');
        const finishBtn = document.getElementById('btn-finish-session');
        timerAccent.className = "absolute -top-10 -right-10 w-40 h-40 opacity-20 rounded-full blur-2xl color-transition bg-gray-500";
        finishBtn.className = "bg-gray-600 hover:bg-gray-500 text-white font-semibold py-3 px-8 rounded-xl flex items-center gap-2 color-transition";
    }

    async function submitAnalysis() {
        if (!reviewDpaId) {
            alert("Silakan 'Finish Session' pada suatu tugas terlebih dahulu.");
            return;
        }

        const btn = document.getElementById('btn-submit-analysis');
        btn.innerText = "Saving...";
        btn.disabled = true;

        try {
            let response = await fetch(`/activities/${reviewDpaId}/complete`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({
                    actual_mins: reviewActualMins,
                    achievements: document.getElementById('input-achievements').value,
                    blockers: document.getElementById('input-blockers').value
                })
            });

            if (response.ok) { 
                window.location.reload(); 
            } else { 
                let errorData = await response.json();
                console.error("Laravel Error:", errorData);
                alert("Gagal: " + (errorData.message || "Terjadi kesalahan di Backend")); 
                btn.innerText = "Mark as Completed"; 
                btn.disabled = false; 
            }
        } catch (error) { 
            console.error(error); 
            alert("Jaringan terputus / Server mati.");
            btn.innerText = "Mark as Completed"; 
            btn.disabled = false; 
        }
    }

    async function doneForToday() {
        if (!reviewDpaId) {
            alert("Silakan 'Finish Session' pada suatu tugas terlebih dahulu.");
            return;
        }

        const btn = document.getElementById('btn-done-today');
        btn.innerText = "Saving...";
        btn.disabled = true;

        try {
            let response = await fetch(`/activities/${reviewDpaId}/done-for-today`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ actual_mins: reviewActualMins })
            });

            if (response.ok) { 
                window.location.reload(); 
            } else { 
                let errorData = await response.json();
                console.error("Laravel Error:", errorData);
                alert("Gagal: " + (errorData.message || "Terjadi kesalahan di Backend")); 
                btn.innerText = "Done for Today"; 
                btn.disabled = false; 
            }
        } catch (error) { 
            console.error(error); 
            alert("Jaringan terputus / Server mati.");
            btn.innerText = "Done for Today"; 
            btn.disabled = false; 
        }
    }

    async function submitTask() {
        const titleInput = document.getElementById('task-input');
        const minsInput = document.getElementById('mins-input');
        const projectSelect = document.getElementById('project-select');
        
        // PENTING: goalSelect sekarang mengambil nilai dari input tersembunyi
        const goalSelect = document.getElementById('goal-milestone-select');
        const btnAdd = document.getElementById('btn-add');

        const timeIn = document.getElementById('input-clock-in')?.value;
        const timeOut = document.getElementById('input-clock-out')?.value;

        if (!titleInput.value) { alert("Judul task tidak boleh kosong!"); return; }

        let goalId = null;
        let milestoneId = null;
        if (goalSelect.value) {
            const parts = goalSelect.value.split('_');
            goalId = parts[0];
            milestoneId = parts[1] !== 'null' ? parts[1] : null;
        }

        let projectId = (projectSelect.style.display !== 'none' && projectSelect.value) ? projectSelect.value : null;

        const activeDateValue = document.getElementById('date-navigator').value;

        btnAdd.innerText = "Adding..."; btnAdd.disabled = true;

        try {
            let response = await fetch('/activities', { 
                method: 'POST', 
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }, 
                body: JSON.stringify({ 
                    title: titleInput.value, 
                    category: currentMode, 
                    planned_mins: minsInput.value || 0,
                    project_id: projectId,
                    goal_id: goalId,
                    milestone_id: milestoneId,
                    clock_in: currentMode === 'cimory' ? timeIn : null,
                    clock_out: currentMode === 'cimory' ? timeOut : null,
                    target_date: activeDateValue 
                }) 
            });
            if (response.ok) { window.location.reload(); } 
            else { 
                let err = await response.json(); alert("Gagal: " + (err.message || "Error tidak diketahui")); 
                btnAdd.innerText = "Add Task"; btnAdd.disabled = false; 
            }
        } catch (error) { 
            alert("Jaringan terputus."); btnAdd.innerText = "Add Task"; btnAdd.disabled = false; 
        }
    }

    document.addEventListener('DOMContentLoaded', () => { 
        const savedMode = localStorage.getItem('activeOSMode') || 'cimory';
        switchMode(savedMode); 
    });
</script>
</body>
</html>
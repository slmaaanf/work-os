<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weekly Recap - OS</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #FAFAF9; }
        dialog::backdrop { background-color: rgba(31, 41, 55, 0.5); backdrop-filter: blur(2px); }
        .calendar-cell::-webkit-scrollbar { width: 4px; }
        .calendar-cell::-webkit-scrollbar-thumb { background-color: #E5E7EB; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #E5E7EB; border-radius: 4px; }
    </style>
</head>
<body class="text-gray-800 antialiased min-h-screen p-4 md:p-8">

<div class="max-w-[1000px] mx-auto">
    <!-- HEADER -->
    <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-end gap-4 border-b border-gray-200 pb-6">
        <div>
            <a href="/" class="text-sm font-semibold text-sky-500 hover:text-sky-600 flex items-center gap-2 mb-3">
                &larr; Back to Today's Plan
            </a>
            <h1 class="text-3xl font-bold text-gray-800 tracking-tight">Analytics Dashboard</h1>
            <p class="text-gray-500 mt-1">Review your weekly progress and monthly consistency.</p>
        </div>
    </div>

    <!-- WEEKLY SECTION (TOP) -->
    <h2 class="text-xl font-bold text-gray-700 mb-4">This Week</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Total Focus Time</p>
                <div class="text-4xl font-bold text-gray-700 font-mono">{{ $totalHours }} <span class="text-lg text-gray-400">Hours</span></div>
            </div>
            <div class="w-14 h-14 bg-sky-50 rounded-full flex items-center justify-center text-2xl">⏳</div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Tasks Completed</p>
                <div class="text-4xl font-bold text-gray-700 font-mono">{{ $totalTasks }} <span class="text-lg text-gray-400">Tasks</span></div>
            </div>
            <div class="w-14 h-14 bg-emerald-50 rounded-full flex items-center justify-center text-2xl">✅</div>
        </div>
    </div>

    <!-- MAIN CHART & JOURNAL GRID -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
        
        <!-- CHART.JS AREA -->
        <div class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm">
            <h2 class="text-lg font-bold text-gray-700 mb-6">Time Distribution</h2>
            <div class="relative h-64 w-full flex justify-center">
                <canvas id="timeChart"></canvas>
            </div>
        </div>

        <!-- WEEKLY JOURNAL & INSIGHTS -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex flex-col h-full">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-bold text-gray-700">Weekly Journal & Insights</h2>
                <span class="text-xs text-gray-400 bg-gray-100 px-2 py-1 rounded-md">This Week</span>
            </div>
            
            <div class="space-y-4 max-h-[300px] overflow-y-auto pr-2 custom-scrollbar">
                @php
                    $insightfulTasks = $completedTasks->filter(function($task) {
                        return !empty($task->achievements) || !empty($task->blockers);
                    })->sortByDesc('updated_at');
                @endphp

                @forelse($insightfulTasks as $task)
                    @php 
                        $cat = $task->activity->category->value ?? 'work'; 
                        $badgeClass = match($cat) { 'cimory' => 'bg-sky-100 text-sky-600', 'work' => 'bg-orange-100 text-orange-600', 'personal' => 'bg-pink-100 text-pink-600', default => 'bg-gray-100 text-gray-600' }; 
                    @endphp
                    
                    <div class="bg-[#FAFAF9] border border-gray-100 p-4 rounded-xl hover:border-sky-200 transition">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h3 class="font-semibold text-gray-800 text-sm mb-1">{{ $task->activity->title }}</h3>
                                <p class="text-[10px] text-gray-400 font-mono">{{ $task->updated_at->timezone('Asia/Jakarta')->format('l, d M Y') }}</p>
                            </div>
                            <span class="text-[9px] font-bold px-2 py-0.5 rounded uppercase {{ $badgeClass }}">{{ $cat }}</span>
                        </div>
                        
                        <div class="space-y-2">
                            @if($task->achievements)
                                <div class="text-xs text-gray-600 flex gap-2 items-start">
                                    <span class="text-emerald-500 shrink-0">✅</span>
                                    <span>{{ $task->achievements }}</span>
                                </div>
                            @endif
                            
                            @if($task->blockers)
                                <div class="text-xs text-gray-600 flex gap-2 items-start bg-red-50/50 p-2 rounded-lg border border-red-50">
                                    <span class="text-red-500 shrink-0">⚠️</span>
                                    <span class="text-red-700">{{ $task->blockers }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-8 text-center opacity-60">
                        <span class="text-3xl mb-2">📝</span>
                        <p class="text-sm text-gray-500 font-medium">No insights recorded yet.</p>
                        <p class="text-xs text-gray-400 mt-1">Fill out the achievements & blockers to see them here.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div> <!-- PENUTUP GRID 2 KOLOM -->

    <!-- MONTHLY CALENDAR SECTION (BOTTOM) -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-4">
        <h2 class="text-xl font-bold text-gray-700">Monthly Calendar ({{ $startOfMonth->format('F Y') }})</h2>
        
        <div class="flex gap-2">
            <a href="?month={{ $prevMonth->month }}&year={{ $prevMonth->year }}" class="p-2 rounded-lg bg-white border border-gray-200 hover:bg-gray-50 text-gray-500 hover:text-sky-500 transition shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/></svg>
            </a>
            <a href="/recap" class="px-4 py-1.5 rounded-lg bg-white border border-gray-200 hover:bg-gray-50 text-xs font-semibold text-gray-600 flex items-center transition shadow-sm">Today</a>
            <a href="?month={{ $nextMonth->month }}&year={{ $nextMonth->year }}" class="p-2 rounded-lg bg-white border border-gray-200 hover:bg-gray-50 text-gray-500 hover:text-sky-500 transition shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/></svg>
            </a>
        </div>
    </div>

    <div class="bg-white p-6 md:p-8 rounded-2xl border border-gray-100 shadow-sm">
        
        <!-- Hari Senin - Minggu -->
        <div class="grid grid-cols-7 gap-2 mb-3">
            @foreach(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $day)
                <div class="text-center text-[10px] md:text-xs font-bold text-gray-400 uppercase tracking-wider">{{ $day }}</div>
            @endforeach
        </div>

        <!-- Kotak-kotak Tanggal -->
        <div class="grid grid-cols-7 gap-1 md:gap-2 border-l border-t border-gray-100">
            @foreach($calendarDays as $dayInfo)
                @if($dayInfo === null)
                    <div class="bg-[#FAFAF9] p-2 h-28 md:h-36 border-r border-b border-gray-100 opacity-50"></div>
                @else
                    @php 
                        $isToday = $dayInfo['date_string'] === \Carbon\Carbon::today()->timezone('Asia/Jakarta')->format('Y-m-d'); 
                    @endphp
                    
                    <div onclick="showModal('{{ $dayInfo['date_string'] }}')" class="calendar-cell bg-white border-r border-b border-gray-100 p-1 md:p-2 h-28 md:h-36 flex flex-col relative transition duration-200 overflow-y-auto {{ $dayInfo['has_data'] ? 'hover:bg-gray-50 cursor-pointer group' : '' }}">
                        
                        <div class="flex justify-center w-full mb-1 md:mb-2">
                            <span class="text-[11px] md:text-[13px] font-medium {{ $isToday ? 'text-white bg-sky-500 rounded-full w-6 h-6 flex items-center justify-center' : 'text-gray-600' }}">
                                {{ $dayInfo['day'] }}
                            </span>
                        </div>
                        
                        @if($dayInfo['has_data'])
                            <div class="space-y-1.5 w-full mt-1">
                                @php 
                                    $displayLimit = 4;
                                    $activitiesCount = $dayInfo['plan']->activities->count();
                                @endphp
                                
                                @foreach($dayInfo['plan']->activities->take($displayLimit) as $act)
                                    @php 
                                        $cat = $act->activity->category->value ?? 'work'; 
                                        $isCompleted = $act->status === 'completed';
                                        
                                        $dotColor = match($cat) { 
                                            'cimory' => 'bg-sky-500', 
                                            'work' => 'bg-orange-500', 
                                            'personal' => 'bg-pink-500', 
                                            default => 'bg-gray-500' 
                                        };

                                        $timestamp = $isCompleted ? $act->updated_at : $act->created_at;
                                        $timeStr = $timestamp ? str_replace(' ', '', \Carbon\Carbon::parse($timestamp)->timezone('Asia/Jakarta')->format('ga')) : '';
                                    @endphp
                                    
                                    <div class="flex items-center gap-1 md:gap-1.5 overflow-hidden whitespace-nowrap text-[9px] md:text-[11px]">
                                        <div class="w-1.5 h-1.5 rounded-full {{ $dotColor }} shrink-0"></div>
                                        <span class="text-gray-400 shrink-0">{{ $timeStr }}</span>
                                        
                                        @if($isCompleted)
                                            <div class="w-2.5 h-2.5 md:w-3 md:h-3 rounded-full bg-emerald-500 flex items-center justify-center shrink-0">
                                                <svg class="w-1.5 h-1.5 md:w-2 md:h-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path></svg>
                                            </div>
                                            <span class="text-gray-400 line-through truncate" title="{{ $act->activity->title }}">{{ $act->activity->title }}</span>
                                        @else
                                            <div class="w-2.5 h-2.5 md:w-3 md:h-3 rounded-full border-[1.5px] border-gray-600 shrink-0"></div>
                                            <span class="text-gray-800 font-semibold truncate" title="{{ $act->activity->title }}">{{ $act->activity->title }}</span>
                                        @endif
                                    </div>
                                @endforeach
                                
                                @if($activitiesCount > $displayLimit)
                                    <div class="text-[9px] font-medium text-gray-400 pl-3 pt-1">
                                        +{{ $activitiesCount - $displayLimit }} more
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- MODAL POPUP REVIEW -->
                    @if($dayInfo['has_data'])
                        <dialog id="modal-{{ $dayInfo['date_string'] }}" class="bg-white p-0 rounded-2xl shadow-2xl border border-gray-100 w-[95%] max-w-lg m-auto overflow-hidden">
                            <div class="p-6 bg-slate-50 border-b border-gray-100 flex justify-between items-center">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-800">Activity Review</h3>
                                    <p class="text-sm text-gray-500">{{ $dayInfo['date']->format('l, j F Y') }}</p>
                                </div>
                                <button onclick="closeModal('{{ $dayInfo['date_string'] }}')" class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-200 text-gray-600 hover:bg-red-100 hover:text-red-500 transition font-bold">&times;</button>
                            </div>
                            
                            <div class="p-6 space-y-4 max-h-[60vh] overflow-y-auto">
                                @foreach($dayInfo['plan']->activities as $activity)
                                    @php 
                                        $cat = $activity->activity->category->value ?? 'work'; 
                                        $isCompleted = $activity->status === 'completed';
                                    @endphp
                                    <div class="bg-white rounded-xl p-4 border {{ $isCompleted ? 'border-emerald-100 shadow-sm' : 'border-gray-200 border-dashed' }}">
                                        
                                        <!-- BAGIAN HEADER KARTU (TERMASUK TOMBOL DELETE) -->
                                        <div class="flex justify-between items-start mb-2">
                                            <div class="flex items-center gap-2">
                                                <span class="text-lg leading-none">{{ $isCompleted ? '✅' : '⏳' }}</span>
                                                <h4 class="font-bold text-sm {{ $isCompleted ? 'text-gray-500 line-through' : 'text-gray-800' }}">{{ $activity->activity->title }}</h4>
                                            </div>
                                            
                                            <div class="flex items-center gap-3">
                                                <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-gray-100 text-gray-600 uppercase">{{ $cat }}</span>
                                                <!-- TOMBOL HAPUS (BARU) -->
                                                <button onclick="deleteActivity({{ $activity->id }})" class="text-red-300 hover:text-red-600 transition-colors shrink-0" title="Hapus Permanen">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        @if($isCompleted)
                                            <div class="text-xs text-emerald-600 font-mono font-medium mb-3 pl-7">⏱️ {{ $activity->actual_mins }} Mins Focused</div>
                                            @if($activity->achievements)
                                                <div class="text-xs text-gray-700 bg-emerald-50/50 p-2 ml-7 rounded border border-emerald-50 mb-2">
                                                    <span class="text-emerald-500 font-bold">Achievement:</span><br>{{ $activity->achievements }}
                                                </div>
                                            @endif
                                            @if($activity->blockers)
                                                <div class="text-xs text-gray-700 bg-amber-50/50 p-2 ml-7 rounded border border-amber-50">
                                                    <span class="text-amber-500 font-bold">Blocker:</span><br>{{ $activity->blockers }}
                                                </div>
                                            @endif
                                        @else
                                            <div class="text-xs text-gray-400 font-mono font-medium pl-7 mt-1">Target: {{ $activity->planned_mins }} Mins (In Queue)</div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </dialog>
                    @endif

                @endif
            @endforeach
        </div>
    </div>
</div>

<!-- SCRIPT UNTUK CHART & MODAL -->
<script>
    function showModal(dateString) {
        const modal = document.getElementById('modal-' + dateString);
        if(modal) { modal.showModal(); }
    }
    function closeModal(dateString) {
        const modal = document.getElementById('modal-' + dateString);
        if(modal) { modal.close(); }
    }

    // FUNGSI HAPUS AKTIVITAS (BARU)
    async function deleteActivity(id) {
        if (!confirm('Yakin ingin menghapus task ini dari riwayat? Data akan hilang permanen.')) return;

        try {
            let response = await fetch(`/activities/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                window.location.reload(); 
            } else {
                alert('Gagal menghapus data.');
            }
        } catch (error) {
            alert('Terjadi kesalahan pada sistem.');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('timeChart').getContext('2d');
        const dataCimory = {{ $chartData['cimory'] ?? 0 }};
        const dataWork = {{ $chartData['work'] ?? 0 }};
        const dataPersonal = {{ $chartData['personal'] ?? 0 }};

        const total = dataCimory + dataWork + dataPersonal;
        const chartValues = total > 0 ? [dataCimory, dataWork, dataPersonal] : [1, 1, 1];
        const bgColors = total > 0 ? ['#38BDF8', '#FB923C', '#F472B6'] : ['#F3F4F6', '#F3F4F6', '#F3F4F6'];

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['🏢 Cimory', '💻 Work', '🌱 Personal'],
                datasets: [{
                    data: chartValues,
                    backgroundColor: bgColors,
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: {
                    legend: { position: 'bottom', labels: { padding: 20, font: { family: 'Inter', size: 12 } } },
                    tooltip: { callbacks: { label: function(context) { return total === 0 ? ' No Data' : ' ' + context.label + ': ' + context.raw + ' Mins'; } } }
                }
            }
        });
    });
</script>

</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Goals & Habits - OS</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #FAFAF9; }
        dialog::backdrop { background-color: rgba(31, 41, 55, 0.5); backdrop-filter: blur(2px); }
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
            <h1 class="text-3xl font-bold text-gray-800 tracking-tight">Goals & Habit Tracker</h1>
            <p class="text-gray-500 mt-1">Track your long-term milestones and daily consistency.</p>
        </div>
        <div class="flex gap-2">
            <a href="/recap" class="bg-white border border-gray-200 text-gray-600 hover:text-gray-900 hover:bg-gray-50 font-medium py-2 px-4 rounded-xl text-sm transition flex items-center gap-2 shadow-sm">
                📊 Weekly & Monthly Recap
            </a>
        </div>
    </div>

    <!-- MACRO GOALS (MILESTONES) -->
    <div class="mb-10">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-700 flex items-center gap-2">🚀 Long-term Milestones</h2>
            <button onclick="document.getElementById('modal-add-goal').showModal()" class="text-sm font-semibold text-sky-500 hover:text-sky-600 bg-sky-50 px-3 py-1.5 rounded-lg border border-sky-100 transition">+ Add Goal</button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @forelse($goals as $goal)
                @php 
                    $color = $goal->color ?? 'sky'; 
                    $bgClass = "bg-{$color}-50";
                    $textClass = "text-{$color}-600";
                    $barClass = "bg-{$color}-500";
                @endphp
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex flex-col hover:shadow-md transition">
                    <div class="mb-4">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-bold text-gray-800 leading-tight">{{ $goal->title }}</h3>
                            <span class="text-xs font-bold px-2 py-1 rounded {{ $bgClass }} {{ $textClass }}">{{ $goal->progress }}%</span>
                        </div>
                        <p class="text-[11px] text-gray-400">{{ $goal->description }}</p>
                    </div>

                    <div class="w-full bg-gray-100 rounded-full h-2 mb-4">
                        <div class="{{ $barClass }} h-2 rounded-full transition-all duration-1000" style="width: {{ $goal->progress }}%"></div>
                    </div>

                    <div class="mt-auto space-y-2 pt-2 border-t border-gray-50">
                        @forelse($goal->milestones as $ms)
                            <div class="flex items-center gap-2 text-xs">
                                @if($ms->is_completed)
                                    <span class="text-emerald-500 shrink-0">✅</span>
                                    <span class="text-gray-400 line-through truncate">{{ $ms->title }}</span>
                                @else
                                    <div class="w-3 h-3 rounded border border-gray-300 shrink-0"></div>
                                    <span class="text-gray-600 font-medium truncate">{{ $ms->title }}</span>
                                @endif
                            </div>
                        @empty
                            <p class="text-xs text-gray-400 italic">Belum ada milestone.</p>
                        @endforelse
                    </div>
                </div>
            @empty
                <div class="col-span-3 py-10 text-center border border-dashed border-gray-200 rounded-2xl bg-white/50">
                    <p class="text-gray-400 font-medium">Belum ada Goal yang ditambahkan.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- DAILY HABIT TRACKER (SIMPLE & CLEAN) -->
    <div>
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-700 flex items-center gap-2">🌱 Daily Habit Tracker</h2>
            <button onclick="document.getElementById('modal-add-habit').showModal()" class="text-sm font-semibold text-emerald-600 hover:text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-lg border border-emerald-100 transition">+ Add Habit</button>
        </div>
        
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm overflow-x-auto">
            <table class="w-full text-left min-w-[700px]">
                <thead>
                    <tr>
                        <th class="pb-4 text-xs font-bold text-gray-400 uppercase tracking-wider w-1/3">Habit</th>
                        <th class="pb-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">Streak</th>
                        @foreach($days as $day)
                            <th class="pb-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-center w-10">{{ $day['name'] }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($habits as $habit)
                        <tr class="hover:bg-gray-50/50 transition group">
                            <td class="py-3 pr-4">
                                <div class="font-medium text-sm text-gray-800">{{ $habit['title'] }}</div>
                                <div class="text-[10px] text-gray-400 font-mono uppercase mt-0.5">{{ $habit['category'] }} ROUTINE</div>
                            </td>
                            <td class="py-3 text-center">
                                <span class="text-xs font-bold bg-amber-50 text-amber-600 px-2 py-1 rounded-md border border-amber-100">
                                    🔥 {{ $habit['streak'] }}
                                </span>
                            </td>
                            
                            <!-- Perulangan 7 Hari & Kotak Centang Interaktif -->
                            @foreach($habit['history'] as $index => $isDone)
                                @php $dateStr = $habit['dates'][$index]; @endphp
                                <td class="py-3 text-center">
                                    <button onclick="toggleHabit({{ $habit['id'] }}, '{{ $dateStr }}', this)" class="w-6 h-6 rounded-md border flex items-center justify-center mx-auto transition-colors {{ $isDone ? 'bg-emerald-500 border-emerald-500 text-white shadow-sm' : 'bg-gray-50 border-gray-200 hover:border-emerald-300' }}">
                                        @if($isDone)
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        @endif
                                    </button>
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-sm text-gray-400 italic">Belum ada habit sederhana. Klik "+ Add Habit" untuk mulai mencatat kebiasaanmu!</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL ADD GOAL -->
<dialog id="modal-add-goal" class="bg-transparent p-0 w-[95%] max-w-lg m-auto">
    <!-- Wrapper utama yang mengatur Flexbox & Batas Tinggi (max-height) -->
    <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 flex flex-col max-h-[90vh] overflow-hidden">
        
        <!-- HEADER (Tetap menempel di atas) -->
        <div class="p-5 bg-slate-50 border-b border-gray-100 flex justify-between items-center shrink-0">
            <h3 class="text-lg font-bold text-gray-800">Add New Goal</h3>
            <button onclick="document.getElementById('modal-add-goal').close()" class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-200 text-gray-600 hover:bg-red-100 hover:text-red-500 transition font-bold">&times;</button>
        </div>
        
        <!-- BODY (Area yang bisa di-scroll jika isinya banyak) -->
        <div class="p-6 space-y-4 overflow-y-auto flex-1 custom-scrollbar">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Goal Title</label>
                <input type="text" id="goal-title" placeholder="e.g. IELTS Band 7.0" class="w-full bg-gray-50 border border-gray-200 text-sm rounded-lg p-2.5 outline-none focus:border-sky-400">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Description</label>
                <textarea id="goal-desc" rows="2" placeholder="Deskripsi singkat..." class="w-full bg-gray-50 border border-gray-200 text-sm rounded-lg p-2.5 outline-none focus:border-sky-400"></textarea>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Accent Color</label>
                <div class="flex gap-3">
                    <label class="cursor-pointer flex items-center gap-1.5"><input type="radio" name="goal-color" value="sky" checked> <span class="w-4 h-4 rounded-full bg-sky-500 block"></span></label>
                    <label class="cursor-pointer flex items-center gap-1.5"><input type="radio" name="goal-color" value="emerald"> <span class="w-4 h-4 rounded-full bg-emerald-500 block"></span></label>
                    <label class="cursor-pointer flex items-center gap-1.5"><input type="radio" name="goal-color" value="amber"> <span class="w-4 h-4 rounded-full bg-amber-500 block"></span></label>
                    <label class="cursor-pointer flex items-center gap-1.5"><input type="radio" name="goal-color" value="pink"> <span class="w-4 h-4 rounded-full bg-pink-500 block"></span></label>
                </div>
            </div>
            
            <div class="pt-4 border-t border-gray-100">
                <div class="flex justify-between items-center mb-2">
                    <label class="block text-xs font-bold text-gray-500 uppercase">Milestones (Steps)</label>
                    <button type="button" onclick="addMilestoneInput()" class="text-xs text-sky-500 font-semibold hover:underline">+ Add Step</button>
                </div>
                <div id="milestone-container" class="space-y-2">
                    <input type="text" class="milestone-input w-full bg-gray-50 border border-gray-200 text-sm rounded-lg p-2 outline-none focus:border-sky-400" placeholder="Step 1">
                </div>
            </div>
        </div>

        <!-- FOOTER (Tombol Save Goal tetap menempel di bawah) -->
        <div class="p-5 bg-white border-t border-gray-100 shrink-0">
            <button type="button" onclick="saveGoal()" class="w-full bg-gray-800 hover:bg-gray-900 text-white font-semibold py-3 rounded-xl transition shadow-md">
                Save Goal
            </button>
        </div>
    </div>
</dialog>

<!-- MODAL ADD HABIT -->
<dialog id="modal-add-habit" class="bg-white p-0 rounded-2xl shadow-2xl border border-gray-100 w-[95%] max-w-md m-auto overflow-hidden">
    <div class="p-5 bg-slate-50 border-b border-gray-100 flex justify-between items-center">
        <h3 class="text-lg font-bold text-gray-800">Add New Habit</h3>
        <button onclick="document.getElementById('modal-add-habit').close()" class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-200 text-gray-600 hover:bg-red-100 hover:text-red-500 transition font-bold">&times;</button>
    </div>
    <div class="p-6 space-y-4">
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Habit Name</label>
            <input type="text" id="habit-title" placeholder="e.g. 📚 Read 10 Pages / 📝 Journaling" class="w-full bg-gray-50 border border-gray-200 text-sm rounded-lg p-2.5 outline-none focus:border-emerald-400">
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Category / Focus</label>
            <input type="text" id="habit-category" placeholder="e.g. Personal / IELTS / Health" class="w-full bg-gray-50 border border-gray-200 text-sm rounded-lg p-2.5 outline-none focus:border-emerald-400" value="Personal">
        </div>
        <button type="button" onclick="saveHabit()" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 rounded-xl mt-2 transition shadow-md">Save Habit</button>
    </div>
</dialog>

<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function addMilestoneInput() {
        const container = document.getElementById('milestone-container');
        const input = document.createElement('input');
        input.type = 'text';
        input.className = 'milestone-input w-full bg-gray-50 border border-gray-200 text-sm rounded-lg p-2 outline-none focus:border-sky-400 mt-2';
        input.placeholder = 'Next Step...';
        container.appendChild(input);
        
        // Auto scroll ke bawah tiap kali nambah input baru
        const bodyDiv = document.querySelector('#modal-add-goal .overflow-y-auto');
        setTimeout(() => { bodyDiv.scrollTop = bodyDiv.scrollHeight; }, 50);
    }

    async function saveGoal() {
    const title = document.getElementById('goal-title').value;
    const desc = document.getElementById('goal-desc').value;
    const color = document.querySelector('input[name="goal-color"]:checked').value;
    const milestones = Array.from(document.querySelectorAll('.milestone-input')).map(i => i.value.trim()).filter(v => v !== '');

    if (!title) { alert('Title wajib diisi!'); return; }

    // 1. Kunci tombol agar tidak bisa di-spam klik
    const btn = document.querySelector('button[onclick="saveGoal()"]');
    const originalText = btn.innerHTML;
    btn.innerHTML = 'Saving... ⏳';
    btn.disabled = true;
    btn.classList.add('opacity-50', 'cursor-not-allowed');

    try {
        let res = await fetch('/goals', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ title, description: desc, color, milestones })
        });
        
        if (res.ok) {
            window.location.reload();
        } else {
            alert('Terjadi kesalahan saat menyimpan.');
        }
    } catch (error) {
        alert('Gagal menyambung ke server.');
    } finally {
        // 2. Kembalikan kondisi tombol jika prosesnya gagal (agar bisa diklik lagi)
        if (!res?.ok) {
            btn.innerHTML = originalText;
            btn.disabled = false;
            btn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    }
}

    async function saveHabit() {
        const title = document.getElementById('habit-title').value;
        const category = document.getElementById('habit-category').value;

        if (!title) { alert('Nama habit wajib diisi!'); return; }

        let res = await fetch('/habits', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ title, category })
        });
        if (res.ok) window.location.reload();
    }

    async function toggleHabit(habitId, dateStr, btnElement) {
        let res = await fetch(`/habits/${habitId}/toggle`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ date: dateStr })
        });

        if (res.ok) {
            const isChecked = btnElement.classList.toggle('bg-emerald-500');
            btnElement.classList.toggle('border-emerald-500');
            btnElement.classList.toggle('text-white');
            btnElement.classList.toggle('bg-gray-50');
            btnElement.classList.toggle('border-gray-200');

            if (isChecked) {
                btnElement.innerHTML = `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>`;
            } else {
                btnElement.innerHTML = ``;
            }
        }
    }
</script>

</body>
</html>
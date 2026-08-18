<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Life & Work OS</title>
    @vite(['resources/css/app.css', 'resources/js/today.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body class="flex h-screen overflow-hidden bg-[var(--color-bg)]">

    <!-- Mobile Header -->
    <div class="md:hidden fixed top-0 left-0 right-0 h-16 bg-[var(--color-surface)] border-b border-[var(--color-border)] flex items-center justify-between px-4 z-50">
        <span class="font-semibold text-[var(--color-text)] tracking-tight">LIFE & WORK OS</span>
        <button class="p-2 text-[var(--color-text-muted)]">☰</button>
    </div>

    <!-- Desktop Sidebar -->
    <aside class="hidden md:flex flex-col w-64 bg-[var(--color-surface)] border-r border-[var(--color-border)] h-full">
        <div class="px-8 py-10">
            <h1 class="text-sm font-semibold tracking-widest text-[var(--color-text-muted)] uppercase">Life & Work OS</h1>
        </div>
        <div class="mx-8 border-t border-[var(--color-border)]"></div>
        <nav class="flex-1 px-4 py-8 space-y-2">
            <a href="#" class="flex items-center px-4 py-2.5 rounded-[var(--radius-sm)] bg-[var(--color-bg)] text-[var(--color-text)] font-medium">
                <span class="mr-3 opacity-70">▣</span> Today
            </a>
            <a href="#" class="flex items-center px-4 py-2.5 rounded-[var(--radius-sm)] text-[var(--color-text-muted)] hover:bg-[var(--color-bg)] transition-colors">
                <span class="mr-3 opacity-70">▦</span> Calendar
            </a>
            <a href="#" class="flex items-center px-4 py-2.5 rounded-[var(--radius-sm)] text-[var(--color-text-muted)] hover:bg-[var(--color-bg)] transition-colors">
                <span class="mr-3 opacity-70">⌁</span> Memory
            </a>
        </nav>
        <div class="mx-8 border-t border-[var(--color-border)]"></div>
        <div class="px-4 py-6">
            <a href="#" class="px-4 py-2 text-[var(--color-text-muted)] hover:text-[var(--color-text)] text-sm transition-colors">Settings</a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto px-6 py-8 md:px-12 md:py-10 mt-16 md:mt-0">
        <div class="max-w-3xl mx-auto">
            @yield('content')
        </div>
    </main>

</body>
</html>
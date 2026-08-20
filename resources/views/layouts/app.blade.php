<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Life & Work OS</title>
    
    <!-- PEMANGGILAN VITE (Ini yang bikin CSS menyala!) -->
    @vite(['resources/css/app.css'])
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-[#F7F6F2] text-gray-900 font-sans antialiased min-h-screen">
    
    <main class="p-4 md:p-8">
        @yield('content')
    </main>

</body>
</html>
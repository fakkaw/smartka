<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Admin') — SMARTKA Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
    }
    // Prevent Flash of Unstyled Content (FOUC)
    if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
  </script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Inter', sans-serif; }
    h1,h2,h3 { font-family: 'Plus Jakarta Sans', sans-serif; }
    .admin-link.active { background:#1e3a5f; color:#fff; }
    .admin-link:hover  { background:#1e3a5f; }
  </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900 min-h-screen text-gray-800 dark:text-gray-100 transition-colors duration-200" x-data="{ sidebarOpen: false }">

  {{-- SIDEBAR --}}
  <aside class="fixed top-0 left-0 h-full w-60 bg-gray-900 z-40 flex flex-col
    transform transition-transform duration-300
    md:translate-x-0"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">

    {{-- Logo --}}
    <div class="px-5 py-3 border-b border-gray-700 flex items-center justify-center">
      <img src="{{ asset('logo.png') }}" alt="SMARTKA Logo" class="h-16 w-auto object-contain brightness-0 invert">
    </div>

    {{-- Admin info --}}
    <div class="px-4 py-3 border-b border-gray-700">
      <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 bg-blue-600/30 rounded-full flex items-center justify-center text-sm">
          <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        </div>
        <div>
          <div class="text-white text-xs font-semibold">{{ auth()->user()->name }}</div>
          <div class="text-gray-400 text-[10px]">Administrator</div>
        </div>
      </div>
    </div>

    {{-- Menu --}}
    <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
      @php
        $currentPath = request()->path();
      @endphp

      @foreach([
        ['admin/dashboard',  'dashboard', 'Dashboard',     'admin.dashboard'],
        ['admin/mata-pelajaran', 'subjects', 'Mata Pelajaran', 'admin.mata-pelajaran.index'],
        ['admin/topik',      'topics', 'Topik / Bab',   'admin.topik.index'],
        ['admin/soal',       'questions', 'Bank Soal',      'admin.soal.index'],
        ['admin/paket',      'packages', 'Paket Latihan',  'admin.paket.index'],
        ['admin/pengguna',   'users', 'Pengguna',       'admin.pengguna.index'],
        ['admin/ai-monitor', 'ai', 'AI Monitor',     'admin.ai-monitor.index'],
      ] as [$path, $key, $label, $routeName])
      <a href="{{ route($routeName) }}"
        class="admin-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-gray-300 transition
          {{ str_starts_with($currentPath, $path) ? 'active' : '' }}">
        <span class="w-5 h-5 flex items-center justify-center flex-shrink-0 text-current opacity-80">
          @switch($key)
            @case('dashboard')
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
              @break
            @case('subjects')
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
              @break
            @case('topics')
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
              @break
            @case('questions')
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
              @break
            @case('packages')
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
              @break
            @case('users')
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
              @break
            @case('ai')
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
              @break
          @endswitch
        </span>
        {{ $label }}
      </a>
      @endforeach

      <div class="pt-3 pb-1">
        <div class="text-gray-500 text-xs uppercase tracking-wider px-3 py-1">Pengaturan</div>
      </div>

      <a href="{{ route('home') }}" target="_blank"
        class="admin-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-gray-300 transition">
        <span class="w-5 h-5 flex items-center justify-center flex-shrink-0 text-current opacity-80">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
        </span>
        Lihat Website
      </a>
    </nav>

    {{-- Logout --}}
    <div class="px-3 py-3 border-t border-gray-700">
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-red-400 hover:bg-red-900/30 transition">
          <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
          Keluar
        </button>
      </form>
    </div>
  </aside>

  {{-- Overlay mobile --}}
  <div x-show="sidebarOpen" @click="sidebarOpen = false"
    class="fixed inset-0 bg-black/50 z-30 md:hidden" x-transition></div>

  {{-- MAIN --}}
  <div class="md:ml-60 min-h-screen flex flex-col">

    {{-- Topbar --}}
    <header class="sticky top-0 z-20 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between shadow-sm transition-colors">
      <div class="flex items-center gap-4">
        <button class="md:hidden text-gray-800 dark:text-gray-200" @click="sidebarOpen = true">☰</button>
        <div>
          <h1 class="font-bold text-gray-800 dark:text-gray-100" style="font-family:'Plus Jakarta Sans',sans-serif;">
            @yield('page-title', 'Dashboard')
          </h1>
          <p class="text-gray-400 text-xs">@yield('page-subtitle', '')</p>
        </div>
      </div>
      <div class="flex items-center gap-3">
        <span class="text-xs bg-red-100 text-red-700 px-3 py-1 rounded-full font-bold">ADMIN</span>
        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-sm text-white">👨‍💼</div>
      </div>
    </header>

    {{-- Flash --}}
    @if(session('success'))
    <div class="mx-6 mt-4 bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3 text-sm flex items-center gap-2">
      <span>✅</span> {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="mx-6 mt-4 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm flex items-center gap-2">
      <span>⚠️</span> {{ session('error') }}
    </div>
    @endif

    <main class="flex-1 p-6">
      @yield('content')
    </main>

    <footer class="px-6 py-3 border-t border-gray-200 dark:border-gray-700 text-xs text-gray-400 dark:text-gray-500 text-center transition-colors">
      © {{ date('Y') }} SMARTKA Admin Panel
    </footer>
  </div>
</body>
</html>
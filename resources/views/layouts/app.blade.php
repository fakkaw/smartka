<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Dashboard') — SMARTKA</title>
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
    .sidebar-link.active { background: #eff6ff; color: #1a56db; font-weight: 600; }
    .sidebar-link:hover  { background: #f9fafb; }
    ::-webkit-scrollbar       { width: 5px; }
    ::-webkit-scrollbar-track { background: #f1f1f1; }
    ::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }
  </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 min-h-screen text-gray-800 dark:text-gray-100 transition-colors duration-200" x-data="{ sidebarOpen: false }">

  {{-- ── SIDEBAR ────────────────────────────────── --}}
  <aside class="fixed top-0 left-0 h-full w-60 bg-white dark:bg-gray-800 border-r border-gray-100 dark:border-gray-700 shadow-sm z-40 flex flex-col
    transform transition-transform duration-300
    md:translate-x-0"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">

    {{-- Logo --}}
    <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center justify-center">
      <a href="/" class="flex items-center">
        <img src="{{ asset('logo.png') }}" alt="SMARTKA Logo" class="h-16 w-auto object-contain">
      </a>
    </div>

    {{-- User info --}}
    <div class="px-4 py-4 border-b border-gray-100 dark:border-gray-700 transition-all duration-300 overflow-hidden"
         x-data="{ showProfile: localStorage.getItem('show_sidebar_profile') !== 'hidden' }"
         @sidebar-profile-changed.window="showProfile = $event.detail"
         x-show="showProfile">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center flex-shrink-0">
          <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        </div>
        <div class="min-w-0">
          <div class="font-semibold text-sm text-gray-800 dark:text-gray-100 truncate">{{ auth()->user()->name }}</div>
          <div class="flex items-center gap-1.5 mt-0.5">
            @if(auth()->user()->isPremium())
            <span class="bg-green-100 text-green-700 text-xs font-bold px-2 py-0.5 rounded-full">PREMIUM</span>
            @else
            <span class="bg-gray-100 text-gray-500 text-xs font-medium px-2 py-0.5 rounded-full">FREE</span>
            @endif
            <span class="text-gray-400 dark:text-gray-500 text-xs">Kelas {{ auth()->user()->class_level }}</span>
          </div>
        </div>
      </div>
    </div>

    {{-- Menu --}}
    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
      @foreach([
        ['dashboard',        'Beranda',       route('dashboard'),        'dashboard'],
        ['latihan',          'Latihan Soal',  route('latihan.index'),    'latihan.*'],
        ['tryout',           'Try Out',        route('tryout.index'),     'tryout.*'],
        ['laporan',          'Laporan',        route('laporan.index'),    'laporan.*'],
        ['ai',               'AI Tutor',       route('ai.tutor'),         'ai.*'],
        ['pembahasan',       'Pembahasan',     route('pembahasan.index'), 'pembahasan.*'],
        ['peringkat',        'Peringkat',      route('peringkat.index'),  'peringkat.*'],
        ['akun',             'Pengaturan',     route('akun.show'),        'akun.*'],
      ] as [$key, $label, $href, $pattern])
      <a href="{{ $href }}"
        class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition {{ request()->routeIs($pattern) ? 'active bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-semibold' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-800 dark:hover:text-gray-200' }}">
        <span class="w-5 h-5 flex items-center justify-center flex-shrink-0 text-current opacity-80">
          @switch($key)
            @case('dashboard')
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
              @break
            @case('latihan')
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
              @break
            @case('tryout')
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              @break
            @case('laporan')
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2"/></svg>
              @break
            @case('ai')
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
              @break
            @case('pembahasan')
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
              @break
            @case('peringkat')
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222m4 9.722v-7.5l-4-2.222"/></svg>
              @break
            @case('akun')
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
              @break
          @endswitch
        </span>
        <span>{{ $label }}</span>
      </a>
      @endforeach
    </nav>

    {{-- Upgrade banner (free user) --}}
    @if(!auth()->user()->isPremium())
    <div class="mx-3 mb-3 bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl p-4 text-white">
      <div class="font-bold text-sm mb-1 flex items-center gap-1.5">
        <svg class="w-4 h-4 text-yellow-300" fill="currentColor" viewBox="0 0 20 20"><path d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z"/></svg>
        Upgrade ke Premium
      </div>
      <div class="text-blue-200 text-xs mb-3">Soal unlimited & AI Chat tanpa batas!</div>
      <a href="{{ route('premium') }}" class="block text-center bg-white text-blue-700 text-xs font-bold py-2 rounded-xl hover:bg-blue-50 transition">
        Upgrade Sekarang
      </a>
    </div>
    @endif

    {{-- Logout --}}
    <div class="px-3 py-3 border-t border-gray-100 dark:border-gray-700">
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-red-500 hover:bg-red-50 transition">
          <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
          Keluar
        </button>
      </form>
    </div>
  </aside>

  {{-- Sidebar overlay mobile --}}
  <div x-show="sidebarOpen" @click="sidebarOpen = false"
    class="fixed inset-0 bg-black/40 z-30 md:hidden" x-transition></div>

  {{-- ── MAIN CONTENT ───────────────────────────── --}}
  <div class="md:ml-60 min-h-screen flex flex-col">

    {{-- Top bar --}}
    <header class="sticky top-0 z-20 bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 px-6 py-4 flex items-center justify-between shadow-sm transition-colors">
      <div class="flex items-center gap-4">
        <button class="md:hidden text-gray-600 dark:text-gray-300" @click="sidebarOpen = true">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>
        <div>
          <h1 class="font-bold text-gray-800 dark:text-gray-100 text-base" style="font-family:'Plus Jakarta Sans',sans-serif;">
            @yield('page-title', 'Dashboard')
          </h1>
          <p class="text-gray-400 dark:text-gray-500 text-xs">@yield('page-subtitle', 'Selamat datang kembali!')</p>
        </div>
      </div>
      <div class="flex items-center gap-3">
        {{-- Notifikasi --}}
        <div class="relative" x-data="{ showNotif: false }">
          <button @click="showNotif = !showNotif" @click.outside="showNotif = false" class="w-9 h-9 bg-gray-100 dark:bg-gray-700 rounded-xl flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition relative focus:outline-none">
            🔔
            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full"></span>
          </button>
          
          <div x-show="showNotif" x-transition.opacity
               class="absolute right-0 mt-2 w-72 bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 z-50 overflow-hidden" style="display: none;">
            <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex justify-between items-center">
              <span class="font-bold text-sm text-gray-800 dark:text-gray-100">Notifikasi</span>
              <button class="text-xs text-blue-600 dark:text-blue-400 hover:underline">Tandai dibaca</button>
            </div>
            <div class="max-h-64 overflow-y-auto">
              <div class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-50 dark:border-gray-700/50 cursor-pointer transition">
                <p class="text-sm text-gray-800 dark:text-gray-200">Selamat datang di <strong class="dark:text-white">SMARTKA</strong>! Mari mulai belajarmu hari ini 🚀</p>
                <span class="text-xs text-gray-400 dark:text-gray-500 mt-1 block">Baru saja</span>
              </div>
              <div class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition">
                <p class="text-sm text-gray-800 dark:text-gray-200">Paket latihan soal Pilihan Ganda & Essay terbaru telah dirilis!</p>
                <span class="text-xs text-gray-400 dark:text-gray-500 mt-1 block">2 jam yang lalu</span>
              </div>
            </div>
            <div class="p-3 border-t border-gray-100 dark:border-gray-700 text-center bg-gray-50 dark:bg-gray-900">
              <a href="#" class="text-xs font-semibold text-blue-600 hover:text-blue-800 transition">Lihat Semua Notifikasi</a>
            </div>
          </div>
        </div>
        {{-- Avatar --}}
        <div class="w-9 h-9 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center text-base border border-blue-200 dark:border-blue-800">🧑‍🎓</div>
      </div>
    </header>

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="mx-6 mt-4 bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3 text-sm flex gap-2">
      <span>✅</span> {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="mx-6 mt-4 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm flex gap-2">
      <span>⚠️</span> {{ session('error') }}
    </div>
    @endif

    {{-- Page content --}}
    <main class="flex-1 p-6">
      @yield('content')
    </main>

    {{-- Dalam loop menu, atau tambahkan setelah loop --}}
@if(!auth()->user()->isPremium())
<a href="{{ route('premium') }}"
  class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition
    bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 text-blue-700 dark:text-blue-300 font-semibold border border-blue-200 dark:border-blue-800/50 hover:dark:bg-blue-900/40">
  <span class="text-lg w-6 text-center">⭐</span>
  <span>Upgrade Premium</span>
  <span class="ml-auto text-xs bg-blue-600 text-white px-1.5 py-0.5 rounded font-semibold">HOT</span>
</a>
@endif

    {{-- Footer --}}
    <footer class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 text-xs text-gray-400 dark:text-gray-500 text-center transition-colors">
      © {{ date('Y') }} SMARTKA — Belajar Cerdas, Raih Prestasi Terbaik 🚀
    </footer>
  </div>

  {{-- AI Chat Floating Widget --}}
  @auth
    @include('components.ai-chat-widget')
  @endauth

</body>
</html>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'SMARTKA') — Belajar Cerdas, Raih Prestasi Terbaik</title>
  <meta name="description" content="Platform latihan soal & try out untuk siswa kelas 6, 9, dan 12. Ribuan soal, analisis mendalam, dan bimbingan AI personal.">
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
    h1,h2,h3,h4 { font-family: 'Plus Jakarta Sans', sans-serif; }

    /* Navbar scroll effect */
    .navbar-scrolled {
      background: rgba(255,255,255,0.98) !important;
      box-shadow: 0 1px 20px rgba(0,0,0,0.08) !important;
    }

    /* Animasi float */
    @keyframes float {
      0%, 100% { transform: translateY(0px); }
      50%       { transform: translateY(-12px); }
    }
    .float-anim { animation: float 3s ease-in-out infinite; }
    .float-anim-delay { animation: float 3s ease-in-out infinite 1s; }
    .float-anim-delay2 { animation: float 3s ease-in-out infinite 2s; }

    /* Gradient text */
    .gradient-text {
      background: linear-gradient(135deg, #1a56db, #0e9f6e);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    /* Card hover */
    .feature-card:hover { transform: translateY(-4px); }
    .feature-card { transition: all 0.3s ease; }

    /* Blob background */
    .blob {
      border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
    }

    /* Typing animation */
    @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0} }
    .cursor { animation: blink 1s infinite; }

    /* Smooth fade in */
    @keyframes fadeInUp {
      from { opacity:0; transform:translateY(20px); }
      to   { opacity:1; transform:translateY(0); }
    }
    .fade-in-up { animation: fadeInUp 0.6s ease forwards; }
  </style>
</head>
<body class="bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 overflow-x-hidden transition-colors duration-200">

  @yield('content')

  <script>
    // Navbar scroll effect
    window.addEventListener('scroll', () => {
      const nav = document.getElementById('navbar');
      if (nav) {
        if (window.scrollY > 50) nav.classList.add('navbar-scrolled');
        else nav.classList.remove('navbar-scrolled');
      }
    });
  </script>
</body>
</html>
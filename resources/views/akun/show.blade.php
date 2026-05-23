@extends('layouts.app')

@section('title', 'Pengaturan Akun')
@section('page-title', 'Pengaturan')
@section('page-subtitle', 'Kelola informasi profil, keamanan kata sandi, dan preferensi belajarmu.')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-data="{ tab: 'profil' }">

  {{-- ── LEFT COLUMN: STUDENT PROFILE PROFILE CARD ────────────────── --}}
  <div class="lg:col-span-1 space-y-6">
    <div class="bg-white rounded-2xl border border-gray-100 p-6 text-center shadow-sm dark:bg-gray-800 dark:border-gray-700">
      
      {{-- Avatar Preview Container --}}
      <div class="relative w-28 h-28 mx-auto group">
        @if($user->avatar)
          <img src="{{ asset('storage/' . $user->avatar) }}" 
               alt="{{ $user->name }}" 
               class="w-full h-full object-cover rounded-2xl border-4 border-gray-50 dark:border-gray-700 shadow-sm transition group-hover:scale-105">
        @else
          <div class="w-full h-full bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center text-4xl text-white shadow-inner font-bold select-none transition group-hover:scale-105">
            {{ strtoupper(substr($user->name, 0, 1)) }}
          </div>
        @endif
        
        <div class="absolute -bottom-1.5 -right-1.5 bg-blue-600 text-white p-1.5 rounded-lg text-xs shadow-md border border-white dark:border-gray-800 cursor-pointer" 
             @click="tab = 'profil'; $nextTick(() => $refs.avatarInput.focus())" title="Ganti Foto">
          📷
        </div>
      </div>

      {{-- Name and Badges --}}
      <h3 class="font-bold text-lg text-gray-900 mt-4 dark:text-white">{{ $user->name }}</h3>
      <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ $user->email }}</p>

      <div class="flex flex-wrap items-center justify-center gap-2 mt-3">
        <span class="bg-blue-50 text-blue-700 text-xs font-semibold px-2.5 py-1 rounded-full dark:bg-blue-950/40 dark:text-blue-300">
          Kelas {{ $user->class_level }}
        </span>
        @if($user->isPremium())
          <span class="bg-gradient-to-r from-amber-500 to-yellow-500 text-white text-xs font-extrabold px-3 py-1 rounded-full shadow-sm">
            👑 PREMIUM
          </span>
        @else
          <span class="bg-gray-100 text-gray-500 text-xs font-medium px-2.5 py-1 rounded-full dark:bg-gray-700 dark:text-gray-400">
            🆓 AKUN GRATIS
          </span>
        @endif
      </div>

      {{-- Student Statistics Grid --}}
      <div class="grid grid-cols-3 gap-2 mt-6 pt-6 border-t border-gray-100 dark:border-gray-700"
           x-data="{ totalLogin: 0 }"
           @login-recorded.window="totalLogin = $event.detail"
           x-init="totalLogin = JSON.parse(localStorage.getItem('ep_riwayat') || '[]').length">
        <div class="bg-gray-50 rounded-xl p-2.5 text-center dark:bg-gray-700/50">
          <div class="text-[10px] text-gray-400 dark:text-gray-500 font-medium">Rata-rata</div>
          <div class="text-base font-extrabold text-blue-600 dark:text-blue-400 mt-0.5">
            {{ number_format($user->getAverageScore(), 1) }}
          </div>
        </div>
        <div class="bg-gray-50 rounded-xl p-2.5 text-center dark:bg-gray-700/50">
          <div class="text-[10px] text-gray-400 dark:text-gray-500 font-medium">Soal</div>
          <div class="text-base font-extrabold text-green-600 dark:text-green-400 mt-0.5">
            {{ $user->getTotalAnswered() }}
          </div>
        </div>
        <div class="bg-gray-50 rounded-xl p-2.5 text-center dark:bg-gray-700/50">
          <div class="text-[10px] text-gray-400 dark:text-gray-500 font-medium">Total Login</div>
          <div class="text-base font-extrabold text-amber-600 dark:text-amber-400 mt-0.5" x-text="totalLogin">
            0
          </div>
        </div>
      </div>

      @if(!$user->isPremium())
        <div class="mt-6 bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100 rounded-xl p-4 text-left dark:from-gray-700 dark:to-gray-800 dark:border-gray-600">
          <h4 class="font-bold text-blue-900 text-xs flex items-center gap-1.5 dark:text-blue-400">
            ⭐ Upgrade SMARTKA Premium
          </h4>
          <p class="text-[11px] text-gray-600 dark:text-gray-400 mt-1">
            Dapatkan bank soal lengkap, AI Tutor unlimited, dan pembahasan detail per paket!
          </p>
          <a href="{{ route('premium') }}" class="block text-center bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-2 rounded-lg mt-3 transition shadow-sm">
            Upgrade Sekarang
          </a>
        </div>
      @else
        <div class="mt-6 text-xs text-gray-400 dark:text-gray-500 bg-gray-50 rounded-xl py-3 dark:bg-gray-700/30">
          Langganan aktif hingga: <strong class="text-gray-600 dark:text-gray-300">{{ $user->subscription_ends_at ? $user->subscription_ends_at->translatedFormat('d F Y') : 'Selamanya' }}</strong>
        </div>
      @endif

    </div>
  </div>

  {{-- ── RIGHT COLUMN: SETTINGS TABS & FORMS ────────────────── --}}
  <div class="lg:col-span-2 space-y-6">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden dark:bg-gray-800 dark:border-gray-700">
      
      {{-- Tab Navigation Bar --}}
      <div class="flex border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
        <button @click="tab = 'profil'"
          class="flex-1 py-4 px-6 font-bold text-sm text-center border-b-2 transition"
          :class="tab === 'profil' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-white'">
          🧑‍🎓 Profil Siswa
        </button>
        <button @click="tab = 'password'"
          class="flex-1 py-4 px-6 font-bold text-sm text-center border-b-2 transition"
          :class="tab === 'password' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-white'">
          🔒 Kata Sandi
        </button>
        <button @click="tab = 'preferences'"
          class="flex-1 py-4 px-6 font-bold text-sm text-center border-b-2 transition"
          :class="tab === 'preferences' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-white'">
          ⚙️ Preferensi
        </button>
      </div>

      {{-- Tab Body --}}
      <div class="p-6">
        
        {{-- ── TAB 1: PROFILE FORM ── --}}
        <div x-show="tab === 'profil'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2">
          <form action="{{ route('akun.update-profile') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              {{-- Full Name --}}
              <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Nama Lengkap</label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                  class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('name') border-red-400 @enderror"
                  placeholder="Masukkan nama lengkap">
                @error('name')
                  <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>
                @enderror
              </div>

              {{-- Email --}}
              <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Alamat Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                  class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('email') border-red-400 @enderror"
                  placeholder="name@student.id">
                @error('email')
                  <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>
                @enderror
              </div>

              {{-- Phone Number --}}
              <div>
                <label for="phone" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Nomor WhatsApp / HP</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                  class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('phone') border-red-400 @enderror"
                  placeholder="Contoh: 081234567890">
                @error('phone')
                  <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>
                @enderror
              </div>

              {{-- Class Level --}}
              <div>
                <label for="class_level" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Jenjang Kelas</label>
                <select name="class_level" id="class_level"
                  class="w-full border border-gray-200 rounded-xl px-4 py-3 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('class_level') border-red-400 @enderror">
                  <option value="6" {{ old('class_level', $user->class_level) == 6 ? 'selected' : '' }}>🏫 Kelas 6 SD</option>
                  <option value="9" {{ old('class_level', $user->class_level) == 9 ? 'selected' : '' }}>🏢 Kelas 9 SMP</option>
                  <option value="12" {{ old('class_level', $user->class_level) == 12 ? 'selected' : '' }}>🎓 Kelas 12 SMA</option>
                </select>
                @error('class_level')
                  <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>
                @enderror
              </div>

              {{-- Avatar Upload --}}
              <div>
                <label for="avatar" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Ganti Foto Profil (Avatar)</label>
                <input type="file" name="avatar" id="avatar" x-ref="avatarInput"
                  class="w-full border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-gray-600 cursor-pointer dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 dark:file:bg-blue-950 dark:file:text-blue-300"
                  accept="image/jpeg,image/png,image/jpg,image/webp">
                <span class="block text-[11px] text-gray-400 dark:text-gray-500 mt-1.5">Format: JPG, PNG, WEBP. Maksimum berkas 2 MB.</span>
                @error('avatar')
                  <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>
                @enderror
              </div>
            </div>

            <div class="pt-4 border-t border-gray-100 dark:border-gray-700 flex justify-end">
              <button type="submit" 
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-xl transition shadow-sm">
                💾 Simpan Profil
              </button>
            </div>
          </form>
        </div>

        {{-- ── TAB 2: PASSWORD FORM ── --}}
        <div x-show="tab === 'password'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" style="display:none;">
          <form action="{{ route('akun.update-password') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="max-w-xl space-y-6">
              {{-- Current Password --}}
              <div>
                <label for="current_password" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Kata Sandi Saat Ini</label>
                <input type="password" name="current_password" id="current_password"
                  class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('current_password') border-red-400 @enderror"
                  placeholder="Masukkan kata sandi lama Anda">
                @error('current_password')
                  <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>
                @enderror
              </div>

              {{-- New Password --}}
              <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Kata Sandi Baru</label>
                <input type="password" name="password" id="password"
                  class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('password') border-red-400 @enderror"
                  placeholder="Kata sandi baru (minimal 8 karakter)">
                @error('password')
                  <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>
                @enderror
              </div>

              {{-- Confirm Password --}}
              <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Konfirmasi Kata Sandi Baru</label>
                <input type="password" name="password_confirmation" id="password_confirmation"
                  class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                  placeholder="Masukkan kembali kata sandi baru Anda">
              </div>
            </div>

            <div class="pt-4 border-t border-gray-100 dark:border-gray-700 flex justify-end">
              <button type="submit" 
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-xl transition shadow-sm">
                🔒 Ganti Kata Sandi
              </button>
            </div>
          </form>
        </div>

        {{-- ── TAB 3: PREFERENCES & DARK MODE SYNC ── --}}
        <div x-show="tab === 'preferences'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" style="display:none;"
          x-data="{
            theme: localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'),
            showSidebarProfile: localStorage.getItem('show_sidebar_profile') !== 'hidden',
            emailNotif: true,
            pushNotif: false,
            aiRecommend: true,

            // Toast feedback states
            toastVisible: false,
            toastMessage: '',
            toastIcon: '✅',
            showToast(msg, icon = '✅') {
              this.toastMessage = msg;
              this.toastIcon = icon;
              this.toastVisible = true;
              setTimeout(() => { this.toastVisible = false; }, 3200);
            },

            // Login History states
            riwayat: [],
            filterMode: 'all',

            init() {
              this.setTheme(this.theme);
              
              // Load riwayat from localStorage
              const savedRiwayat = localStorage.getItem('ep_riwayat');
              if (savedRiwayat) {
                this.riwayat = JSON.parse(savedRiwayat);
              } else {
                // Initialize with some dummy entries if completely new
                const now = Date.now();
                this.riwayat = [
                  { id: 'd1', ts: new Date(now - 172800000).toISOString(), device: 'Chrome 124 — Windows 10', ip: '192.168.1.10', status: 'success' },
                  { id: 'd2', ts: new Date(now - 90000000).toISOString(),  device: 'Firefox 125 — macOS',    ip: '10.0.0.22',    status: 'failed'  },
                  { id: 'd3', ts: new Date(now - 14400000).toISOString(),  device: 'Chrome 124 — Android',   ip: '192.168.0.5',  status: 'success' }
                ];
                localStorage.setItem('ep_riwayat', JSON.stringify(this.riwayat));
              }

              // Auto record a new successful login session on page load
              this.recordAutomaticLogin();
            },

            setTheme(mode) {
              this.theme = mode;
              localStorage.setItem('theme', mode);
              if (mode === 'dark') {
                document.documentElement.classList.add('dark');
              } else {
                document.documentElement.classList.remove('dark');
              }
            },
            toggleTheme() {
              this.setTheme(this.theme === 'light' ? 'dark' : 'light');
            },
            toggleSidebarProfile() {
              this.showSidebarProfile = !this.showSidebarProfile;
              localStorage.setItem('show_sidebar_profile', this.showSidebarProfile ? 'visible' : 'hidden');
              window.dispatchEvent(new CustomEvent('sidebar-profile-changed', { detail: this.showSidebarProfile }));
            },

            // Riwayat login logic
            deteksiDevice() {
              const ua = navigator.userAgent;
              let br = 'Browser';
              if (ua.includes('Chrome') && !ua.includes('Edg')) br = 'Chrome';
              else if (ua.includes('Firefox')) br = 'Firefox';
              else if (ua.includes('Safari') && !ua.includes('Chrome')) br = 'Safari';
              else if (ua.includes('Edg')) br = 'Edge';
              let os = 'OS';
              if (ua.includes('Windows')) os = 'Windows';
              else if (ua.includes('Macintosh')) os = 'macOS';
              else if (ua.includes('Android')) os = 'Android';
              else if (ua.includes('iPhone') || ua.includes('iPad')) os = 'iOS';
              else if (ua.includes('Linux')) os = 'Linux';
              return `${br} — ${os}`;
            },

            recordAutomaticLogin() {
              const newEntry = {
                id: 'auto_' + Date.now(),
                ts: new Date().toISOString(),
                device: this.deteksiDevice(),
                ip: `192.168.1.${Math.floor(Math.random()*254+1)}`,
                status: 'success'
              };
              this.riwayat.unshift(newEntry);
              localStorage.setItem('ep_riwayat', JSON.stringify(this.riwayat));
              
              // Broadcast session count to profile stats
              window.dispatchEvent(new CustomEvent('login-recorded', { detail: this.riwayat.length }));
            },

            tambahLogin() {
              const status = Math.random() > 0.2 ? 'success' : 'failed';
              const newEntry = {
                id: 'l_' + Date.now(),
                ts: new Date().toISOString(),
                device: this.deteksiDevice(),
                ip: `10.${Math.floor(Math.random()*255)}.${Math.floor(Math.random()*255)}.${Math.floor(Math.random()*254+1)}`,
                status
              };
              this.riwayat.unshift(newEntry);
              localStorage.setItem('ep_riwayat', JSON.stringify(this.riwayat));
              
              // Broadcast update
              window.dispatchEvent(new CustomEvent('login-recorded', { detail: this.riwayat.length }));
              
              this.showToast(`Login baru dicatat (${status === 'success' ? 'Berhasil' : 'Gagal'})`, status === 'success' ? '✅' : '❌');
            },

            hapusSemua() {
              if (!confirm('Hapus semua riwayat login? Tindakan ini tidak bisa dibatalkan.')) return;
              this.riwayat = [];
              localStorage.setItem('ep_riwayat', JSON.stringify([]));
              
              // Broadcast update
              window.dispatchEvent(new CustomEvent('login-recorded', { detail: 0 }));
              
              this.showToast('Semua riwayat login dihapus.', '🗑️');
            },

            get filteredRiwayat() {
              if (this.filterMode === 'all') return this.riwayat;
              return this.riwayat.filter(x => x.status === this.filterMode);
            },

            get totalCount() { return this.riwayat.length; },
            get successCount() { return this.riwayat.filter(x => x.status === 'success').length; },
            get failedCount() { return this.riwayat.filter(x => x.status === 'failed').length; },

            waktuRelatif(iso) {
              const diff = Math.floor((Date.now() - new Date(iso)) / 1000);
              if (diff < 60) return 'Baru saja';
              if (diff < 3600) return `${Math.floor(diff/60)} mnt lalu`;
              if (diff < 86400) return `${Math.floor(diff/3600)} jam lalu`;
              if (diff < 86400*7) return `${Math.floor(diff/86400)} hari lalu`;
              return new Date(iso).toLocaleDateString('id-ID', {day:'numeric',month:'short',year:'numeric'});
            },

            getDeviceIcon(device) {
              const devIcons = {'Windows':'🖥️','macOS':'🍎','Android':'📱','iOS':'📱','Linux':'🐧'};
              const osName = Object.keys(devIcons).find(k => device.includes(k)) || '';
              return devIcons[osName] || '💻';
            },

            formatDate(iso) {
              return new Date(iso).toLocaleDateString('id-ID',{day:'numeric',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'});
            }
          }">
          <div class="space-y-6">
            
            {{-- Dark Mode Toggle Row --}}
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-100 dark:bg-gray-700/50 dark:border-gray-700">
              <div class="flex items-center gap-3">
                <span class="text-2xl">🌓</span>
                <div>
                  <h4 class="font-bold text-gray-800 dark:text-white text-sm">Mode Tampilan Aplikasi (Dark Mode)</h4>
                  <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Aktifkan tema gelap untuk kenyamanan membaca di malam hari.</p>
                </div>
              </div>
              <div>
                <button type="button" @click="toggleTheme()"
                  class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                  :class="theme === 'dark' ? 'bg-blue-600' : 'bg-gray-200 dark:bg-gray-600'">
                  <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                    :class="theme === 'dark' ? 'translate-x-5' : 'translate-x-0'"></span>
                </button>
              </div>
            </div>

            {{-- Sidebar Profile Visibility Toggle --}}
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-100 dark:bg-gray-700/50 dark:border-gray-700">
              <div class="flex items-center gap-3">
                <span class="text-2xl">👤</span>
                <div>
                  <h4 class="font-bold text-gray-800 dark:text-white text-sm">Profil di Sidebar</h4>
                  <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Tampilkan atau sembunyikan nama, foto, dan info akunmu di menu navigasi kiri.</p>
                </div>
              </div>
              <div class="flex items-center gap-3">
                <span x-text="showSidebarProfile ? 'Tampil' : 'Tersembunyi'" 
                  :class="showSidebarProfile ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-gray-500'"
                  class="text-xs font-semibold transition-colors"></span>
                <button type="button" @click="toggleSidebarProfile()"
                  class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                  :class="showSidebarProfile ? 'bg-blue-600' : 'bg-gray-200 dark:bg-gray-600'">
                  <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                    :class="showSidebarProfile ? 'translate-x-5' : 'translate-x-0'"></span>
                </button>
              </div>
            </div>

            {{-- Notification Switches --}}
            <div class="border-t border-gray-100 dark:border-gray-700 pt-6">
              <h3 class="font-bold text-gray-800 dark:text-white text-sm mb-4">Pengaturan Notifikasi Belajar</h3>
              
              <div class="space-y-4">
                {{-- Switch 1 --}}
                <div class="flex items-center justify-between py-2">
                  <div>
                    <h4 class="font-semibold text-gray-800 dark:text-white text-sm">Pengingat Latihan Soal Harian</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Dapatkan notifikasi dorongan belajar setiap pagi agar konsisten.</p>
                  </div>
                  <div>
                    <button type="button" @click="emailNotif = !emailNotif"
                      class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                      :class="emailNotif ? 'bg-blue-600' : 'bg-gray-200 dark:bg-gray-600'">
                      <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                        :class="emailNotif ? 'translate-x-5' : 'translate-x-0'"></span>
                    </button>
                  </div>
                </div>

                {{-- Switch 2 --}}
                <div class="flex items-center justify-between py-2">
                  <div>
                    <h4 class="font-semibold text-gray-800 dark:text-white text-sm">Laporan Prestasi via WhatsApp</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Kirim salinan ringkasan kemajuan mingguan langsung ke WhatsApp orang tua.</p>
                  </div>
                  <div>
                    <button type="button" @click="pushNotif = !pushNotif"
                      class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                      :class="pushNotif ? 'bg-blue-600' : 'bg-gray-200 dark:bg-gray-600'">
                      <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                        :class="pushNotif ? 'translate-x-5' : 'translate-x-0'"></span>
                    </button>
                  </div>
                </div>

                {{-- Switch 3 --}}
                <div class="flex items-center justify-between py-2">
                  <div>
                    <h4 class="font-semibold text-gray-800 dark:text-white text-sm">Rekomendasi Pembahasan AI SMARTKA</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Ijinkan AI menganalisa bab kelemahanmu secara otomatis dan menyusun modul belajar.</p>
                  </div>
                  <div>
                    <button type="button" @click="aiRecommend = !aiRecommend"
                      class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                      :class="aiRecommend ? 'bg-blue-600' : 'bg-gray-200 dark:bg-gray-600'">
                      <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                        :class="aiRecommend ? 'translate-x-5' : 'translate-x-0'"></span>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            {{-- ─── RIWAYAT LOGIN SECTION (IN PREFERENCES) ─── --}}
            <div class="border-t border-gray-100 dark:border-gray-700 pt-6">
              <div class="flex items-center justify-between mb-4">
                <div>
                  <h3 class="font-bold text-gray-800 dark:text-white text-sm">🕐 Riwayat Aktivitas Login</h3>
                  <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Daftar riwayat sesi masuk aplikasi Anda (disimpan di browser).</p>
                </div>
                <div class="flex gap-2">
                  <button type="button" @click="tambahLogin()"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs px-3 py-2 rounded-xl transition flex items-center gap-1 shadow-sm">
                    <span>➕</span> Catat Login
                  </button>
                  <button type="button" @click="hapusSemua()"
                    class="bg-red-50 hover:bg-red-100 dark:bg-red-950/20 dark:hover:bg-red-950/40 text-red-600 dark:text-red-400 font-semibold text-xs px-3 py-2 rounded-xl border border-red-200 dark:border-red-900/50 transition flex items-center gap-1">
                    <span>🗑️</span> Hapus
                  </button>
                </div>
              </div>

              {{-- Stats & Filter Cards --}}
              <div class="grid grid-cols-3 gap-3 mb-4">
                <div class="bg-blue-50/50 dark:bg-blue-950/30 border border-blue-100/50 dark:border-blue-900/40 rounded-2xl p-3 text-center">
                  <div class="text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-wider font-semibold">Total Login</div>
                  <div class="text-lg font-extrabold text-blue-600 dark:text-blue-400 mt-0.5" x-text="totalCount">0</div>
                </div>
                <div class="bg-green-50/50 dark:bg-green-950/30 border border-green-100/50 dark:border-green-900/40 rounded-2xl p-3 text-center">
                  <div class="text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-wider font-semibold">Berhasil</div>
                  <div class="text-lg font-extrabold text-green-600 dark:text-green-400 mt-0.5" x-text="successCount">0</div>
                </div>
                <div class="bg-red-50/50 dark:bg-red-950/30 border border-red-100/50 dark:border-red-900/40 rounded-2xl p-3 text-center">
                  <div class="text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-wider font-semibold">Gagal</div>
                  <div class="text-lg font-extrabold text-red-600 dark:text-red-400 mt-0.5" x-text="failedCount">0</div>
                </div>
              </div>

              {{-- Filter Tabs --}}
              <div class="flex gap-2 mb-3">
                <button type="button" @click="filterMode = 'all'"
                  class="text-xs font-semibold px-3 py-1.5 rounded-lg border transition-all duration-200"
                  :class="filterMode === 'all' ? 'bg-blue-600 text-white border-blue-600 shadow-sm' : 'bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 border-gray-100 dark:border-gray-700 hover:text-gray-700 dark:hover:text-white'">
                  🔘 Semua
                </button>
                <button type="button" @click="filterMode = 'success'"
                  class="text-xs font-semibold px-3 py-1.5 rounded-lg border transition-all duration-200"
                  :class="filterMode === 'success' ? 'bg-green-600 text-white border-green-600 shadow-sm' : 'bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 border-gray-100 dark:border-gray-700 hover:text-gray-700 dark:hover:text-white'">
                  ✅ Berhasil
                </button>
                <button type="button" @click="filterMode = 'failed'"
                  class="text-xs font-semibold px-3 py-1.5 rounded-lg border transition-all duration-200"
                  :class="filterMode === 'failed' ? 'bg-red-600 text-white border-red-600 shadow-sm' : 'bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 border-gray-100 dark:border-gray-700 hover:text-gray-700 dark:hover:text-white'">
                  ❌ Gagal
                </button>
              </div>

              <div class="text-[11px] text-gray-400 dark:text-gray-500 mb-3" x-text="'Menampilkan ' + filteredRiwayat.length + ' dari ' + totalCount + ' entri'"></div>

              {{-- History Scroll List --}}
              <div class="max-h-60 overflow-y-auto space-y-2 pr-1 custom-scrollbar">
                {{-- Empty State --}}
                <div x-show="filteredRiwayat.length === 0" class="text-center py-8 text-gray-400 dark:text-gray-500">
                  <div class="text-3xl mb-2">📭</div>
                  <p class="text-xs">Belum ada riwayat login yang cocok.</p>
                </div>

                {{-- Template Loop --}}
                <template x-for="item in filteredRiwayat" :key="item.id">
                  <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/30 border border-gray-100 dark:border-gray-700/60 rounded-2xl hover:border-blue-400 transition-colors">
                    {{-- Status Indicator Dot --}}
                    <div class="w-2.5 h-2.5 rounded-full flex-shrink-0"
                      :class="item.status === 'success' ? 'bg-green-500 shadow-[0_0_0_3px_rgba(34,197,94,0.2)]' : 'bg-red-500 shadow-[0_0_0_3px_rgba(239,68,68,0.2)]'">
                    </div>
                    
                    {{-- Icon Status --}}
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center text-sm flex-shrink-0"
                      :class="item.status === 'success' ? 'bg-green-100 text-green-700 dark:bg-green-950/40 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-950/40 dark:text-red-400'">
                      <span x-text="item.status === 'success' ? '🔓' : '🔒'"></span>
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                      <div class="text-xs font-semibold text-gray-800 dark:text-white truncate" x-text="getDeviceIcon(item.device) + ' ' + item.device"></div>
                      <div class="text-[10px] text-gray-500 dark:text-gray-400 mt-0.5">
                        <span x-text="'🌐 ' + item.ip"></span> &nbsp;·&nbsp; <span x-text="'📅 ' + formatDate(item.ts)"></span>
                      </div>
                    </div>

                    {{-- Badge Status --}}
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full flex-shrink-0"
                      :class="item.status === 'success' ? 'bg-green-100 text-green-700 dark:bg-green-950/50 dark:text-green-400 border border-green-200 dark:border-green-900/30' : 'bg-red-100 text-red-700 dark:bg-red-950/50 dark:text-red-400 border border-red-200 dark:border-red-900/30'"
                      x-text="item.status === 'success' ? 'Sukses' : 'Gagal'">
                    </span>

                    {{-- Relative Time --}}
                    <div class="text-[10px] text-gray-400 dark:text-gray-500 text-right min-w-[55px]" x-text="waktuRelatif(item.ts)"></div>
                  </div>
                </template>
              </div>
            </div>

            <div class="pt-6 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center">
              <button type="button" @click="tab = 'profil'"
                class="bg-blue-50 text-blue-700 font-semibold px-6 py-3 rounded-xl transition hover:bg-blue-100 dark:bg-blue-950 dark:text-blue-300">
                ⬅️ Kembali ke Profil
              </button>
              
              {{-- Sun / Moon Theme Toggle in Settings (Bottom Right) --}}
              <div class="flex items-center gap-1 bg-gray-100 dark:bg-gray-700/80 p-1.5 rounded-xl border border-gray-200 dark:border-gray-600 shadow-inner">
                <!-- Light Mode (Sun) -->
                <button type="button" @click="setTheme('light')"
                  class="flex items-center justify-center p-2 rounded-lg transition-all duration-300"
                  :class="theme === 'light' ? 'bg-white text-yellow-500 shadow-md scale-105' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300'"
                  title="Mode Terang (Light Mode)">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                  </svg>
                </button>
                <!-- Dark Mode (Moon) -->
                <button type="button" @click="setTheme('dark')"
                  class="flex items-center justify-center p-2 rounded-lg transition-all duration-300"
                  :class="theme === 'dark' ? 'bg-gray-800 text-indigo-400 shadow-md scale-105 border border-gray-700' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300'"
                  title="Mode Gelap (Dark Mode)">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                  </svg>
                </button>
              </div>
            </div>

          </div>

          {{-- Toast Notification popup --}}
          <div x-show="toastVisible" 
               x-transition:enter="transition ease-out duration-300"
               x-transition:enter-start="opacity-0 translate-y-4"
               x-transition:enter-end="opacity-100 translate-y-0"
               x-transition:leave="transition ease-in duration-200"
               x-transition:leave-start="opacity-100 translate-y-0"
               x-transition:leave-end="opacity-0 translate-y-4"
               class="fixed bottom-6 right-6 bg-gray-900 text-white dark:bg-white dark:text-gray-900 px-4 py-3 rounded-xl shadow-xl flex items-center gap-2.5 z-50 text-xs font-semibold border border-gray-800 dark:border-gray-100"
               style="display: none;">
            <span x-text="toastIcon"></span>
            <span x-text="toastMessage"></span>
          </div>

        </div>

      </div>
    </div>
  </div>

</div>
@endsection

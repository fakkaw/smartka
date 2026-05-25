@extends('layouts.app')
@section('title', 'AI Tutor')
@section('page-title', 'Smartka AI Tutor')
@section('page-subtitle', 'Didukung Google Gemini — Tanya apapun tentang pelajaranmu!')

@section('content')
<div class="h-[calc(100vh-140px)] flex gap-4"
  x-data="aiChat()"
  x-init="init()">

  {{-- ── SIDEBAR RIWAYAT ─────────────────────────── --}}
  <div class="w-64 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm flex flex-col hidden md:flex">
    <div class="p-4 border-b border-gray-100 dark:border-gray-700">
      <button @click="newChat()"
        class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition flex items-center justify-center gap-2">
        <span>✏️</span> Chat Baru
      </button>
    </div>

    {{-- Kuota (free user) --}}
    @if(!auth()->user()->isPremium())
    <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 bg-amber-50 dark:bg-amber-900/20">
      <div class="flex justify-between text-xs text-gray-600 dark:text-gray-400 mb-1.5">
        <span>Kuota hari ini</span>
        <span class="font-bold {{ $aiQuota <= 1 ? 'text-red-500' : 'text-green-600' }}">
          {{ $aiQuota }}/5
        </span>
      </div>
      <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
        <div class="h-1.5 rounded-full {{ $aiQuota <= 1 ? 'bg-red-500' : 'bg-green-500' }}"
          style="width: {{ ($aiQuota / 5) * 100 }}%"></div>
      </div>
      @if($aiQuota === 0)
      <a href="#" class="mt-2 block text-center text-xs text-blue-600 font-semibold hover:underline">
        Upgrade untuk tanya tanpa batas →
      </a>
      @endif
    </div>
    @else
    <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-700 bg-green-50 dark:bg-green-900/20">
      <div class="text-xs text-green-600 dark:text-green-500 flex items-center gap-1 font-semibold">
        <span>✓</span> Premium — pertanyaan tanpa batas
      </div>
    </div>
    @endif

    {{-- List sessions --}}
    <div class="flex-1 overflow-y-auto py-2">
      <div class="px-3 py-1.5 text-xs font-semibold text-gray-400 uppercase tracking-wide">
        Riwayat Chat
      </div>
      <template x-if="sessions.length === 0">
        <div class="px-4 py-6 text-center text-gray-400 text-xs">
          Belum ada riwayat chat
        </div>
      </template>
      <template x-for="s in sessions" :key="s.id">
        <button @click="loadSession(s.id)"
          class="w-full text-left px-3 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-700 transition rounded-lg mx-1 group"
          :class="activeSessionId === s.id ? 'bg-blue-50 dark:bg-gray-700' : ''">
          <div class="text-sm font-medium text-gray-700 dark:text-gray-300 truncate group-hover:text-blue-600 dark:group-hover:text-blue-400"
            :class="activeSessionId === s.id ? 'text-blue-600' : ''"
            x-text="s.title || 'Chat tanpa judul'"></div>
          <div class="text-xs text-gray-400 mt-0.5" x-text="s.message_count + ' pesan'"></div>
        </button>
      </template>
    </div>
  </div>

  {{-- ── AREA CHAT UTAMA ──────────────────────────── --}}
  <div class="flex-1 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm flex flex-col overflow-hidden">

    {{-- Header chat --}}
    <div class="bg-blue-600 px-5 py-4 flex items-center gap-3 flex-shrink-0">
      <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center text-xl">🤖</div>
      <div class="flex-1">
        <div class="text-white font-bold">Smartka AI</div>
        <div class="text-blue-200 text-xs flex items-center gap-1">
          <span class="w-2 h-2 bg-green-400 rounded-full inline-block animate-pulse"></span>
          Online — Didukung Google Gemini
        </div>
      </div>
      <div class="flex items-center gap-2">
        <span class="text-xs bg-white/20 text-white px-3 py-1 rounded-full">
          ✨ Gemini AI
        </span>
        {{-- Mobile: tombol riwayat --}}
        <button class="md:hidden text-white/80 hover:text-white">📋</button>
      </div>
    </div>

    {{-- Progress bar kuota tipis --}}
    @if(!auth()->user()->isPremium())
    <div class="h-0.5 bg-gray-100 dark:bg-gray-700">
      <div class="h-full bg-green-500 transition-all"
        :style="'width: ' + (quota / 5 * 100) + '%'"
        :class="quota <= 1 ? 'bg-red-500' : 'bg-green-500'">
      </div>
    </div>
    @endif

    {{-- Area pesan --}}
    <div class="flex-1 overflow-y-auto p-5 space-y-4" id="messages-container"
      x-ref="messagesContainer">

      {{-- Welcome state (belum ada pesan) --}}
      <template x-if="messages.length === 0">
        <div class="flex flex-col items-center justify-center h-full py-10 text-center">
          <div class="text-6xl mb-4">🤖</div>
          <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-2"
            style="font-family:'Plus Jakarta Sans',sans-serif;">
            Halo, {{ auth()->user()->name }}! 👋
          </h3>
          <p class="text-gray-500 text-sm max-w-sm mb-6">
            Saya Smartka AI, siap membantu belajarmu.
            Tanya soal apapun, minta penjelasan materi, atau upload foto soal!
          </p>

          {{-- Quick chips --}}
          <div class="flex flex-wrap gap-2 justify-center">
            @foreach([
              '📐 Jelaskan limit fungsi',
              '🔬 Rumus fisika gerak',
              '📚 Contoh soal SPLDV',
              '💡 Tips belajar efektif',
            ] as $chip)
            <button @click="sendQuickMessage('{{ $chip }}')"
              class="bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 text-blue-700 dark:text-blue-400 text-sm px-4 py-2 rounded-full transition border border-blue-200 dark:border-blue-800">
              {{ $chip }}
            </button>
            @endforeach
          </div>
        </div>
      </template>

      {{-- Daftar pesan --}}
      <template x-for="(msg, index) in messages" :key="index">
        <div>
          {{-- Pesan user --}}
          <template x-if="msg.role === 'user'">
            <div class="flex justify-end gap-3">
              <div class="max-w-lg">
                {{-- Gambar jika ada --}}
                <template x-if="msg.image_url">
                  <img :src="msg.image_url" class="rounded-xl mb-2 max-w-xs" alt="Gambar soal">
                </template>
                <div class="bg-blue-600 text-white text-sm rounded-2xl rounded-tr-sm px-4 py-3 leading-relaxed"
                  x-text="msg.content">
                </div>
                <div class="text-right text-xs text-gray-400 mt-1" x-text="msg.time"></div>
              </div>
              <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/50 rounded-full flex items-center justify-center text-sm flex-shrink-0">
                🧑‍🎓
              </div>
            </div>
          </template>

          {{-- Pesan AI --}}
          <template x-if="msg.role === 'model'">
            <div class="flex gap-3">
              <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-sm flex-shrink-0">
                🤖
              </div>
              <div class="max-w-lg flex-1">
                <div class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-sm rounded-2xl rounded-tl-sm px-4 py-3 leading-relaxed prose prose-sm max-w-none"
                  x-html="msg.html || msg.content">
                </div>
                {{-- Action buttons --}}
                <div class="flex items-center gap-3 mt-1.5">
                  <span class="text-xs text-gray-400" x-text="msg.time"></span>
                  <button @click="copyMessage(msg.content)"
                    class="text-xs text-gray-400 hover:text-gray-600 transition">
                    📋 Salin
                  </button>
                  <button @click="feedback(msg.id, 'helpful')"
                    class="text-xs transition"
                    :class="msg.feedback === 'helpful' ? 'text-green-600' : 'text-gray-400 hover:text-green-600'">
                    👍 Membantu
                  </button>
                  <button @click="feedback(msg.id, 'not_helpful')"
                    class="text-xs transition"
                    :class="msg.feedback === 'not_helpful' ? 'text-red-500' : 'text-gray-400 hover:text-red-500'">
                    👎
                  </button>
                </div>
              </div>
            </div>
          </template>
        </div>
      </template>

      {{-- Typing indicator --}}
      <template x-if="loading">
        <div class="flex gap-3">
          <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-sm">🤖</div>
          <div class="bg-gray-100 dark:bg-gray-700 rounded-2xl rounded-tl-sm px-4 py-3 flex items-center gap-1.5">
            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay:0.15s"></div>
            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay:0.3s"></div>
          </div>
        </div>
      </template>

      {{-- Modal limit reached --}}
      <template x-if="limitReached">
        <div class="flex gap-3">
          <div class="w-8 h-8 bg-gray-400 rounded-full flex items-center justify-center text-sm">🤖</div>
          <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-2xl rounded-tl-sm px-4 py-4 max-w-sm">
            <div class="font-bold text-amber-800 dark:text-amber-500 text-sm mb-1">
              Kuota harian habis 😢
            </div>
            <p class="text-amber-700 dark:text-amber-400 text-xs mb-3">
              Kamu sudah menggunakan 5 pertanyaan gratis hari ini.
              Upgrade ke Premium untuk tanya tanpa batas!
            </p>
            <a href="#"
              class="block text-center bg-blue-600 text-white text-xs font-bold py-2 rounded-xl hover:bg-blue-700 transition">
              Upgrade ke Premium — Rp 79.000/bulan
            </a>
            <button @click="limitReached = false"
              class="mt-2 w-full text-center text-xs text-gray-500 hover:text-gray-700">
              Ingatkan besok
            </button>
          </div>
        </div>
      </template>
    </div>

    {{-- Preview gambar sebelum kirim --}}
    <template x-if="imagePreview">
      <div class="px-5 py-2 border-t border-gray-100 dark:border-gray-700 flex items-center gap-3">
        <img :src="imagePreview" class="h-16 rounded-lg object-cover" alt="Preview">
        <div class="text-xs text-gray-500 flex-1">Gambar siap dikirim</div>
        <button @click="removeImage()" class="text-red-500 hover:text-red-700 text-sm">✕ Hapus</button>
      </div>
    </template>

    {{-- Input bar --}}
    <div class="border-t border-gray-100 dark:border-gray-700 p-4 flex-shrink-0">
      <div class="flex items-end gap-3">
        {{-- Upload gambar --}}
        <label class="flex-shrink-0 w-10 h-10 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-xl flex items-center justify-center cursor-pointer transition text-gray-500 dark:text-gray-400"
          :class="quota <= 0 && !isPremium ? 'opacity-50 cursor-not-allowed' : ''">
          📷
          <input type="file" accept="image/*" class="hidden" x-ref="fileInput"
            @change="handleImageSelect($event)"
            :disabled="quota <= 0 && !isPremium">
        </label>

        {{-- Input teks --}}
        <div class="flex-1 relative">
          <textarea
            x-model="input"
            x-ref="inputArea"
            @keydown.enter.prevent="!$event.shiftKey && sendMessage()"
            @keydown.shift.enter="input += '\n'"
            @input="autoResize($refs.inputArea)"
            placeholder="Tanya apapun tentang pelajaranmu... (Enter = kirim, Shift+Enter = baris baru)"
            :disabled="loading || (quota <= 0 && !isPremium)"
            rows="1"
            class="w-full border border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none transition disabled:bg-gray-50 dark:disabled:bg-gray-900 disabled:text-gray-400 dark:disabled:text-gray-600"
            style="max-height: 120px; overflow-y: auto;"
          ></textarea>
        </div>

        {{-- Tombol kirim --}}
        <button @click="sendMessage()"
          :disabled="!input.trim() || loading || (quota <= 0 && !isPremium)"
          class="flex-shrink-0 w-10 h-10 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-300 text-white rounded-xl flex items-center justify-center transition">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
          </svg>
        </button>
      </div>

      {{-- Info bawah --}}
      <div class="flex justify-between items-center mt-2 px-1">
        <span class="text-xs text-gray-400">Enter untuk kirim · Shift+Enter untuk baris baru</span>
        @if(!auth()->user()->isPremium())
        <span class="text-xs font-semibold"
          :class="quota <= 1 ? 'text-red-500' : 'text-gray-400'"
          x-text="quota + '/5 pertanyaan gratis tersisa hari ini'">
        </span>
        @endif
      </div>
    </div>
  </div>
</div>

{{-- ── ALPINE.JS LOGIC ───────────────────────────────── --}}
<script>
function aiChat() {
  return {
    messages:        [],
    input:           '',
    loading:         false,
    activeSessionId: null,
    sessions:        [],
    imagePreview:    null,
    imageFile:       null,
    limitReached:    false,
    quota:           {{ $aiQuota }},
    isPremium:       {{ auth()->user()->isPremium() ? 'true' : 'false' }},

    // ── Init ──────────────────────────────────────────
    async init() {
      await this.loadSessions();
    },

    // ── Load daftar session ───────────────────────────
    async loadSessions() {
      try {
        const res  = await fetch('{{ route('ai.sessions') }}');
        const data = await res.json();
        this.sessions = data;
      } catch (e) {
        console.error('Failed to load sessions', e);
      }
    },

    // ── Load session tertentu ─────────────────────────
    async loadSession(sessionId) {
      try {
        const res  = await fetch(`{{ url('ai/sessions') }}/${sessionId}`);
        const data = await res.json();

        this.activeSessionId = sessionId;
        this.messages        = data.messages.map(m => ({
          id:       m.id,
          role:     m.role,
          content:  m.content,
          html:     this.renderMarkdown(m.content),
          feedback: m.feedback,
          time:     this.formatTime(m.created_at),
        }));

        this.$nextTick(() => this.scrollToBottom());
      } catch (e) {
        console.error('Failed to load session', e);
      }
    },

    // ── Chat baru ─────────────────────────────────────
    newChat() {
      this.messages        = [];
      this.activeSessionId = null;
      this.input           = '';
      this.imagePreview    = null;
      this.imageFile       = null;
      this.limitReached    = false;
    },

    // ── Kirim pesan cepat dari chip ───────────────────
    sendQuickMessage(text) {
      this.input = text;
      this.sendMessage();
    },

    // ── Kirim pesan ───────────────────────────────────
    async sendMessage() {
      if (!this.input.trim() && !this.imageFile) return;
      if (this.loading) return;

      // Cek kuota free user
      if (!this.isPremium && this.quota <= 0) {
        this.limitReached = true;
        return;
      }

      const userMsg = this.input.trim();
      const now     = new Date().toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'});

      // Tambah bubble user
      this.messages.push({
        role:      'user',
        content:   userMsg,
        image_url: this.imagePreview,
        time:      now,
      });

      this.input    = '';
      this.loading  = true;

      this.$nextTick(() => {
        this.scrollToBottom();
        this.autoResize(this.$refs.inputArea);
      });

      // Bangun FormData
      const formData = new FormData();
      formData.append('message',    userMsg);
      formData.append('_token',     '{{ csrf_token() }}');

      if (this.activeSessionId) {
        formData.append('session_id', this.activeSessionId);
      }
      if (this.imageFile) {
        formData.append('image', this.imageFile);
      }

      // Reset gambar
      this.imagePreview = null;
      this.imageFile    = null;
      if (this.$refs.fileInput) this.$refs.fileInput.value = '';

      try {
        const res  = await fetch('{{ route('ai.send') }}', {
          method: 'POST',
          body:   formData,
        });

        const data = await res.json();

        if (!res.ok) {
          if (data.error === 'limit_reached') {
            this.limitReached = true;
            this.quota        = 0;
          } else {
            this.messages.push({
              role:    'model',
              content: '⚠️ ' + (data.message || 'Terjadi kesalahan. Coba lagi ya!'),
              html:    '⚠️ ' + (data.message || 'Terjadi kesalahan. Coba lagi ya!'),
              time:    now,
            });
          }
          return;
        }

        // Update session
        if (!this.activeSessionId) {
          this.activeSessionId = data.session_id;
          await this.loadSessions();
        }

        // Update kuota
        if (data.remaining !== null && data.remaining !== undefined) {
          this.quota = data.remaining;
        }

        // Tambah bubble AI
        this.messages.push({
          id:      data.message_id,
          role:    'model',
          content: data.reply,
          html:    this.renderMarkdown(data.reply),
          time:    new Date().toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'}),
        });

      } catch (e) {
        this.messages.push({
          role:    'model',
          content: '⚠️ Koneksi bermasalah. Periksa internetmu dan coba lagi.',
          html:    '⚠️ Koneksi bermasalah. Periksa internetmu dan coba lagi.',
          time:    now,
        });
      } finally {
        this.loading = false;
        this.$nextTick(() => this.scrollToBottom());
      }
    },

    // ── Feedback ──────────────────────────────────────
    async feedback(messageId, type) {
      if (!messageId) return;
      try {
        await fetch(`{{ url('ai/chat') }}/${messageId}/feedback`, {
          method:  'POST',
          headers: {
            'Content-Type':     'application/json',
            'X-CSRF-TOKEN':     '{{ csrf_token() }}',
          },
          body: JSON.stringify({ feedback: type }),
        });

        const msg = this.messages.find(m => m.id === messageId);
        if (msg) msg.feedback = type;
      } catch (e) {
        console.error('Feedback failed', e);
      }
    },

    // ── Salin pesan ───────────────────────────────────
    copyMessage(text) {
      navigator.clipboard.writeText(text).then(() => {
        alert('Teks berhasil disalin!');
      });
    },

    // ── Handle pilih gambar ───────────────────────────
    handleImageSelect(event) {
      const file = event.target.files[0];
      if (!file) return;

      if (file.size > 4 * 1024 * 1024) {
        alert('Ukuran gambar maksimal 4MB ya!');
        return;
      }

      this.imageFile = file;
      const reader   = new FileReader();
      reader.onload  = (e) => { this.imagePreview = e.target.result; };
      reader.readAsDataURL(file);
    },

    // ── Hapus gambar ──────────────────────────────────
    removeImage() {
      this.imagePreview = null;
      this.imageFile    = null;
      if (this.$refs.fileInput) this.$refs.fileInput.value = '';
    },

    // ── Scroll ke bawah ───────────────────────────────
    scrollToBottom() {
      const container = document.getElementById('messages-container');
      if (container) container.scrollTop = container.scrollHeight;
    },

    // ── Auto resize textarea ──────────────────────────
    autoResize(el) {
      if (!el) return;
      el.style.height = 'auto';
      el.style.height = Math.min(el.scrollHeight, 120) + 'px';
    },

    // ── Format waktu ──────────────────────────────────
    formatTime(dateStr) {
      if (!dateStr) return '';
      return new Date(dateStr).toLocaleTimeString('id-ID', {
        hour: '2-digit', minute: '2-digit'
      });
    },

    // ── Render markdown sederhana ─────────────────────
    renderMarkdown(text) {
      if (!text) return '';
      return text
        // Bold
        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
        // Italic
        .replace(/\*(.*?)\*/g, '<em>$1</em>')
        // Code inline
        .replace(/`([^`]+)`/g, '<code class="bg-gray-200 px-1 rounded text-xs">$1</code>')
        // Headers
        .replace(/^### (.*$)/gm, '<h3 class="font-bold text-base mt-3 mb-1">$1</h3>')
        .replace(/^## (.*$)/gm,  '<h2 class="font-bold text-lg mt-3 mb-1">$1</h2>')
        // Bullet list
        .replace(/^[-•] (.*$)/gm, '<li class="ml-4 list-disc">$1</li>')
        // Numbered list
        .replace(/^\d+\. (.*$)/gm, '<li class="ml-4 list-decimal">$1</li>')
        // Line breaks
        .replace(/\n\n/g, '</p><p class="mt-2">')
        .replace(/\n/g, '<br>');
    },
  }
}
</script>
@endsection
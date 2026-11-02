<template>
  <Head title="Masuk - Sistem Koordinasi UP Sengkang" />

  <div class="min-h-[100dvh] w-full flex flex-col md:flex-row relative bg-slate-950 font-sans antialiased overflow-x-hidden">
    
    <!-- ============================================================== -->
    <!-- HERO BACKGROUND (DESKTOP) & BANNER ATAS (MOBILE)               -->
    <!-- ============================================================== -->
    <div class="relative w-full md:absolute md:inset-0 md:h-full z-0 overflow-hidden shrink-0">
      <!-- Background Image Pembangkit Listrik UP Sengkang (High Definition) -->
      <img 
        :src="$page.props.branding?.login_bg_url || '/assets/bumn/bg-sengkang-hd.jpg'" 
        alt="PLN Nusantara Power UP Sengkang" 
        class="w-full h-48 md:h-full object-cover object-center opacity-80 md:opacity-90 transition-opacity duration-500"
        @error="$event.target.src = '/assets/bumn/bg-sengkang.png'"
      />
      <!-- Gradient Overlays: Deep Sky to Slate Dark -->
      <div class="absolute inset-0 bg-gradient-to-b md:bg-gradient-to-l from-slate-950/90 via-slate-900/60 to-slate-950 md:to-transparent"></div>
      
      <!-- Mobile Hero Content (Header Visual di HP) -->
      <div class="md:hidden absolute inset-0 flex flex-col justify-center items-center text-center px-6 pt-safe">
        <div class="w-14 h-14 bg-white/10 backdrop-blur-md rounded-2xl p-2.5 border border-white/20 shadow-xl flex items-center justify-center mb-2.5">
          <img :src="$page.props.branding?.logo_url || '/assets/bumn/logo-pln.png'" alt="Logo PLN" class="w-full h-full object-contain drop-shadow-md rounded-lg" />
        </div>
        <p class="text-sky-300 text-[10px] font-extrabold tracking-widest uppercase">PLN Nusantara Power</p>
        <h1 class="text-white text-lg font-black leading-tight drop-shadow">UP Sengkang</h1>
        <p class="text-slate-300 text-[11px] font-medium mt-0.5 opacity-90">Sistem Koordinasi & Eskalasi Masalah Operasi</p>
      </div>

      <!-- Desktop Slogan Kanan Bawah & Copyright -->
      <div class="absolute bottom-12 right-12 text-right hidden md:block z-10 max-w-lg">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sky-500/20 border border-sky-400/30 text-sky-300 text-xs font-bold mb-4 backdrop-blur-md">
          <span class="w-2 h-2 rounded-full bg-sky-400 animate-pulse"></span>
          <span>Keandalan Pembangkitan Listrik</span>
        </div>
        <h2 class="text-white text-3xl lg:text-4xl font-extrabold leading-tight drop-shadow-2xl">
          Kolaborasi Hari Ini<br />
          untuk Energi<br />
          Masa Depan
        </h2>
        <p class="text-sky-200/90 text-sm mt-3 font-semibold tracking-wide drop-shadow">
          PLN Nusantara Power — Unit Pembangkitan Sengkang
        </p>
        <div class="mt-4 pt-3 border-t border-white/15 flex items-center justify-end gap-2 text-xs text-sky-200/70 font-medium drop-shadow">
          <span>&copy; {{ new Date().getFullYear() }} PLN NP UP Sengkang</span>
          <span>&bull;</span>
          <span class="text-white font-semibold">devATP</span>
        </div>
      </div>
    </div>

    <!-- ============================================================== -->
    <!-- FORM LOGIN CARD: MOBILE ELEVATED SHEET / DESKTOP SIDE PANEL     -->
    <!-- ============================================================== -->
    <div 
      class="w-full md:w-[46%] lg:w-[38%] min-h-[calc(100dvh-12rem)] md:min-h-screen bg-white relative z-10 flex flex-col justify-between shadow-2xl md:shadow-[25px_0_35px_rgba(0,0,0,0.2)] rounded-t-[32px] md:rounded-t-none md:rounded-r-[40px] -mt-4 md:mt-0 overflow-y-auto"
    >
      
      <!-- Konten Utama Form -->
      <div class="w-full max-w-sm mx-auto px-6 sm:px-8 pt-7 sm:pt-10 pb-6 flex-1 flex flex-col justify-center">
        
        <!-- Header Desktop (Hanya muncul di Layar Komputer) -->
        <div class="hidden md:flex items-center gap-3.5 mb-8">
          <div class="w-12 h-12 bg-sky-50 rounded-2xl p-2 border border-sky-100 shadow-2xs flex items-center justify-center shrink-0">
            <img :src="$page.props.branding?.logo_url || '/assets/bumn/logo-pln.png'" alt="Logo PLN" class="w-full h-full object-contain" />
          </div>
          <div class="leading-tight">
            <div class="text-sky-600 font-extrabold text-base tracking-wide flex items-center gap-1.5">
              <span>PLN NUSANTARA POWER</span>
            </div>
            <div class="text-slate-500 text-xs font-semibold">Unit Pembangkitan Sengkang</div>
          </div>
        </div>

        <!-- Judul Form (Responsive) -->
        <div class="mb-5 sm:mb-6">
          <h2 class="text-xl sm:text-2xl font-black text-slate-800 tracking-tight leading-tight">
            Masuk ke Sistem
          </h2>
          <p class="text-slate-500 text-xs sm:text-xs font-medium mt-1">
            Gunakan Nomor Induk Dokumen (NID) Anda untuk masuk ke sistem.
          </p>
        </div>

        <!-- Alert Banner Kesalahan (Reaktif Tanpa Refresh) -->
        <transition
          enter-active-class="transition duration-250 ease-out"
          enter-from-class="opacity-0 -translate-y-2 scale-95"
          enter-to-class="opacity-100 translate-y-0 scale-100"
          leave-active-class="transition duration-150 ease-in"
          leave-from-class="opacity-100 scale-100"
          leave-to-class="opacity-0 -translate-y-2 scale-95"
        >
          <div 
            v-if="errorMessage" 
            class="mb-4 p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs flex items-start gap-3 shadow-2xs"
            role="alert"
          >
            <div class="w-7 h-7 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0 mt-0.5 text-xs">
              <i class="fas fa-circle-exclamation"></i>
            </div>
            <div class="flex-1 min-w-0 pt-0.5">
              <div class="font-bold text-rose-900 text-xs">Gagal Masuk</div>
              <p class="text-[11px] text-rose-700 mt-0.5 leading-relaxed">{{ errorMessage }}</p>
            </div>
            <button 
              type="button" 
              @click="errorMessage = ''" 
              class="text-rose-400 hover:text-rose-700 p-1 text-xs cursor-pointer rounded-lg hover:bg-rose-100/60 transition"
              title="Tutup Pesan"
            >
              <i class="fas fa-times"></i>
            </button>
          </div>
        </transition>

        <!-- Form Login -->
        <form @submit.prevent="submit" class="space-y-4" novalidate>
          
          <!-- Input 1: NID Pegawai -->
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center justify-between">
              <span class="flex items-center gap-1.5">
                <i class="fas fa-id-card text-sky-600 text-[11px]"></i>
                <span>Nomor Induk Dokumen (NID)</span>
              </span>
              <span class="text-[10px] text-slate-400 font-normal">NID Karyawan</span>
            </label>
            <div class="relative">
              <input 
                type="text" 
                v-model="form.nid" 
                @input="clearError"
                placeholder="Contoh: 8108050JA" 
                autocomplete="username"
                autocapitalize="characters"
                autocorrect="off"
                spellcheck="false"
                required 
                class="w-full border rounded-xl pl-3.5 pr-4 py-3 sm:py-2.5 text-base sm:text-xs text-slate-800 outline-none transition shadow-2xs placeholder:text-slate-400 uppercase"
                :class="errorMessage ? 'border-rose-300 bg-rose-50/20 focus:border-rose-500 focus:ring-1 focus:ring-rose-500' : 'border-slate-200 bg-slate-50/60 focus:bg-white focus:border-sky-500 focus:ring-1 focus:ring-sky-500'"
              />
            </div>
          </div>

          <!-- Input 2: Kata Sandi dengan Toggle Show/Hide -->
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1.5">
              <i class="fas fa-lock text-sky-600 text-[11px]"></i>
              <span>Kata Sandi</span>
            </label>
            <div class="relative">
              <input 
                :type="showPassword ? 'text' : 'password'" 
                v-model="form.password" 
                @input="clearError"
                placeholder="Masukkan kata sandi" 
                autocomplete="current-password"
                required 
                class="w-full border rounded-xl pl-3.5 pr-11 py-3 sm:py-2.5 text-base sm:text-xs text-slate-800 outline-none transition shadow-2xs placeholder:text-slate-400"
                :class="errorMessage ? 'border-rose-300 bg-rose-50/20 focus:border-rose-500 focus:ring-1 focus:ring-rose-500' : 'border-slate-200 bg-slate-50/60 focus:bg-white focus:border-sky-500 focus:ring-1 focus:ring-sky-500'"
              />
              <!-- Tombol Intip Password (Sangat Berguna di HP) -->
              <button 
                type="button" 
                @click="showPassword = !showPassword"
                tabindex="-1"
                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-2 text-xs cursor-pointer transition active:scale-90"
                :title="showPassword ? 'Sembunyikan Kata Sandi' : 'Lihat Kata Sandi'"
              >
                <i :class="showPassword ? 'fas fa-eye-slash text-sky-600' : 'fas fa-eye'"></i>
              </button>
            </div>
          </div>

          <!-- Remember Me Checkbox -->
          <div class="flex items-center justify-between pt-0.5">
            <label class="flex items-center gap-2.5 text-xs text-slate-600 cursor-pointer select-none">
              <input 
                type="checkbox" 
                v-model="form.remember" 
                class="w-4 h-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500 cursor-pointer" 
              />
              <span class="font-medium text-slate-700">Ingat sesi saya</span>
            </label>
          </div>

          <!-- Tombol Masuk Utama (Touch Friendly & Anti Double-Click) -->
          <button 
            type="submit" 
            :disabled="form.processing" 
            class="w-full bg-sky-600 hover:bg-sky-700 active:scale-[0.98] text-white rounded-xl py-3.5 sm:py-2.5 text-sm sm:text-xs font-bold shadow-md hover:shadow-lg transition-all mt-3 flex items-center justify-center gap-2 cursor-pointer disabled:opacity-60 disabled:pointer-events-none disabled:cursor-not-allowed"
          >
            <i v-if="form.processing" class="fas fa-spinner fa-spin text-sm"></i>
            <i v-else class="fas fa-right-to-bracket text-xs"></i>
            <span>{{ form.processing ? 'Memverifikasi Akun...' : 'Masuk ke Sistem' }}</span>
          </button>
        </form>

      </div>

      <!-- Nilai Utama BUMN (AKHLAK) & Copyright Footer -->
      <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/80 text-center pb-[max(1.25rem,env(safe-area-inset-bottom,0px))]">
        <p class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest mb-1.5 flex items-center justify-center gap-1.5">
          <i class="fas fa-shield-halved text-sky-500 text-[9px]"></i>
          <span>Tata Nilai Utama AKHLAK BUMN</span>
        </p>
        <div class="flex justify-center items-center gap-1.5 text-[10px] font-bold text-sky-800 flex-wrap mb-3">
          <span class="bg-sky-100/80 px-2 py-0.5 rounded-md border border-sky-200/50">Amanah</span>
          <span class="bg-sky-100/80 px-2 py-0.5 rounded-md border border-sky-200/50">Kompeten</span>
          <span class="bg-sky-100/80 px-2 py-0.5 rounded-md border border-sky-200/50">Harmonis</span>
          <span class="bg-sky-100/80 px-2 py-0.5 rounded-md border border-sky-200/50">Loyal</span>
          <span class="bg-sky-100/80 px-2 py-0.5 rounded-md border border-sky-200/50">Adaptif</span>
          <span class="bg-sky-100/80 px-2 py-0.5 rounded-md border border-sky-200/50">Kolaboratif</span>
        </div>

        <!-- Copyright & Dev Credit -->
        <div class="pt-2 border-t border-slate-200/70 flex items-center justify-between text-[10px] text-slate-400">
          <span>&copy; {{ new Date().getFullYear() }} PLN UP Sengkang</span>
          <span class="flex items-center gap-1 text-slate-500 font-medium">
            <span>Copyright</span>
            <span class="font-bold text-sky-600 bg-sky-50 px-1.5 py-0.5 rounded border border-sky-200/70">devATP</span>
          </span>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { useToast } from '@/Composables/useToast';

const { error: toastError } = useToast();
const page = usePage();

const showPassword = ref(false);
const errorMessage = ref('');

const form = useForm({
  nid: '',
  email: '',
  password: '',
  remember: false,
});

// Pantau jika ada error dari session props atau flash saat pertama render
watch(
  () => page.props.errors,
  (errors) => {
    if (errors && (errors.nid || errors.email || errors.password)) {
      errorMessage.value = errors.nid || errors.email || errors.password;
    }
  },
  { immediate: true }
);

function clearError() {
  if (errorMessage.value) {
    errorMessage.value = '';
    form.clearErrors();
  }
}

function submit() {
  if (form.processing) return;
  errorMessage.value = '';
  form.clearErrors();

  if (!form.nid || !form.password) {
    errorMessage.value = 'Silakan masukkan NID dan kata sandi Anda.';
    return;
  }

  // Format NID huruf kapital dan kirim juga ke email untuk kompatibilitas
  form.nid = form.nid.trim();
  form.email = form.nid;

  form.post('/login', {
    preserveScroll: true,
    onSuccess: () => {
      if (typeof window !== 'undefined') {
        sessionStorage.setItem('show_welcome_popup', 'true');
      }
    },
    onError: (errors) => {
      const msg = errors.nid || errors.email || errors.password || 'NID atau kata sandi yang Anda masukkan salah.';
      errorMessage.value = msg;
      toastError(msg, 'Gagal Masuk');
    },
    onFinish: () => {
      form.reset('password');
    },
  });
}
</script>

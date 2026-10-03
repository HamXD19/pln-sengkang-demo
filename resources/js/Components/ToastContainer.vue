<template>
  <div 
    class="fixed top-3 left-3 right-3 sm:left-auto sm:right-5 sm:top-5 z-[9999] pointer-events-none flex flex-col gap-2.5 items-stretch sm:items-end w-auto sm:w-[420px] max-w-full"
    aria-live="polite"
  >
    <transition-group 
      enter-active-class="transform ease-out duration-300 transition"
      enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-4 scale-95"
      enter-to-class="translate-y-0 opacity-100 sm:translate-x-0 scale-100"
      leave-active-class="transition ease-in duration-200"
      leave-from-class="opacity-100 scale-100"
      leave-to-class="opacity-0 scale-95 sm:translate-x-4"
      move-class="transition-all duration-300 ease-in-out"
    >
      <div
        v-for="toast in toasts"
        :key="toast.id"
        @mouseenter="pauseToast(toast.id)"
        @mouseleave="resumeToast(toast.id)"
        class="pointer-events-auto relative w-full overflow-hidden rounded-2xl border bg-white/95 backdrop-blur-md shadow-xl transition-all duration-200 hover:shadow-2xl"
        :class="containerBorderClass(toast.type)"
        role="alert"
      >
        <div class="flex items-start gap-3.5 p-4">
          <!-- Icon Badge -->
          <div 
            class="w-9 h-9 rounded-xl flex items-center justify-center text-sm shrink-0 shadow-2xs border"
            :class="iconContainerClass(toast.type)"
          >
            <i :class="iconClass(toast.type)"></i>
          </div>

          <!-- Body -->
          <div class="flex-1 min-w-0 pt-0.5">
            <h4 class="text-xs font-bold text-slate-800 leading-tight flex items-center gap-1.5">
              <span>{{ toast.title }}</span>
              <span 
                v-if="toast.type === 'success'" 
                class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"
              ></span>
            </h4>
            <p class="text-xs text-slate-600 mt-1 leading-relaxed break-words">
              {{ toast.message }}
            </p>
          </div>

          <!-- Close Button -->
          <button
            type="button"
            @click="removeToast(toast.id)"
            class="text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg p-1.5 text-xs transition cursor-pointer shrink-0"
            title="Tutup Notifikasi"
          >
            <i class="fas fa-times"></i>
          </button>
        </div>

        <!-- Progress Bar Indicator -->
        <div class="h-1 w-full bg-slate-100 overflow-hidden">
          <div 
            class="h-full transition-all duration-75 ease-linear"
            :class="progressBarClass(toast.type)"
            :style="{ width: `${Math.max(0, toast.progress)}%` }"
          ></div>
        </div>
      </div>
    </transition-group>
  </div>
</template>

<script setup>
import { watch, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useToast } from '@/Composables/useToast';

const { toasts, removeToast, pauseToast, resumeToast, success, error, warning, info } = useToast();

const page = usePage();
let lastFlashKey = '';

function processFlash(flash) {
  if (!flash) return;
  const currentKey = JSON.stringify({
    s: flash.success || null,
    e: flash.error || null,
    w: flash.warning || null,
    i: flash.info || null,
  });

  // If nothing in flash or already displayed, don't trigger duplicate
  if (currentKey === lastFlashKey || (!flash.success && !flash.error && !flash.warning && !flash.info)) {
    return;
  }

  lastFlashKey = currentKey;

  if (flash.success) {
    success(flash.success, 'Berhasil');
  }
  if (flash.error) {
    error(flash.error, 'Terjadi Kesalahan');
  }
  if (flash.warning) {
    warning(flash.warning, 'Peringatan');
  }
  if (flash.info) {
    info(flash.info, 'Informasi');
  }
}

onMounted(() => {
  processFlash(page.props.flash);
});

watch(
  () => page.props.flash,
  (newFlash) => {
    processFlash(newFlash);
  },
  { deep: true }
);

function containerBorderClass(type) {
  switch (type) {
    case 'success':
      return 'border-emerald-200/80 shadow-emerald-500/5';
    case 'error':
      return 'border-rose-200/80 shadow-rose-500/5';
    case 'warning':
      return 'border-amber-200/80 shadow-amber-500/5';
    case 'info':
    default:
      return 'border-sky-200/80 shadow-sky-500/5';
  }
}

function iconContainerClass(type) {
  switch (type) {
    case 'success':
      return 'bg-emerald-50 text-emerald-600 border-emerald-200/90';
    case 'error':
      return 'bg-rose-50 text-rose-600 border-rose-200/90';
    case 'warning':
      return 'bg-amber-50 text-amber-600 border-amber-200/90';
    case 'info':
    default:
      return 'bg-sky-50 text-sky-600 border-sky-200/90';
  }
}

function iconClass(type) {
  switch (type) {
    case 'success':
      return 'fas fa-check-circle text-base';
    case 'error':
      return 'fas fa-exclamation-circle text-base';
    case 'warning':
      return 'fas fa-triangle-exclamation text-base';
    case 'info':
    default:
      return 'fas fa-info-circle text-base';
  }
}

function progressBarClass(type) {
  switch (type) {
    case 'success':
      return 'bg-emerald-500';
    case 'error':
      return 'bg-rose-500';
    case 'warning':
      return 'bg-amber-500';
    case 'info':
    default:
      return 'bg-sky-500';
  }
}
</script>

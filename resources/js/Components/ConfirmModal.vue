<template>
  <teleport to="body">
    <transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="state.isOpen"
        class="fixed inset-0 z-[9998] flex items-center justify-center p-4 sm:p-6 bg-slate-950/65 backdrop-blur-xs"
        @click.self="handleCancel"
        role="dialog"
        aria-modal="true"
        :aria-labelledby="state.title"
      >
        <transition
          enter-active-class="transition duration-200 ease-out"
          enter-from-class="opacity-0 scale-95 translate-y-2"
          enter-to-class="opacity-100 scale-100 translate-y-0"
          leave-active-class="transition duration-150 ease-in"
          leave-from-class="opacity-100 scale-100 translate-y-0"
          leave-to-class="opacity-0 scale-95 translate-y-2"
        >
          <div
            v-if="state.isOpen"
            class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl border border-slate-100 p-6 overflow-hidden text-left"
          >
            <!-- Header Icon & Title -->
            <div class="flex items-start gap-4">
              <!-- Icon with Pulsing Ambient Ring -->
              <div 
                class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl shrink-0 shadow-xs border relative"
                :class="iconContainerClass"
              >
                <i :class="modalIcon"></i>
              </div>

              <div class="flex-1 min-w-0 pt-0.5">
                <h3 class="text-base font-bold text-slate-800 leading-snug">
                  {{ state.title }}
                </h3>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                  {{ state.message }}
                </p>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 flex flex-col-reverse sm:flex-row items-center justify-end gap-2.5 pt-4 border-t border-slate-100">
              <button
                type="button"
                @click="handleCancel"
                class="w-full sm:w-auto px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-transparent transition cursor-pointer"
              >
                {{ state.cancelText }}
              </button>

              <button
                type="button"
                :disabled="isSubmittingConfirm"
                @click="onConfirmAction"
                class="w-full sm:w-auto px-5 py-2.5 rounded-xl text-xs font-bold text-white transition shadow-sm hover:shadow flex items-center justify-center gap-1.5 cursor-pointer disabled:opacity-50 disabled:pointer-events-none disabled:cursor-not-allowed"
                :class="confirmButtonClass"
              >
                <i v-if="isSubmittingConfirm" class="fas fa-spinner fa-spin text-xs"></i>
                <span>{{ isSubmittingConfirm ? 'Memproses...' : state.confirmText }}</span>
              </button>
            </div>
          </div>
        </transition>
      </div>
    </transition>
  </teleport>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { useConfirm } from '@/Composables/useConfirm';

const { state, handleConfirm, handleCancel } = useConfirm();
const isSubmittingConfirm = ref(false);

watch(() => state.isOpen, (open) => {
  if (open) {
    isSubmittingConfirm.value = false;
  }
});

function onConfirmAction() {
  if (isSubmittingConfirm.value) return;
  isSubmittingConfirm.value = true;
  handleConfirm();
}

const modalIcon = computed(() => {
  if (state.icon) return state.icon;
  switch (state.type) {
    case 'danger':
      return 'fas fa-trash-can';
    case 'warning':
      return 'fas fa-triangle-exclamation';
    case 'info':
    case 'primary':
    default:
      return 'fas fa-circle-question';
  }
});

const iconContainerClass = computed(() => {
  switch (state.type) {
    case 'danger':
      return 'bg-rose-50 text-rose-600 border-rose-200';
    case 'warning':
      return 'bg-amber-50 text-amber-600 border-amber-200';
    case 'info':
    case 'primary':
    default:
      return 'bg-blue-50 text-blue-600 border-blue-200';
  }
});

const confirmButtonClass = computed(() => {
  switch (state.type) {
    case 'danger':
      return 'bg-rose-600 hover:bg-rose-700 focus:ring-4 focus:ring-rose-200';
    case 'warning':
      return 'bg-amber-600 hover:bg-amber-700 focus:ring-4 focus:ring-amber-200';
    case 'info':
    case 'primary':
    default:
      return 'bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-200';
  }
});

// Keydown handler for ESC and Enter
function handleKeydown(e) {
  if (!state.isOpen) return;
  if (e.key === 'Escape') {
    handleCancel();
  }
}

watch(
  () => state.isOpen,
  (val) => {
    if (typeof document !== 'undefined') {
      if (val) {
        document.body.classList.add('overflow-hidden');
      } else {
        document.body.classList.remove('overflow-hidden');
      }
    }
  }
);

onMounted(() => {
  if (typeof window !== 'undefined') {
    window.addEventListener('keydown', handleKeydown);
  }
});

onUnmounted(() => {
  if (typeof window !== 'undefined') {
    window.removeEventListener('keydown', handleKeydown);
  }
  if (typeof document !== 'undefined') {
    document.body.classList.remove('overflow-hidden');
  }
});
</script>

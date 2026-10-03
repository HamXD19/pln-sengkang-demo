import { ref, reactive } from 'vue';

const toasts = ref([]);
let toastIdCounter = 0;

export function useToast() {
  function addToast({
    type = 'info',
    title = '',
    message = '',
    duration = 4500,
  }) {
    if (!message) return;

    // Check default titles if not provided
    let finalTitle = title;
    if (!finalTitle) {
      if (type === 'success') finalTitle = 'Berhasil';
      else if (type === 'error') finalTitle = 'Terjadi Kesalahan';
      else if (type === 'warning') finalTitle = 'Peringatan';
      else finalTitle = 'Informasi';
    }

    const id = ++toastIdCounter;
    const toastItem = reactive({
      id,
      type,
      title: finalTitle,
      message,
      duration,
      progress: 100,
      isPaused: false,
      timer: null,
      interval: null,
      createdAt: Date.now(),
    });

    // Start progress timer
    startCountdown(toastItem);

    // Limit maximum stacked toasts to 5 to avoid screen clutter
    if (toasts.value.length >= 5) {
      const oldest = toasts.value.shift();
      if (oldest) cleanupToast(oldest);
    }

    toasts.value.push(toastItem);
    return id;
  }

  function startCountdown(item) {
    if (item.duration <= 0) return;

    const stepMs = 50;
    const decrement = (stepMs / item.duration) * 100;

    item.interval = setInterval(() => {
      if (!item.isPaused) {
        item.progress -= decrement;
        if (item.progress <= 0) {
          removeToast(item.id);
        }
      }
    }, stepMs);
  }

  function pauseToast(id) {
    const item = toasts.value.find((t) => t.id === id);
    if (item) item.isPaused = true;
  }

  function resumeToast(id) {
    const item = toasts.value.find((t) => t.id === id);
    if (item) item.isPaused = false;
  }

  function removeToast(id) {
    const idx = toasts.value.findIndex((t) => t.id === id);
    if (idx !== -1) {
      const [item] = toasts.value.splice(idx, 1);
      cleanupToast(item);
    }
  }

  function cleanupToast(item) {
    if (item.interval) clearInterval(item.interval);
    if (item.timer) clearTimeout(item.timer);
  }

  function clearAll() {
    toasts.value.forEach(cleanupToast);
    toasts.value = [];
  }

  function success(message, title = '') {
    return addToast({ type: 'success', title, message });
  }

  function error(message, title = '') {
    return addToast({ type: 'error', title, message, duration: 6500 });
  }

  function warning(message, title = '') {
    return addToast({ type: 'warning', title, message, duration: 5500 });
  }

  function info(message, title = '') {
    return addToast({ type: 'info', title, message });
  }

  return {
    toasts,
    addToast,
    removeToast,
    pauseToast,
    resumeToast,
    clearAll,
    success,
    error,
    warning,
    info,
  };
}

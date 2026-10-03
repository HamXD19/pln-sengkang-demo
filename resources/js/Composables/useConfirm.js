import { reactive } from 'vue';

const state = reactive({
  isOpen: false,
  title: 'Konfirmasi Tindakan',
  message: 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
  confirmText: 'Lanjutkan',
  cancelText: 'Batal',
  type: 'danger', // 'danger' | 'warning' | 'info' | 'primary'
  icon: '',
  loading: false,
  resolve: null,
});

export function useConfirm() {
  function confirm({
    title = 'Konfirmasi Tindakan',
    message = 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
    confirmText = 'Lanjutkan',
    cancelText = 'Batal',
    type = 'danger',
    icon = '',
  } = {}) {
    return new Promise((resolve) => {
      state.title = title;
      state.message = message;
      state.confirmText = confirmText;
      state.cancelText = cancelText;
      state.type = type;
      state.icon = icon;
      state.loading = false;
      state.resolve = resolve;
      state.isOpen = true;
    });
  }

  function handleConfirm() {
    state.isOpen = false;
    if (state.resolve) {
      state.resolve(true);
      state.resolve = null;
    }
  }

  function handleCancel() {
    state.isOpen = false;
    if (state.resolve) {
      state.resolve(false);
      state.resolve = null;
    }
  }

  return {
    state,
    confirm,
    handleConfirm,
    handleCancel,
  };
}

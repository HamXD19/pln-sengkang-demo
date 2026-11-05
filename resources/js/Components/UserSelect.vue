<template>
  <div class="relative w-full" ref="containerRef">
    <label v-if="label" class="block text-xs font-bold text-gray-700 mb-1">
      {{ label }} <span v-if="required" class="text-rose-500">*</span>
    </label>

    <!-- KONDISI 1: SUDAH ADA PEGAWAI TERPILIH (Tampilan Kartu Ringkas) -->
    <div 
      v-if="selectedUser && !isEditing"
      class="flex items-center justify-between p-2 sm:p-2.5 bg-blue-50/50 border border-blue-200 rounded-lg text-xs transition hover:bg-blue-50/80"
    >
      <div class="flex items-center gap-2.5 min-w-0">
        <!-- Avatar / Inisial -->
        <div class="w-7 h-7 rounded-full bg-blue-600 text-white font-bold text-[10px] flex items-center justify-center shrink-0 overflow-hidden shadow-2xs">
          <img 
            v-if="selectedUser.avatar_url || selectedUser.avatar" 
            :src="selectedUser.avatar_url || `/storage/${selectedUser.avatar}`" 
            :alt="selectedUser.name"
            class="w-full h-full object-cover"
          />
          <span v-else>{{ getInitials(selectedUser.name) }}</span>
        </div>

        <div class="min-w-0">
          <div class="flex items-center gap-1.5 flex-wrap">
            <span class="font-bold text-gray-900 truncate">{{ selectedUser.name }}</span>
            <span v-if="selectedUser.employee_no" class="font-mono text-[10px] bg-white text-blue-700 px-1.5 py-0.2 rounded border border-blue-200">
              {{ selectedUser.employee_no }}
            </span>
          </div>
          <p class="text-[10px] text-gray-500 truncate">
            {{ getRoleOrPosition(selectedUser) }}
          </p>
        </div>
      </div>

      <div class="flex items-center gap-1 shrink-0 ml-2">
        <button 
          type="button" 
          @click="startEdit"
          class="px-2 py-1 text-[11px] font-semibold text-blue-700 hover:text-blue-900 hover:bg-blue-100 rounded transition cursor-pointer"
          title="Ganti Pegawai"
        >
          <i class="fas fa-search text-[10px] mr-1"></i> Ganti
        </button>
        <button 
          v-if="!required"
          type="button" 
          @click="clearSelection"
          class="p-1 text-gray-400 hover:text-rose-600 rounded transition cursor-pointer"
          title="Hapus Pilihan"
        >
          <i class="fas fa-times text-xs"></i>
        </button>
      </div>
    </div>

    <!-- KONDISI 2: BELUM TERPILIH ATAU SEDANG MENCARI (Input Ketik Kata Kunci) -->
    <div v-else class="relative">
      <div class="relative">
        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
        <input 
          ref="inputRef"
          type="text" 
          v-model="searchQuery" 
          @focus="isOpen = true"
          @keydown.down.prevent="navigateResults(1)"
          @keydown.up.prevent="navigateResults(-1)"
          @keydown.enter.prevent="selectHighlighted"
          @keydown.esc="closeDropdown"
          :placeholder="placeholder"
          class="w-full text-xs pl-8 pr-8 py-2.5 bg-white border border-gray-200 rounded-lg outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition"
          :class="{ 'border-rose-300 focus:border-rose-500': required && !modelValue && touched }"
        />
        <button 
          v-if="searchQuery"
          type="button" 
          @click="clearSearch"
          class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 text-xs cursor-pointer p-0.5"
        >
          <i class="fas fa-times"></i>
        </button>
      </div>

      <!-- DROPDOWN HASIL FILTER KATA KUNCI -->
      <div 
        v-if="isOpen"
        class="absolute z-50 left-0 right-0 mt-1 bg-white border border-gray-200 rounded-xl shadow-xl max-h-56 overflow-y-auto divide-y divide-gray-100 animate-in fade-in duration-100"
      >
        <!-- Header Info Pencarian -->
        <div class="px-3 py-1.5 bg-slate-50 text-[10px] text-gray-500 flex justify-between items-center font-medium sticky top-0 border-b border-gray-100 z-10">
          <span>Ketik nama, NID, atau jabatan</span>
          <span>{{ filteredUsers.length }} pegawai</span>
        </div>

        <!-- List Pegawai -->
        <template v-if="filteredUsers.length > 0">
          <div 
            v-for="(u, idx) in filteredUsers" 
            :key="u.id"
            @click="selectUser(u)"
            @mouseenter="highlightedIndex = idx"
            :class="[
              'p-2.5 flex items-center justify-between text-xs cursor-pointer transition',
              highlightedIndex === idx ? 'bg-blue-50 text-blue-900' : 'hover:bg-slate-50 text-gray-700',
              modelValue == u.id ? 'bg-blue-50/70 font-semibold' : ''
            ]"
          >
            <div class="flex items-center gap-2.5 min-w-0">
              <div class="w-6 h-6 rounded-full bg-slate-200 text-slate-700 font-bold text-[9px] flex items-center justify-center shrink-0 overflow-hidden">
                <img 
                  v-if="u.avatar_url || u.avatar" 
                  :src="u.avatar_url || `/storage/${u.avatar}`" 
                  :alt="u.name"
                  class="w-full h-full object-cover"
                />
                <span v-else>{{ getInitials(u.name) }}</span>
              </div>
              <div class="min-w-0">
                <div class="flex items-center gap-1.5 flex-wrap">
                  <span class="font-bold text-gray-900 text-xs truncate">{{ u.name }}</span>
                  <span v-if="u.employee_no" class="font-mono text-[9px] bg-slate-100 text-slate-600 px-1 rounded">
                    {{ u.employee_no }}
                  </span>
                </div>
                <p class="text-[10px] text-gray-500 truncate">
                  {{ getRoleOrPosition(u) }}
                </p>
              </div>
            </div>

            <i v-if="modelValue == u.id" class="fas fa-check text-blue-600 text-xs shrink-0 ml-2"></i>
          </div>
        </template>

        <!-- Kondisi Tidak Ada Hasil -->
        <div v-else class="p-4 text-center text-gray-400 text-xs">
          <i class="fas fa-search text-gray-300 text-base mb-1 block"></i>
          <span>Tidak ditemukan pegawai dengan kata kunci <b>"{{ searchQuery }}"</b></span>
        </div>
      </div>
    </div>

    <!-- Hidden input untuk validasi form standar -->
    <input 
      type="hidden" 
      :name="name" 
      :value="modelValue" 
      :required="required" 
    />
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted, nextTick } from 'vue';

const props = defineProps({
  modelValue: {
    type: [Number, String],
    default: '',
  },
  users: {
    type: Array,
    default: () => [],
  },
  label: {
    type: String,
    default: '',
  },
  required: {
    type: Boolean,
    default: false,
  },
  placeholder: {
    type: String,
    default: 'Ketik nama atau NID pegawai (PIC)...',
  },
  name: {
    type: String,
    default: 'assigned_to',
  },
});

const emit = defineEmits(['update:modelValue', 'change']);

const containerRef = ref(null);
const inputRef = ref(null);
const searchQuery = ref('');
const isOpen = ref(false);
const isEditing = ref(false);
const touched = ref(false);
const highlightedIndex = ref(0);

const selectedUser = computed(() => {
  if (!props.modelValue) return null;
  return props.users.find(u => String(u.id) === String(props.modelValue)) || null;
});

const filteredUsers = computed(() => {
  const q = searchQuery.value.trim().toLowerCase();
  if (!q) {
    return props.users.slice(0, 30); // Tampilkan 30 pegawai teratas saat belum ketik
  }
  return props.users.filter(u => {
    const nameMatch = (u.name || '').toLowerCase().includes(q);
    const nidMatch = (u.employee_no || '').toLowerCase().includes(q);
    const roleMatch = (u.role?.name || '').toLowerCase().includes(q);
    const posMatch = (u.position?.name || '').toLowerCase().includes(q);
    const unitMatch = (u.unit?.name || '').toLowerCase().includes(q);
    return nameMatch || nidMatch || roleMatch || posMatch || unitMatch;
  }).slice(0, 50);
});

function getInitials(name) {
  if (!name) return 'U';
  return name.split(' ').map(n => n[0]).slice(0, 2).join('').toUpperCase();
}

function getRoleOrPosition(user) {
  const pos = user.position?.name;
  const role = user.role?.name;
  const unit = user.unit?.name;
  if (pos && unit) return `${pos} • ${unit}`;
  if (pos) return pos;
  if (role) return role;
  return 'Pegawai PLN';
}

function selectUser(user) {
  emit('update:modelValue', user.id);
  emit('change', user);
  isOpen.value = false;
  isEditing.value = false;
  searchQuery.value = '';
  touched.value = true;
}

function clearSelection() {
  emit('update:modelValue', '');
  emit('change', null);
  searchQuery.value = '';
  isEditing.value = true;
  nextTick(() => {
    inputRef.value?.focus();
  });
}

function startEdit() {
  isEditing.value = true;
  isOpen.value = true;
  nextTick(() => {
    inputRef.value?.focus();
  });
}

function clearSearch() {
  searchQuery.value = '';
  inputRef.value?.focus();
}

function closeDropdown() {
  isOpen.value = false;
  if (selectedUser.value) {
    isEditing.value = false;
  }
}

function navigateResults(direction) {
  if (!isOpen.value) {
    isOpen.value = true;
    return;
  }
  const max = filteredUsers.value.length - 1;
  highlightedIndex.value = Math.max(0, Math.min(max, highlightedIndex.value + direction));
}

function selectHighlighted() {
  if (filteredUsers.value[highlightedIndex.value]) {
    selectUser(filteredUsers.value[highlightedIndex.value]);
  }
}

function handleClickOutside(event) {
  if (containerRef.value && !containerRef.value.contains(event.target)) {
    closeDropdown();
  }
}

watch(isOpen, (val) => {
  if (val) {
    highlightedIndex.value = 0;
  }
});

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});
</script>

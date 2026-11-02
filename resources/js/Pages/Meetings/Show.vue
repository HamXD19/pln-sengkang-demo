<template>
  <AppLayout :title="`Detail Agenda Rapat - ${meeting.type}`">
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs flex flex-col overflow-hidden">
      
      <!-- Header Rapat -->
      <div class="p-4 sm:p-8 pb-0">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
          <div class="flex items-center gap-3">
            <Link href="/rapat" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-100 transition">
              <i class="fas fa-arrow-left text-base"></i>
            </Link>
            <h1 class="text-lg sm:text-2xl font-black text-slate-800 leading-tight">{{ meeting.type }}</h1>
          </div>

          <!-- Tombol Aksi Cepat: Masuk Workspace & Cetak -->
          <div class="flex items-center gap-2 w-full sm:w-auto">
            <a 
              :href="`/rapat/${meeting.id}/cetak`" 
              target="_blank" 
              class="flex-1 sm:flex-none justify-center border border-blue-200 text-blue-600 hover:bg-blue-50 px-3.5 py-2 rounded-xl text-xs font-bold flex items-center gap-1.5 transition"
            >
              <i class="fas fa-print"></i> Cetak PDF
            </a>
            <Link 
              :href="`/rapat/${meeting.id}/workspace`" 
              class="flex-1 sm:flex-none justify-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-xs transition cursor-pointer active:scale-95"
            >
              <i class="fas fa-laptop-code"></i> Buka Workspace
            </Link>
          </div>
        </div>

        <!-- Meta Info Rapat -->
        <div class="flex flex-wrap items-center gap-y-2 gap-x-4 sm:ml-9 text-xs text-slate-500 font-medium mb-6">
          <div class="flex items-center gap-1.5 bg-slate-50 sm:bg-transparent px-2.5 py-1 sm:p-0 rounded-lg">
            <i class="far fa-calendar-alt text-blue-600"></i> {{ formatDateFull(meeting.meeting_date) }}
          </div>
          <span class="text-slate-200 hidden sm:inline">|</span>
          <div class="flex items-center gap-1.5 bg-slate-50 sm:bg-transparent px-2.5 py-1 sm:p-0 rounded-lg">
            <i class="far fa-clock text-blue-500"></i> {{ meeting.time_start || '08:00' }} - {{ meeting.time_end || '10:00' }} WITA
          </div>
          <span class="text-slate-200 hidden sm:inline">|</span>
          <div class="flex items-center gap-1.5 bg-slate-50 sm:bg-transparent px-2.5 py-1 sm:p-0 rounded-lg">
            <i class="fas fa-map-marker-alt text-rose-500"></i> {{ meeting.location || 'Ruang Rapat UP Sengkang' }}
          </div>
          <div class="sm:ml-auto">
            <span :class="statusBadgeClass(meeting.status)">
              {{ meeting.status || 'Akan Datang' }}
            </span>
          </div>
        </div>

        <!-- Tabs Menu Interaktif (Scrollable di Mobile) -->
        <div class="flex gap-4 sm:gap-8 border-b border-slate-200 text-xs font-semibold overflow-x-auto whitespace-nowrap">
          <button 
            type="button" 
            @click="activeTab = 'agenda'" 
            :class="[
              'pb-3 transition cursor-pointer shrink-0',
              activeTab === 'agenda' ? 'border-b-[3px] border-blue-600 text-blue-600 font-bold' : 'text-slate-500 hover:text-slate-800'
            ]"
          >
            Agenda
          </button>
          <button 
            type="button" 
            @click="activeTab = 'peserta'" 
            :class="[
              'pb-3 transition cursor-pointer shrink-0',
              activeTab === 'peserta' ? 'border-b-[3px] border-blue-600 text-blue-600 font-bold' : 'text-slate-500 hover:text-slate-800'
            ]"
          >
            Peserta ({{ users.length }})
          </button>
          <button 
            type="button" 
            @click="activeTab = 'issue'" 
            :class="[
              'pb-3 transition cursor-pointer shrink-0',
              activeTab === 'issue' ? 'border-b-[3px] border-blue-600 text-blue-600 font-bold' : 'text-slate-500 hover:text-slate-800'
            ]"
          >
            Issue ({{ issues.length }})
          </button>
          <button 
            type="button" 
            @click="activeTab = 'notulen'" 
            :class="[
              'pb-3 transition cursor-pointer shrink-0',
              activeTab === 'notulen' ? 'border-b-[3px] border-blue-600 text-blue-600 font-bold' : 'text-slate-500 hover:text-slate-800'
            ]"
          >
            Notulen
          </button>
          <button 
            type="button" 
            @click="activeTab = 'tindaklanjut'" 
            :class="[
              'pb-3 transition cursor-pointer shrink-0',
              activeTab === 'tindaklanjut' ? 'border-b-[3px] border-blue-600 text-blue-600 font-bold' : 'text-slate-500 hover:text-slate-800'
            ]"
          >
            Tindak Lanjut ({{ tasks.length }})
          </button>
        </div>
      </div>

      <!-- TAB CONTENT 1: AGENDA -->
      <div v-if="activeTab === 'agenda'" class="p-4 sm:p-8 flex-1">
        <div class="space-y-6">
          <div class="flex gap-5">
            <div class="w-9 h-9 bg-slate-600 text-white rounded-lg flex items-center justify-center font-bold text-sm shrink-0 shadow-xs">1</div>
            <div class="flex-1 border-b border-gray-100 pb-6 flex justify-between items-start">
              <div>
                <h3 class="text-sm font-bold text-gray-800">Penyampaian informasi dari manajemen atasnya</h3>
                <p class="text-xs text-gray-500 mt-1">Sosialisasi arahan MUP dan pembaruan KPI operasional mingguan.</p>
              </div>
              <div class="text-xs text-gray-500 font-medium flex items-center gap-1.5 whitespace-nowrap">
                <i class="far fa-clock"></i> 15 menit
              </div>
            </div>
          </div>

          <div class="flex gap-5">
            <div class="w-9 h-9 bg-slate-600 text-white rounded-lg flex items-center justify-center font-bold text-sm shrink-0 shadow-xs">2</div>
            <div class="flex-1 border-b border-gray-100 pb-6 flex justify-between items-start">
              <div>
                <h3 class="text-sm font-bold text-gray-800">Evaluasi task minggu sebelumnya (N-1)</h3>
                <ul class="list-disc pl-5 mt-2 space-y-1 text-xs text-gray-500 font-medium">
                  <li>Task yang selesai</li>
                  <li>Task yang belum selesai (Outstanding & Overdue)</li>
                  <li>Kendala dan resolusi kendala</li>
                </ul>
              </div>
              <div class="text-xs text-gray-500 font-medium flex items-center gap-1.5 whitespace-nowrap">
                <i class="far fa-clock"></i> 30 menit
              </div>
            </div>
          </div>

          <div class="flex gap-5">
            <div class="w-9 h-9 bg-slate-600 text-white rounded-lg flex items-center justify-center font-bold text-sm shrink-0 shadow-xs">3</div>
            <div class="flex-1 border-b border-gray-100 pb-6 flex justify-between items-start">
              <div>
                <h3 class="text-sm font-bold text-gray-800">Perencanaan task minggu ini (N)</h3>
                <ul class="list-disc pl-5 mt-2 space-y-1 text-xs text-gray-500 font-medium">
                  <li>Rencana task rutin dan preventif</li>
                  <li>Penetapan PIC dan target waktu selesai</li>
                </ul>
              </div>
              <div class="text-xs text-gray-500 font-medium flex items-center gap-1.5 whitespace-nowrap">
                <i class="far fa-clock"></i> 45 menit
              </div>
            </div>
          </div>

          <div class="flex gap-5">
            <div class="w-9 h-9 bg-slate-600 text-white rounded-lg flex items-center justify-center font-bold text-sm shrink-0 shadow-xs">4</div>
            <div class="flex-1 flex justify-between items-start">
              <div>
                <h3 class="text-sm font-bold text-gray-800">Rekap Putusan & Tindak Lanjut</h3>
                <p class="text-xs text-gray-500 mt-1">Penetapan keputusan resmi sesuai kewenangan level rapat.</p>
              </div>
              <div class="text-xs text-gray-500 font-medium flex items-center gap-1.5 whitespace-nowrap">
                <i class="far fa-clock"></i> 30 menit
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- TAB CONTENT 2: PESERTA -->
      <div v-if="activeTab === 'peserta'" class="p-8 flex-1">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
          <div 
            v-for="u in users" 
            :key="u.id" 
            class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 hover:border-blue-200 transition bg-slate-50/50"
          >
            <div class="w-10 h-10 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center text-xs shrink-0 overflow-hidden">
              <img v-if="u.avatar" :src="`/storage/${u.avatar}`" class="w-full h-full object-cover" />
              <span v-else>{{ (u.name || 'U').substring(0, 2).toUpperCase() }}</span>
            </div>
            <div class="min-w-0">
              <h4 class="text-xs font-bold text-gray-800 truncate">{{ u.name }}</h4>
              <p class="text-[11px] text-gray-500 truncate">{{ u.position?.name || u.role?.name || 'Staff' }}</p>
              <span class="text-[10px] text-blue-600 font-mono">{{ u.employee_no || '-' }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- TAB CONTENT 3: ISSUE -->
      <div v-if="activeTab === 'issue'" class="p-8 flex-1">
        <div class="space-y-3">
          <template v-if="issues && issues.length > 0">
            <div 
              v-for="iss in issues" 
              :key="iss.id" 
              class="border border-gray-200 p-4 rounded-xl flex items-center justify-between text-xs hover:bg-slate-50 transition"
            >
              <div>
                <div class="flex items-center gap-2 mb-1">
                  <span class="font-mono text-[10px] font-bold bg-slate-800 text-white px-2 py-0.5 rounded">
                    #{{ iss.issue_no }}
                  </span>
                  <span class="font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded text-[10px]">
                    Level {{ iss.current_level }}
                  </span>
                  <span :class="iss.priority === 'CRITICAL' ? 'bg-red-50 text-red-600' : 'bg-amber-50 text-amber-600'" class="px-2 py-0.5 rounded text-[10px] font-bold">
                    {{ iss.priority }}
                  </span>
                </div>
                <h4 class="font-bold text-gray-800">{{ iss.title }}</h4>
              </div>
              <Link :href="`/issues/${iss.id}`" class="px-3 py-1.5 border border-blue-500 text-blue-600 hover:bg-blue-50 rounded-lg text-xs font-semibold">
                Lihat Detail
              </Link>
            </div>
          </template>
          <div v-else class="text-center py-10 text-gray-400 text-xs">
            Tidak ada isu khusus yang terikat pada rapat ini.
          </div>
        </div>
      </div>

      <!-- TAB CONTENT 4: NOTULEN -->
      <div v-if="activeTab === 'notulen'" class="p-8 flex-1">
        <div class="p-4 bg-slate-50 border border-gray-200 rounded-xl text-xs text-gray-700 leading-relaxed whitespace-pre-line">
          {{ meeting.notes || 'Belum ada catatan notulen resmi yang dicatat untuk rapat ini. Catatan dapat diinput saat menyelesaikan rapat di ruang Workspace.' }}
        </div>
      </div>

      <!-- TAB CONTENT 5: TINDAK LANJUT -->
      <div v-if="activeTab === 'tindaklanjut'" class="p-8 flex-1">
        <div class="space-y-3">
          <template v-if="tasks && tasks.length > 0">
            <div 
              v-for="tsk in tasks" 
              :key="tsk.id" 
              class="border border-gray-200 p-3.5 rounded-xl flex items-center justify-between text-xs hover:bg-slate-50 transition"
            >
              <div>
                <h4 class="font-bold text-gray-800">{{ tsk.title }}</h4>
                <p class="text-[11px] text-gray-400 mt-0.5">
                  PIC: {{ tsk.assignee?.name || 'Staff' }} | Target: {{ tsk.due_date ? formatDateOnly(tsk.due_date) : '-' }}
                </p>
              </div>
              <div class="flex items-center gap-3">
                <span class="text-xs font-bold text-gray-700">{{ tsk.progress_percent }}%</span>
                <span class="bg-blue-50 text-blue-700 border border-blue-200 px-2 py-0.5 rounded text-[10px] font-bold">
                  {{ tsk.status }}
                </span>
              </div>
            </div>
          </template>
          <div v-else class="text-center py-10 text-gray-400 text-xs">
            Belum ada tugas tindak lanjut yang terdaftar.
          </div>
        </div>
      </div>

    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  meeting: Object,
  issues: Array,
  tasks: Array,
  users: Array,
});

const activeTab = ref('agenda');

function formatDateFull(dStr) {
  if (!dStr) return '-';
  const d = new Date(dStr);
  return d.toLocaleDateString('id-ID', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  });
}

function formatDateOnly(dStr) {
  if (!dStr) return '-';
  const d = new Date(dStr);
  return d.toLocaleDateString('id-ID', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
  });
}

function statusBadgeClass(s) {
  if (s === 'Berlangsung') return 'bg-rose-50 text-rose-700 border border-rose-200 px-3 py-1 rounded-full text-xs font-bold';
  if (s === 'Selesai') return 'bg-emerald-50 text-emerald-700 border border-emerald-200 px-3 py-1 rounded-full text-xs font-bold';
  return 'bg-amber-50 text-amber-700 border border-amber-200 px-3 py-1 rounded-full text-xs font-bold';
}
</script>

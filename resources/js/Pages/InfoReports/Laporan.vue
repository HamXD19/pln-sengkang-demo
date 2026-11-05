<template>
  <AppLayout title="Laporan Kinerja & Koordinasi Berjenjang">
    <div class="space-y-6">

      <!-- KOP SURAT RESMI CETAK LAPORAN (HANYA MUNCUL SAAT CETAK / PRINT) -->
      <div class="hidden print:block border-b-2 border-gray-900 pb-4 mb-6">
        <div class="flex justify-between items-start">
          <div class="flex items-center gap-3.5">
            <div class="h-16 w-16 shrink-0 flex items-center justify-center">
              <img 
                :src="$page.props.branding?.logo_url || '/assets/bumn/logo-pln.png'" 
                alt="Logo PT PLN Nusantara Power" 
                class="max-h-16 max-w-16 object-contain"
              />
            </div>
            <div>
              <h1 class="text-base font-extrabold tracking-tight text-gray-900 uppercase">PT PLN NUSANTARA POWER</h1>
              <p class="text-[11px] font-bold text-gray-700 uppercase">UNIT PELAKSANA PENGENDALIAN PEMBANGKITAN SENGKANG</p>
              <p class="text-[10px] text-gray-500">Jl. Poros Sengkang - Siwa Km. 20, Kabupaten Wajo, Sulawesi Selatan 90911</p>
            </div>
          </div>
          <div class="text-right">
            <span class="text-[9px] font-mono text-gray-400 block uppercase tracking-wider">Laporan Resmi Terintegrasi</span>
            <span class="text-xs font-mono font-bold text-gray-900 bg-slate-50 px-2.5 py-1 rounded border border-gray-200 inline-block mt-0.5">
              LAP-SKG/{{ new Date().getFullYear() }}/{{ (selectedPeriode || 'BULAN_INI').toUpperCase() }}
            </span>
            <span class="text-[9px] text-emerald-700 font-semibold block mt-1">Status: Terverifikasi Sistem</span>
          </div>
        </div>

        <div class="text-center mt-5">
          <h2 class="text-sm font-extrabold uppercase underline tracking-wider text-gray-900">
            LAPORAN KINERJA KOORDINASI, KEPUTUSAN & REALISASI TASK
          </h2>
          <p class="text-xs font-bold text-blue-700 mt-1 uppercase">
            Periode: {{ formatPeriodeLabel(selectedPeriode) }}
          </p>
        </div>
      </div>

      <!-- Header Filter & Export Bar (Sembunyi saat Cetak) -->
      <div class="no-print print:hidden bg-white rounded-xl border border-gray-100 p-6 shadow-xs flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
          <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
            <i class="fas fa-chart-line text-blue-600"></i> Laporan Berkala Koordinasi & Eskalasi UP Sengkang
          </h2>
          <p class="text-xs text-gray-500 mt-1">Audit trail kepatuhan Lowest Competent Level, SLA Task, dan efektivitas eskalasi (FR-12 & BSD Section 9.6).</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
          <!-- Filter Periode -->
          <select 
            v-model="selectedPeriode" 
            @change="changePeriode"
            class="border border-gray-200 rounded-lg px-3 py-2 text-xs font-semibold text-gray-700 outline-none focus:ring-1 focus:ring-blue-500 bg-white shadow-xs cursor-pointer"
          >
            <option value="bulan_ini">Bulan Ini</option>
            <option value="minggu_ini">Minggu Ini</option>
            <option value="minggu_lalu">Minggu Lalu</option>
            <option value="semua">Semua Data</option>
          </select>

          <a 
            href="/rapat/notulen-mingguan" 
            target="_blank"
            class="px-4 py-2 border border-blue-200 bg-blue-50/70 hover:bg-blue-100/80 text-blue-700 rounded-lg text-xs font-semibold transition flex items-center gap-1.5 shadow-xs"
            title="Cetak format resmi notulen per minggu"
          >
            <i class="fas fa-file-invoice text-blue-600"></i> Notulen Mingguan
          </a>

          <button 
            type="button" 
            @click="printReport"
            class="px-4 py-2 border border-gray-200 text-gray-700 rounded-lg text-xs font-semibold hover:bg-gray-50 transition flex items-center gap-1.5 shadow-xs cursor-pointer"
          >
            <i class="fas fa-print"></i> Cetak Laporan
          </button>

          <a 
            href="/laporan/export" 
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold transition flex items-center gap-1.5 shadow-xs"
          >
            <i class="fas fa-file-csv"></i> Export Data CSV
          </a>

          <a 
            href="/download-akun-excel" 
            class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold transition flex items-center gap-1.5 shadow-xs"
          >
            <i class="fas fa-file-excel"></i> Unduh Excel Akun (154 Pegawai)
          </a>
        </div>
      </div>

      <!-- 4 KPI Performance Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- 1. Lowest Competent Level Resolution Rate -->
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-xs flex flex-col justify-between">
          <div>
            <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block">Resolusi Level Terendah (TL)</span>
            <div class="flex items-baseline gap-2 mt-2">
              <h3 class="text-3xl font-black text-blue-600">{{ tlResolutionRate }}%</h3>
              <span class="text-xs text-gray-500">({{ resolvedByTL }}/{{ totalDecisions }})</span>
            </div>
          </div>
          <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden mt-3">
            <div class="bg-blue-600 h-1.5 rounded-full" :style="`width: ${Math.min(tlResolutionRate, 100)}%`"></div>
          </div>
          <p class="text-[10px] text-gray-500 mt-2"><i class="fas fa-check-circle text-blue-500"></i> Prinsip BSD: Masalah diselesaikan di TL</p>
        </div>

        <!-- 2. Status Penanganan Isu -->
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-xs flex flex-col justify-between">
          <div>
            <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block">Total Isu & Kendala</span>
            <div class="flex items-baseline gap-2 mt-2">
              <h3 class="text-3xl font-black text-gray-800">{{ totalIssues }}</h3>
              <span class="text-xs text-emerald-600 font-semibold">({{ resolvedIssues }} Selesai)</span>
            </div>
          </div>
          <div class="flex items-center gap-2 mt-3 text-[11px]">
            <span class="bg-amber-50 text-amber-700 px-2 py-0.5 rounded font-semibold">{{ openIssues }} Open</span>
            <span class="bg-purple-50 text-purple-700 px-2 py-0.5 rounded font-semibold">{{ escalatedIssues }} Eskalasi</span>
          </div>
          <p class="text-[10px] text-gray-400 mt-2">Akuntabilitas alur koordinasi</p>
        </div>

        <!-- 3. Tingkat Penyelesaian Task (SLA) -->
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-xs flex flex-col justify-between">
          <div>
            <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block">Realisasi Task (SLA)</span>
            <div class="flex items-baseline gap-2 mt-2">
              <h3 class="text-3xl font-black text-emerald-600">{{ taskCompletionRate }}%</h3>
              <span class="text-xs text-gray-500">({{ completedTasks }}/{{ totalTasks }})</span>
            </div>
          </div>
          <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden mt-3">
            <div class="bg-emerald-600 h-1.5 rounded-full" :style="`width: ${Math.min(taskCompletionRate, 100)}%`"></div>
          </div>
          <p class="text-[10px] text-rose-500 font-semibold mt-2">
            <template v-if="overdueTasks > 0">
              <i class="fas fa-exclamation-triangle"></i> {{ overdueTasks }} Task Overdue / Terlambat
            </template>
            <template v-else>
              <i class="fas fa-check text-emerald-500"></i> Seluruh task on-track
            </template>
          </p>
        </div>

        <!-- 4. Putusan Resmi Berjenjang -->
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-xs flex flex-col justify-between">
          <div>
            <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block">Putusan Berjenjang</span>
            <div class="flex items-baseline gap-2 mt-2">
              <h3 class="text-3xl font-black text-indigo-600">{{ totalDecisions }}</h3>
              <span class="text-xs text-gray-500">Keputusan Resmi</span>
            </div>
          </div>
          <div class="flex items-center gap-2 mt-3 text-[10px] text-gray-600">
            <span>TL: <b>{{ resolvedByTL }}</b></span> &bull;
            <span>Asman: <b>{{ resolvedByAsman }}</b></span> &bull;
            <span>MUP: <b>{{ resolvedByMUP }}</b></span>
          </div>
          <p class="text-[10px] text-gray-400 mt-2">Kewenangan bertingkat</p>
        </div>

      </div>

      <!-- Tabel Audit Trail Keputusan -->
      <div class="bg-white rounded-xl border border-gray-100 shadow-xs p-5">
        <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2">
          <i class="fas fa-gavel text-blue-600"></i> Audit Trail: Rekap Putusan Terkini
        </h3>
        <!-- Desktop Table: Rekap Putusan -->
        <div class="hidden md:block overflow-x-auto">
          <table class="w-full text-left text-xs whitespace-nowrap">
            <thead class="bg-[#f8fafc] border-b border-gray-100 text-gray-500 font-semibold">
              <tr>
                <th class="px-4 py-3">Terkait Issue</th>
                <th class="px-4 py-3">Tingkat</th>
                <th class="px-4 py-3">Pernyataan Keputusan</th>
                <th class="px-4 py-3">Pengambil Putusan</th>
                <th class="px-4 py-3">Tanggal</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700">
              <template v-if="recentDecisions && recentDecisions.length > 0">
                <tr v-for="dec in recentDecisions" :key="dec.id" class="hover:bg-slate-50">
                  <td class="px-4 py-3 font-semibold text-blue-600">
                    <Link v-if="dec.issue" :href="`/issues/${dec.issue.id}`">
                      #{{ dec.issue.issue_no }} - {{ dec.issue.title }}
                    </Link>
                    <span v-else>-</span>
                  </td>
                  <td class="px-4 py-3">
                    <span class="bg-indigo-50 text-indigo-700 font-bold px-2 py-0.5 rounded text-[10px]">
                      Level {{ dec.decision_level }}
                    </span>
                  </td>
                  <td class="px-4 py-3 max-w-sm truncate" :title="dec.decision">{{ dec.decision }}</td>
                  <td class="px-4 py-3 font-medium">{{ dec.decision_maker?.name || '-' }}</td>
                  <td class="px-4 py-3 text-gray-500">{{ formatDateOnly(dec.created_at) }}</td>
                </tr>
              </template>
              <tr v-else>
                <td colspan="5" class="text-center py-6 text-gray-400">Tidak ada riwayat putusan pada periode ini.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Mobile Card Feed: Rekap Putusan -->
        <div class="md:hidden space-y-3">
          <template v-if="recentDecisions && recentDecisions.length > 0">
            <div 
              v-for="dec in recentDecisions" 
              :key="dec.id" 
              class="bg-slate-50 rounded-xl border border-slate-200/80 p-3.5 space-y-2 text-xs"
            >
              <div class="flex items-center justify-between">
                <span class="bg-indigo-100 text-indigo-800 font-bold px-2 py-0.5 rounded text-[10px]">
                  Level {{ dec.decision_level }}
                </span>
                <span class="text-[11px] text-slate-400 font-mono">{{ formatDateOnly(dec.created_at) }}</span>
              </div>
              <p class="font-bold text-slate-800 italic">"{{ dec.decision }}"</p>
              <div v-if="dec.issue" class="pt-1.5 border-t border-slate-200/60 flex items-center justify-between">
                <Link :href="`/issues/${dec.issue.id}`" class="text-blue-600 font-semibold truncate pr-2">
                  #{{ dec.issue.issue_no }} - {{ dec.issue.title }}
                </Link>
                <Link :href="`/issues/${dec.issue.id}`" class="text-blue-700 bg-blue-50 px-2 py-0.5 rounded text-[10px] font-bold shrink-0">
                  Lihat
                </Link>
              </div>
              <div class="text-[10px] text-slate-500">
                Oleh: <strong class="text-slate-700">{{ dec.decision_maker?.name || '-' }}</strong>
              </div>
            </div>
          </template>
          <div v-else class="text-center py-6 text-slate-400 text-xs">
            Tidak ada riwayat putusan pada periode ini.
          </div>
        </div>
      </div>

      <!-- Tabel Penugasan Task -->
      <div class="bg-white rounded-xl border border-gray-100 shadow-xs p-5">
        <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2">
          <i class="fas fa-tasks text-emerald-600"></i> Rekap Penugasan Task
        </h3>

        <!-- Desktop Table: Rekap Task -->
        <div class="hidden md:block overflow-x-auto">
          <table class="w-full text-left text-xs whitespace-nowrap">
            <thead class="bg-[#f8fafc] border-b border-gray-100 text-gray-500 font-semibold">
              <tr>
                <th class="px-4 py-3">Nama Task</th>
                <th class="px-4 py-3">PIC Pelaksana</th>
                <th class="px-4 py-3">Tenggat Waktu</th>
                <th class="px-4 py-3">Progres</th>
                <th class="px-4 py-3">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700">
              <template v-if="tasks && tasks.length > 0">
                <tr v-for="tsk in tasks" :key="tsk.id" class="hover:bg-slate-50">
                  <td class="px-4 py-3 font-semibold text-gray-800">{{ tsk.title }}</td>
                  <td class="px-4 py-3">{{ tsk.assignee?.name || '-' }}</td>
                  <td class="px-4 py-3 font-mono text-gray-500">{{ tsk.due_date ? formatDateOnly(tsk.due_date) : '-' }}</td>
                  <td class="px-4 py-3 font-bold">{{ tsk.progress_percent }}%</td>
                  <td class="px-4 py-3">
                    <span :class="statusBadgeClass(tsk.status)">
                      {{ tsk.status }}
                    </span>
                  </td>
                </tr>
              </template>
              <tr v-else>
                <td colspan="5" class="text-center py-6 text-gray-400">Tidak ada penugasan task pada periode ini.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Mobile Card Feed: Rekap Task -->
        <div class="md:hidden space-y-3">
          <template v-if="tasks && tasks.length > 0">
            <div 
              v-for="tsk in tasks" 
              :key="tsk.id" 
              class="bg-slate-50 rounded-xl border border-slate-200/80 p-3.5 space-y-2 text-xs"
            >
              <div class="flex items-center justify-between">
                <span class="font-bold text-slate-800">{{ tsk.title }}</span>
                <span :class="statusBadgeClass(tsk.status)" class="text-[10px] px-2 py-0.5 rounded-full font-bold">
                  {{ tsk.status }}
                </span>
              </div>
              <div class="w-full bg-slate-200 rounded-full h-1.5 overflow-hidden">
                <div class="bg-blue-600 h-1.5 rounded-full" :style="`width: ${tsk.progress_percent || 0}%`"></div>
              </div>
              <div class="flex items-center justify-between text-[11px] text-slate-500 pt-1">
                <span>PIC: <b class="text-slate-700">{{ tsk.assignee?.name || '-' }}</b></span>
                <span class="font-mono">{{ tsk.due_date ? formatDateOnly(tsk.due_date) : '-' }}</span>
              </div>
            </div>
          </template>
          <div v-else class="text-center py-6 text-slate-400 text-xs">
            Tidak ada penugasan task pada periode ini.
          </div>
        </div>
      </div>

      <!-- LEMBAR PENGESAHAN LAPORAN (HANYA MUNCUL SAAT CETAK / PRINT) -->
      <div class="hidden print:block mt-8 pt-6 border-t-2 border-gray-300">
        <div class="flex justify-between items-end text-xs">
          <!-- Digital Audit Box -->
          <div class="flex items-center gap-3 bg-slate-50 border border-gray-200 p-3 rounded-lg max-w-sm">
            <div class="w-14 h-14 bg-white p-1 border border-gray-300 rounded shrink-0 flex items-center justify-center">
              <img 
                :src="$page.props.branding?.logo_url || '/assets/bumn/logo-pln.png'" 
                alt="Logo PLN" 
                class="max-h-12 max-w-12 object-contain"
              />
            </div>
            <div>
              <span class="text-[9px] font-mono text-gray-500 uppercase block">Digital Document Verification</span>
              <p class="text-[10px] text-gray-600 leading-tight mt-0.5">
                Laporan kinerja terverifikasi resmi secara digital pada sistem koordinasi <b>PT PLN Nusantara Power UP Sengkang</b>.
              </p>
            </div>
          </div>

          <!-- Tanda Tangan Pejabat Pengesah -->
          <div class="text-center w-64">
            <p class="font-bold text-gray-700 mb-14">
              Sengkang, {{ new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }}<br>
              <span class="font-normal text-gray-500">Manajer Unit Pelaksana (MUP),</span>
            </p>
            <p class="font-bold text-gray-900 underline uppercase tracking-wide">
              Manajer Unit Pelaksana
            </p>
            <p class="text-gray-500 text-[10px] font-mono">
              PT PLN Nusantara Power UP Sengkang
            </p>
          </div>
        </div>
      </div>

    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  periode: String,
  totalIssues: Number,
  openIssues: Number,
  escalatedIssues: Number,
  resolvedIssues: Number,
  resolvedByTL: Number,
  resolvedByAsman: Number,
  resolvedByMUP: Number,
  totalDecisions: Number,
  tlResolutionRate: Number,
  totalTasks: Number,
  completedTasks: Number,
  inProgressTasks: Number,
  overdueTasks: Number,
  taskCompletionRate: Number,
  recentDecisions: Array,
  recentEscalations: Array,
  tasks: Array,
  units: Array,
});

const selectedPeriode = ref(props.periode || 'bulan_ini');

function changePeriode() {
  router.get('/laporan', { periode: selectedPeriode.value }, { preserveState: true, preserveScroll: true });
}

function printReport() {
  window.print();
}

function formatPeriodeLabel(p) {
  if (p === 'minggu_ini') return 'Minggu Berjalan (Minggu Ini)';
  if (p === 'minggu_lalu') return 'Minggu Lalu (Siklus Evaluasi N-1)';
  if (p === 'semua') return 'Seluruh Periode Operasional';
  return 'Bulan Ini (' + new Date().toLocaleDateString('id-ID', { month: 'long', year: 'numeric' }) + ')';
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
  const status = String(s || '').toLowerCase();
  if (status.includes('selesai') || status.includes('closed') || status.includes('resolved')) {
    return 'px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200';
  }
  if (status.includes('overdue') || status.includes('terlambat')) {
    return 'px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200';
  }
  return 'px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200';
}
</script>

<style>
@media print {
  .no-print,
  aside,
  header,
  nav,
  .print\:hidden {
    display: none !important;
  }
  body, html {
    background: white !important;
    padding: 0 !important;
    margin: 0 !important;
    color: #111827 !important;
  }
  .print\:block {
    display: block !important;
  }
}
</style>

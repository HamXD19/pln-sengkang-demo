<template>
  <AppLayout title="Daftar Keputusan Atasan">
    <!-- Subtitle Navigasi -->
    <p class="text-xs text-gray-500 -mt-3 mb-4">
      Rekap seluruh keputusan resmi berjenjang dari TL, Asman, dan Manajer Unit Pelaksana
    </p>

    <div class="bg-white rounded-xl border border-gray-100 shadow-xs flex flex-col">
      <!-- Area Filter -->
      <div class="p-5 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center bg-white rounded-t-xl gap-4">
        <div>
          <h3 class="text-base font-bold text-gray-800">Rekap Keputusan Resmi</h3>
          <p class="text-xs text-gray-500">Daftar keputusan berjenjang dan tindak lanjutnya</p>
        </div>

        <div class="flex flex-wrap gap-3 w-full md:w-auto">
          <select 
            v-model="levelFilter" 
            @change="applyFilter" 
            class="border border-gray-200 rounded-md px-3 py-2 text-xs text-gray-600 outline-none focus:border-blue-500 w-36"
          >
            <option value="">Semua Level</option>
            <option value="TL">Level TL</option>
            <option value="ASMAN">Level Asman</option>
            <option value="MUP">Level MUP</option>
          </select>

          <div class="relative flex-1 md:w-64">
            <i class="fas fa-search absolute left-3 top-2.5 text-gray-400 text-xs"></i>
            <input 
              type="text" 
              v-model="searchKeyword" 
              @input="onSearchInput" 
              placeholder="Cari keputusan / isu..." 
              class="border border-gray-200 rounded-md pl-8 pr-3 py-2 text-xs text-gray-600 outline-none focus:border-blue-500 w-full"
            />
          </div>
        </div>
      </div>

      <!-- Desktop Table View: Tabel Keputusan -->
      <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-left text-xs whitespace-nowrap">
          <thead class="bg-[#f8fafc] border-b border-gray-100 text-gray-500 font-semibold">
            <tr>
              <th class="px-6 py-4 w-12">No</th>
              <th class="px-6 py-4">Terkait Issue</th>
              <th class="px-6 py-4">Tingkat Putusan</th>
              <th class="px-6 py-4">Pernyataan Keputusan</th>
              <th class="px-6 py-4">Diputuskan Oleh</th>
              <th class="px-6 py-4">Tanggal</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 text-gray-700 font-medium">
            <template v-if="decisions.data && decisions.data.length > 0">
              <tr 
                v-for="(dec, idx) in decisions.data" 
                :key="dec.id" 
                class="hover:bg-blue-50/40 transition"
              >
                <td class="px-6 py-4 text-gray-500">
                  {{ (decisions.current_page - 1) * decisions.per_page + idx + 1 }}
                </td>
                <td class="px-6 py-4">
                  <template v-if="dec.issue">
                    <Link :href="`/issues/${dec.issue.id}`" class="font-bold text-blue-600 hover:underline">
                      {{ dec.issue.title }}
                    </Link>
                    <p class="text-[10px] text-gray-400 font-mono">#{{ dec.issue.issue_no }}</p>
                  </template>
                  <span v-else class="text-gray-400">-</span>
                </td>
                <td class="px-6 py-4">
                  <span class="bg-indigo-50 text-indigo-700 border border-indigo-200 px-2.5 py-0.5 rounded text-[10px] font-bold">
                    Level {{ dec.decision_level }}
                  </span>
                </td>
                <td class="px-6 py-4 font-semibold text-gray-800 max-w-md truncate" :title="dec.decision">
                  {{ dec.decision }}
                </td>
                <td class="px-6 py-4 text-gray-600">
                  {{ dec.decision_maker?.name || '-' }}
                </td>
                <td class="px-6 py-4 text-gray-500">
                  {{ formatDate(dec.created_at) }}
                </td>
              </tr>
            </template>
            <tr v-else>
              <td colspan="6" class="text-center py-8 text-gray-400">
                Tidak ada data keputusan yang ditemukan.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Native Mobile App Card Feed View: Tabel Keputusan -->
      <div class="md:hidden p-3 space-y-3 bg-slate-50/50">
        <template v-if="decisions.data && decisions.data.length > 0">
          <div 
            v-for="dec in decisions.data" 
            :key="dec.id" 
            class="bg-white rounded-xl border border-slate-200/80 p-4 shadow-2xs hover:shadow-xs transition active:scale-[0.99] space-y-3"
          >
            <div class="flex items-center justify-between gap-2">
              <span class="bg-indigo-50 text-indigo-700 border border-indigo-200 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold">
                Keputusan Level {{ dec.decision_level }}
              </span>
              <span class="text-[11px] font-mono text-slate-400">
                {{ formatDate(dec.created_at) }}
              </span>
            </div>

            <!-- Decision Quote Statement -->
            <div class="bg-slate-50 border-l-2 border-indigo-500 p-3 rounded-r-lg text-xs font-semibold text-slate-800">
              "{{ dec.decision }}"
            </div>

            <div v-if="dec.issue" class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs">
              <div class="min-w-0 pr-2">
                <span class="text-slate-400 text-[10px] block font-bold uppercase">Terkait Isu</span>
                <Link :href="`/issues/${dec.issue.id}`" class="font-bold text-blue-600 hover:underline block truncate text-xs">
                  #{{ dec.issue.issue_no }} - {{ dec.issue.title }}
                </Link>
              </div>
              <Link 
                :href="`/issues/${dec.issue.id}`" 
                class="bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white px-2.5 py-1 rounded-md text-[11px] font-bold border border-blue-200 shrink-0 transition"
              >
                Detail
              </Link>
            </div>

            <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
              <span>Pembuat Keputusan:</span>
              <b class="text-slate-700">{{ dec.decision_maker?.name || '-' }}</b>
            </div>
          </div>
        </template>
        <div v-else class="text-center py-8 text-slate-400 text-xs bg-white rounded-xl border border-dashed border-slate-200">
          Tidak ada data keputusan yang ditemukan.
        </div>
      </div>

      <!-- Paginasi -->
      <div v-if="decisions.links && decisions.links.length > 3" class="p-4 border-t border-gray-100 flex justify-between items-center">
        <span class="text-xs text-gray-500">
          Menampilkan {{ decisions.from || 0 }} - {{ decisions.to || 0 }} dari {{ decisions.total }} keputusan
        </span>
        <div class="flex gap-1">
          <Link 
            v-for="(link, i) in decisions.links" 
            :key="i" 
            :href="link.url || '#'" 
            v-html="link.label"
            :class="[
              'px-3 py-1 text-xs rounded border transition',
              link.active ? 'bg-blue-600 text-white border-blue-600 font-bold' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50',
              !link.url ? 'opacity-50 cursor-not-allowed' : ''
            ]"
            :preserve-scroll="true"
          />
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
  decisions: Object,
});

const urlParams = new URLSearchParams(window.location.search);
const levelFilter = ref(urlParams.get('level') || '');
const searchKeyword = ref(urlParams.get('search') || '');

function applyFilter() {
  const query = {};
  if (levelFilter.value) query.level = levelFilter.value;
  if (searchKeyword.value) query.search = searchKeyword.value;

  router.get('/keputusan', query, { preserveState: true, preserveScroll: true, replace: true });
}

let searchTimer = null;
function onSearchInput() {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => {
    applyFilter();
  }, 350);
}

function formatDate(dateStr) {
  if (!dateStr) return '-';
  const d = new Date(dateStr);
  return d.toLocaleDateString('id-ID', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
  });
}
</script>

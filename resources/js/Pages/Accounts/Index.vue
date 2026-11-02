<template>
  <AppLayout title="Daftar Akun Pegawai">
    <div class="space-y-6">

      <!-- Header Banner & Action Button -->
      <div class="bg-gradient-to-r from-slate-900 via-blue-950 to-slate-900 text-white p-5 sm:p-6 rounded-2xl shadow-md border border-slate-800 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
          <div class="flex items-center gap-2 flex-wrap mb-1.5">
            <span class="bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 text-[10px] font-black px-2.5 py-0.5 rounded-full uppercase tracking-wider flex items-center gap-1.5">
              <i class="fas fa-id-badge text-emerald-400"></i> Sinkronisasi Okt 2026
            </span>
            <span class="bg-blue-500/20 text-blue-300 border border-blue-500/30 text-[10px] font-bold px-2.5 py-0.5 rounded-full">
              UP Sengkang
            </span>
          </div>
          <h1 class="text-lg sm:text-xl font-black uppercase tracking-wide text-white flex items-center gap-2">
            <span>Daftar Akun Pegawai & Kredensial</span>
          </h1>
          <p class="text-xs text-slate-300 mt-1 max-w-2xl leading-relaxed">
            Direktori resmi 154 akun pegawai aktif PT PLN Nusantara Power UP Sengkang. Seluruh akun menggunakan <b>NID</b> untuk login dengan kata sandi default <b>123</b>.
          </p>
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-2 w-full sm:w-auto">
          <Link 
            v-if="$page.props.auth?.user?.role?.name && ['SUPER_ADMIN', 'SUPER ADMIN', 'ADMIN', 'MUP'].includes($page.props.auth.user.role.name)"
            href="/master-data?tab=users" 
            class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 active:scale-95 text-white px-4 py-2.5 rounded-xl text-xs font-bold shadow-md transition flex items-center justify-center gap-2 shrink-0 cursor-pointer"
          >
            <i class="fas fa-user-cog text-sm"></i>
            <span>Kelola Akun (Master Data)</span>
          </Link>

          <!-- Tombol Unduh File Excel Langsung -->
          <a 
            href="/download-akun-excel" 
            class="w-full sm:w-auto bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white px-4 py-2.5 rounded-xl text-xs font-bold shadow-md transition flex items-center justify-center gap-2 shrink-0 cursor-pointer"
          >
            <i class="fas fa-file-excel text-sm"></i>
            <span>Unduh File Excel (.xlsx)</span>
          </a>
        </div>
      </div>

      <!-- Filter & Search Toolbar -->
      <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs flex flex-col md:flex-row items-center justify-between gap-3">
        <!-- Input Pencarian -->
        <div class="relative w-full md:w-80">
          <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
          <input 
            type="text" 
            v-model="searchTerm" 
            placeholder="Cari Nama, NID, Jabatan, Seksi..." 
            class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 outline-none focus:border-blue-500 focus:bg-white transition"
          />
        </div>

        <div class="flex flex-wrap items-center gap-2.5 w-full md:w-auto">
          <!-- Filter Peran / Role -->
          <select 
            v-model="selectedRole" 
            class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-700 outline-none focus:border-blue-500 cursor-pointer"
          >
            <option value="all">Semua Peran (Role)</option>
            <option value="SUPER_ADMIN">SUPER ADMIN</option>
            <option value="MUP">MUP (Manager Unit)</option>
            <option value="ASMAN">ASMAN (Asisten Manajer)</option>
            <option value="TL">TL (Team Leader)</option>
            <option value="STAFF">STAFF (Pelaksana)</option>
          </select>

          <!-- Filter Bidang -->
          <select 
            v-model="selectedBidang" 
            class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-700 outline-none focus:border-blue-500 cursor-pointer"
          >
            <option value="all">Semua Bidang Utama</option>
            <option value="Bagian Operasi">Bagian Operasi</option>
            <option value="Bagian Pemeliharaan">Bagian Pemeliharaan</option>
            <option value="Bagian Engineering & QA">Bagian Engineering & QA</option>
            <option value="Bagian Business Support">Bagian Business Support</option>
            <option value="UP Sengkang">Unit Khusus (K3 & Lingkungan)</option>
          </select>

          <!-- Counter Sesi -->
          <div class="px-3 py-2 bg-slate-100 rounded-xl text-xs font-bold text-slate-600">
            <span>Ditemukan: </span>
            <strong class="text-blue-700 font-mono">{{ filteredUsers.length }}</strong>
            <span> / {{ users.length }}</span>
          </div>
        </div>
      </div>

      <!-- Tabel Daftar Akun Pegawai -->
      <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs whitespace-nowrap">
            <thead class="bg-slate-900 text-white font-bold uppercase tracking-wider text-[11px]">
              <tr>
                <th class="px-4 py-3.5 text-center w-12">No</th>
                <th class="px-5 py-3.5">NID (Login)</th>
                <th class="px-4 py-3.5 text-center">Password Default</th>
                <th class="px-5 py-3.5">Nama Pegawai</th>
                <th class="px-4 py-3.5 text-center">Role</th>
                <th class="px-5 py-3.5">Jabatan Struktural</th>
                <th class="px-5 py-3.5">Bidang Utama</th>
                <th class="px-5 py-3.5">Seksi / Unit Kerja</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
              <tr 
                v-for="(u, index) in filteredUsers" 
                :key="u.id" 
                class="hover:bg-blue-50/50 transition-colors"
                :class="index % 2 === 0 ? 'bg-white' : 'bg-slate-50/50'"
              >
                <!-- No -->
                <td class="px-4 py-3 text-center text-slate-400 font-mono text-[11px]">
                  {{ index + 1 }}
                </td>

                <!-- NID -->
                <td class="px-5 py-3 font-mono font-bold text-slate-900">
                  <span class="bg-slate-100 border border-slate-200 px-2 py-0.5 rounded text-xs select-all text-blue-900 font-bold">
                    {{ u.nid }}
                  </span>
                </td>

                <!-- Password Default -->
                <td class="px-4 py-3 text-center">
                  <span class="bg-emerald-50 text-emerald-800 border border-emerald-200 px-2 py-0.5 rounded font-mono font-bold text-xs">
                    123
                  </span>
                </td>

                <!-- Nama Pegawai -->
                <td class="px-5 py-3">
                  <span class="font-bold text-slate-900" :class="{'text-rose-900 font-black': u.role === 'SUPER_ADMIN', 'text-purple-900 font-extrabold': u.role === 'MUP', 'text-blue-900 font-extrabold': u.role === 'ASMAN', 'text-teal-900 font-bold': u.role === 'TL'}">
                    {{ u.name }}
                  </span>
                </td>

                <!-- Role Badge -->
                <td class="px-4 py-3 text-center">
                  <span 
                    v-if="u.role === 'SUPER_ADMIN'" 
                    class="bg-rose-100 text-rose-800 border border-rose-300 text-[10px] font-black px-2.5 py-0.5 rounded-full"
                  >
                    SUPER ADMIN
                  </span>
                  <span 
                    v-else-if="u.role === 'MUP'" 
                    class="bg-purple-100 text-purple-800 border border-purple-200 text-[10px] font-black px-2 py-0.5 rounded-full"
                  >
                    MUP
                  </span>
                  <span 
                    v-else-if="u.role === 'ASMAN'" 
                    class="bg-blue-100 text-blue-800 border border-blue-200 text-[10px] font-black px-2 py-0.5 rounded-full"
                  >
                    ASMAN
                  </span>
                  <span 
                    v-else-if="u.role === 'TL'" 
                    class="bg-teal-100 text-teal-800 border border-teal-200 text-[10px] font-black px-2 py-0.5 rounded-full"
                  >
                    TL
                  </span>
                  <span 
                    v-else 
                    class="bg-slate-100 text-slate-600 border border-slate-200 text-[10px] font-bold px-2 py-0.5 rounded-full"
                  >
                    STAFF
                  </span>
                </td>

                <!-- Jabatan -->
                <td class="px-5 py-3 text-slate-600 font-medium text-[11px]">
                  {{ u.position }}
                </td>

                <!-- Bidang Utama -->
                <td class="px-5 py-3 text-slate-700 text-xs font-semibold">
                  {{ u.bidang }}
                </td>

                <!-- Seksi / Unit Kerja -->
                <td class="px-5 py-3 text-slate-600 text-xs">
                  {{ u.unit }}
                </td>
              </tr>

              <tr v-if="filteredUsers.length === 0">
                <td colspan="8" class="text-center py-10 text-slate-400">
                  <i class="fas fa-search text-2xl mb-2 block"></i>
                  <span>Tidak ada akun yang sesuai dengan pencarian.</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  users: {
    type: Array,
    default: () => [],
  },
  roles: {
    type: Array,
    default: () => [],
  },
  units: {
    type: Array,
    default: () => [],
  },
  totalCount: {
    type: Number,
    default: 154,
  },
});

const searchTerm = ref('');
const selectedRole = ref('all');
const selectedBidang = ref('all');

const filteredUsers = computed(() => {
  return props.users.filter((u) => {
    // Search
    if (searchTerm.value) {
      const q = searchTerm.value.toLowerCase().trim();
      const matchName = u.name?.toLowerCase().includes(q);
      const matchNid = u.nid?.toLowerCase().includes(q);
      const matchPos = u.position?.toLowerCase().includes(q);
      const matchUnit = u.unit?.toLowerCase().includes(q);
      const matchBidang = u.bidang?.toLowerCase().includes(q);
      if (!matchName && !matchNid && !matchPos && !matchUnit && !matchBidang) {
        return false;
      }
    }

    // Role filter
    if (selectedRole.value !== 'all') {
      if (u.role !== selectedRole.value) {
        return false;
      }
    }

    // Bidang filter
    if (selectedBidang.value !== 'all') {
      if (u.bidang !== selectedBidang.value) {
        return false;
      }
    }

    return true;
  });
});
</script>

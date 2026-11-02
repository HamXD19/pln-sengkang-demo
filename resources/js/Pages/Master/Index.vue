<template>
  <AppLayout title="Master Data Organisasi">
    <div class="space-y-6">

      <!-- Header & Navigasi Tab Master Data -->
      <div class="bg-white rounded-xl border border-gray-100 shadow-xs p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 pb-4 border-b border-gray-100">
          <div>
            <h2 class="text-lg font-bold text-gray-800">
              {{ isSuperAdmin ? 'Portal Super Admin — Manajemen Akun & Tampilan Sistem' : 'Master Organisasi & Data Manajemen UP Sengkang' }}
            </h2>
            <p class="text-xs text-gray-500 mt-1">
              {{ isSuperAdmin ? 'Kelola akun karyawan, hak akses jabatan, dan kustomisasi logo & foto background login sistem PLN NP UP Sengkang.' : 'Struktur relasi hirarki Incumbent-Parent, data karyawan, dan organogram visual PLN NP UP Sengkang.' }}
            </p>
          </div>
          <div class="flex items-center gap-2">
            <button 
              v-if="!isSuperAdmin"
              type="button" 
              @click="showAddHierarchyModal = true"
              class="bg-slate-800 hover:bg-slate-900 text-white px-3.5 py-2.5 rounded-lg text-xs font-semibold shadow-xs transition flex items-center gap-1.5 cursor-pointer"
            >
              <i class="fas fa-layer-group"></i> Tambah Relasi Hirarki
            </button>
            <button 
              type="button" 
              @click="showAddUserModal = true"
              class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg text-xs font-semibold shadow-xs transition flex items-center gap-1.5 cursor-pointer"
            >
              <i class="fas fa-user-plus"></i> Tambah Pegawai Baru
            </button>
          </div>
        </div>

        <!-- Tab Selector: Khusus Super Admin -->
        <div v-if="isSuperAdmin" class="flex gap-6 border-b border-gray-100 text-xs font-semibold pt-4 overflow-x-auto">
          <button 
            type="button" 
            @click="switchTab('users')" 
            :class="currentTab === 'users' ? 'pb-3 text-blue-600 border-b-2 border-blue-600 font-bold whitespace-nowrap' : 'pb-3 text-gray-400 hover:text-gray-600 whitespace-nowrap'"
            class="cursor-pointer flex items-center gap-2"
          >
            <i class="fas fa-users-gear text-sky-600"></i>
            <span>Kelola Akun Pegawai ({{ users.total || users.data?.length || 0 }})</span>
          </button>
          <button 
            type="button" 
            @click="switchTab('branding')" 
            :class="currentTab === 'branding' ? 'pb-3 text-blue-600 border-b-2 border-blue-600 font-bold whitespace-nowrap' : 'pb-3 text-gray-400 hover:text-gray-600 whitespace-nowrap'"
            class="cursor-pointer flex items-center gap-2"
          >
            <i class="fas fa-palette text-amber-500"></i>
            <span>Kelola Image & Logo</span>
          </button>
        </div>

        <!-- Tab Selector: Admin/MUP Reguler -->
        <div v-else class="flex gap-6 border-b border-gray-100 text-xs font-semibold pt-4 overflow-x-auto">
          <button 
            type="button" 
            @click="switchTab('hierarchy')" 
            :class="currentTab === 'hierarchy' ? 'pb-3 text-blue-600 border-b-2 border-blue-600 font-bold whitespace-nowrap' : 'pb-3 text-gray-400 hover:text-gray-600 whitespace-nowrap'"
            class="cursor-pointer"
          >
            <i class="fas fa-table mr-1.5"></i> Master_Organisasi (Incumbent & Parent)
          </button>
          <button 
            type="button" 
            @click="switchTab('organogram')" 
            :class="currentTab === 'organogram' ? 'pb-3 text-blue-600 border-b-2 border-blue-600 font-bold whitespace-nowrap' : 'pb-3 text-gray-400 hover:text-gray-600 whitespace-nowrap'"
            class="cursor-pointer"
          >
            <i class="fas fa-sitemap mr-1.5"></i> Struktur Organisasi (Organogram)
          </button>
          <button 
            type="button" 
            @click="switchTab('users')" 
            :class="currentTab === 'users' ? 'pb-3 text-blue-600 border-b-2 border-blue-600 font-bold whitespace-nowrap' : 'pb-3 text-gray-400 hover:text-gray-600 whitespace-nowrap'"
            class="cursor-pointer"
          >
            <i class="fas fa-users mr-1.5"></i> Data Karyawan ({{ users.total || users.data?.length || 0 }})
          </button>
          <button 
            type="button" 
            @click="switchTab('units')" 
            :class="currentTab === 'units' ? 'pb-3 text-blue-600 border-b-2 border-blue-600 font-bold whitespace-nowrap' : 'pb-3 text-gray-400 hover:text-gray-600 whitespace-nowrap'"
            class="cursor-pointer"
          >
            <i class="fas fa-building mr-1.5"></i> Referensi Unit Kerja ({{ units.length }})
          </button>
          <button 
            type="button" 
            @click="switchTab('roles')" 
            :class="currentTab === 'roles' ? 'pb-3 text-blue-600 border-b-2 border-blue-600 font-bold whitespace-nowrap' : 'pb-3 text-gray-400 hover:text-gray-600 whitespace-nowrap'"
            class="cursor-pointer"
          >
            <i class="fas fa-shield-alt mr-1.5"></i> Matriks Wewenang BSD
          </button>
          <button 
            type="button" 
            @click="switchTab('branding')" 
            :class="currentTab === 'branding' ? 'pb-3 text-blue-600 border-b-2 border-blue-600 font-bold whitespace-nowrap' : 'pb-3 text-gray-400 hover:text-gray-600 whitespace-nowrap'"
            class="cursor-pointer"
          >
            <i class="fas fa-palette mr-1.5"></i> Kelola Image & Logo
          </button>
        </div>
      </div>

      <!-- ==================== TAB 1: MASTER_ORGANISASI ==================== -->
      <div v-if="currentTab === 'hierarchy'" class="bg-white rounded-xl border border-gray-100 shadow-xs flex flex-col">
        <div class="p-5 border-b border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-3 bg-slate-50/50 rounded-t-xl">
          <div>
            <span class="text-xs font-bold text-gray-800 uppercase tracking-wider flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span> Tabel Pemetaan Hirarki Relasi: Incumbent &rarr; Parent
            </span>
            <p class="text-[11px] text-gray-500 mt-0.5">Format pemetaan struktural sesuai lembar kerja sheet <code>Master_Organisasi</code></p>
          </div>
          <div class="text-xs text-gray-600 font-semibold bg-white px-3 py-1.5 rounded-md border border-gray-200">
            Total Relasi: <span class="text-blue-600 font-bold font-mono">{{ hierarchies.length }}</span>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs whitespace-nowrap">
            <thead class="bg-[#0f2744] text-white font-bold">
              <tr>
                <th class="px-6 py-3.5 w-12 text-center">No</th>
                <th class="px-6 py-3.5 text-sky-200 font-mono">Incumbent</th>
                <th class="px-6 py-3.5 text-amber-200 font-mono">Parent</th>
                <th class="px-6 py-3.5">Uraian Jabatan Incumbent</th>
                <th class="px-6 py-3.5">Atasan Langsung (Parent Title)</th>
                <th class="px-6 py-3.5">Pejabat Saat Ini</th>
                <th class="px-6 py-3.5 text-center">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 text-gray-700 font-medium">
              <template v-if="hierarchies && hierarchies.length > 0">
                <tr v-for="(h, idx) in hierarchies" :key="h.id" class="hover:bg-blue-50/40 transition">
                  <td class="px-6 py-3.5 text-center text-gray-500">{{ idx + 1 }}</td>
                  <td class="px-6 py-3.5 font-mono font-black text-blue-700 bg-blue-50/30">{{ h.incumbent }}</td>
                  <td class="px-6 py-3.5 font-mono font-black text-amber-700 bg-amber-50/30">{{ h.parent }}</td>
                  <td class="px-6 py-3.5 font-bold text-gray-800">{{ h.incumbent_title || '-' }}</td>
                  <td class="px-6 py-3.5 text-gray-600">{{ h.parent_title || '-' }}</td>
                  <td class="px-6 py-3.5 font-semibold text-gray-800">
                    {{ h.person_name || '-' }}
                  </td>
                  <td class="px-6 py-3.5 text-center">
                    <button @click="deleteHierarchy(h.id)" class="text-rose-500 hover:text-rose-700 p-1 cursor-pointer" title="Hapus Relasi">
                      <i class="fas fa-trash-alt"></i>
                    </button>
                  </td>
                </tr>
              </template>
              <tr v-else>
                <td colspan="7" class="text-center py-8 text-gray-400">Belum ada relasi hirarki yang dicatat.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- ==================== TAB 2: ORGANOGRAM ==================== -->
      <div v-if="currentTab === 'organogram'" class="bg-white rounded-xl border border-gray-100 shadow-xs p-6 overflow-x-auto">
        <div class="min-w-[1050px] pb-6">
          <div class="text-center mb-6">
            <span class="bg-blue-50 text-blue-700 border border-blue-200 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider">
              Struktur Organisasi Unit Pelaksana
            </span>
            <h3 class="text-base font-extrabold text-gray-800 mt-2">PT PLN NUSANTARA POWER UP SENGKANG</h3>
          </div>

          <!-- LEVEL 1: MANAGER UP SENGKANG & TL K3 / LINGKUNGAN -->
          <div class="flex items-center justify-center gap-12 relative mb-10">
            <!-- TOP CARD: MANAGER UP SENGKANG -->
            <div class="relative z-10 w-72 bg-white rounded-xl border-2 border-sky-500 shadow-lg p-3 text-center">
              <div class="w-14 h-14 mx-auto rounded-full bg-sky-100 text-sky-700 border-2 border-sky-500 flex items-center justify-center font-bold text-sm shadow-xs overflow-hidden mb-2">
                <img :src="getUserAvatar('8108050JA', 'MOH. FURQON AKHSANI')" class="w-full h-full object-cover" alt="MUP">
              </div>
              <div class="bg-sky-600 text-white py-1 px-3 rounded-md text-[10px] font-extrabold uppercase tracking-wider mb-1">
                MANAGER UP SENGKANG
              </div>
              <h4 class="text-xs font-bold text-gray-900">MOH. FURQON AKHSANI</h4>
              <p class="text-[10px] text-gray-500 font-mono mt-0.5">NID: 8108050JA</p>
            </div>

            <!-- RIGHT SIDE: TL K3 & TL LINGKUNGAN -->
            <div class="flex flex-col gap-3 pl-8 border-l-2 border-dashed border-sky-400">
              <div class="w-64 bg-red-600 text-white rounded-lg p-2.5 shadow-md flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-full overflow-hidden bg-white/20 border border-white/30 flex items-center justify-center font-bold text-xs shrink-0 shadow-xs">
                  <img :src="getUserAvatar('9315231TA', 'YOGA KURNIAWAN')" class="w-full h-full object-cover" alt="TL K3">
                </div>
                <div class="truncate">
                  <span class="block text-[9px] font-extrabold uppercase tracking-wider text-red-100">TEAM LEADER K3 & KEAMANAN</span>
                  <span class="block text-xs font-bold truncate">YOGA KURNIAWAN</span>
                  <span class="block text-[9px] text-red-200 font-mono">NID: 9315231TA</span>
                </div>
              </div>

              <div class="w-64 bg-emerald-700 text-white rounded-lg p-2.5 shadow-md flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-full overflow-hidden bg-white/20 border border-white/30 flex items-center justify-center font-bold text-xs shrink-0 shadow-xs">
                  <img :src="getUserAvatar('7223005PKW', 'H. ABDUL MALIK')" class="w-full h-full object-cover" alt="TL Lingkungan">
                </div>
                <div class="truncate">
                  <span class="block text-[9px] font-extrabold uppercase tracking-wider text-emerald-100">TEAM LEADER LINGKUNGAN</span>
                  <span class="block text-xs font-bold truncate">H. ABDUL MALIK</span>
                  <span class="block text-[9px] text-emerald-200 font-mono">NID: 7223005PKW</span>
                </div>
              </div>
            </div>
          </div>

          <!-- GARIS DISTRIBUSI CABANG 4 ASMAN -->
          <div class="w-full flex justify-center mb-6">
            <div class="w-[90%] border-t-2 border-sky-500 relative">
              <span class="absolute -top-3 left-[12%] w-2 h-6 border-l-2 border-sky-500"></span>
              <span class="absolute -top-3 left-[37%] w-2 h-6 border-l-2 border-sky-500"></span>
              <span class="absolute -top-3 left-[63%] w-2 h-6 border-l-2 border-sky-500"></span>
              <span class="absolute -top-3 left-[88%] w-2 h-6 border-l-2 border-sky-500"></span>
            </div>
          </div>

          <!-- 4 PILAR UTAMA ASMAN & TEAM LEADERNYA -->
          <div class="grid grid-cols-4 gap-4">
            
            <!-- KOLOM 1: OPERASI (KUNING) -->
            <div class="space-y-3">
              <div class="bg-amber-500 text-gray-900 rounded-xl p-3 shadow-md border-2 border-amber-600 text-center">
                <div class="w-10 h-10 mx-auto rounded-full bg-white flex items-center justify-center font-bold text-amber-700 text-xs shadow-xs mb-1.5 overflow-hidden border border-amber-300">
                  <img :src="getUserAvatar('7323002PKW', 'H. MUHAMMAD ISMAIL')" class="w-full h-full object-cover" alt="Asman Operasi">
                </div>
                <p class="text-[9px] font-black uppercase tracking-wider text-gray-950">ASSISTANT MANAGER OPERASI</p>
                <h5 class="text-xs font-bold text-gray-900">H. MUHAMMAD ISMAIL</h5>
                <p class="text-[9px] font-mono text-gray-800">NID: 7323002PKW</p>
              </div>

              <div class="space-y-2 border-t-2 border-amber-400 pt-2">
                <div 
                  v-for="tl in tlsOperasi" 
                  :key="tl[2]"
                  class="bg-amber-100/90 border border-amber-300 rounded-lg p-2 text-xs hover:shadow-xs transition flex items-center gap-2.5"
                >
                  <div class="w-8 h-8 rounded-full overflow-hidden bg-white border border-amber-300 shrink-0 shadow-xs">
                    <img :src="getUserAvatar(tl[2], tl[1])" class="w-full h-full object-cover" :alt="tl[1]">
                  </div>
                  <div class="min-w-0 flex-1">
                    <span class="block text-[8px] font-black text-amber-900 uppercase tracking-tight truncate">{{ tl[0] }}</span>
                    <span class="block font-bold text-gray-900 text-[11px] truncate">{{ tl[1] }}</span>
                    <span class="block text-[9px] text-gray-600 font-mono">NID: {{ tl[2] }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- KOLOM 2: PEMELIHARAAN (BIRU TUA) -->
            <div class="space-y-3">
              <div class="bg-blue-900 text-white rounded-xl p-3 shadow-md border-2 border-blue-950 text-center">
                <div class="w-10 h-10 mx-auto rounded-full bg-white flex items-center justify-center font-bold text-blue-900 text-xs shadow-xs mb-1.5 overflow-hidden border border-blue-400">
                  <img :src="getUserAvatar('8208036JA', 'CAHYA PATRIA')" class="w-full h-full object-cover" alt="Asman Har">
                </div>
                <p class="text-[9px] font-black uppercase tracking-wider text-blue-200">ASSISTANT MANAGER PEMELIHARAAN</p>
                <h5 class="text-xs font-bold text-white">CAHYA PATRIA C.T</h5>
                <p class="text-[9px] font-mono text-blue-300">NID: 8208036JA</p>
              </div>

              <div class="space-y-2 border-t-2 border-blue-800 pt-2">
                <div 
                  v-for="tl in tlsHar" 
                  :key="tl[2]"
                  class="bg-blue-50 border border-blue-200 rounded-lg p-2 text-xs hover:shadow-xs transition flex items-center gap-2.5"
                >
                  <div class="w-8 h-8 rounded-full overflow-hidden bg-white border border-blue-300 shrink-0 shadow-xs">
                    <img :src="getUserAvatar(tl[2], tl[1])" class="w-full h-full object-cover" :alt="tl[1]">
                  </div>
                  <div class="min-w-0 flex-1">
                    <span class="block text-[8px] font-black text-blue-900 uppercase tracking-tight truncate">{{ tl[0] }}</span>
                    <span class="block font-bold text-gray-900 text-[11px] truncate">{{ tl[1] }}</span>
                    <span class="block text-[9px] text-gray-600 font-mono">NID: {{ tl[2] }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- KOLOM 3: ENJINIRING (SKY BLUE) -->
            <div class="space-y-3">
              <div class="bg-sky-500 text-white rounded-xl p-3 shadow-md border-2 border-sky-600 text-center">
                <div class="w-10 h-10 mx-auto rounded-full bg-white flex items-center justify-center font-bold text-sky-700 text-xs shadow-xs mb-1.5 overflow-hidden border border-sky-300">
                  <img :src="getUserAvatar('8711202Z', 'DANNY SETIAWAN')" class="w-full h-full object-cover" alt="Asman Enjiniring">
                </div>
                <p class="text-[9px] font-black uppercase tracking-wider text-sky-100">ASSISTANT MANAGER ENJINIRING</p>
                <h5 class="text-xs font-bold text-white">DANNY SETIAWAN</h5>
                <p class="text-[9px] font-mono text-sky-200">NID: 8711202Z</p>
              </div>

              <div class="space-y-2 border-t-2 border-sky-400 pt-2">
                <div 
                  v-for="tl in tlsEnjiniring" 
                  :key="tl[2]"
                  class="bg-sky-50 border border-sky-200 rounded-lg p-2 text-xs hover:shadow-xs transition flex items-center gap-2.5"
                >
                  <div class="w-8 h-8 rounded-full overflow-hidden bg-white border border-sky-300 shrink-0 shadow-xs">
                    <img :src="getUserAvatar(tl[2], tl[1])" class="w-full h-full object-cover" :alt="tl[1]">
                  </div>
                  <div class="min-w-0 flex-1">
                    <span class="block text-[8px] font-black text-sky-900 uppercase tracking-tight truncate">{{ tl[0] }}</span>
                    <span class="block font-bold text-gray-900 text-[11px] truncate">{{ tl[1] }}</span>
                    <span class="block text-[9px] text-gray-600 font-mono">NID: {{ tl[2] }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- KOLOM 4: KEUANGAN & UMUM (UNGU / VIOLET) -->
            <div class="space-y-3">
              <div class="bg-purple-700 text-white rounded-xl p-3 shadow-md border-2 border-purple-800 text-center">
                <div class="w-10 h-10 mx-auto rounded-full bg-white flex items-center justify-center font-bold text-purple-700 text-xs shadow-xs mb-1.5 overflow-hidden border border-purple-300">
                  <img :src="getUserAvatar('8410014D3R', 'MUHAMMAD SUDIRMAN')" class="w-full h-full object-cover" alt="Asman Keuangan">
                </div>
                <p class="text-[9px] font-black uppercase tracking-wider text-purple-200">ASMAN KEUANGAN & UMUM</p>
                <h5 class="text-xs font-bold text-white">MUHAMMAD SUDIRMAN</h5>
                <p class="text-[9px] font-mono text-purple-200">NID: 8410014D3R</p>
              </div>

              <div class="space-y-2 border-t-2 border-purple-400 pt-2">
                <div 
                  v-for="tl in tlsKeuangan" 
                  :key="tl[2]"
                  class="bg-purple-50 border border-purple-200 rounded-lg p-2 text-xs hover:shadow-xs transition flex items-center gap-2.5"
                >
                  <div class="w-8 h-8 rounded-full overflow-hidden bg-white border border-purple-300 shrink-0 shadow-xs">
                    <img :src="getUserAvatar(tl[2], tl[1])" class="w-full h-full object-cover" :alt="tl[1]">
                  </div>
                  <div class="min-w-0 flex-1">
                    <span class="block text-[8px] font-black text-purple-900 uppercase tracking-tight truncate">{{ tl[0] }}</span>
                    <span class="block font-bold text-gray-900 text-[11px] truncate">{{ tl[1] }}</span>
                    <span class="block text-[9px] text-gray-600 font-mono">NID: {{ tl[2] }}</span>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>

      <!-- ==================== TAB 3: DATA KARYAWAN ==================== -->
      <div v-if="currentTab === 'users'" class="bg-white rounded-xl border border-gray-100 shadow-xs flex flex-col">
        <!-- Area Filter User -->
        <div class="p-5 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
          <div class="flex flex-wrap gap-3 flex-1 w-full">
            <div class="relative flex-1 min-w-[200px]">
              <i class="fas fa-search absolute left-3 top-2.5 text-gray-400 text-xs"></i>
              <input 
                type="text" 
                v-model="userFilters.search" 
                @input="onUserSearchInput"
                placeholder="Cari nama, email, NID..." 
                class="border border-gray-200 rounded-md pl-8 pr-3 py-2 text-xs text-gray-600 outline-none focus:border-blue-500 w-full"
              />
            </div>

            <select 
              v-model="userFilters.role_id" 
              @change="applyUserFilter"
              class="border border-gray-200 rounded-md px-3 py-2 text-xs text-gray-600 outline-none focus:border-blue-500 w-32 bg-white"
            >
              <option value="">Semua Role</option>
              <option v-for="r in roles" :key="r.id" :value="r.id">{{ r.name }}</option>
            </select>

            <select 
              v-model="userFilters.unit_id" 
              @change="applyUserFilter"
              class="border border-gray-200 rounded-md px-3 py-2 text-xs text-gray-600 outline-none focus:border-blue-500 w-40 bg-white"
            >
              <option value="">Semua Unit</option>
              <option v-for="u in units" :key="u.id" :value="u.id">{{ u.name }}</option>
            </select>

            <select 
              v-model="userFilters.tl_id" 
              @change="applyUserFilter"
              class="border border-gray-200 rounded-md px-3 py-2 text-xs text-gray-600 outline-none focus:border-blue-500 w-40 bg-white"
            >
              <option value="">Semua TL</option>
              <option v-for="tl in tls" :key="tl.id" :value="tl.id">{{ tl.name }}</option>
            </select>

            <select 
              v-model="userFilters.asman_id" 
              @change="applyUserFilter"
              class="border border-gray-200 rounded-md px-3 py-2 text-xs text-gray-600 outline-none focus:border-blue-500 w-44 bg-white"
            >
              <option value="">Semua ASMAN</option>
              <option v-for="asman in asmans" :key="asman.id" :value="asman.id">{{ asman.name }}</option>
            </select>

            <button 
              v-if="userFilters.search || userFilters.role_id || userFilters.unit_id || userFilters.tl_id || userFilters.asman_id" 
              @click="resetUserFilter"
              type="button"
              class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-gray-600 rounded-md text-xs font-semibold flex items-center gap-1 cursor-pointer transition"
            >
              <i class="fas fa-times"></i> Reset
            </button>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs whitespace-nowrap">
            <thead class="bg-[#f8fafc] border-b border-gray-100 text-gray-500 font-semibold">
              <tr>
                <th class="px-6 py-4">Foto & Nama</th>
                <th class="px-6 py-4">NID</th>
                <th class="px-6 py-4">Role BSD</th>
                <th class="px-6 py-4">Jabatan</th>
                <th class="px-6 py-4">Unit Kerja</th>
                <th class="px-6 py-4">Team Leader (TL)</th>
                <th class="px-6 py-4">Asisten Manajer (ASMAN)</th>
                <th class="px-6 py-4 text-center">Status</th>
                <th class="px-6 py-4 text-center">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700 font-medium">
              <template v-if="users.data && users.data.length > 0">
                <tr v-for="usr in users.data" :key="usr.id" class="hover:bg-blue-50/40 transition">
                  <td class="px-6 py-4 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full overflow-hidden bg-slate-200 border border-gray-200 shrink-0">
                      <img v-if="usr.avatar_url || usr.avatar" :src="usr.avatar_url || `/storage/${usr.avatar}`" class="w-full h-full object-cover">
                      <span v-else class="w-full h-full flex items-center justify-center font-bold text-xs text-gray-500">
                        {{ (usr.name || 'U').substring(0, 2).toUpperCase() }}
                      </span>
                    </div>
                    <div>
                      <span class="font-bold text-gray-800 block">{{ usr.name }}</span>
                      <span class="text-[10px] text-gray-400 font-mono">{{ usr.email }}</span>
                    </div>
                  </td>
                  <td class="px-6 py-4 font-mono font-semibold text-gray-600">{{ usr.employee_no || '-' }}</td>
                  <td class="px-6 py-4">
                    <span v-if="usr.role?.name === 'SUPER_ADMIN'" class="bg-rose-50 text-rose-700 border border-rose-300 px-2.5 py-0.5 rounded text-[10px] font-black">
                      SUPER ADMIN
                    </span>
                    <span v-else class="bg-blue-50 text-blue-700 border border-blue-200 px-2.5 py-0.5 rounded text-[10px] font-bold">
                      {{ usr.role?.name || 'STAFF' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-gray-700">{{ usr.position?.name || '-' }}</td>
                  <td class="px-6 py-4 text-gray-600">{{ usr.organization_unit?.name || 'UP Sengkang' }}</td>
                  <td class="px-6 py-4">
                    <div v-if="usr.hierarchy_info?.tl" class="flex items-center gap-2">
                      <div class="w-6 h-6 rounded-full overflow-hidden bg-blue-100 shrink-0 border border-blue-200">
                        <img :src="usr.hierarchy_info.tl.avatar" class="w-full h-full object-cover">
                      </div>
                      <div class="min-w-0 max-w-[170px]">
                        <span class="font-bold text-gray-800 text-xs block truncate" :title="usr.hierarchy_info.tl.name">
                          {{ usr.hierarchy_info.tl.name }}
                        </span>
                        <span v-if="usr.hierarchy_info.tl.is_self" class="text-[9px] font-bold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-200 inline-block">
                          Diri Sendiri (TL)
                        </span>
                        <span v-else class="text-[10px] text-gray-400 block truncate" :title="usr.hierarchy_info.tl.position">
                          {{ usr.hierarchy_info.tl.position }}
                        </span>
                      </div>
                    </div>
                    <span v-else-if="['ASMAN', 'MUP', 'ADMIN'].includes(usr.role?.name)" class="text-slate-400 italic text-[11px]">
                      - (Manajemen)
                    </span>
                    <span v-else class="text-gray-400 italic text-[11px]">-</span>
                  </td>
                  <td class="px-6 py-4">
                    <div v-if="usr.hierarchy_info?.asman" class="flex items-center gap-2">
                      <div class="w-6 h-6 rounded-full overflow-hidden bg-indigo-100 shrink-0 border border-indigo-200">
                        <img :src="usr.hierarchy_info.asman.avatar" class="w-full h-full object-cover">
                      </div>
                      <div class="min-w-0 max-w-[170px]">
                        <span class="font-bold text-gray-800 text-xs block truncate" :title="usr.hierarchy_info.asman.name">
                          {{ usr.hierarchy_info.asman.name }}
                        </span>
                        <span v-if="usr.hierarchy_info.asman.is_self" class="text-[9px] font-bold text-indigo-600 bg-indigo-50 px-1.5 py-0.5 rounded border border-indigo-200 inline-block">
                          Diri Sendiri (ASMAN)
                        </span>
                        <span v-else class="text-[10px] text-gray-400 block truncate" :title="usr.hierarchy_info.asman.position">
                          {{ usr.hierarchy_info.asman.position }}
                        </span>
                      </div>
                    </div>
                    <span v-else-if="usr.hierarchy_info?.is_direct_to_mup" class="bg-slate-100 text-slate-700 border border-slate-200 px-2 py-0.5 rounded text-[10px] font-bold inline-block">
                      Langsung ke MUP
                    </span>
                    <span v-else-if="usr.role?.name === 'MUP'" class="text-purple-700 font-bold text-[11px]">
                      Pimpinan Unit
                    </span>
                    <span v-else class="text-gray-400 italic text-[11px]">-</span>
                  </td>
                  <td class="px-6 py-4 text-center">
                    <span :class="usr.is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200'" class="px-2 py-0.5 rounded text-[10px] font-bold border">
                      {{ usr.is_active ? 'Aktif' : 'Non-Aktif' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-center">
                    <div class="flex items-center justify-center gap-1.5">
                      <button 
                        type="button"
                        @click="openEditUserModal(usr)" 
                        class="px-2.5 py-1 bg-blue-50 hover:bg-blue-600 hover:text-white text-blue-700 rounded text-xs font-bold transition border border-blue-200 flex items-center gap-1 cursor-pointer" 
                        title="Edit Pegawai"
                      >
                        <i class="fas fa-edit"></i> Edit
                      </button>
                      <button 
                        type="button"
                        @click="resetPassword(usr)" 
                        class="px-2 py-1 bg-amber-50 hover:bg-amber-600 hover:text-white text-amber-700 rounded text-xs font-bold transition border border-amber-200 flex items-center gap-1 cursor-pointer" 
                        title="Reset Password Pegawai ke Default 123"
                      >
                        <i class="fas fa-key text-[10px]"></i> Reset 123
                      </button>
                      <button 
                        type="button"
                        @click="deleteUser(usr.id)" 
                        class="p-1 text-rose-500 hover:text-rose-700 rounded transition cursor-pointer" 
                        title="Hapus Pegawai"
                      >
                        <i class="fas fa-trash-alt"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              </template>
              <tr v-else>
                <td colspan="9" class="text-center py-8 text-gray-400">Tidak ada pegawai yang sesuai filter.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Paginasi Users -->
        <div v-if="users.links && users.links.length > 3" class="p-4 border-t border-gray-100 flex justify-between items-center">
          <span class="text-xs text-gray-500">
            Total {{ users.total }} pegawai
          </span>
          <div class="flex gap-1">
            <Link 
              v-for="(link, i) in users.links" 
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

      <!-- ==================== TAB 4: REFERENSI UNIT KERJA ==================== -->
      <div v-if="currentTab === 'units'" class="bg-white rounded-xl border border-gray-100 shadow-xs p-6">
        <h3 class="text-sm font-bold text-gray-800 mb-4">Referensi Unit Kerja & Bidang</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div v-for="unit in units" :key="unit.id" class="p-4 rounded-xl border border-gray-200 bg-slate-50/50">
            <h4 class="font-bold text-gray-800 text-xs">{{ unit.name }}</h4>
            <p class="text-[11px] text-gray-500 mt-1">{{ unit.code || 'UP Sengkang' }}</p>
          </div>
        </div>
      </div>

      <!-- ==================== TAB 5: MATRIKS WEWENANG ==================== -->
      <div v-if="currentTab === 'roles'" class="bg-white rounded-xl border border-gray-100 shadow-xs p-6 space-y-4">
        <h3 class="text-sm font-bold text-gray-800">Matriks Wewenang & Hak Akses Berjenjang (BSD)</h3>
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs whitespace-nowrap">
            <thead class="bg-slate-100 text-gray-700 font-bold">
              <tr>
                <th class="px-4 py-3">Level Role</th>
                <th class="px-4 py-3">Wewenang Putusan</th>
                <th class="px-4 py-3">Eskalasi</th>
                <th class="px-4 py-3">Delegasi Task</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700">
              <tr>
                <td class="px-4 py-3 font-bold text-blue-700">STAFF / PELAKSANA</td>
                <td class="px-4 py-3">Hanya pelaporan issue lapangan</td>
                <td class="px-4 py-3">Otomatis masuk ke TL</td>
                <td class="px-4 py-3">Task mandiri</td>
              </tr>
              <tr>
                <td class="px-4 py-3 font-bold text-blue-700">TEAM LEADER (TL)</td>
                <td class="px-4 py-3">Putusan teknis tingkat seksi (Lowest Competent Level)</td>
                <td class="px-4 py-3">Eskalasi ke ASMAN jika butuh anggaran/lintas bidang</td>
                <td class="px-4 py-3">Dapat delegasi ke staf seksinya</td>
              </tr>
              <tr>
                <td class="px-4 py-3 font-bold text-blue-700">ASISTEN MANAJER (ASMAN)</td>
                <td class="px-4 py-3">Putusan operasional bidang dan koordinasi lintas seksi</td>
                <td class="px-4 py-3">Eskalasi ke MUP jika wewenang strategis</td>
                <td class="px-4 py-3">Dapat delegasi ke TL & staf bidangnya</td>
              </tr>
              <tr>
                <td class="px-4 py-3 font-bold text-blue-700">MANAJER UNIT (MUP)</td>
                <td class="px-4 py-3">Putusan strategis puncak seluruh unit UP Sengkang</td>
                <td class="px-4 py-3">Tingkat tertinggi (tidak ada eskalasi internal)</td>
                <td class="px-4 py-3">Dapat delegasi ke seluruh jajaran unit</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- ==================== TAB 6: KELOLA IMAGE & LOGO (BRANDING) ==================== -->
      <div v-if="currentTab === 'branding'" class="space-y-6">
        
        <!-- Header Information Card -->
        <div class="bg-gradient-to-r from-[#0b1e36] to-[#0f3460] text-white rounded-2xl p-6 shadow-md border border-slate-700/50 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
          <div class="space-y-1">
            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-sky-500/20 border border-sky-400/30 text-sky-300 text-[11px] font-bold">
              <i class="fas fa-palette"></i>
              <span>Kustomisasi Visual & Identitas Sistem</span>
            </div>
            <h3 class="text-base sm:text-lg font-black tracking-tight text-white mt-1">
              Pengaturan Gambar Halaman Login & Logo Aplikasi
            </h3>
            <p class="text-xs text-slate-300 max-w-2xl leading-relaxed">
              Super Admin dapat memperbarui foto latar belakang halaman login dan logo resmi PLN Nusantara Power secara mandiri. Perubahan langsung aktif secara real-time untuk seluruh pengguna tanpa kendala cache browser.
            </p>
          </div>

          <div class="flex items-center gap-2 shrink-0">
            <a 
              href="/login" 
              target="_blank" 
              class="px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-2xs cursor-pointer"
            >
              <i class="fas fa-external-link-alt"></i>
              <span>Cek Halaman Login</span>
            </a>
          </div>
        </div>

        <!-- Grid 2 Kolom: Background Login & Logo Aplikasi -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

          <!-- CARD 1: BACKGROUND HALAMAN LOGIN -->
          <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs overflow-hidden flex flex-col justify-between">
            <div>
              <!-- Card Header -->
              <div class="p-5 border-b border-gray-100 flex items-center justify-between bg-slate-50/60">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-xl bg-sky-600 text-white flex items-center justify-center text-base shadow-xs">
                    <i class="fas fa-image"></i>
                  </div>
                  <div>
                    <h4 class="text-sm font-extrabold text-gray-800">Background Halaman Login</h4>
                    <p class="text-[11px] text-gray-500">Foto landscape pemandangan / pembangkit UP Sengkang</p>
                  </div>
                </div>
                <span class="text-[10px] font-extrabold bg-sky-100 text-sky-700 px-2.5 py-1 rounded-full uppercase tracking-wider">
                  Rasio 16:9 HD
                </span>
              </div>

              <!-- Card Body: Preview Area -->
              <div class="p-5 space-y-4">
                <!-- Preview Frame with Mini Mockup Overlay -->
                <div class="relative w-full aspect-video rounded-xl overflow-hidden border border-slate-200 shadow-inner bg-slate-900 group">
                  <img 
                    :src="bgPreview || branding?.login_bg_url || '/assets/bumn/bg-sengkang-hd.jpg'" 
                    alt="Preview Background Login"
                    class="w-full h-full object-cover object-center transition-all duration-300"
                  />
                  <!-- Gradient overlay -->
                  <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/30 to-transparent"></div>

                  <!-- Mini Mockup Box Overlay to simulate real login -->
                  <div class="absolute bottom-3 left-3 bg-white/90 backdrop-blur-md rounded-lg p-2.5 max-w-[200px] border border-white/40 shadow-lg text-[10px] pointer-events-none">
                    <div class="flex items-center gap-1.5 mb-1">
                      <img :src="logoPreview || branding?.logo_url || '/assets/bumn/logo-pln.png'" class="w-4 h-4 object-contain" />
                      <span class="font-extrabold text-slate-800 text-[9px]">PLN NP Sengkang</span>
                    </div>
                    <div class="h-2 w-16 bg-slate-200 rounded mb-1"></div>
                    <div class="h-4 w-full bg-blue-600 rounded text-white text-[8px] font-bold flex items-center justify-center">
                      Masuk
                    </div>
                  </div>

                  <!-- Badge Live Preview jika ada file terpilih -->
                  <div v-if="bgPreview" class="absolute top-3 right-3 bg-emerald-500 text-white text-[10px] font-black px-2.5 py-1 rounded-full shadow-md flex items-center gap-1 animate-pulse">
                    <i class="fas fa-check-circle"></i>
                    <span>Pratinjau Foto Baru</span>
                  </div>
                </div>

                <!-- Guidance Info -->
                <div class="bg-slate-50 rounded-xl p-3 border border-slate-200/70 text-xs text-slate-600 space-y-1">
                  <p class="font-bold text-slate-700 flex items-center gap-1.5 text-[11px]">
                    <i class="fas fa-info-circle text-sky-500"></i> Ketentuan Foto Background:
                  </p>
                  <p class="text-[11px] text-slate-500 pl-4">
                    &bull; Format: <strong>JPG, JPEG, PNG, WEBP</strong> (Maksimal 15 MB)<br />
                    &bull; Resolusi ideal: <strong>1920 &times; 1080 piksel</strong> (Full HD Landscape)<br />
                    &bull; Sistem otomatis memperbarui latar login untuk semua perangkat.
                  </p>
                </div>

                <!-- Input File Picker -->
                <div>
                  <input 
                    ref="bgFileInput" 
                    type="file" 
                    accept="image/png,image/jpeg,image/webp" 
                    @change="onBgFileSelected" 
                    class="hidden" 
                    id="upload-bg-input"
                  />
                  <label 
                    for="upload-bg-input" 
                    class="w-full flex flex-col items-center justify-center p-4 border-2 border-dashed border-sky-300 hover:border-sky-500 rounded-xl bg-sky-50/50 hover:bg-sky-50 transition cursor-pointer text-center group"
                  >
                    <i class="fas fa-cloud-arrow-up text-2xl text-sky-500 group-hover:scale-110 transition-transform mb-1.5"></i>
                    <span class="text-xs font-bold text-slate-700">Pilih Foto Background Baru</span>
                    <span class="text-[10px] text-slate-400 mt-0.5">Klik untuk browse file gambar dari komputer/HP</span>
                  </label>

                  <!-- Status Selected File -->
                  <div v-if="bgFile" class="mt-3 flex items-center justify-between p-2.5 bg-emerald-50 border border-emerald-200 rounded-xl text-xs text-emerald-800">
                    <div class="flex items-center gap-2 truncate">
                      <i class="fas fa-file-image text-emerald-600 text-base"></i>
                      <div class="truncate">
                        <p class="font-bold truncate">{{ bgFile.name }}</p>
                        <p class="text-[10px] text-emerald-600">{{ (bgFile.size / 1024 / 1024).toFixed(2) }} MB</p>
                      </div>
                    </div>
                    <button 
                      type="button" 
                      @click="cancelBgSelection" 
                      class="text-rose-500 hover:text-rose-700 p-1 font-bold text-xs cursor-pointer"
                      title="Batalkan Pilihan"
                    >
                      <i class="fas fa-times-circle text-base"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Card Actions Footer -->
            <div class="p-5 border-t border-gray-100 bg-slate-50/70 flex flex-wrap items-center justify-between gap-3">
              <button 
                type="button" 
                @click="confirmResetBg" 
                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-gray-300 bg-white hover:bg-gray-100 text-gray-700 text-xs font-semibold shadow-2xs transition cursor-pointer active:scale-95"
                title="Pulihkan kembali ke foto default pembangkit listrik UP Sengkang"
              >
                <i class="fas fa-rotate-left text-gray-500"></i>
                <span>Kembalikan Default</span>
              </button>

              <button 
                type="button" 
                @click="submitUpdateBg" 
                :disabled="!bgForm.background || bgForm.processing"
                class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-xs font-bold shadow-xs transition cursor-pointer active:scale-95 disabled:opacity-50 disabled:pointer-events-none"
              >
                <i v-if="bgForm.processing" class="fas fa-spinner fa-spin"></i>
                <i v-else class="fas fa-check"></i>
                <span>{{ bgForm.processing ? 'Menyimpan...' : 'Simpan Background Baru' }}</span>
              </button>
            </div>
          </div>

          <!-- CARD 2: LOGO RESMI APLIKASI / PLN -->
          <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs overflow-hidden flex flex-col justify-between">
            <div>
              <!-- Card Header -->
              <div class="p-5 border-b border-gray-100 flex items-center justify-between bg-slate-50/60">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center text-base shadow-xs">
                    <i class="fas fa-certificate"></i>
                  </div>
                  <div>
                    <h4 class="text-sm font-extrabold text-gray-800">Logo Resmi Aplikasi (PLN)</h4>
                    <p class="text-[11px] text-gray-500">Logo resmi di sidebar, header, dan login box</p>
                  </div>
                </div>
                <span class="text-[10px] font-extrabold bg-amber-100 text-amber-800 px-2.5 py-1 rounded-full uppercase tracking-wider">
                  PNG / SVG
                </span>
              </div>

              <!-- Card Body: Preview Area -->
              <div class="p-5 space-y-4">
                <!-- Dual Preview: Mode Gelap (Sidebar) & Mode Terang (Header) -->
                <div class="grid grid-cols-2 gap-3">
                  
                  <!-- Mode Gelap (Sidebar Dark #0b1e36) -->
                  <div class="bg-[#0b1e36] rounded-xl p-4 border border-slate-700 text-center flex flex-col items-center justify-center min-h-[140px] relative">
                    <span class="absolute top-2 left-2 text-[9px] font-extrabold text-slate-400 uppercase tracking-wider">
                      Latar Gelap (Sidebar)
                    </span>
                    <div class="w-16 h-16 flex items-center justify-center my-auto">
                      <img 
                        :src="logoPreview || branding?.logo_url || '/assets/bumn/logo-pln.png'" 
                        alt="Logo Preview Dark"
                        class="max-w-full max-h-full object-contain filter drop-shadow-md"
                      />
                    </div>
                    <span class="text-[10px] text-slate-300 font-bold mt-1">PLN NP UP Sengkang</span>
                  </div>

                  <!-- Mode Terang (Header / Card Putih) -->
                  <div class="bg-slate-100 rounded-xl p-4 border border-slate-200 text-center flex flex-col items-center justify-center min-h-[140px] relative">
                    <span class="absolute top-2 left-2 text-[9px] font-extrabold text-slate-500 uppercase tracking-wider">
                      Latar Terang (Header)
                    </span>
                    <div class="w-16 h-16 flex items-center justify-center my-auto">
                      <img 
                        :src="logoPreview || branding?.logo_url || '/assets/bumn/logo-pln.png'" 
                        alt="Logo Preview Light"
                        class="max-w-full max-h-full object-contain filter drop-shadow-xs"
                      />
                    </div>
                    <span class="text-[10px] text-slate-700 font-bold mt-1">PLN NP UP Sengkang</span>
                  </div>

                </div>

                <!-- Guidance Info -->
                <div class="bg-slate-50 rounded-xl p-3 border border-slate-200/70 text-xs text-slate-600 space-y-1">
                  <p class="font-bold text-slate-700 flex items-center gap-1.5 text-[11px]">
                    <i class="fas fa-info-circle text-amber-500"></i> Ketentuan File Logo:
                  </p>
                  <p class="text-[11px] text-slate-500 pl-4">
                    &bull; Format: <strong>PNG (Transparan disarankan)</strong> atau <strong>WEBP/SVG</strong><br />
                    &bull; Rasio: <strong>1:1 (Persegi/Square)</strong> minimal 200 &times; 200 piksel<br />
                    &bull; Ukuran file: Maksimal <strong>5 MB</strong>.
                  </p>
                </div>

                <!-- Input File Picker -->
                <div>
                  <input 
                    ref="logoFileInput" 
                    type="file" 
                    accept="image/png,image/jpeg,image/webp,image/svg+xml" 
                    @change="onLogoFileSelected" 
                    class="hidden" 
                    id="upload-logo-input"
                  />
                  <label 
                    for="upload-logo-input" 
                    class="w-full flex flex-col items-center justify-center p-4 border-2 border-dashed border-amber-300 hover:border-amber-500 rounded-xl bg-amber-50/50 hover:bg-amber-50 transition cursor-pointer text-center group"
                  >
                    <i class="fas fa-cloud-arrow-up text-2xl text-amber-500 group-hover:scale-110 transition-transform mb-1.5"></i>
                    <span class="text-xs font-bold text-slate-700">Pilih File Logo Baru</span>
                    <span class="text-[10px] text-slate-400 mt-0.5">Klik untuk browse file PNG transparan baru</span>
                  </label>

                  <!-- Status Selected File -->
                  <div v-if="logoFile" class="mt-3 flex items-center justify-between p-2.5 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-900">
                    <div class="flex items-center gap-2 truncate">
                      <i class="fas fa-file-image text-amber-600 text-base"></i>
                      <div class="truncate">
                        <p class="font-bold truncate">{{ logoFile.name }}</p>
                        <p class="text-[10px] text-amber-700">{{ (logoFile.size / 1024 / 1024).toFixed(2) }} MB</p>
                      </div>
                    </div>
                    <button 
                      type="button" 
                      @click="cancelLogoSelection" 
                      class="text-rose-500 hover:text-rose-700 p-1 font-bold text-xs cursor-pointer"
                      title="Batalkan Pilihan"
                    >
                      <i class="fas fa-times-circle text-base"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Card Actions Footer -->
            <div class="p-5 border-t border-gray-100 bg-slate-50/70 flex flex-wrap items-center justify-between gap-3">
              <button 
                type="button" 
                @click="confirmResetLogo" 
                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-gray-300 bg-white hover:bg-gray-100 text-gray-700 text-xs font-semibold shadow-2xs transition cursor-pointer active:scale-95"
                title="Pulihkan kembali ke logo default resmi PLN"
              >
                <i class="fas fa-rotate-left text-gray-500"></i>
                <span>Kembalikan Default</span>
              </button>

              <button 
                type="button" 
                @click="submitUpdateLogo" 
                :disabled="!logoForm.logo || logoForm.processing"
                class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold shadow-xs transition cursor-pointer active:scale-95 disabled:opacity-50 disabled:pointer-events-none"
              >
                <i v-if="logoForm.processing" class="fas fa-spinner fa-spin"></i>
                <i v-else class="fas fa-check"></i>
                <span>{{ logoForm.processing ? 'Menyimpan...' : 'Simpan Logo Baru' }}</span>
              </button>
            </div>
          </div>

        </div>

      </div>

    </div>

    <!-- MODAL TAMBAH RELASI HIRARKI -->
    <div v-if="showAddHierarchyModal" class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-xl max-w-lg w-full p-6 shadow-2xl border border-gray-100">
        <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-4">
          <h3 class="text-sm font-bold text-gray-800">Tambah Relasi Hirarki Incumbent &rarr; Parent</h3>
          <button @click="showAddHierarchyModal = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold cursor-pointer">&times;</button>
        </div>

        <form @submit.prevent="submitAddHierarchy" class="space-y-4">
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">Kode Incumbent <span class="text-red-500">*</span></label>
              <input type="text" v-model="hierarchyForm.incumbent" required placeholder="Contoh: 10000001" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500">
            </div>
            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">Kode Parent <span class="text-red-500">*</span></label>
              <input type="text" v-model="hierarchyForm.parent" required placeholder="Contoh: 10000000" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500">
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Uraian Jabatan Incumbent</label>
            <input type="text" v-model="hierarchyForm.incumbent_title" placeholder="Contoh: TEAM LEADER K3 & KEAMANAN" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500">
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Uraian Jabatan Parent (Atasan)</label>
            <input type="text" v-model="hierarchyForm.parent_title" placeholder="Contoh: MANAGER UP SENGKANG" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500">
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Nama Pejabat Saat Ini</label>
            <input type="text" v-model="hierarchyForm.person_name" placeholder="Contoh: YOGA KURNIAWAN" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500">
          </div>

          <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
            <button type="button" @click="showAddHierarchyModal = false" class="px-4 py-2 border border-gray-200 text-gray-600 rounded-md text-xs hover:bg-gray-50 cursor-pointer">Batal</button>
            <button 
              type="submit" 
              :disabled="hierarchyForm.processing" 
              class="px-5 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-md text-xs font-semibold shadow-xs cursor-pointer disabled:opacity-50 disabled:pointer-events-none disabled:cursor-not-allowed flex items-center gap-1.5"
            >
              <i v-if="hierarchyForm.processing" class="fas fa-spinner fa-spin text-xs"></i>
              <span>{{ hierarchyForm.processing ? 'Menyimpan...' : 'Simpan Relasi' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- MODAL TAMBAH PEGAWAI BARU -->
    <div v-if="showAddUserModal" class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-4">
          <h3 class="text-sm font-bold text-gray-800">Tambah Pegawai / Pengguna Baru</h3>
          <button @click="showAddUserModal = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold cursor-pointer">&times;</button>
        </div>

        <form @submit.prevent="submitAddUser" class="space-y-4">
          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
            <input type="text" v-model="userForm.name" required placeholder="Nama lengkap pegawai" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500">
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">Nomor Induk Dokumen / NID</label>
              <input type="text" v-model="userForm.employee_no" placeholder="Contoh: 8108050JA" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500">
            </div>
            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
              <input type="email" v-model="userForm.email" required placeholder="pegawai@pln.co.id" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500">
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">Role BSD <span class="text-red-500">*</span></label>
              <select v-model="userForm.role_id" required class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500 bg-white">
                <option v-for="r in roles" :key="r.id" :value="r.id">{{ r.name }}</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">Unit Kerja</label>
              <select v-model="userForm.unit_id" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500 bg-white">
                <option value="">-- Pilih Unit --</option>
                <option v-for="u in units" :key="u.id" :value="u.id">{{ u.name }}</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">Jabatan / Posisi</label>
              <select v-model="userForm.position_id" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500 bg-white">
                <option value="">-- Pilih Jabatan --</option>
                <option v-for="p in positions" :key="p.id" :value="p.id">{{ p.name }}</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">Password Awal <span class="text-gray-400 font-normal">(Default: 123)</span></label>
              <input type="text" v-model="userForm.password" placeholder="Kosongkan untuk otomatis 123" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500">
            </div>
          </div>

          <!-- Penugasan TL & ASMAN Tambah Pegawai -->
          <div class="grid grid-cols-2 gap-3 p-3 bg-slate-50 border border-slate-200 rounded-xl">
            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1 flex items-center gap-1.5">
                <i class="fas fa-users-cog text-blue-600"></i> Team Leader (TL)
              </label>
              <select v-model="userForm.tl_id" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500 bg-white">
                <option value="">-- Otomatis Sesuai Unit --</option>
                <option v-for="tl in tls" :key="tl.id" :value="tl.id">{{ tl.name }} ({{ tl.position?.name || 'TL' }})</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1 flex items-center gap-1.5">
                <i class="fas fa-user-tie text-indigo-600"></i> Asisten Manajer (ASMAN)
              </label>
              <select v-model="userForm.asman_id" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500 bg-white">
                <option value="">-- Otomatis Sesuai Induk Unit --</option>
                <option v-for="asman in asmans" :key="asman.id" :value="asman.id">{{ asman.name }} ({{ asman.position?.name || 'ASMAN' }})</option>
              </select>
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Foto Profil / Avatar</label>
            <input type="file" accept="image/*" @change="onUserAvatarChange" class="w-full text-xs border border-gray-200 rounded-lg p-2 bg-white">
          </div>

          <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
            <button type="button" @click="showAddUserModal = false" class="px-4 py-2 border border-gray-200 text-gray-600 rounded-md text-xs hover:bg-gray-50 cursor-pointer">Batal</button>
            <button 
              type="submit" 
              :disabled="userForm.processing" 
              class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-xs font-semibold shadow-xs cursor-pointer disabled:opacity-50 disabled:pointer-events-none disabled:cursor-not-allowed flex items-center gap-1.5"
            >
              <i v-if="userForm.processing" class="fas fa-spinner fa-spin text-xs"></i>
              <span>{{ userForm.processing ? 'Menyimpan...' : 'Simpan Pegawai' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- MODAL EDIT PEGAWAI -->
    <div v-if="showEditUserModal" class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-4">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold">
              <i class="fas fa-user-edit"></i>
            </div>
            <div>
              <h3 class="text-sm font-bold text-gray-800">Edit Data Pegawai & Penugasan</h3>
              <p class="text-[10px] text-gray-400">Sesuaikan peran, unit kerja, jabatan, dan penugasan TL / ASMAN</p>
            </div>
          </div>
          <button @click="showEditUserModal = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold cursor-pointer">&times;</button>
        </div>

        <!-- Banner Info Hierarki Otomatis -->
        <div class="mb-4 p-3 bg-blue-50/80 border border-blue-200 rounded-xl text-xs text-blue-900 space-y-1">
          <p class="font-bold flex items-center gap-1.5 text-blue-800">
            <i class="fas fa-sitemap"></i> Pemetaan Rantai Komando & Atasan
          </p>
          <p class="text-[11px] text-blue-700 leading-relaxed">
            Pilih <b>Team Leader (TL)</b> dan <b>Asisten Manajer (ASMAN)</b> secara spesifik dari dropdown, atau biarkan <i>Otomatis</i> mengikuti unit kerja.
          </p>
        </div>

        <form @submit.prevent="submitEditUser" class="space-y-4">
          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
            <input type="text" v-model="editUserForm.name" required placeholder="Nama lengkap pegawai" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500">
            <p v-if="editUserForm.errors.name" class="text-rose-500 text-[10px] mt-1">{{ editUserForm.errors.name }}</p>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">Nomor Induk Dokumen / NID</label>
              <input type="text" v-model="editUserForm.employee_no" placeholder="Contoh: 8108050JA" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500 font-mono">
              <p v-if="editUserForm.errors.employee_no" class="text-rose-500 text-[10px] mt-1">{{ editUserForm.errors.employee_no }}</p>
            </div>
            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
              <input type="email" v-model="editUserForm.email" required placeholder="pegawai@pln.co.id" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500">
              <p v-if="editUserForm.errors.email" class="text-rose-500 text-[10px] mt-1">{{ editUserForm.errors.email }}</p>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">Role BSD <span class="text-red-500">*</span></label>
              <select v-model="editUserForm.role_id" required class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500 bg-white font-medium">
                <option v-for="r in roles" :key="r.id" :value="r.id">{{ r.name }}</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">Unit Kerja</label>
              <select v-model="editUserForm.unit_id" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500 bg-white font-medium">
                <option value="">-- Pilih Unit --</option>
                <option v-for="u in units" :key="u.id" :value="u.id">{{ u.name }}</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">Jabatan / Posisi</label>
              <select v-model="editUserForm.position_id" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500 bg-white font-medium">
                <option value="">-- Pilih Jabatan --</option>
                <option v-for="p in positions" :key="p.id" :value="p.id">{{ p.name }}</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">Status Akun</label>
              <select v-model="editUserForm.is_active" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500 bg-white font-medium">
                <option :value="1">Aktif</option>
                <option :value="0">Non-Aktif</option>
              </select>
            </div>
          </div>

          <!-- PILIH TEAM LEADER (TL) & ASISTEN MANAJER (ASMAN) -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-3 bg-slate-50 border border-slate-200 rounded-xl">
            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1 flex items-center gap-1.5">
                <i class="fas fa-users-cog text-blue-600"></i> Team Leader (TL)
              </label>
              <select v-model="editUserForm.tl_id" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500 bg-white font-medium">
                <option value="">-- Otomatis Sesuai Unit Kerja (Default) --</option>
                <option v-for="tl in tls" :key="tl.id" :value="tl.id">
                  {{ tl.name }} ({{ tl.position?.name || tl.organization_unit?.name || 'TL' }})
                </option>
              </select>
              <p class="text-[10px] text-gray-400 mt-1">Atasan langsung untuk Rapat Pagi</p>
            </div>

            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1 flex items-center gap-1.5">
                <i class="fas fa-user-tie text-indigo-600"></i> Asisten Manajer (ASMAN)
              </label>
              <select v-model="editUserForm.asman_id" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500 bg-white font-medium">
                <option value="">-- Otomatis Sesuai Induk Unit (Default) --</option>
                <option v-for="asman in asmans" :key="asman.id" :value="asman.id">
                  {{ asman.name }} ({{ asman.position?.name || asman.organization_unit?.name || 'ASMAN' }})
                </option>
              </select>
              <p class="text-[10px] text-gray-400 mt-1">Pembina bidang untuk Rapat Siang</p>
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">
              Ganti Password <span class="text-gray-400 font-normal">(Kosongkan jika tidak ingin mengubah)</span>
            </label>
            <input type="password" v-model="editUserForm.password" placeholder="Ketik password baru (atau kosongkan jika tetap)" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500">
            <p v-if="editUserForm.errors.password" class="text-rose-500 text-[10px] mt-1">{{ editUserForm.errors.password }}</p>
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Foto Profil / Avatar</label>
            <div class="flex items-center gap-3">
              <div v-if="editUserAvatarPreview" class="w-10 h-10 rounded-full overflow-hidden border border-gray-200 shrink-0">
                <img :src="editUserAvatarPreview" class="w-full h-full object-cover">
              </div>
              <input type="file" accept="image/*" @change="onEditUserAvatarChange" class="w-full text-xs border border-gray-200 rounded-lg p-2 bg-white flex-1 cursor-pointer">
            </div>
            <p class="text-[10px] text-gray-400 mt-1">Format: JPG, PNG, WEBP (Maksimal 2MB). Kosongkan jika foto tidak diubah.</p>
          </div>

          <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
            <button type="button" @click="showEditUserModal = false" class="px-4 py-2 border border-gray-200 text-gray-600 rounded-md text-xs hover:bg-gray-50 cursor-pointer">
              Batal
            </button>
            <button 
              type="submit" 
              :disabled="editUserForm.processing" 
              class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-xs font-semibold shadow-xs cursor-pointer flex items-center gap-1.5 disabled:opacity-50 disabled:pointer-events-none disabled:cursor-not-allowed"
            >
              <i v-if="editUserForm.processing" class="fas fa-spinner fa-spin"></i>
              <span>{{ editUserForm.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>

  </AppLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { router, Link, useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useConfirm } from '@/Composables/useConfirm';

const { confirm } = useConfirm();
const page = usePage();

const props = defineProps({
  users: Object,
  roles: Array,
  positions: Array,
  units: Array,
  hierarchies: Array,
  tls: {
    type: Array,
    default: () => [],
  },
  asmans: {
    type: Array,
    default: () => [],
  },
  organogramUsers: Object,
  usersByName: Object,
  activeTab: {
    type: String,
    default: 'hierarchy',
  },
  branding: {
    type: Object,
    default: () => ({}),
  },
});

const isSuperAdmin = computed(() => {
  const roleName = (page.props.auth?.user?.role?.name || '').toUpperCase();
  return ['SUPER_ADMIN', 'SUPER ADMIN'].includes(roleName);
});

const currentTab = ref(props.activeTab || (isSuperAdmin.value ? 'users' : 'hierarchy'));

watch(() => props.activeTab, (newTab) => {
  if (newTab) currentTab.value = newTab;
});

function switchTab(tab) {
  currentTab.value = tab;
  const url = new URL(window.location.href);
  url.searchParams.set('tab', tab);
  window.history.replaceState({}, '', url.toString());
}

// ==================== BRANDING & IMAGE MANAGEMENT ====================
const bgFileInput = ref(null);
const logoFileInput = ref(null);
const bgFile = ref(null);
const logoFile = ref(null);
const bgPreview = ref(null);
const logoPreview = ref(null);

const bgForm = useForm({
  background: null,
});

const logoForm = useForm({
  logo: null,
});

function onBgFileSelected(event) {
  const file = event.target.files?.[0];
  if (!file) return;
  bgFile.value = file;
  bgForm.background = file;
  bgPreview.value = URL.createObjectURL(file);
}

function cancelBgSelection() {
  bgFile.value = null;
  bgForm.background = null;
  bgPreview.value = null;
  if (bgFileInput.value) bgFileInput.value.value = '';
}

function submitUpdateBg() {
  if (!bgForm.background) return;
  bgForm.post('/master-data/branding/background', {
    preserveScroll: true,
    onSuccess: () => {
      cancelBgSelection();
    },
  });
}

async function confirmResetBg() {
  const confirmed = await confirm({
    title: 'Kembalikan Foto Default?',
    message: 'Gambar latar belakang login akan dipulihkan kembali ke foto default pembangkit listrik UP Sengkang.',
    confirmText: 'Ya, Kembalikan',
    cancelText: 'Batal',
    type: 'warning',
    icon: 'fas fa-rotate-left',
  });
  if (confirmed) {
    router.post('/master-data/branding/reset-background', {}, {
      preserveScroll: true,
    });
  }
}

function onLogoFileSelected(event) {
  const file = event.target.files?.[0];
  if (!file) return;
  logoFile.value = file;
  logoForm.logo = file;
  logoPreview.value = URL.createObjectURL(file);
}

function cancelLogoSelection() {
  logoFile.value = null;
  logoForm.logo = null;
  logoPreview.value = null;
  if (logoFileInput.value) logoFileInput.value.value = '';
}

function submitUpdateLogo() {
  if (!logoForm.logo) return;
  logoForm.post('/master-data/branding/logo', {
    preserveScroll: true,
    onSuccess: () => {
      cancelLogoSelection();
    },
  });
}

async function confirmResetLogo() {
  const confirmed = await confirm({
    title: 'Kembalikan Logo Default?',
    message: 'Logo resmi aplikasi akan dipulihkan kembali ke logo default resmi PT PLN Nusantara Power.',
    confirmText: 'Ya, Kembalikan',
    cancelText: 'Batal',
    type: 'warning',
    icon: 'fas fa-rotate-left',
  });
  if (confirmed) {
    router.post('/master-data/branding/reset-logo', {}, {
      preserveScroll: true,
    });
  }
}

// User filter
const urlParams = new URLSearchParams(window.location.search);
const userFilters = ref({
  search: urlParams.get('search') || '',
  role_id: urlParams.get('role_id') || '',
  unit_id: urlParams.get('unit_id') || '',
  tl_id: urlParams.get('tl_id') || '',
  asman_id: urlParams.get('asman_id') || '',
});

function applyUserFilter() {
  const query = { tab: 'users' };
  if (userFilters.value.search) query.search = userFilters.value.search;
  if (userFilters.value.role_id) query.role_id = userFilters.value.role_id;
  if (userFilters.value.unit_id) query.unit_id = userFilters.value.unit_id;
  if (userFilters.value.tl_id) query.tl_id = userFilters.value.tl_id;
  if (userFilters.value.asman_id) query.asman_id = userFilters.value.asman_id;

  router.get('/master-data', query, { preserveState: true, preserveScroll: true, replace: true });
}

function resetUserFilter() {
  userFilters.value = {
    search: '',
    role_id: '',
    unit_id: '',
    tl_id: '',
    asman_id: '',
  };
  router.get('/master-data', { tab: 'users' }, { preserveState: true, preserveScroll: true, replace: true });
}

let userSearchDebounce = null;
function onUserSearchInput() {
  clearTimeout(userSearchDebounce);
  userSearchDebounce = setTimeout(() => {
    applyUserFilter();
  }, 350);
}

// Modals
const showAddHierarchyModal = ref(false);
const hierarchyForm = useForm({
  incumbent: '',
  parent: '',
  incumbent_title: '',
  parent_title: '',
  person_name: '',
});

function submitAddHierarchy() {
  if (hierarchyForm.processing) return;
  hierarchyForm.post('/master-data/hierarchies', {
    onSuccess: () => {
      showAddHierarchyModal.value = false;
      hierarchyForm.reset();
    },
  });
}

const isDeletingHierarchy = ref(false);
async function deleteHierarchy(id) {
  if (isDeletingHierarchy.value) return;
  const confirmed = await confirm({
    title: 'Hapus Relasi Hirarki',
    message: 'Apakah Anda yakin ingin menghapus relasi hirarki organisasi ini? Perubahan akan mempengaruhi alur pelaporan.',
    confirmText: 'Ya, Hapus Relasi',
    cancelText: 'Batal',
    type: 'danger',
    icon: 'fas fa-sitemap',
  });
  if (confirmed) {
    isDeletingHierarchy.value = true;
    router.delete(`/master-data/hierarchies/${id}`, {
      preserveScroll: true,
      onFinish: () => {
        isDeletingHierarchy.value = false;
      },
    });
  }
}

const showAddUserModal = ref(false);
const userForm = useForm({
  name: '',
  email: '',
  employee_no: '',
  role_id: '',
  position_id: '',
  unit_id: '',
  tl_id: '',
  asman_id: '',
  password: '',
  avatar: null,
});

function onUserAvatarChange(e) {
  if (e.target.files && e.target.files[0]) {
    userForm.avatar = e.target.files[0];
  }
}

function submitAddUser() {
  if (userForm.processing) return;
  userForm.post('/master-data/users', {
    onSuccess: () => {
      showAddUserModal.value = false;
      userForm.reset();
    },
  });
}

const isDeletingUser = ref(false);
async function deleteUser(id) {
  if (isDeletingUser.value) return;
  const confirmed = await confirm({
    title: 'Hapus Pengguna',
    message: 'Apakah Anda yakin ingin menghapus data akun pengguna ini? Tindakan ini tidak dapat dibatalkan.',
    confirmText: 'Ya, Hapus Pengguna',
    cancelText: 'Batal',
    type: 'danger',
    icon: 'fas fa-trash-can',
  });
  if (confirmed) {
    isDeletingUser.value = true;
    router.delete(`/master-data/users/${id}`, {
      preserveScroll: true,
      onFinish: () => {
        isDeletingUser.value = false;
      },
    });
  }
}

const isResettingPassword = ref(false);
async function resetPassword(user) {
  if (isResettingPassword.value) return;
  const confirmed = await confirm({
    title: 'Reset Password Pegawai',
    message: `Apakah Anda yakin ingin me-reset password untuk ${user.name} (NID: ${user.employee_no || '-'}) kembali ke default (123)?`,
    confirmText: 'Ya, Reset ke 123',
    cancelText: 'Batal',
    type: 'warning',
    icon: 'fas fa-key',
  });
  if (confirmed) {
    isResettingPassword.value = true;
    router.post(`/master-data/users/${user.id}/reset-password`, {}, {
      preserveScroll: true,
      onFinish: () => {
        isResettingPassword.value = false;
      },
    });
  }
}

// Edit User logic
const showEditUserModal = ref(false);
const editUserForm = useForm({
  id: null,
  name: '',
  email: '',
  employee_no: '',
  role_id: '',
  position_id: '',
  unit_id: '',
  tl_id: '',
  asman_id: '',
  is_active: 1,
  password: '',
  avatar: null,
});

const editUserAvatarPreview = ref(null);

function openEditUserModal(usr) {
  editUserForm.clearErrors();
  editUserForm.id = usr.id;
  editUserForm.name = usr.name || '';
  editUserForm.email = usr.email || '';
  editUserForm.employee_no = usr.employee_no || '';
  editUserForm.role_id = usr.role_id || '';
  editUserForm.position_id = usr.position_id || '';
  editUserForm.unit_id = usr.unit_id || '';
  editUserForm.tl_id = usr.tl_id || '';
  editUserForm.asman_id = usr.asman_id || '';
  editUserForm.is_active = usr.is_active ? 1 : 0;
  editUserForm.password = '';
  editUserForm.avatar = null;
  editUserAvatarPreview.value = usr.avatar_url || (usr.avatar ? `/storage/${usr.avatar}` : null);
  showEditUserModal.value = true;
}

function onEditUserAvatarChange(e) {
  if (e.target.files && e.target.files[0]) {
    const file = e.target.files[0];
    editUserForm.avatar = file;
    editUserAvatarPreview.value = URL.createObjectURL(file);
  }
}

function submitEditUser() {
  if (editUserForm.processing) return;
  editUserForm.transform((data) => ({
    ...data,
    _method: 'PUT',
  })).post(`/master-data/users/${editUserForm.id}`, {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: () => {
      showEditUserModal.value = false;
      editUserForm.reset();
    },
  });
}

function getUserAvatar(nid, name) {
  const cleanNid = String(nid || '').trim();
  if (cleanNid && props.organogramUsers && props.organogramUsers[cleanNid]) {
    const usr = props.organogramUsers[cleanNid];
    if (usr.avatar) return `/storage/${usr.avatar}`;
  }
  const cleanName = String(name || '').trim().toUpperCase();
  if (cleanName && props.usersByName && props.usersByName[cleanName]) {
    const usr = props.usersByName[cleanName];
    if (usr.avatar) return `/storage/${usr.avatar}`;
  }
  return `https://ui-avatars.com/api/?name=${encodeURIComponent(name || 'User')}&background=0284c7&color=fff`;
}

// Organogram static TL mappings
const tlsOperasi = [
  ['TL PRODUKSI BLOK. 1A', 'MURSALAM', '8915056TD'],
  ['TL PRODUKSI BLOK. 1B', 'MEDY IVANTRY TANDI BUA', '8917074BL'],
  ['TL PRODUKSI BLOK. 1C', 'H. HERMAN JAYA, ST', '7423003PKW'],
  ['TL PRODUKSI BLOK. 1D', 'MUH NASAR D', '7223009PKW'],
  ['TL PRODUKSI BLOK. 2B', 'NADAR', '8223015PKW'],
  ['TL PRODUKSI BLOK. 2C', 'AGUSNAWAN / KHARAS ANDRI', '8323018PKW'],
  ['TL PRODUKSI BLOK. 2D', 'SUNARDI SM', '8623017PKW'],
  ['TL RENDAL OPERASI', 'SINUNG RAHARJO', '8812558PC'],
];

const tlsHar = [
  ['TL HAR MESIN 2 & SIPIL', 'SAINAL TAJANG', '7823006PKW'],
  ['TL RENDAL HAR', 'YERSON', '9015144ZJY'],
  ['TL HAR LISTRIK', 'SUHAIMI', '9115227KP'],
  ['TL HAR INSTRUMEN', 'H. JALALUDDIN', '7623008PKW'],
  ['TL HAR MESIN 1', 'ANDI WAHYUDI', '9517013ZJY'],
];

const tlsEnjiniring = [
  ['TL SYSTEM OWNER BLOK 1', 'HASANUDDIN RIFKI', '9618012ZJY'],
  ['TL SYSTEM OWNER BLOK 2', 'NURDIANTO', '8812035JA'],
  ['TL CONDITION MONITORING', 'NUR FAHMI', '9015099ZJY'],
];

const tlsKeuangan = [
  ['TL UMUM & CSR', 'SITTI MARYAM', '9013289ZY'],
  ['TL GUDANG', 'MUHAMMAD SUDIRMAN (PLT)', '8410014D3R'],
];
</script>

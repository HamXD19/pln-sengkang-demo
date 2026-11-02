<template>
  <AppLayout :title="pageTitle">
    <!-- Subtitle Navigasi -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 -mt-3 mb-4">
      <p class="text-xs text-gray-500">
        <template v-if="isLeader">
          Daftar tiket eskalasi resmi yang dinaikkan berjenjang untuk mendapatkan kewenangan atau putusan pimpinan.
        </template>
        <template v-else>
          Pencatatan dan pemantauan isu kendala teknis lapangan oleh staf untuk ditindaklanjuti.
        </template>
      </p>

      <!-- Tab Switcher HANYA TAMPIL UNTUK ADMIN -->
      <div v-if="isAdmin" class="flex items-center gap-1 bg-gray-100 p-1 rounded-xl self-start sm:self-auto border border-gray-200">
        <button 
          type="button" 
          @click="currentTab = 'issues'"
          class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 cursor-pointer"
          :class="currentTab === 'issues' ? 'bg-white text-gray-800 shadow-xs' : 'text-gray-500 hover:text-gray-800'"
        >
          <i class="fas fa-exclamation-circle text-amber-500"></i>
          <span>Tabel Issue</span>
          <span v-if="issues.total" class="bg-amber-100 text-amber-800 text-[10px] px-1.5 py-0.2 rounded-full font-bold ml-0.5">
            {{ issues.total }}
          </span>
        </button>

        <button 
          type="button" 
          @click="currentTab = 'escalations'"
          class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 cursor-pointer"
          :class="currentTab === 'escalations' ? 'bg-white text-gray-800 shadow-xs' : 'text-gray-500 hover:text-gray-800'"
        >
          <i class="fas fa-level-up-alt text-indigo-500"></i>
          <span>Tabel Eskalasi</span>
          <span v-if="escalations?.total" class="bg-indigo-100 text-indigo-800 text-[10px] px-1.5 py-0.2 rounded-full font-bold ml-0.5">
            {{ escalations.total }}
          </span>
        </button>
      </div>
    </div>

    <!-- ============================================== -->
    <!-- 1. TABEL ISSUE (HANYA UNTUK STAF & ADMIN)       -->
    <!-- ============================================== -->
    <div v-if="!isLeader && currentTab === 'issues'" class="bg-white rounded-xl border border-gray-100 shadow-xs flex flex-col">
      
      <!-- Area Filter & Tombol Buat Issue Baru -->
      <!-- Area Filter & Tombol Buat Issue Baru -->
      <!-- Area Pencarian & Aksi Ramping Ala Mobile App Native -->
      <div class="p-3.5 sm:p-5 border-b border-gray-100 flex flex-col gap-3 bg-white rounded-t-xl">
        <!-- Pembeda Isu: Tab Isu Aktif vs Arsip Isu Ditindaklanjuti -->
        <div class="flex items-center justify-between flex-wrap gap-2.5 pb-1">
          <div class="flex items-center gap-1.5 p-1 bg-slate-100 rounded-xl border border-slate-200/80 shadow-2xs">
            <button 
              type="button" 
              @click="setTabView('active')" 
              :class="[
                'px-3.5 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-2 cursor-pointer',
                currentTabView === 'active' 
                  ? 'bg-white text-blue-700 shadow-2xs font-extrabold ring-1 ring-blue-100' 
                  : 'text-slate-600 hover:text-slate-900'
              ]"
            >
              <i class="fas fa-bolt text-amber-500"></i>
              <span>Isu Aktif (Perlu Tindak Lanjut)</span>
              <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold" :class="currentTabView === 'active' ? 'bg-blue-100 text-blue-800' : 'bg-slate-200 text-slate-700'">
                {{ issueCounts.active || 0 }}
              </span>
            </button>

            <button 
              type="button" 
              @click="setTabView('archived')" 
              :class="[
                'px-3.5 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-2 cursor-pointer',
                currentTabView === 'archived' 
                  ? 'bg-white text-indigo-700 shadow-2xs font-extrabold ring-1 ring-indigo-100' 
                  : 'text-slate-600 hover:text-slate-900'
              ]"
            >
              <i class="fas fa-archive text-indigo-500"></i>
              <span>Arsip Isu (Task / Eskalasi / Selesai)</span>
              <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold" :class="currentTabView === 'archived' ? 'bg-indigo-100 text-indigo-800' : 'bg-slate-200 text-slate-700'">
                {{ issueCounts.archived || 0 }}
              </span>
            </button>

            <button 
              type="button" 
              @click="setTabView('all')" 
              :class="[
                'px-2.5 py-1.5 rounded-lg text-xs font-medium transition flex items-center gap-1.5 cursor-pointer',
                currentTabView === 'all' 
                  ? 'bg-white text-slate-800 shadow-2xs font-bold' 
                  : 'text-slate-500 hover:text-slate-800'
              ]"
            >
              <span>Semua</span>
              <span class="text-[10px] text-slate-400">({{ issueCounts.all || 0 }})</span>
            </button>
          </div>

          <span v-if="currentTabView === 'archived'" class="text-[11px] text-indigo-700 bg-indigo-50 border border-indigo-200 px-2.5 py-1 rounded-lg flex items-center gap-1.5">
            <i class="fas fa-info-circle"></i>
            <span>Arsip: Isu yang telah menjadi Task, dieskalasikan, atau diselesaikan.</span>
          </span>
        </div>

        <div class="flex items-center gap-2">
          <!-- Input Pencarian Cerdas -->
          <div class="relative flex-1">
            <i class="fas fa-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
            <input 
              type="text" 
              v-model="filters.search" 
              @input="onSearchInput" 
              placeholder="Cari isu / no tiket..." 
              class="w-full bg-slate-50 border border-slate-200/90 rounded-xl pl-8 pr-7 py-2 text-xs text-slate-800 outline-none focus:border-blue-500 focus:bg-white focus:ring-1 focus:ring-blue-500 transition shadow-2xs placeholder:text-slate-400"
            />
            <button 
              v-if="filters.search" 
              @click="clearSearch" 
              type="button" 
              title="Bersihkan pencarian"
              class="absolute right-2 top-2 text-slate-400 hover:text-slate-600 text-xs cursor-pointer p-0.5"
            >
              <i class="fas fa-times-circle"></i>
            </button>
          </div>

          <!-- Tombol Buka Filter Modal/Bottom Sheet -->
          <button 
            type="button" 
            @click="openFilterModal" 
            class="px-3 py-2 rounded-xl text-xs font-bold border transition flex items-center gap-1.5 shrink-0 cursor-pointer active:scale-95 shadow-2xs"
            :class="filters.status ? 'bg-blue-50 border-blue-300 text-blue-700 font-extrabold' : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100'"
            title="Buka Filter Status"
          >
            <i class="fas fa-filter text-[11px]" :class="filters.status ? 'text-blue-600' : 'text-slate-500'"></i>
            <span>Filter</span>
            <span v-if="filters.status" class="w-2 h-2 rounded-full bg-blue-600"></span>
          </button>

          <!-- Tombol Buat Isu Baru (Ramping & Proporsional) -->
          <button 
            v-if="isStaffOrAdmin" 
            type="button" 
            @click="showCreateModal = true" 
            class="bg-amber-500 hover:bg-amber-600 text-slate-950 px-3.5 py-2 rounded-xl text-xs font-bold shadow-xs transition flex items-center gap-1.5 shrink-0 cursor-pointer active:scale-95"
          >
            <i class="fas fa-plus text-[10px]"></i>
            <span>Buat Isu</span>
          </button>
        </div>

        <!-- Tag Indikator Filter Aktif (Jika status sedang difilter) -->
        <div v-if="filters.status" class="flex items-center gap-2 pt-1 text-xs">
          <span class="text-slate-400 text-[11px] font-medium">Filter aktif:</span>
          <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200 shadow-2xs">
            <span>{{ filterStatusLabel }}</span>
            <button @click="clearStatusFilter" class="hover:text-rose-600 font-bold ml-1 text-xs cursor-pointer" title="Hapus filter">&times;</button>
          </span>
          <button @click="resetFilter" class="text-[11px] text-slate-400 hover:text-rose-600 font-medium underline ml-auto cursor-pointer">
            Reset Semua
          </button>
        </div>
      </div>

      <!-- Desktop Table View: Tabel Issue -->
      <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-left text-xs whitespace-nowrap">
          <thead class="bg-[#f8fafc] border-b border-gray-100 text-gray-600 font-bold uppercase tracking-wider text-[11px]">
            <tr>
              <th class="px-6 py-4 w-12 text-center">No</th>
              <th class="px-6 py-4">Judul & Kode Issue</th>
              <th class="px-6 py-4">Pelapor (Staff)</th>
              <th class="px-6 py-4">Level Saat Ini</th>
              <th class="px-6 py-4">Prioritas</th>
              <th class="px-6 py-4">Status</th>
              <th class="px-6 py-4">Target / SLA</th>
              <th class="px-6 py-4 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 text-gray-700 font-medium">
            <template v-if="issues.data && issues.data.length > 0">
              <tr v-for="(iss, idx) in issues.data" :key="iss.id" class="hover:bg-amber-50/20 transition">
                <td class="px-6 py-4 text-center text-gray-500 font-bold">
                  {{ (issues.from || 1) + idx }}
                </td>

                <td class="px-6 py-4">
                  <Link :href="`/issues/${iss.id}`" class="font-bold text-gray-800 hover:text-blue-600 text-sm block">
                    {{ iss.title }}
                  </Link>
                  <div class="flex items-center gap-2 mt-1 text-[11px] text-gray-400">
                    <span class="font-mono text-slate-500 font-bold bg-slate-100 px-1.5 py-0.5 rounded">#{{ iss.issue_no }}</span>
                    <span>{{ iss.organization_unit?.name || 'Unit Operasi' }}</span>
                  </div>

                  <!-- Tanda Arsip: Sudah Jadi Task / Eskalasi / Putusan -->
                  <div v-if="iss.tasks && iss.tasks.length > 0" class="mt-1.5 flex items-center gap-1 flex-wrap">
                    <Link :href="`/pekerjaan`" class="inline-flex items-center gap-1 text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-0.5 rounded-md hover:bg-emerald-100 transition shadow-2xs" title="Buka modul pekerjaan">
                      <i class="fas fa-tasks text-emerald-600 text-[9px]"></i>
                      <span>Task #{{ iss.tasks[0].id }}: {{ iss.tasks[0].title }} ({{ iss.tasks[0].progress_percent ?? 0 }}%)</span>
                    </Link>
                  </div>
                  <div v-else-if="iss.escalations && iss.escalations.length > 0" class="mt-1.5 flex items-center gap-1 flex-wrap">
                    <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200 px-2 py-0.5 rounded-md shadow-2xs">
                      <i class="fas fa-level-up-alt text-purple-600 text-[9px]"></i>
                      <span>Eskalasi: {{ iss.escalations[0].from_level }} &rarr; {{ iss.escalations[0].to_level }} ({{ iss.escalations[0].status }})</span>
                    </span>
                  </div>
                  <div v-else-if="iss.decisions && iss.decisions.length > 0" class="mt-1.5 flex items-center gap-1 flex-wrap">
                    <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-sky-50 text-sky-700 border border-sky-200 px-2 py-0.5 rounded-md shadow-2xs" :title="iss.decisions[0].decision">
                      <i class="fas fa-gavel text-sky-600 text-[9px]"></i>
                      <span class="line-clamp-1 max-w-[220px]">Putusan: {{ iss.decisions[0].decision }}</span>
                    </span>
                  </div>
                </td>

                <td class="px-6 py-4">
                  <div class="font-bold text-gray-800">{{ iss.reporter?.name || 'Staff' }}</div>
                  <div class="text-[10px] text-gray-400">{{ iss.reporter?.role?.name || 'STAFF' }}</div>
                </td>

                <td class="px-6 py-4">
                  <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                    Level {{ iss.current_level }}
                  </span>
                </td>

                <td class="px-6 py-4">
                  <span :class="priorityBadgeClass(iss.priority)">
                    {{ iss.priority }}
                  </span>
                </td>

                <td class="px-6 py-4">
                  <span :class="statusBadgeClass(iss.status)">
                    {{ iss.status }}
                  </span>
                </td>

                <td class="px-6 py-4 text-gray-500 font-mono text-[11px]">
                  {{ iss.due_date ? String(iss.due_date).substring(0, 10) : '-' }}
                </td>

                <td class="px-6 py-4 text-center">
                  <div class="flex items-center justify-center gap-2">
                    <Link 
                      :href="`/issues/${iss.id}`" 
                      class="text-blue-600 hover:text-blue-800 font-bold text-xs bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded-md transition"
                    >
                      Buka
                    </Link>
                    <button 
                      v-if="isStaffOrAdmin" 
                      type="button" 
                      @click="openEditModal(iss)" 
                      class="text-gray-500 hover:text-gray-700 p-1" 
                      title="Edit Isu"
                    >
                      <i class="fas fa-pencil-alt text-xs"></i>
                    </button>
                    <button 
                      v-if="isStaffOrAdmin" 
                      type="button" 
                      @click="confirmDelete(iss)" 
                      class="text-red-400 hover:text-red-600 p-1" 
                      title="Hapus Isu"
                    >
                      <i class="fas fa-trash text-xs"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </template>
            <tr v-else>
              <td colspan="8" class="text-center py-12 text-slate-400 text-xs">
                <div class="space-y-1.5">
                  <i :class="currentTabView === 'active' ? 'fas fa-check-circle text-emerald-400 text-2xl' : 'fas fa-archive text-slate-300 text-2xl'" class="mb-1 block"></i>
                  <p class="font-bold text-slate-700 text-sm">
                    {{ currentTabView === 'active' ? 'Tidak ada issue aktif yang tertunggak.' : (currentTabView === 'archived' ? 'Belum ada arsip issue yang ditindaklanjuti.' : 'Tidak ada issue yang ditemukan.') }}
                  </p>
                  <p class="text-[11px] text-slate-400 max-w-md mx-auto">
                    {{ currentTabView === 'active' ? 'Bagus! Semua issue staf telah ditindaklanjuti (menjadi task, dieskalasi, atau diputuskan). Anda dapat melihat riwayatnya pada tab Arsip Isu.' : 'Issue yang sudah selesai, dibuatkan task, atau dieskalasi akan otomatis diarsipkan di sini agar tidak bertabrakan dengan issue aktif.' }}
                  </p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Native Mobile App Card Feed View: Tabel Issue -->
      <div class="md:hidden p-3 space-y-3 bg-slate-50/50">
        <template v-if="issues.data && issues.data.length > 0">
          <div 
            v-for="iss in issues.data" 
            :key="iss.id" 
            class="bg-white rounded-xl border border-slate-200/80 p-4 shadow-2xs hover:shadow-xs transition active:scale-[0.99] space-y-3"
          >
            <div class="flex items-center justify-between gap-2">
              <div class="flex items-center gap-1.5">
                <span class="font-mono text-xs font-bold text-slate-700 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-md">
                  #{{ iss.issue_no }}
                </span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                  Level {{ iss.current_level }}
                </span>
              </div>
              <span :class="statusBadgeClass(iss.status)" class="text-[10px] px-2 py-0.5 rounded-full font-bold">
                {{ iss.status }}
              </span>
            </div>

            <Link :href="`/issues/${iss.id}`" class="block font-bold text-slate-800 hover:text-blue-600 text-sm leading-snug">
              {{ iss.title }}
            </Link>

            <!-- Tanda Arsip Mobile -->
            <div v-if="iss.tasks && iss.tasks.length > 0" class="flex items-center gap-1 flex-wrap">
              <Link :href="`/pekerjaan`" class="inline-flex items-center gap-1 text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-0.5 rounded-md hover:bg-emerald-100 transition shadow-2xs">
                <i class="fas fa-tasks text-emerald-600 text-[9px]"></i>
                <span>Task #{{ iss.tasks[0].id }}: {{ iss.tasks[0].title }} ({{ iss.tasks[0].progress_percent ?? 0 }}%)</span>
              </Link>
            </div>
            <div v-else-if="iss.escalations && iss.escalations.length > 0" class="flex items-center gap-1 flex-wrap">
              <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200 px-2 py-0.5 rounded-md shadow-2xs">
                <i class="fas fa-level-up-alt text-purple-600 text-[9px]"></i>
                <span>Eskalasi: {{ iss.escalations[0].from_level }} &rarr; {{ iss.escalations[0].to_level }}</span>
              </span>
            </div>
            <div v-else-if="iss.decisions && iss.decisions.length > 0" class="flex items-center gap-1 flex-wrap">
              <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-sky-50 text-sky-700 border border-sky-200 px-2 py-0.5 rounded-md shadow-2xs">
                <i class="fas fa-gavel text-sky-600 text-[9px]"></i>
                <span class="line-clamp-1">{{ iss.decisions[0].decision }}</span>
              </span>
            </div>

            <div class="flex flex-wrap items-center gap-2 text-xs">
              <span :class="priorityBadgeClass(iss.priority)">
                <i class="fas fa-flag text-[9px] mr-1"></i>{{ iss.priority }}
              </span>
              <span class="text-slate-500 text-[11px] flex items-center gap-1">
                <i class="fas fa-building text-slate-400 text-[10px]"></i> {{ iss.organization_unit?.name || 'Unit Operasi' }}
              </span>
            </div>

            <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs">
              <div class="text-[11px] text-slate-500">
                <span>Pelapor: <b class="text-slate-700">{{ iss.reporter?.name || 'Staff' }}</b></span>
              </div>
              <div class="flex items-center gap-1.5">
                <Link 
                  :href="`/issues/${iss.id}`" 
                  class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-3 py-1.5 rounded-lg shadow-xs flex items-center gap-1 active:scale-95 transition"
                >
                  <span>Buka</span>
                  <i class="fas fa-chevron-right text-[9px]"></i>
                </Link>
                <button 
                  v-if="isStaffOrAdmin" 
                  type="button" 
                  @click="openEditModal(iss)" 
                  class="p-1.5 text-slate-500 hover:text-blue-600 hover:bg-slate-100 rounded-lg transition"
                  title="Edit Isu"
                >
                  <i class="fas fa-pencil-alt text-xs"></i>
                </button>
                <button 
                  v-if="isStaffOrAdmin" 
                  type="button" 
                  @click="confirmDelete(iss)" 
                  class="p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                  title="Hapus Isu"
                >
                  <i class="fas fa-trash text-xs"></i>
                </button>
              </div>
            </div>
          </div>
        </template>
        <div v-else class="text-center py-10 px-4 bg-white rounded-xl border border-dashed border-slate-200 space-y-3">
          <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-lg">
            <i :class="currentTabView === 'active' ? 'fas fa-check-circle text-emerald-400' : 'fas fa-archive text-slate-400'"></i>
          </div>
          <div>
            <p class="text-sm font-bold text-slate-700">
              {{ currentTabView === 'active' ? 'Tidak ada issue aktif yang tertunggak' : (currentTabView === 'archived' ? 'Belum ada arsip issue' : 'Tidak ada issue ditemukan') }}
            </p>
            <p class="text-xs text-slate-400 mt-1 max-w-xs mx-auto">
              {{ currentTabView === 'active' ? 'Seluruh issue telah ditindaklanjuti atau diarsipkan.' : 'Issue yang sudah selesai atau diputuskan akan muncul di tab arsip ini.' }}
            </p>
          </div>
        </div>
      </div>

      <!-- Area Pagination Issues -->
      <div v-if="issues.links && issues.links.length > 3" class="p-4 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
        <span>Menampilkan {{ issues.from || 0 }} - {{ issues.to || 0 }} dari {{ issues.total }} issue</span>
        <div class="flex gap-1">
          <template v-for="(lnk, idx) in issues.links" :key="idx">
            <Link 
              v-if="lnk.url" 
              :href="lnk.url" 
              v-html="lnk.label" 
              class="px-2.5 py-1 border rounded-md"
              :class="lnk.active ? 'bg-blue-600 text-white border-blue-600 font-bold' : 'border-gray-200 text-gray-600 hover:bg-gray-50'"
            />
            <span v-else v-html="lnk.label" class="px-2.5 py-1 border border-gray-200 rounded-md text-gray-300"></span>
          </template>
        </div>
      </div>
    </div>

    <!-- ============================================== -->
    <!-- 2. TABEL ESKALASI (BERJENJANG TL & ASMAN)      -->
    <!-- ============================================== -->
    <div v-if="isLeader || currentTab === 'escalations'" class="bg-white rounded-xl border border-gray-100 shadow-xs flex flex-col">
      <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-3">
        <div>
          <h3 class="text-sm font-bold text-indigo-950 flex items-center gap-2">
            <i class="fas fa-level-up-alt text-indigo-600"></i>
            Tabel Eskalasi Berjenjang
          </h3>
          <p class="text-xs text-indigo-800/80 mt-0.5">
            Daftar eskalasi resmi yang dinaikkan oleh TL ke Asman atau Asman ke MUP untuk mendapatkan kewenangan / putusan rapat.
          </p>
        </div>
        <Link 
          href="/rapat"
          class="border border-indigo-200 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 px-3.5 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5"
        >
          <i class="far fa-calendar-alt"></i> Buka Ruang Rapat
        </Link>
      </div>

      <!-- Desktop Table View: Tabel Eskalasi -->
      <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-left text-xs whitespace-nowrap">
          <thead class="bg-[#f8fafc] border-b border-gray-100 text-gray-600 font-bold uppercase tracking-wider text-[11px]">
            <tr>
              <th class="px-6 py-4 w-12 text-center">No</th>
              <th class="px-6 py-4">Kode & Judul Isu</th>
              <th class="px-6 py-4">Alur Eskalasi</th>
              <th class="px-6 py-4">Alasan Eskalasi</th>
              <th class="px-6 py-4">Pengeskalasi</th>
              <th class="px-6 py-4">Ditujukan Ke</th>
              <th class="px-6 py-4">Status</th>
              <th class="px-6 py-4">Tanggal</th>
              <th class="px-6 py-4 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 text-gray-700 font-medium">
            <template v-if="escalations?.data && escalations.data.length > 0">
              <tr v-for="(esc, idx) in escalations.data" :key="esc.id" class="hover:bg-indigo-50/20 transition">
                <td class="px-6 py-4 text-center text-gray-500 font-bold">
                  {{ (escalations.from || 1) + idx }}
                </td>

                <td class="px-6 py-4">
                  <Link v-if="esc.issue" :href="`/issues/${esc.issue.id}`" class="font-bold text-gray-800 hover:text-blue-600 block">
                    {{ esc.issue.title }}
                  </Link>
                  <span class="text-[10px] font-mono text-gray-400">#{{ esc.issue?.issue_no || '-' }}</span>
                </td>

                <td class="px-6 py-4">
                  <div class="flex items-center gap-1 font-bold text-indigo-900">
                    <span class="bg-slate-100 px-1.5 py-0.5 rounded text-[10px]">{{ esc.from_level }}</span>
                    <i class="fas fa-arrow-right text-[10px] text-indigo-400"></i>
                    <span class="bg-indigo-600 text-white px-1.5 py-0.5 rounded text-[10px]">{{ esc.to_level }}</span>
                  </div>
                </td>

                <td class="px-6 py-4">
                  <p class="text-xs text-gray-700 line-clamp-1 max-w-xs" :title="esc.reason">
                    {{ esc.reason }}
                  </p>
                </td>

                <td class="px-6 py-4">
                  <div class="font-bold text-gray-800">{{ esc.from_user?.name || '-' }}</div>
                  <div class="text-[10px] text-gray-400">{{ esc.from_user?.role?.name || '' }}</div>
                </td>

                <td class="px-6 py-4">
                  <div class="font-bold text-gray-800">{{ esc.to_user?.name || ('Pimpinan ' + esc.to_level) }}</div>
                  <div class="text-[10px] text-indigo-600 font-semibold">Jenjang {{ esc.to_level }}</div>
                </td>

                <td class="px-6 py-4">
                  <span class="px-2 py-0.5 rounded text-[10px] font-bold" :class="statusBadgeClass(esc.status)">
                    {{ esc.status }}
                  </span>
                </td>

                <td class="px-6 py-4 text-gray-500 font-mono text-[11px]">
                  {{ esc.created_at ? String(esc.created_at).substring(0, 10) : '-' }}
                </td>

                <td class="px-6 py-4 text-center">
                  <Link 
                    v-if="esc.issue" 
                    :href="`/issues/${esc.issue.id}`" 
                    class="text-blue-600 hover:text-blue-800 font-bold text-xs bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded-md transition"
                  >
                    Buka Detail
                  </Link>
                </td>
              </tr>
            </template>
            <tr v-else>
              <td colspan="9" class="text-center py-12 text-gray-400 text-xs">
                Tidak ada data eskalasi aktif saat ini.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Native Mobile App Card Feed View: Tabel Eskalasi -->
      <div class="md:hidden p-3 space-y-3 bg-slate-50/50">
        <template v-if="escalations?.data && escalations.data.length > 0">
          <div 
            v-for="esc in escalations.data" 
            :key="esc.id" 
            class="bg-white rounded-xl border border-slate-200/80 p-4 shadow-2xs hover:shadow-xs transition active:scale-[0.99] space-y-3"
          >
            <!-- Header: Issue No & Status Badge -->
            <div class="flex items-center justify-between gap-2">
              <span class="font-mono text-xs font-bold text-indigo-700 bg-indigo-50 border border-indigo-200 px-2 py-0.5 rounded-md">
                #{{ esc.issue?.issue_no || '-' }}
              </span>
              <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold" :class="statusBadgeClass(esc.status)">
                {{ esc.status }}
              </span>
            </div>

            <!-- Title & Issue Link -->
            <Link v-if="esc.issue" :href="`/issues/${esc.issue.id}`" class="block font-bold text-slate-800 hover:text-indigo-600 text-sm leading-snug">
              {{ esc.issue.title }}
            </Link>
            <div v-else class="text-sm font-semibold text-slate-400 italic">Isu terkait tidak tersedia</div>

            <!-- Reason Quote Box -->
            <div v-if="esc.reason" class="bg-indigo-50/60 border-l-2 border-indigo-500 rounded-r-lg p-2.5 text-xs text-indigo-950 italic">
              "{{ esc.reason }}"
            </div>

            <!-- Flow & PIC Meta -->
            <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100 text-[11px]">
              <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider">Alur Eskalasi</span>
                <div class="flex items-center gap-1 font-bold text-slate-700 mt-0.5">
                  <span class="bg-slate-100 px-1.5 py-0.5 rounded text-[10px]">{{ esc.from_level }}</span>
                  <i class="fas fa-arrow-right text-[9px] text-indigo-500"></i>
                  <span class="bg-indigo-600 text-white px-1.5 py-0.5 rounded text-[10px]">{{ esc.to_level }}</span>
                </div>
              </div>
              <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider">Pengeskalasi</span>
                <span class="font-bold text-slate-800 truncate block mt-0.5">{{ esc.from_user?.name || '-' }}</span>
              </div>
            </div>

            <!-- Footer Action -->
            <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
              <span class="text-[11px] text-slate-400 font-mono flex items-center gap-1">
                <i class="far fa-clock"></i> {{ esc.created_at ? String(esc.created_at).substring(0, 10) : '-' }}
              </span>
              <Link 
                v-if="esc.issue" 
                :href="`/issues/${esc.issue.id}`" 
                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-3.5 py-1.5 rounded-lg shadow-xs flex items-center gap-1.5 active:scale-95 transition"
              >
                <span>Tinjau Isu</span>
                <i class="fas fa-chevron-right text-[9px]"></i>
              </Link>
            </div>
          </div>
        </template>
        <div v-else class="text-center py-8 text-slate-400 text-xs bg-white rounded-xl border border-dashed border-slate-200">
          Tidak ada data eskalasi aktif saat ini.
        </div>
      </div>

      <!-- Area Pagination Escalations -->
      <div v-if="escalations?.links && escalations.links.length > 3" class="p-4 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
        <span>Menampilkan {{ escalations.from || 0 }} - {{ escalations.to || 0 }} dari {{ escalations.total }} eskalasi</span>
        <div class="flex gap-1">
          <template v-for="(lnk, idx) in escalations.links" :key="idx">
            <Link 
              v-if="lnk.url" 
              :href="lnk.url" 
              v-html="lnk.label" 
              class="px-2.5 py-1 border rounded-md"
              :class="lnk.active ? 'bg-indigo-600 text-white border-indigo-600 font-bold' : 'border-gray-200 text-gray-600 hover:bg-gray-50'"
            />
            <span v-else v-html="lnk.label" class="px-2.5 py-1 border border-gray-200 rounded-md text-gray-300"></span>
          </template>
        </div>
      </div>
    </div>

    <!-- ==================== MODAL BUAT ISSUE BARU ==================== -->
    <!-- ==================== MODAL BUAT ISSUE BARU ==================== -->
    <div v-if="showCreateModal" class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 space-y-4 max-h-[92vh] overflow-y-auto">
        <div class="flex justify-between items-center pb-3 border-b border-gray-100">
          <div>
            <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
              <i class="fas fa-plus text-amber-500"></i> Buat Issue Kendala Lapangan
            </h3>
            <p class="text-xs text-gray-500 mt-0.5">Catat temuan kendala operasional lapangan untuk ditindaklanjuti.</p>
          </div>
          <button @click="showCreateModal = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold cursor-pointer">&times;</button>
        </div>

        <form @submit.prevent="submitCreate" class="space-y-4">
          <!-- Judul Kendala -->
          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Judul Kendala / Issue <span class="text-rose-500">*</span></label>
            <input 
              type="text" 
              v-model="createForm.title" 
              placeholder="Contoh: Terjadi getaran abnormal pada bearing pompa pendingin..." 
              class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500" 
              required 
            />
          </div>

          <!-- Deskripsi Kendala -->
          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Deskripsi & Kronologi Lapangan <span class="text-rose-500">*</span></label>
            <textarea 
              v-model="createForm.description" 
              rows="4" 
              placeholder="Uraikan kronologi, indikator teknis di lapangan, dan dampak terhadap operasional..."
              class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500" 
              required
            ></textarea>
          </div>

          <!-- Input 1: Foto Bukti Lapangan (Khusus Foto/Gambar dengan validasi ketat) -->
          <div class="p-3 bg-slate-50 border border-slate-200/80 rounded-xl space-y-2">
            <div class="flex items-center justify-between">
              <label class="block text-xs font-bold text-slate-800 flex items-center gap-1.5">
                <i class="fas fa-camera text-blue-600"></i> Foto Bukti Lapangan (Opsional)
              </label>
              <button 
                v-if="createForm.photo || photoPreview" 
                type="button" 
                @click="clearPhoto" 
                class="text-[11px] text-rose-600 hover:text-rose-700 font-bold flex items-center gap-1 cursor-pointer"
              >
                <i class="fas fa-times-circle"></i> Hapus Foto
              </button>
            </div>
            
            <input 
              ref="photoInputRef"
              type="file" 
              accept="image/jpeg,image/png,image/webp,image/jpg"
              @change="onPhotoSelected"
              class="w-full text-xs border border-gray-200 rounded-lg p-2 bg-white file:mr-2 file:py-1 file:px-2.5 file:rounded file:border-0 file:text-[11px] file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer"
            />
            <p class="text-[10px] text-gray-400">Khusus berkas gambar (JPG, JPEG, PNG, WEBP maks 10MB). Dokumen PDF/Word ditolak di kolom ini.</p>

            <!-- Pesan Error Validasi Foto -->
            <div v-if="photoError" class="p-2 bg-rose-50 border border-rose-200 rounded-lg text-rose-700 text-[11px] font-semibold flex items-center gap-1.5">
              <i class="fas fa-exclamation-circle text-rose-600 shrink-0"></i>
              <span>{{ photoError }}</span>
            </div>

            <!-- Preview Thumbnail Foto -->
            <div v-if="photoPreview" class="relative inline-block mt-2">
              <img :src="photoPreview" alt="Preview Foto" class="h-24 w-auto max-w-full rounded-lg border border-slate-300 object-cover shadow-2xs" />
              <span class="absolute bottom-1 right-1 bg-black/70 text-white text-[9px] font-bold px-1.5 py-0.5 rounded">Foto Terpilih</span>
            </div>
          </div>

          <!-- Input 2: Dokumen Pendukung / Laporan Teknis (Khusus Dokumen dengan validasi ketat) -->
          <div class="p-3 bg-slate-50 border border-slate-200/80 rounded-xl space-y-2">
            <div class="flex items-center justify-between">
              <label class="block text-xs font-bold text-slate-800 flex items-center gap-1.5">
                <i class="fas fa-file-lines text-amber-600"></i> Dokumen Pendukung / Data Teknis (Opsional)
              </label>
              <button 
                v-if="createForm.document || selectedDocName" 
                type="button" 
                @click="clearDocument" 
                class="text-[11px] text-rose-600 hover:text-rose-700 font-bold flex items-center gap-1 cursor-pointer"
              >
                <i class="fas fa-times-circle"></i> Hapus Dokumen
              </button>
            </div>
            
            <input 
              ref="docInputRef"
              type="file" 
              accept=".pdf,.doc,.docx,.xls,.xlsx"
              @change="onDocumentSelected"
              class="w-full text-xs border border-gray-200 rounded-lg p-2 bg-white file:mr-2 file:py-1 file:px-2.5 file:rounded file:border-0 file:text-[11px] file:bg-amber-50 file:text-amber-800 hover:file:bg-amber-100 cursor-pointer"
            />
            <p class="text-[10px] text-gray-400">Khusus berkas dokumen: PDF, Word (DOC/DOCX), atau Excel (XLS/XLSX) maks 10MB. Foto ditolak di kolom ini.</p>

            <!-- Pesan Error Validasi Dokumen -->
            <div v-if="documentError" class="p-2 bg-rose-50 border border-rose-200 rounded-lg text-rose-700 text-[11px] font-semibold flex items-center gap-1.5">
              <i class="fas fa-exclamation-circle text-rose-600 shrink-0"></i>
              <span>{{ documentError }}</span>
            </div>

            <!-- Badge Nama Dokumen Terpilih -->
            <div v-if="selectedDocName" class="p-2 bg-white border border-amber-200 rounded-lg flex items-center justify-between text-xs text-slate-700">
              <span class="truncate font-mono text-[11px]"><i class="fas fa-file-pdf text-rose-500 mr-1.5"></i>{{ selectedDocName }}</span>
              <span class="text-[10px] bg-amber-100 text-amber-800 px-1.5 py-0.5 rounded font-bold shrink-0">Dokumen</span>
            </div>
          </div>

          <!-- Info Otomatisasi Status & Prioritas -->
          <div class="p-3 bg-blue-50/60 border border-blue-100 rounded-xl flex items-start gap-2.5 text-xs text-blue-900">
            <i class="fas fa-info-circle text-blue-500 text-sm mt-0.5 shrink-0"></i>
            <p class="text-[11px] leading-relaxed">
              Status isu otomatis <b>Terbuka (Open)</b>. Tingkat prioritas dan penugasan PIC akan dievaluasi serta ditetapkan oleh Team Leader (TL) dalam rapat koordinasi.
            </p>
          </div>

          <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
            <button 
              type="button" 
              @click="showCreateModal = false" 
              class="px-4 py-2 border border-gray-200 text-gray-600 rounded-lg text-xs hover:bg-gray-50 cursor-pointer font-medium"
            >
              Batal
            </button>
            <button 
              type="submit" 
              :disabled="createForm.processing || !!photoError || !!documentError"
              class="px-5 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 rounded-lg text-xs font-bold shadow-xs cursor-pointer transition disabled:opacity-50 disabled:pointer-events-none disabled:cursor-not-allowed flex items-center gap-1.5"
            >
              <i v-if="createForm.processing" class="fas fa-spinner fa-spin"></i>
              <span>{{ createForm.processing ? 'Menyimpan...' : 'Laporkan Issue' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- ==================== MODAL EDIT ISSUE ==================== -->
    <div v-if="showEditModal" class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 space-y-4 max-h-[92vh] overflow-y-auto">
        <div class="flex justify-between items-center pb-3 border-b border-gray-100">
          <h3 class="text-sm font-bold text-gray-800">Edit Data Issue Kendala</h3>
          <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold cursor-pointer">&times;</button>
        </div>

        <form @submit.prevent="submitEdit" class="space-y-4">
          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Judul Kendala <span class="text-rose-500">*</span></label>
            <input type="text" v-model="editForm.title" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500" required />
          </div>



          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Deskripsi Kendala Secara Detail <span class="text-rose-500">*</span></label>
            <textarea 
              v-model="editForm.description" 
              rows="4" 
              class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500" 
              required
            ></textarea>
          </div>

          <!-- Update Foto Bukti -->
          <div class="p-3 bg-slate-50 border border-slate-200/80 rounded-xl space-y-2">
            <label class="block text-xs font-bold text-slate-800 flex items-center gap-1.5">
              <i class="fas fa-camera text-blue-600"></i> Perbarui Foto Bukti (Khusus Gambar: JPG, PNG, WEBP)
            </label>
            <div v-if="editForm.current_photo" class="p-2 bg-white border border-slate-200 rounded-lg text-xs flex items-center justify-between">
              <span class="truncate text-slate-700 font-mono text-[11px]"><i class="fas fa-image text-blue-500 mr-1.5"></i>{{ editForm.current_photo }}</span>
              <span class="text-[10px] text-slate-400">Foto saat ini</span>
            </div>
            <input 
              type="file" 
              accept="image/jpeg,image/png,image/webp,image/jpg"
              @change="onEditPhotoSelected"
              class="w-full text-xs border border-gray-200 rounded-lg p-2 bg-white file:mr-2 file:py-1 file:px-2.5 file:rounded file:border-0 file:text-[11px] file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer"
            />
            <div v-if="editPhotoError" class="p-2 bg-rose-50 border border-rose-200 rounded-lg text-rose-700 text-[11px] font-semibold flex items-center gap-1.5">
              <i class="fas fa-exclamation-circle text-rose-600 shrink-0"></i>
              <span>{{ editPhotoError }}</span>
            </div>
          </div>

          <!-- Update Dokumen Pendukung -->
          <div class="p-3 bg-slate-50 border border-slate-200/80 rounded-xl space-y-2">
            <label class="block text-xs font-bold text-slate-800 flex items-center gap-1.5">
              <i class="fas fa-file-lines text-amber-600"></i> Perbarui Dokumen Teknis (Khusus PDF, DOCX, XLSX)
            </label>
            <div v-if="editForm.current_doc" class="p-2 bg-white border border-slate-200 rounded-lg text-xs flex items-center justify-between">
              <span class="truncate text-slate-700 font-mono text-[11px]"><i class="fas fa-file-pdf text-amber-600 mr-1.5"></i>{{ editForm.current_doc }}</span>
              <span class="text-[10px] text-slate-400">Dokumen saat ini</span>
            </div>
            <input 
              type="file" 
              accept=".pdf,.doc,.docx,.xls,.xlsx"
              @change="onEditDocSelected"
              class="w-full text-xs border border-gray-200 rounded-lg p-2 bg-white file:mr-2 file:py-1 file:px-2.5 file:rounded file:border-0 file:text-[11px] file:bg-amber-50 file:text-amber-800 hover:file:bg-amber-100 cursor-pointer"
            />
            <div v-if="editDocError" class="p-2 bg-rose-50 border border-rose-200 rounded-lg text-rose-700 text-[11px] font-semibold flex items-center gap-1.5">
              <i class="fas fa-exclamation-circle text-rose-600 shrink-0"></i>
              <span>{{ editDocError }}</span>
            </div>
          </div>

          <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
            <button 
              type="button" 
              @click="showEditModal = false" 
              class="px-4 py-2 border border-gray-200 text-gray-600 rounded-lg text-xs hover:bg-gray-50 cursor-pointer font-medium"
            >
              Batal
            </button>
            <button 
              type="submit" 
              :disabled="editForm.processing || !!editPhotoError || !!editDocError"
              class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold shadow-xs cursor-pointer transition disabled:opacity-50 disabled:pointer-events-none disabled:cursor-not-allowed flex items-center gap-1.5"
            >
              <i v-if="editForm.processing" class="fas fa-spinner fa-spin"></i>
              <span>{{ editForm.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- ============================================================== -->
    <!-- 4. MODAL / BOTTOM SHEET FILTER STATUS                          -->
    <!-- ============================================================== -->
    <div 
      v-if="showFilterModal" 
      class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-slate-900/50 backdrop-blur-xs transition-opacity animate-in fade-in duration-200"
      @click.self="showFilterModal = false"
    >
      <div 
        class="w-full sm:max-w-md bg-white rounded-t-2xl sm:rounded-2xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col max-h-[85vh] transition-transform animate-in slide-in-from-bottom sm:slide-in-from-bottom-4 duration-200"
      >
        <!-- Mobile drag indicator -->
        <div class="sm:hidden pt-2.5 pb-1 flex justify-center bg-slate-50 border-b border-slate-100">
          <div class="w-10 h-1 bg-slate-300 rounded-full"></div>
        </div>

        <!-- Modal Header -->
        <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
          <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center text-xs">
              <i class="fas fa-filter"></i>
            </div>
            <div>
              <h3 class="text-xs font-bold text-slate-800">Filter Status Issue</h3>
              <p class="text-[10px] text-slate-400">Pilih status isu yang ingin ditampilkan</p>
            </div>
          </div>
          <button 
            type="button" 
            @click="showFilterModal = false" 
            class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 text-xs transition cursor-pointer"
          >
            <i class="fas fa-times"></i>
          </button>
        </div>

        <!-- Modal Body: Status Options List -->
        <div class="p-4 space-y-2 overflow-y-auto">
          <button
            v-for="opt in filterStatusOptions"
            :key="opt.value"
            type="button"
            @click="tempSelectedStatus = opt.value"
            class="w-full text-left p-3 rounded-xl border transition flex items-center justify-between gap-3 cursor-pointer"
            :class="tempSelectedStatus === opt.value ? 'bg-blue-50/70 border-blue-500 shadow-2xs' : 'bg-white border-slate-200 hover:bg-slate-50'"
          >
            <div class="flex items-center gap-3">
              <div 
                class="w-8 h-8 rounded-lg flex items-center justify-center text-xs shrink-0"
                :class="tempSelectedStatus === opt.value ? 'bg-blue-600 text-white' : 'bg-slate-100 ' + opt.color"
              >
                <i :class="opt.icon"></i>
              </div>
              <div>
                <div class="text-xs font-bold" :class="tempSelectedStatus === opt.value ? 'text-blue-900' : 'text-slate-800'">
                  {{ opt.label }}
                </div>
                <div class="text-[11px] text-slate-400">
                  {{ opt.desc }}
                </div>
              </div>
            </div>

            <!-- Radio circle icon -->
            <div 
              class="w-4 h-4 rounded-full border flex items-center justify-center shrink-0"
              :class="tempSelectedStatus === opt.value ? 'border-blue-600 bg-blue-600 text-white' : 'border-slate-300 bg-white'"
            >
              <i v-if="tempSelectedStatus === opt.value" class="fas fa-check text-[8px]"></i>
            </div>
          </button>
        </div>

        <!-- Modal Footer -->
        <div class="p-4 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between gap-2.5">
          <button 
            type="button" 
            @click="resetAndCloseFilter" 
            class="px-4 py-2 border border-slate-200 hover:bg-slate-100 text-slate-600 rounded-xl text-xs font-semibold transition cursor-pointer"
          >
            Reset
          </button>
          <button 
            type="button" 
            @click="applyAndCloseFilter" 
            class="flex-1 py-2 bg-blue-600 hover:bg-blue-700 active:scale-[0.98] text-white rounded-xl text-xs font-bold shadow-xs transition text-center cursor-pointer"
          >
            Terapkan Filter
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router, Link, useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useConfirm } from '@/Composables/useConfirm';

const { confirm } = useConfirm();

const props = defineProps({
  issues: Object,
  escalations: Object,
  userRole: String,
  activeTab: String,
  tabView: {
    type: String,
    default: 'active',
  },
  issueCounts: {
    type: Object,
    default: () => ({ active: 0, archived: 0, all: 0 }),
  },
});

const page = usePage();
const authRole = computed(() => {
  return (props.userRole || page.props.auth?.user?.role?.name || 'STAFF').toUpperCase();
});

const isLeader = computed(() => {
  return ['TL', 'ASMAN', 'MUP'].includes(authRole.value);
});

const isAdmin = computed(() => authRole.value === 'ADMIN');
const isStaff = computed(() => authRole.value === 'STAFF');

const isStaffOrAdmin = computed(() => {
  return isStaff.value || isAdmin.value;
});

const pageTitle = computed(() => {
  if (isLeader.value) return 'Daftar Eskalasi';
  if (isStaff.value) return 'Kelola Issue';
  return 'Kelola Issue & Eskalasi';
});

const currentTab = ref(
  isLeader.value 
    ? 'escalations' 
    : (props.activeTab === 'escalations' || props.activeTab === 'eskalasi' ? 'escalations' : 'issues')
);

const currentTabView = ref(props.tabView || 'active');

function setTabView(tab) {
  currentTabView.value = tab;
  const query = {};
  if (filters.value.search) query.search = filters.value.search;
  if (filters.value.status) query.status = filters.value.status;
  query.tab = currentTab.value;
  query.tab_view = tab;

  router.get('/issues', query, { preserveState: true, preserveScroll: true, replace: true });
}

const currentWeek = new Date().getWeek ? new Date().getWeek() : 40;

const urlParams = new URLSearchParams(window.location.search);
const filters = ref({
  search: urlParams.get('search') || '',
  status: urlParams.get('status') || '',
});

const hasActiveFilter = computed(() => {
  return filters.value.search !== '' || filters.value.status !== '';
});

function applyFilter() {
  const query = {};
  if (filters.value.search) query.search = filters.value.search;
  if (filters.value.status) query.status = filters.value.status;
  query.tab = currentTab.value;
  query.tab_view = currentTabView.value;

  router.get('/issues', query, { preserveState: true, preserveScroll: true, replace: true });
}

const showFilterModal = ref(false);
const tempSelectedStatus = ref('');

const filterStatusOptions = [
  { value: '', label: 'Semua Status', desc: 'Tampilkan seluruh tiket isu', icon: 'fas fa-layer-group', color: 'text-slate-500' },
  { value: 'Open', label: 'Terbuka (Open)', desc: 'Isu baru yang belum ditindaklanjuti', icon: 'fas fa-envelope-open-text', color: 'text-amber-500' },
  { value: 'In Progress', label: 'Dalam Proses', desc: 'Sedang dikerjakan atau ditindaklanjuti', icon: 'fas fa-spinner', color: 'text-blue-500' },
  { value: 'Escalated', label: 'Dieskalasi', desc: 'Diteruskan ke level pimpinan / rapat', icon: 'fas fa-level-up-alt', color: 'text-purple-500' },
  { value: 'Closed', label: 'Selesai (Closed)', desc: 'Isu telah tuntas terselesaikan', icon: 'fas fa-check-circle', color: 'text-emerald-500' },
];

const filterStatusLabel = computed(() => {
  const match = filterStatusOptions.find(opt => opt.value.toLowerCase() === (filters.value.status || '').toLowerCase());
  return match ? match.label : filters.value.status;
});

function openFilterModal() {
  tempSelectedStatus.value = filters.value.status || '';
  showFilterModal.value = true;
}

function applyAndCloseFilter() {
  filters.value.status = tempSelectedStatus.value;
  applyFilter();
  showFilterModal.value = false;
}

function resetAndCloseFilter() {
  tempSelectedStatus.value = '';
  filters.value.status = '';
  applyFilter();
  showFilterModal.value = false;
}

function clearStatusFilter() {
  filters.value.status = '';
  applyFilter();
}

function setStatusFilter(statusVal) {
  filters.value.status = statusVal;
  applyFilter();
}

function clearSearch() {
  filters.value.search = '';
  applyFilter();
}

let searchDebounce = null;
function onSearchInput() {
  clearTimeout(searchDebounce);
  searchDebounce = setTimeout(() => {
    applyFilter();
  }, 350);
}

function resetFilter() {
  filters.value = { search: '', status: '' };
  router.get('/issues', { tab: currentTab.value });
}

// Create Modal States & Validation
const showCreateModal = ref(false);
const photoInputRef = ref(null);
const docInputRef = ref(null);
const photoError = ref('');
const documentError = ref('');
const photoPreview = ref(null);
const selectedDocName = ref('');

const createForm = useForm({
  title: '',
  description: '',
  photo: null,
  document: null,
});

function onPhotoSelected(event) {
  photoError.value = '';
  const file = event.target.files[0];
  if (!file) {
    createForm.photo = null;
    photoPreview.value = null;
    return;
  }

  const ext = file.name.split('.').pop().toLowerCase();
  const allowedImageTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
  const allowedExts = ['jpg', 'jpeg', 'png', 'webp'];
  const forbiddenDocExts = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'zip', 'rar', 'txt'];

  if (forbiddenDocExts.includes(ext) || (!allowedImageTypes.includes(file.type) && !allowedExts.includes(ext))) {
    photoError.value = 'Berkas tidak valid! Kolom ini khusus Foto/Gambar (JPG, JPEG, PNG, WEBP). Dokumen seperti PDF atau Word tidak diperbolehkan.';
    event.target.value = '';
    createForm.photo = null;
    photoPreview.value = null;
    return;
  }

  if (file.size > 10 * 1024 * 1024) {
    photoError.value = 'Ukuran foto melebihi batas maksimal 10MB.';
    event.target.value = '';
    createForm.photo = null;
    photoPreview.value = null;
    return;
  }

  createForm.photo = file;
  photoPreview.value = URL.createObjectURL(file);
}

function clearPhoto() {
  createForm.photo = null;
  photoPreview.value = null;
  photoError.value = '';
  if (photoInputRef.value) {
    photoInputRef.value.value = '';
  }
}

function onDocumentSelected(event) {
  documentError.value = '';
  const file = event.target.files[0];
  if (!file) {
    createForm.document = null;
    selectedDocName.value = '';
    return;
  }

  const ext = file.name.split('.').pop().toLowerCase();
  const allowedDocExts = ['pdf', 'doc', 'docx', 'xls', 'xlsx'];
  const forbiddenImageExts = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp', 'svg'];

  if (file.type.startsWith('image/') || forbiddenImageExts.includes(ext)) {
    documentError.value = 'Berkas tidak valid! Kolom ini khusus Dokumen (PDF, Word, Excel). Berkas foto/gambar tidak diperbolehkan pada kolom dokumen.';
    event.target.value = '';
    createForm.document = null;
    selectedDocName.value = '';
    return;
  }

  if (!allowedDocExts.includes(ext)) {
    documentError.value = 'Format dokumen tidak didukung. Harap pilih berkas PDF, DOC, DOCX, XLS, atau XLSX.';
    event.target.value = '';
    createForm.document = null;
    selectedDocName.value = '';
    return;
  }

  if (file.size > 10 * 1024 * 1024) {
    documentError.value = 'Ukuran dokumen melebihi batas maksimal 10MB.';
    event.target.value = '';
    createForm.document = null;
    selectedDocName.value = '';
    return;
  }

  createForm.document = file;
  selectedDocName.value = `${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`;
}

function clearDocument() {
  createForm.document = null;
  selectedDocName.value = '';
  documentError.value = '';
  if (docInputRef.value) {
    docInputRef.value.value = '';
  }
}

function submitCreate() {
  if (createForm.processing) return;
  if (photoError.value || documentError.value) {
    return;
  }
  createForm.post('/issues', {
    onSuccess: () => {
      showCreateModal.value = false;
      createForm.reset();
      clearPhoto();
      clearDocument();
    },
  });
}

// Edit Modal States & Validation
const showEditModal = ref(false);
const editPhotoError = ref('');
const editDocError = ref('');

const editForm = useForm({
  id: null,
  title: '',
  description: '',
  photo: null,
  document: null,
  current_photo: null,
  current_doc: null,
});

function onEditPhotoSelected(event) {
  editPhotoError.value = '';
  const file = event.target.files[0];
  if (!file) {
    editForm.photo = null;
    return;
  }

  const ext = file.name.split('.').pop().toLowerCase();
  const allowedImageTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
  const allowedExts = ['jpg', 'jpeg', 'png', 'webp'];
  const forbiddenDocExts = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'zip', 'rar', 'txt'];

  if (forbiddenDocExts.includes(ext) || (!allowedImageTypes.includes(file.type) && !allowedExts.includes(ext))) {
    editPhotoError.value = 'Berkas harus berupa gambar (JPG, JPEG, PNG, WEBP). Dokumen tidak diperbolehkan.';
    event.target.value = '';
    editForm.photo = null;
    return;
  }

  if (file.size > 10 * 1024 * 1024) {
    editPhotoError.value = 'Ukuran foto maksimal 10MB.';
    event.target.value = '';
    editForm.photo = null;
    return;
  }

  editForm.photo = file;
}

function onEditDocSelected(event) {
  editDocError.value = '';
  const file = event.target.files[0];
  if (!file) {
    editForm.document = null;
    return;
  }

  const ext = file.name.split('.').pop().toLowerCase();
  const allowedDocExts = ['pdf', 'doc', 'docx', 'xls', 'xlsx'];
  const forbiddenImageExts = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp', 'svg'];

  if (file.type.startsWith('image/') || forbiddenImageExts.includes(ext)) {
    editDocError.value = 'Berkas harus berupa dokumen (PDF, DOC, DOCX, XLS, XLSX). Foto tidak diperbolehkan.';
    event.target.value = '';
    editForm.document = null;
    return;
  }

  if (!allowedDocExts.includes(ext)) {
    editDocError.value = 'Format dokumen tidak didukung.';
    event.target.value = '';
    editForm.document = null;
    return;
  }

  if (file.size > 10 * 1024 * 1024) {
    editDocError.value = 'Ukuran dokumen maksimal 10MB.';
    event.target.value = '';
    editForm.document = null;
    return;
  }

  editForm.document = file;
}

function openEditModal(iss) {
  editPhotoError.value = '';
  editDocError.value = '';
  editForm.id = iss.id;
  editForm.title = iss.title;
  editForm.description = iss.clean_description || iss.description;
  editForm.current_photo = iss.attachment_name || iss.attachment || null;
  editForm.current_doc = iss.document_name || iss.document || null;
  editForm.photo = null;
  editForm.document = null;
  showEditModal.value = true;
}

function submitEdit() {
  if (editForm.processing) return;
  if (editPhotoError.value || editDocError.value) {
    return;
  }

  if (editForm.photo || editForm.document) {
    editForm.transform((data) => ({ ...data, _method: 'put' })).post(`/issues/${editForm.id}`, {
      onSuccess: () => {
        showEditModal.value = false;
        editForm.reset();
      },
    });
  } else {
    editForm.put(`/issues/${editForm.id}`, {
      onSuccess: () => {
        showEditModal.value = false;
        editForm.reset();
      },
    });
  }
}

const isDeletingIssue = ref(false);
async function confirmDelete(iss) {
  if (isDeletingIssue.value) return;
  const confirmed = await confirm({
    title: 'Hapus Isu',
    message: `Apakah Anda yakin ingin menghapus issue "${iss.title}" (#${iss.issue_no})? Seluruh riwayat eskalasi terkait akan dihapus permanen.`,
    confirmText: 'Ya, Hapus Isu',
    cancelText: 'Batal',
    type: 'danger',
    icon: 'fas fa-trash-can',
  });
  if (confirmed) {
    isDeletingIssue.value = true;
    router.delete(`/issues/${iss.id}`, {
      onFinish: () => {
        isDeletingIssue.value = false;
      },
    });
  }
}

function priorityBadgeClass(p) {
  const priority = String(p || '').toUpperCase();
  if (priority === 'CRITICAL' || priority === 'HIGH') {
    return 'px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200';
  }
  if (priority === 'MEDIUM') {
    return 'px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200';
  }
  return 'px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200';
}

function statusBadgeClass(s) {
  const status = String(s || '').toLowerCase();
  if (status.includes('closed') || status.includes('selesai') || status.includes('resolved') || status.includes('decided')) {
    return 'px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200';
  }
  if (status.includes('progress') || status.includes('proses')) {
    return 'px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200';
  }
  if (status.includes('escalated') || status.includes('eskalasi')) {
    return 'px-2 py-0.5 rounded text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200';
  }
  // Open / Terbuka
  return 'px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200';
}
</script>

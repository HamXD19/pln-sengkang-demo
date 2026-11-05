<template>
  <AppLayout title="Rapat Koordinasi Mingguan">
    <div class="space-y-5 sm:space-y-6">

      <!-- Header Banner & Action Button -->
      <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 sm:p-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <div class="flex items-center gap-2 flex-wrap mb-1">
            <h2 class="text-base sm:text-lg font-black text-slate-800 flex items-center gap-2">
              <i class="far fa-calendar-check text-blue-600"></i> Siklus Rapat Koordinasi Mingguan
            </h2>
            <span class="bg-blue-50 text-blue-700 border border-blue-200 text-[10px] font-bold px-2.5 py-0.5 rounded-full">
              UP Sengkang
            </span>
          </div>
          <p class="text-xs text-slate-500">
            Forum terpadu evaluasi N-1, perencanaan N, dan pembahasan isu khusus per bidang sesuai jenjang komando (BSD Section 2 & 3).
          </p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5 w-full sm:w-auto">
          <!-- Tombol Cetak Notulen Per Minggu -->
          <button 
            type="button" 
            @click="showWeeklyModal = true"
            class="w-full sm:w-auto bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 px-4 py-2.5 rounded-xl text-xs font-bold shadow-2xs transition flex items-center justify-center gap-2 shrink-0 cursor-pointer active:scale-95"
            title="Cetak risalah notulensi per minggu"
          >
            <i class="fas fa-print text-blue-600"></i> Cetak Notulen Per Minggu
          </button>

          <!-- Tombol Buat Agenda Rapat -->
          <button 
            v-if="userRole !== 'STAFF'"
            type="button" 
            @click="openCreateModal()"
            class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl text-xs font-bold shadow-xs transition flex items-center justify-center gap-2 shrink-0 cursor-pointer active:scale-95"
          >
            <i class="fas fa-plus"></i> Buat Agenda Rapat
          </button>
        </div>
      </div>

      <!-- Banner Siklus Mingguan Baku PLN UP Sengkang -->
      <div class="bg-gradient-to-r from-blue-950 via-slate-900 to-indigo-950 rounded-2xl p-4 sm:p-5 text-white border border-slate-800 shadow-md">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-3 pb-3 border-b border-slate-800">
          <div>
            <div class="flex items-center gap-2">
              <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
              <h3 class="text-xs sm:text-sm font-extrabold text-white tracking-wide uppercase">
                Siklus Rapat Mingguan Terjadwal (Baku) &bull; UP Sengkang
              </h3>
            </div>
            <p class="text-[11px] text-slate-300 mt-0.5">
              Jadwal otomatis disetel sesuai alur berjenjang: Senin Pagi (TL+Staf), Senin Siang (Asman+TL), dan Selasa Pagi (RTM Asman+MUP).
            </p>
          </div>
          <span class="text-[10px] bg-blue-500/20 text-blue-300 border border-blue-500/30 px-2.5 py-1 rounded-full font-semibold shrink-0">
            Siklus Standar Operasional
          </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-3.5">
          <!-- Card 1: TL + Staf -->
          <div class="bg-slate-800/80 hover:bg-slate-800 border border-slate-700/80 rounded-xl p-3.5 flex flex-col justify-between transition group">
            <div class="space-y-1">
              <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded bg-blue-500/20 text-blue-300 border border-blue-500/30">
                  🌅 1. Senin Pagi
                </span>
                <span class="text-[11px] font-mono text-slate-400 font-bold">08:30 - 10:00 WITA</span>
              </div>
              <h4 class="text-xs font-bold text-white group-hover:text-blue-400 transition pt-1">Rapat TL + Staf</h4>
              <p class="text-[11px] text-slate-400 leading-snug">
                Evaluasi kendala lapangan seksi (N-1) dan inventarisasi isu staf.
              </p>
            </div>
            <div class="pt-3 border-t border-slate-700/60 mt-3 flex items-center justify-between">
              <span class="text-[10px] text-slate-400">Level: <b class="text-slate-200">Seksi</b></span>
              <button 
                v-if="userRole !== 'STAFF'"
                type="button" 
                @click="openCreateModal('Rapat TL + Staf')"
                class="text-[10px] font-bold bg-blue-600 hover:bg-blue-500 text-white px-2.5 py-1 rounded-lg transition flex items-center gap-1 cursor-pointer"
              >
                <i class="fas fa-plus"></i> Setel Room
              </button>
            </div>
          </div>

          <!-- Card 2: Asman + TL -->
          <div class="bg-slate-800/80 hover:bg-slate-800 border border-slate-700/80 rounded-xl p-3.5 flex flex-col justify-between transition group">
            <div class="space-y-1">
              <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                  ☀️ 2. Senin Siang
                </span>
                <span class="text-[11px] font-mono text-slate-400 font-bold">13:30 - 15:30 WITA</span>
              </div>
              <h4 class="text-xs font-bold text-white group-hover:text-indigo-400 transition pt-1">Rapat Asman + TL</h4>
              <p class="text-[11px] text-slate-400 leading-snug">
                Sinkronisasi progres bidang dan tindak lanjut eskalasi dari TL.
              </p>
            </div>
            <div class="pt-3 border-t border-slate-700/60 mt-3 flex items-center justify-between">
              <span class="text-[10px] text-slate-400">Level: <b class="text-slate-200">Bidang</b></span>
              <button 
                v-if="userRole === 'ASMAN' || userRole === 'MUP' || userRole === 'ADMIN'"
                type="button" 
                @click="openCreateModal('Rapat Asman + TL')"
                class="text-[10px] font-bold bg-indigo-600 hover:bg-indigo-500 text-white px-2.5 py-1 rounded-lg transition flex items-center gap-1 cursor-pointer"
              >
                <i class="fas fa-plus"></i> Setel Room
              </button>
            </div>
          </div>

          <!-- Card 3: RTM Asman + MUP -->
          <div class="bg-slate-800/80 hover:bg-slate-800 border border-slate-700/80 rounded-xl p-3.5 flex flex-col justify-between transition group">
            <div class="space-y-1">
              <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded bg-purple-500/20 text-purple-300 border border-purple-500/30">
                  🏛️ 3. Selasa Pagi
                </span>
                <span class="text-[11px] font-mono text-slate-400 font-bold">08:30 - 11:00 WITA</span>
              </div>
              <h4 class="text-xs font-bold text-white group-hover:text-purple-400 transition pt-1">RTM Asman + MUP</h4>
              <p class="text-[11px] text-slate-400 leading-snug">
                Rapat Tinjauan Manajemen & putusan strategis pimpinan puncak.
              </p>
            </div>
            <div class="pt-3 border-t border-slate-700/60 mt-3 flex items-center justify-between">
              <span class="text-[10px] text-slate-400">Level: <b class="text-slate-200">Puncak (MUP)</b></span>
              <button 
                v-if="userRole === 'MUP' || userRole === 'ADMIN'"
                type="button" 
                @click="openCreateModal('Rapat Tinjauan Manajemen')"
                class="text-[10px] font-bold bg-purple-600 hover:bg-purple-500 text-white px-2.5 py-1 rounded-lg transition flex items-center gap-1 cursor-pointer"
              >
                <i class="fas fa-plus"></i> Setel Room
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Bilah Navigasi & Filter Toolbar Laptop/Mobile -->
      <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-3 sm:p-4 space-y-3">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
          
          <!-- Segmented Tab Bar -->
          <div class="flex items-center gap-1.5 bg-slate-100/90 p-1 rounded-xl w-full sm:w-auto shrink-0">
            <button 
              type="button"
              @click="setTab('jadwal')" 
              :class="[
                'flex-1 sm:flex-none text-center px-4 py-2 rounded-lg text-xs font-bold transition flex items-center justify-center gap-2 cursor-pointer',
                currentTab === 'jadwal' ? 'bg-white text-blue-600 shadow-2xs font-extrabold' : 'text-slate-600 hover:text-slate-900'
              ]"
            >
              <i class="far fa-clock"></i> Jadwal Aktif / Mendatang
            </button>
            <button 
              type="button"
              @click="setTab('riwayat')" 
              :class="[
                'flex-1 sm:flex-none text-center px-4 py-2 rounded-lg text-xs font-bold transition flex items-center justify-center gap-2 cursor-pointer',
                currentTab === 'riwayat' ? 'bg-white text-blue-600 shadow-2xs font-extrabold' : 'text-slate-600 hover:text-slate-900'
              ]"
            >
              <i class="fas fa-archive"></i> Riwayat Selesai
            </button>
          </div>

          <!-- Filter Dropdowns (Bidang & Jenis Rapat) -->
          <div class="flex flex-wrap sm:flex-nowrap items-center gap-2.5 w-full lg:w-auto">
            <!-- Indikator Bidang Otomatis (Untuk Akun ASMAN, TL, STAFF) -->
            <div v-if="userRole !== 'MUP' && userRole !== 'ADMIN'" class="flex items-center gap-2 px-3 py-2 bg-sky-50 border border-sky-200/90 rounded-xl text-xs text-sky-800 shadow-2xs font-semibold">
              <i class="fas fa-sitemap text-sky-600 text-xs"></i>
              <span class="text-slate-500 font-normal">Bidang:</span>
              <strong class="text-sky-900 font-bold">{{ userUnitName || 'Bidang Terkait' }}</strong>
              <span class="text-[10px] bg-sky-200/70 text-sky-800 font-bold px-1.5 py-0.5 rounded ml-0.5">Otomatis</span>
            </div>

            <!-- Filter Bidang Manual (Hanya untuk MUP & Admin yang Membawahi Seluruh Bidang) -->
            <div v-else class="flex-1 sm:flex-initial min-w-[170px]">
              <div class="relative">
                <i class="fas fa-sitemap absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                <select 
                  v-model="filterUnitId" 
                  @change="applyFilter"
                  class="w-full pl-8 pr-7 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-700 outline-none focus:border-blue-500 focus:bg-white transition cursor-pointer appearance-none"
                >
                  <option value="semua">Semua Bidang / Divisi</option>
                  <option v-for="d in departments" :key="d.id" :value="d.id">
                    {{ d.name }}
                  </option>
                </select>
                <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-[10px] pointer-events-none"></i>
              </div>
            </div>

            <!-- Filter Jenis Rapat -->
            <div class="flex-1 sm:flex-initial min-w-[160px]">
              <div class="relative">
                <i class="fas fa-filter absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                <select 
                  v-model="filterType" 
                  @change="applyFilter"
                  class="w-full pl-8 pr-7 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-700 outline-none focus:border-blue-500 focus:bg-white transition cursor-pointer appearance-none"
                >
                  <option value="semua">Semua Forum</option>
                  <option value="TL + Staf">Rapat TL + Staf</option>
                  <option v-if="userRole !== 'STAFF'" value="Asman + TL">Rapat Asman + TL</option>
                  <option v-if="userRole !== 'STAFF'" value="Tinjauan Manajemen">Rapat Manajemen (RTM)</option>
                </select>
                <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-[10px] pointer-events-none"></i>
              </div>
            </div>

            <!-- Total Pertemuan Info Pill -->
            <div class="hidden xl:flex items-center gap-1.5 px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-500 whitespace-nowrap">
              <span>Total:</span>
              <strong class="text-slate-800 font-mono font-bold">{{ meetings.length }} Sesi</strong>
            </div>
          </div>

        </div>
      </div>

      <!-- Daftar Kartu Rapat Dinamis (Desain Laptop Anti-Tumpang Tindih & Selaras) -->
      <div class="space-y-3.5">
        <template v-if="meetings && meetings.length > 0">
          <div 
            v-for="m in meetings" 
            :key="m.id" 
            class="bg-white rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-md hover:border-blue-200/80 transition-all p-4 sm:p-5 flex flex-col md:flex-row md:items-center justify-between gap-4"
          >
            <!-- Sisi Kiri: Tanggal & Informasi Agenda -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 flex-1 min-w-0 w-full">
              
              <!-- Tanggal Badge Desktop/Laptop (Bersih & Elegan) -->
              <div class="hidden sm:flex flex-col items-center justify-center w-20 py-2.5 px-2 bg-slate-50/80 border border-slate-150 rounded-xl shrink-0 text-center">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ getDayName(m.meeting_date) }}</span>
                <span class="text-2xl font-black text-slate-800 leading-tight my-0.5">{{ getDayNum(m.meeting_date) }}</span>
                <span class="text-[10px] font-semibold text-slate-500">{{ getMonthYear(m.meeting_date) }}</span>
              </div>

              <!-- Header Mobile: Tanggal & Status (Hanya Tampil di Layar Ponsel) -->
              <div class="sm:hidden flex items-center justify-between w-full pb-2.5 border-b border-slate-100">
                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 text-xs font-bold font-mono">
                  <i class="far fa-calendar-alt text-blue-600"></i>
                  <span>{{ getDayName(m.meeting_date) }}, {{ getDayNum(m.meeting_date) }} {{ getMonthYear(m.meeting_date) }}</span>
                </div>

                <!-- Status Pill Mobile -->
                <span v-if="m.status === 'Berlangsung'" class="bg-rose-50 text-rose-700 border border-rose-200 text-[10px] font-extrabold px-2.5 py-0.5 rounded-full flex items-center gap-1.5">
                  <span class="w-1.5 h-1.5 bg-rose-500 rounded-full animate-pulse"></span> LIVE
                </span>
                <span v-else-if="m.status === 'Selesai'" class="bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-bold px-2 py-0.5 rounded-full">
                  <i class="fas fa-check-circle text-[9px] mr-1"></i>Selesai
                </span>
                <span v-else class="bg-amber-50 text-amber-700 border border-amber-200 text-[10px] font-bold px-2 py-0.5 rounded-full">
                  <i class="far fa-clock text-[9px] mr-1"></i>Akan Datang
                </span>
              </div>

              <!-- Detail Rapat Utama -->
              <div class="flex-1 min-w-0 space-y-2 w-full">
                <!-- Baris Judul & Badges Selaras -->
                <div class="flex items-center gap-2 flex-wrap">
                  <h3 class="text-sm sm:text-base font-black text-slate-800 leading-snug">
                    {{ m.type }}
                  </h3>
                  
                  <!-- Status Desktop (Warna Lembut Selaras BUMN) -->
                  <span v-if="m.status === 'Berlangsung'" class="hidden sm:inline-flex bg-rose-50 text-rose-700 border border-rose-200 text-[10px] font-extrabold px-2.5 py-0.5 rounded-full items-center gap-1.5">
                    <span class="w-1.5 h-1.5 bg-rose-500 rounded-full animate-pulse"></span> LIVE
                  </span>
                  <span v-else-if="m.status === 'Selesai'" class="hidden sm:inline-flex bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-bold px-2 py-0.5 rounded-full">
                    Selesai
                  </span>
                  <span v-else class="hidden sm:inline-flex bg-amber-50 text-amber-700 border border-amber-200 text-[10px] font-bold px-2 py-0.5 rounded-full">
                    Akan Datang
                  </span>

                  <!-- Badge Bidang / Unit (Konsisten & Rapi) -->
                  <span class="bg-slate-100 text-slate-700 border border-slate-200 text-[10px] font-bold px-2 py-0.5 rounded-md flex items-center gap-1">
                    <i class="fas fa-sitemap text-slate-500 text-[9px]"></i>
                    {{ getUnitBadgeText(m) }}
                  </span>

                  <!-- Badge Mode Pertemuan -->
                  <span v-if="m.meeting_mode === 'Online' || m.zoom_link" class="bg-blue-50 text-blue-700 border border-blue-200 text-[10px] font-bold px-2 py-0.5 rounded-md flex items-center gap-1">
                    <i class="fas fa-video text-blue-500 text-[9px]"></i> Zoom
                  </span>
                  <span v-else-if="m.meeting_mode === 'Hybrid'" class="bg-sky-50 text-sky-700 border border-sky-200 text-[10px] font-bold px-2 py-0.5 rounded-md flex items-center gap-1">
                    <i class="fas fa-network-wired text-sky-600 text-[9px]"></i> Hybrid
                  </span>

                  <!-- Badge Isu Diagendakan -->
                  <span v-if="m.issues && m.issues.length > 0" class="bg-blue-50 text-blue-700 border border-blue-200 text-[10px] font-bold px-2 py-0.5 rounded-md flex items-center gap-1">
                    <i class="fas fa-list-check text-blue-600 text-[9px]"></i> {{ m.issues.length }} Isu
                  </span>
                </div>

                <!-- Metadata Rapat (Waktu, Tempat, Pimpinan) - Anti-Overlap pada Resolusi Laptop -->
                <div class="flex flex-wrap items-center gap-x-4 gap-y-1.5 text-xs text-slate-500 font-medium">
                  <span class="flex items-center gap-1.5 bg-slate-50 sm:bg-transparent px-2.5 py-1 sm:p-0 rounded-lg">
                    <i class="far fa-clock text-blue-500 shrink-0"></i>
                    <span>{{ m.time_start || '08:00' }} - {{ m.time_end || '10:00' }} WITA</span>
                  </span>
                  <span class="hidden sm:inline text-slate-300">&bull;</span>
                  <span class="flex items-center gap-1.5 bg-slate-50 sm:bg-transparent px-2.5 py-1 sm:p-0 rounded-lg">
                    <i class="fas fa-map-marker-alt text-rose-500 shrink-0"></i>
                    <span class="max-w-[220px] truncate" :title="m.location">{{ m.location || 'Ruang Rapat Utama' }}</span>
                  </span>
                  <span class="hidden sm:inline text-slate-300">&bull;</span>
                  <span class="flex items-center gap-1.5 bg-slate-50 sm:bg-transparent px-2.5 py-1 sm:p-0 rounded-lg">
                    <i class="fas fa-user-tie text-slate-400 shrink-0"></i>
                    <span>Pimpinan: <strong class="text-slate-800 font-semibold">{{ m.leader?.name || 'Pimpinan Unit' }}</strong></span>
                  </span>
                </div>

                <!-- Notes jika ada (Rapi & Tidak Mencolok) -->
                <p v-if="m.notes" class="text-xs text-slate-600 line-clamp-1 italic bg-slate-50 border-l-2 border-blue-500 px-2.5 py-1 rounded-r-lg">
                  <i class="fas fa-sticky-note text-blue-500 mr-1"></i> {{ m.notes }}
                </p>
              </div>
            </div>

            <!-- Sisi Kanan: Action Buttons (Selaras & Proporsional di Layar Laptop) -->
            <div class="flex flex-col sm:flex-row md:flex-col items-stretch md:items-end gap-2 shrink-0 pt-3 md:pt-0 border-t md:border-t-0 border-slate-100 w-full md:w-44">
              <!-- Tombol Utama: Masuk Workspace -->
              <Link 
                :href="`/rapat/${m.id}/workspace`" 
                :class="[
                  'w-full py-2 px-3 rounded-xl text-xs font-bold text-center transition flex items-center justify-center gap-1.5 shadow-xs cursor-pointer active:scale-95',
                  m.status === 'Berlangsung' 
                    ? 'bg-rose-600 hover:bg-rose-700 text-white ring-2 ring-rose-200' 
                    : 'bg-blue-600 hover:bg-blue-700 text-white'
                ]"
              >
                <i class="fas fa-laptop-code text-xs"></i>
                <span>{{ m.status === 'Berlangsung' ? 'Masuk LIVE' : 'Buka Workspace' }}</span>
              </Link>

              <!-- Tombol Sekunder: Detail & Cetak -->
              <div class="flex items-center gap-2 w-full">
                <Link 
                  :href="`/rapat/${m.id}`" 
                  class="flex-1 border border-slate-200 hover:bg-slate-50 text-slate-700 py-1.5 rounded-lg text-xs font-semibold text-center transition cursor-pointer"
                >
                  Detail
                </Link>
                <a 
                  :href="`/rapat/${m.id}/cetak`" 
                  target="_blank" 
                  class="flex-1 border border-blue-200 text-blue-600 hover:bg-blue-50 py-1.5 rounded-lg text-xs font-semibold text-center transition flex items-center justify-center gap-1" 
                  title="Cetak Notulen PDF"
                >
                  <i class="fas fa-print text-[11px]"></i> Cetak
                </a>
              </div>
            </div>
          </div>
        </template>
        <div v-else class="bg-white rounded-2xl border border-slate-200/80 p-12 text-center text-slate-400">
          <i class="far fa-calendar-times text-4xl mb-3 text-slate-300"></i>
          <p class="text-sm font-bold text-slate-600">Tidak ada agenda rapat yang sesuai filter.</p>
          <p class="text-xs text-slate-400 mt-1">Coba sesuaikan filter bidang atau jenis rapat di atas.</p>
        </div>
      </div>

    </div>

    <!-- ==================== MODAL BUAT AGENDA RAPAT ==================== -->
    <div v-if="showCreateModal" class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl border border-gray-100 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-4">
          <div class="flex items-center gap-2">
            <i class="far fa-calendar-plus text-blue-600 text-base"></i>
            <h3 class="text-sm font-bold text-gray-800">Buat Agenda Rapat Koordinasi</h3>
          </div>
          <button @click="showCreateModal = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold cursor-pointer">&times;</button>
        </div>

        <form @submit.prevent="submitCreate" class="space-y-4">
          <div v-if="Object.keys(createForm.errors).length > 0" class="p-3 bg-red-50 border border-red-200 text-red-700 text-xs rounded-lg">
            <p class="font-bold flex items-center gap-1.5"><i class="fas fa-exclamation-triangle"></i> Gagal menyimpan agenda rapat:</p>
            <ul class="list-disc pl-5 mt-1 space-y-0.5">
              <li v-for="(err, key) in createForm.errors" :key="key">{{ err }}</li>
            </ul>
          </div>

          <!-- 0. Preset Siklus Baku Switcher di dalam Modal -->
          <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 space-y-2.5">
            <div class="flex items-center justify-between text-xs">
              <span class="font-bold text-slate-700 flex items-center gap-1.5">
                <i class="far fa-clock text-blue-600"></i> Preset Siklus Standar:
              </span>
              <!-- Pilihan Siklus Minggu Ini vs Minggu Depan -->
              <div class="flex items-center gap-1 bg-white border border-slate-200 rounded-lg p-0.5 text-[11px] font-semibold">
                <button 
                  type="button" 
                  @click="selectedCycleWeek = 'this'"
                  class="px-2 py-0.5 rounded transition cursor-pointer"
                  :class="selectedCycleWeek === 'this' ? 'bg-blue-600 text-white font-bold' : 'text-slate-600 hover:text-slate-900'"
                >
                  Minggu Ini
                </button>
                <button 
                  type="button" 
                  @click="selectedCycleWeek = 'next'"
                  class="px-2 py-0.5 rounded transition cursor-pointer"
                  :class="selectedCycleWeek === 'next' ? 'bg-blue-600 text-white font-bold' : 'text-slate-600 hover:text-slate-900'"
                >
                  Minggu Depan
                </button>
              </div>
            </div>

            <div class="grid grid-cols-3 gap-2">
              <button 
                type="button" 
                @click="applySchedulePreset('Rapat TL + Staf')"
                class="p-2 rounded-lg border text-left transition cursor-pointer"
                :class="createForm.type === 'Rapat TL + Staf' ? 'bg-blue-50 border-blue-400 text-blue-900 shadow-2xs' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50'"
              >
                <div class="text-[10px] font-extrabold text-blue-700 uppercase">Senin Pagi</div>
                <div class="text-[11px] font-bold truncate">TL + Staf</div>
                <div class="text-[9px] text-slate-500">08:30 WITA</div>
              </button>

              <button 
                v-if="userRole === 'ASMAN' || userRole === 'MUP' || userRole === 'ADMIN'"
                type="button" 
                @click="applySchedulePreset('Rapat Asman + TL')"
                class="p-2 rounded-lg border text-left transition cursor-pointer"
                :class="createForm.type === 'Rapat Asman + TL' ? 'bg-indigo-50 border-indigo-400 text-indigo-900 shadow-2xs' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50'"
              >
                <div class="text-[10px] font-extrabold text-indigo-700 uppercase">Senin Siang</div>
                <div class="text-[11px] font-bold truncate">Asman + TL</div>
                <div class="text-[9px] text-slate-500">13:30 WITA</div>
              </button>

              <button 
                v-if="userRole === 'MUP' || userRole === 'ADMIN'"
                type="button" 
                @click="applySchedulePreset('Rapat Tinjauan Manajemen')"
                class="p-2 rounded-lg border text-left transition cursor-pointer"
                :class="createForm.type === 'Rapat Tinjauan Manajemen' ? 'bg-purple-50 border-purple-400 text-purple-900 shadow-2xs' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50'"
              >
                <div class="text-[10px] font-extrabold text-purple-700 uppercase">Selasa Pagi</div>
                <div class="text-[11px] font-bold truncate">RTM MUP</div>
                <div class="text-[9px] text-slate-500">08:30 WITA</div>
              </button>
            </div>

            <!-- Alert Helper Note -->
            <div class="text-[11px] text-emerald-800 bg-emerald-50 border border-emerald-200 rounded-lg p-2 flex items-start gap-1.5">
              <i class="fas fa-check-circle text-emerald-600 mt-0.5 text-xs shrink-0"></i>
              <span>
                <b>Tersetel Otomatis:</b> Tanggal dan jam telah disesuaikan dengan siklus baku PLN. Anda tetap dapat mengubah tanggal & waktu di bawah secara bebas jika terdapat kendala operasional (libur, darurat, dll).
              </span>
            </div>
          </div>

          <!-- 1. Tipe Rapat & Bidang Penyelenggara -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">
                Forum / Jenis Rapat <span class="text-red-500">*</span>
              </label>
              <select 
                v-model="createForm.type" 
                @change="onTypeChange"
                required 
                class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500 bg-white"
              >
                <option value="Rapat TL + Staf">Rapat TL + Staf (Seksi / Operasional)</option>
                <option v-if="userRole === 'ASMAN' || userRole === 'MUP' || userRole === 'ADMIN'" value="Rapat Asman + TL">Rapat Asman + TL (Bidang / Koordinasi)</option>
                <option v-if="userRole === 'MUP' || userRole === 'ADMIN'" value="Rapat Tinjauan Manajemen">Rapat Tinjauan Manajemen / RTM (MUP)</option>
                <option value="CUSTOM">Lainnya (Judul Khusus)</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">
                Bidang / Unit Kerja Penyelenggara <span class="text-red-500">*</span>
              </label>
              <div v-if="userRole !== 'MUP' && userRole !== 'ADMIN'" class="w-full text-xs border border-slate-200 bg-slate-50 rounded-lg p-2.5 text-slate-700 font-bold flex items-center justify-between">
                <span>{{ userUnitName || 'Unit Anda' }}</span>
                <span class="text-[10px] text-sky-700 bg-sky-100 px-2 py-0.5 rounded font-semibold">Tersinkron Otomatis</span>
              </div>
              <select 
                v-else
                v-model="createForm.unit_id" 
                required 
                class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500 bg-white"
              >
                <option v-for="u in allUnits" :key="u.id" :value="u.id">
                  {{ u.name }}
                </option>
              </select>
            </div>
          </div>

          <div v-if="createForm.type === 'CUSTOM'">
            <label class="block text-xs font-bold text-gray-700 mb-1">Judul Rapat Khusus <span class="text-red-500">*</span></label>
            <input 
              type="text" 
              v-model="createForm.custom_type" 
              placeholder="Contoh: Rapat Tanggap Darurat Trip Turbin Blok 1" 
              class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500"
            />
          </div>

          <!-- 2. Tanggal & Pimpinan Rapat -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">Tanggal Rapat <span class="text-red-500">*</span></label>
              <input 
                type="date" 
                v-model="createForm.meeting_date" 
                required 
                class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">Pimpinan Rapat (Leader) <span class="text-red-500">*</span></label>
              <select 
                v-model="createForm.leader_id" 
                required 
                class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500 bg-white"
              >
                <option v-for="ldr in leaders" :key="ldr.id" :value="ldr.id">
                  {{ ldr.name }} ({{ ldr.role?.name || 'Pejabat' }} - {{ ldr.organization_unit?.name || '' }})
                </option>
              </select>
            </div>
          </div>

          <!-- 3. Waktu Mulai & Selesai -->
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">Waktu Mulai (WITA) <span class="text-red-500">*</span></label>
              <input 
                type="time" 
                v-model="createForm.time_start" 
                required 
                class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500"
              />
            </div>
            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">Waktu Selesai (WITA) <span class="text-red-500">*</span></label>
              <input 
                type="time" 
                v-model="createForm.time_end" 
                required 
                class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500"
              />
            </div>
          </div>

          <!-- 4. Mode & Lokasi -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">Tipe / Mode Pertemuan <span class="text-red-500">*</span></label>
              <select 
                v-model="createForm.meeting_mode" 
                class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500 bg-white"
              >
                <option value="Offline">Tatap Muka (Offline di Ruangan)</option>
                <option value="Online">Online (Zoom / Video Conference)</option>
                <option value="Hybrid">Hybrid (Tatap Muka & Zoom)</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">Lokasi / Ruangan Rapat</label>
              <select 
                v-model="createForm.location" 
                class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500 bg-white"
              >
                <option value="Ruang Rapat Operasi & Keandalan">Ruang Rapat Operasi & Keandalan</option>
                <option value="Ruang Rapat Utama UP Sengkang">Ruang Rapat Utama UP Sengkang</option>
                <option value="Ruang Rapat Pemeliharaan">Ruang Rapat Pemeliharaan</option>
                <option value="Ruang Rapat VIP Manajemen (MUP)">Ruang Rapat VIP Manajemen (MUP)</option>
                <option value="Online / Video Conference (Virtual)">Online / Video Conference (Virtual)</option>
              </select>
            </div>
          </div>

          <!-- Bagian Zoom (Jika Online/Hybrid) -->
          <div v-if="createForm.meeting_mode === 'Online' || createForm.meeting_mode === 'Hybrid'" class="p-3.5 bg-blue-50/70 border border-blue-200 rounded-xl space-y-3">
            <div class="flex items-center gap-2 text-xs font-bold text-blue-800">
              <i class="fas fa-video text-blue-600"></i> Detail Tautan Rapat Zoom / Online
            </div>
            <div>
              <label class="block text-[11px] font-semibold text-gray-700 mb-1">Tautan / Link Zoom Meeting</label>
              <input 
                type="url" 
                v-model="createForm.zoom_link" 
                placeholder="https://zoom.us/j/987654321?pwd=..." 
                class="w-full text-xs border border-blue-200 rounded-lg p-2 outline-none focus:border-blue-600 bg-white"
              />
            </div>
            <div class="grid grid-cols-2 gap-2">
              <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Meeting ID (Opsional)</label>
                <input 
                  type="text" 
                  v-model="createForm.zoom_meeting_id" 
                  placeholder="890 1234 5678" 
                  class="w-full text-xs border border-blue-200 rounded-lg p-2 outline-none focus:border-blue-600 bg-white"
                />
              </div>
              <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Passcode (Opsional)</label>
                <input 
                  type="text" 
                  v-model="createForm.zoom_passcode" 
                  placeholder="PLNNP2026" 
                  class="w-full text-xs border border-blue-200 rounded-lg p-2 outline-none focus:border-blue-600 bg-white"
                />
              </div>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">Status Sesi</label>
              <select 
                v-model="createForm.status" 
                class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500 bg-white"
              >
                <option value="Akan Datang">Akan Datang (Terjadwal)</option>
                <option value="Berlangsung">Berlangsung Sekarang (LIVE)</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">Siklus Mingguan</label>
              <input 
                type="text" 
                disabled 
                :value="`Tahun ${new Date().getFullYear()}`" 
                class="w-full text-xs border border-gray-200 rounded-lg p-2.5 bg-slate-50 text-gray-500"
              />
            </div>
          </div>

          <!-- Isu yang Diagendakan -->
          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1.5 flex items-center justify-between">
              <span><i class="fas fa-list-check text-blue-600 mr-1"></i> Pilih Isu yang Diagendakan ke Rapat</span>
              <span class="text-[10px] text-gray-400 font-normal">Centang isu untuk dibahas</span>
            </label>
            <div class="max-h-36 overflow-y-auto border border-gray-200 rounded-xl p-2.5 space-y-2 bg-slate-50/50 divide-y divide-gray-100">
              <template v-if="availableIssues && availableIssues.length > 0">
                <label 
                  v-for="iss in availableIssues" 
                  :key="iss.id" 
                  class="flex items-start gap-2.5 pt-1.5 first:pt-0 cursor-pointer hover:bg-white p-1 rounded transition"
                >
                  <input 
                    type="checkbox" 
                    :value="iss.id" 
                    v-model="createForm.issue_ids" 
                    class="mt-0.5 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                  />
                  <div class="text-[11px] leading-tight">
                    <span class="font-bold text-gray-800">#{{ iss.issue_no }}</span>: {{ iss.title }}
                    <div class="flex items-center gap-2 mt-0.5">
                      <span class="text-[9px] font-semibold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded">Level {{ iss.current_level }}</span>
                      <span :class="iss.priority === 'CRITICAL' ? 'text-red-600 bg-red-50' : 'text-amber-600 bg-amber-50'" class="text-[9px] font-semibold px-1.5 py-0.5 rounded">{{ iss.priority }}</span>
                    </div>
                  </div>
                </label>
              </template>
              <p v-else class="text-xs text-gray-400 text-center py-2">Tidak ada isu aktif yang perlu diagendakan.</p>
            </div>
          </div>

          <!-- Notes -->
          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Topik Utama / Catatan Awal Pembahasan</label>
            <textarea 
              v-model="createForm.notes" 
              rows="2" 
              placeholder="Contoh: Evaluasi task minggu N-1, tindak lanjut kebocoran seal pompa..." 
              class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500"
            ></textarea>
          </div>

          <!-- Tombol Submit -->
          <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
            <button 
              type="button" 
              @click="showCreateModal = false" 
              class="px-4 py-2 border border-gray-200 text-gray-600 rounded-lg text-xs hover:bg-gray-50 font-semibold cursor-pointer"
            >
              Batal
            </button>
            <button 
              type="submit" 
              :disabled="createForm.processing"
              class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold shadow-xs transition flex items-center gap-1.5 cursor-pointer disabled:opacity-50 disabled:pointer-events-none disabled:cursor-not-allowed"
            >
              <i v-if="createForm.processing" class="fas fa-spinner fa-spin text-xs"></i>
              <i v-else class="fas fa-save"></i> 
              {{ createForm.processing ? 'Menyimpan...' : 'Simpan & Jadwalkan' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- ==================== MODAL CETAK NOTULEN PER MINGGU ==================== -->
    <div v-if="showWeeklyModal" class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-100">
        <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-4">
          <div class="flex items-center gap-2">
            <i class="fas fa-print text-blue-600 text-base"></i>
            <h3 class="text-sm font-bold text-gray-800">Cetak Notulen Per Minggu</h3>
          </div>
          <button @click="showWeeklyModal = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold cursor-pointer">&times;</button>
        </div>

        <div class="space-y-4">
          <p class="text-xs text-gray-600">
            Sistem akan mengonsolidasi seluruh sesi rapat koordinasi (TL+Staf, Asman+TL, RTM), rekap kendala eskalasi, putusan, dan task tindak lanjut ke dalam <b>format resmi risalah notulen PLN UP Sengkang</b>.
          </p>

          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Pilih Siklus Minggu (Week)</label>
            <select 
              v-model="selectedWeeklyWeek" 
              class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500 bg-white font-semibold"
            >
              <option v-for="w in (availableWeeks && availableWeeks.length > 0 ? availableWeeks : fallbackWeeks)" :key="w.week_number" :value="w.week_number">
                {{ w.label }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Tahun Anggaran</label>
            <select 
              v-model="selectedWeeklyYear" 
              class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500 bg-white font-semibold"
            >
              <option :value="2026">2026</option>
              <option :value="2025">2025</option>
            </select>
          </div>

          <div class="bg-blue-50/70 border border-blue-200 p-3 rounded-xl flex items-center justify-between text-xs">
            <div>
              <span class="text-blue-900 font-bold block">Minggu Berjalan (Saat Ini)</span>
              <span class="text-blue-700 text-[11px]">Siklus W{{ currentWeek }} Tahun {{ currentYear }}</span>
            </div>
            <button 
              type="button" 
              @click="openWeeklyNotulen(currentWeek, currentYear)"
              class="px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-[11px] font-bold shadow-2xs transition cursor-pointer"
            >
              Langsung Cetak W{{ currentWeek }}
            </button>
          </div>

          <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
            <button 
              type="button" 
              @click="showWeeklyModal = false" 
              class="px-4 py-2 border border-gray-200 text-gray-600 rounded-lg text-xs hover:bg-gray-50 font-semibold cursor-pointer"
            >
              Tutup
            </button>
            <button 
              type="button" 
              @click="openWeeklyNotulen()"
              class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold shadow-xs transition flex items-center gap-1.5 cursor-pointer"
            >
              <i class="fas fa-file-invoice"></i> Buka / Cetak Notulen
            </button>
          </div>
        </div>
      </div>
    </div>

  </AppLayout>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import { Link, router, useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  meetings: Array,
  leaders: Array,
  availableIssues: Array,
  departments: {
    type: Array,
    default: () => [],
  },
  allUnits: {
    type: Array,
    default: () => [],
  },
  tab: {
    type: String,
    default: 'jadwal',
  },
  selectedUnitId: {
    type: [String, Number],
    default: 'semua',
  },
  selectedType: {
    type: String,
    default: 'semua',
  },
  userRole: {
    type: String,
    default: 'STAFF',
  },
  userUnitId: {
    type: Number,
    default: null,
  },
  userUnitName: {
    type: String,
    default: 'UP Sengkang',
  },
  currentWeek: {
    type: Number,
    default: 45,
  },
  currentYear: {
    type: Number,
    default: 2026,
  },
  availableWeeks: {
    type: Array,
    default: () => [],
  },
});

const page = usePage();
const authUserId = page.props.auth?.user?.id || 1;

const currentTab = ref(props.tab || 'jadwal');
const filterUnitId = ref(props.selectedUnitId || 'semua');
const filterType = ref(props.selectedType || 'semua');

const showCreateModal = ref(false);
const showWeeklyModal = ref(false);
const selectedWeeklyWeek = ref(props.currentWeek || 45);
const selectedWeeklyYear = ref(props.currentYear || 2026);

const fallbackWeeks = computed(() => {
  const list = [];
  const curr = props.currentWeek || 45;
  for (let i = curr; i >= Math.max(1, curr - 6); i--) {
    list.push({
      week_number: i,
      year: props.currentYear || 2026,
      label: `Minggu Ke-${i} (Tahun ${props.currentYear || 2026})`,
    });
  }
  return list;
});

function openWeeklyNotulen(targetWeek, targetYear) {
  const w = targetWeek || selectedWeeklyWeek.value;
  const y = targetYear || selectedWeeklyYear.value;
  showWeeklyModal.value = false;
  window.open(`/rapat/notulen-mingguan?week=${w}&year=${y}`, '_blank');
}

// Perhitungan Siklus Rapat Baku PLN UP Sengkang:
// 1. Rapat TL + Staf: Senin Pagi (08:30 - 10:00 WITA)
// 2. Rapat Asman + TL: Senin Siang (13:30 - 15:30 WITA)
// 3. RTM Asman + MUP: Selasa Pagi (08:30 - 11:00 WITA)
function getCycleDates() {
  const now = new Date();
  const day = now.getDay(); // 0 = Min, 1 = Sen, 2 = Sel, 3 = Rab, 4 = Kam, 5 = Jum, 6 = Sab

  // Selisih hari ke hari Senin minggu ini
  let diffToMonThisWeek = 1 - day;
  if (day === 0) diffToMonThisWeek = -6;

  const monThisWeek = new Date(now);
  monThisWeek.setDate(now.getDate() + diffToMonThisWeek);

  const tueThisWeek = new Date(monThisWeek);
  tueThisWeek.setDate(monThisWeek.getDate() + 1);

  // Senin & Selasa minggu berikutnya
  const monNextWeek = new Date(monThisWeek);
  monNextWeek.setDate(monThisWeek.getDate() + 7);

  const tueNextWeek = new Date(monNextWeek);
  tueNextWeek.setDate(monNextWeek.getDate() + 1);

  const formatDate = (d) => {
    const y = d.getFullYear();
    const m = String(d.getMonth() + 1).padStart(2, '0');
    const dayNum = String(d.getDate()).padStart(2, '0');
    return `${y}-${m}-${dayNum}`;
  };

  const isPastMonday = day > 1 || day === 0; // Jika Selasa s/d Minggu, Senin minggu ini sudah lewat
  const isPastTuesday = day > 2 || day === 0; // Jika Rabu s/d Minggu, Selasa minggu ini sudah lewat

  return {
    thisWeekMonday: formatDate(monThisWeek),
    thisWeekTuesday: formatDate(tueThisWeek),
    nextWeekMonday: formatDate(monNextWeek),
    nextWeekTuesday: formatDate(tueNextWeek),
    isPastMonday,
    isPastTuesday,
    defaultMonday: isPastMonday ? formatDate(monNextWeek) : formatDate(monThisWeek),
    defaultTuesday: isPastTuesday ? formatDate(tueNextWeek) : formatDate(tueThisWeek),
  };
}

const cycleDates = getCycleDates();
const selectedCycleWeek = ref(cycleDates.isPastMonday ? 'next' : 'this');

function getTargetDate(dayType) {
  if (dayType === 'monday') {
    return selectedCycleWeek.value === 'next' ? cycleDates.nextWeekMonday : cycleDates.thisWeekMonday;
  }
  return selectedCycleWeek.value === 'next' ? cycleDates.nextWeekTuesday : cycleDates.thisWeekTuesday;
}

const defaultMeetingType = props.userRole === 'ASMAN' 
  ? 'Rapat Asman + TL' 
  : (props.userRole === 'MUP' ? 'Rapat Tinjauan Manajemen' : 'Rapat TL + Staf');

const createForm = useForm({
  type: defaultMeetingType,
  unit_id: props.userUnitId || (props.allUnits?.[0]?.id || 1),
  custom_type: '',
  meeting_date: cycleDates.defaultMonday,
  time_start: '08:30',
  time_end: '10:00',
  meeting_mode: 'Offline',
  location: 'Ruang Rapat Seksi / Operasi',
  zoom_link: '',
  zoom_meeting_id: '',
  zoom_passcode: '',
  leader_id: authUserId,
  status: 'Akan Datang',
  notes: '',
  issue_ids: [],
});

function applySchedulePreset(type) {
  createForm.type = type;

  if (type === 'Rapat TL + Staf') {
    // 1. Senin Pagi (08:30 - 10:00 WITA)
    createForm.meeting_date = getTargetDate('monday');
    createForm.time_start = '08:30';
    createForm.time_end = '10:00';
    createForm.location = 'Ruang Rapat Seksi / Operasi';
    const tlLeader = props.leaders?.find(l => l.role?.name === 'TL' && l.unit_id === createForm.unit_id)
                  || props.leaders?.find(l => l.role?.name === 'TL')
                  || authUserId;
    if (tlLeader) createForm.leader_id = typeof tlLeader === 'object' ? tlLeader.id : tlLeader;

  } else if (type === 'Rapat Asman + TL') {
    // 2. Senin Siang (13:30 - 15:30 WITA)
    createForm.meeting_date = getTargetDate('monday');
    createForm.time_start = '13:30';
    createForm.time_end = '15:30';
    createForm.location = 'Ruang Rapat Bidang / Keandalan';
    const asmanLeader = props.leaders?.find(l => l.role?.name === 'ASMAN' && l.unit_id === createForm.unit_id)
                     || props.leaders?.find(l => l.role?.name === 'ASMAN')
                     || authUserId;
    if (asmanLeader) createForm.leader_id = typeof asmanLeader === 'object' ? asmanLeader.id : asmanLeader;

  } else if (type === 'Rapat Tinjauan Manajemen') {
    // 3. Selasa Pagi (08:30 - 11:00 WITA)
    createForm.meeting_date = getTargetDate('tuesday');
    createForm.time_start = '08:30';
    createForm.time_end = '11:00';
    createForm.location = 'Ruang Rapat Utama UP Sengkang';
    const mupLeader = props.leaders?.find(l => l.role?.name === 'MUP') || authUserId;
    if (mupLeader) createForm.leader_id = typeof mupLeader === 'object' ? mupLeader.id : mupLeader;
  }
}

function openCreateModal(presetType = null) {
  const targetType = presetType || createForm.type || defaultMeetingType;
  if (targetType.includes('Manajemen') || targetType.includes('RTM')) {
    selectedCycleWeek.value = cycleDates.isPastTuesday ? 'next' : 'this';
  } else {
    selectedCycleWeek.value = cycleDates.isPastMonday ? 'next' : 'this';
  }
  applySchedulePreset(targetType);
  showCreateModal.value = true;
}

function onTypeChange() {
  if (createForm.type !== 'CUSTOM') {
    applySchedulePreset(createForm.type);
  }
}

watch(selectedCycleWeek, () => {
  if (createForm.type !== 'CUSTOM') {
    applySchedulePreset(createForm.type);
  }
});

function setTab(newTab) {
  currentTab.value = newTab;
  applyFilter();
}

function applyFilter() {
  router.get('/rapat', {
    tab: currentTab.value,
    unit_id: filterUnitId.value,
    type: filterType.value,
  }, {
    preserveState: true,
    preserveScroll: true,
  });
}

function submitCreate() {
  if (createForm.processing) return;
  createForm.post('/rapat', {
    onSuccess: () => {
      showCreateModal.value = false;
      createForm.reset();
    },
  });
}

function getUnitBadgeText(m) {
  if (m.organization_unit?.name) {
    return m.organization_unit.name;
  }
  if (m.leader?.organization_unit?.name) {
    return m.leader.organization_unit.name;
  }
  if (m.type?.includes('Manajemen') || m.type?.includes('RTM')) {
    return 'UP Sengkang';
  }
  return 'Seksi Unit';
}

function getDayName(dStr) {
  if (!dStr) return '';
  const d = new Date(dStr);
  return d.toLocaleDateString('id-ID', { weekday: 'short' }).toUpperCase();
}

function getDayNum(dStr) {
  if (!dStr) return '';
  const d = new Date(dStr);
  return String(d.getDate()).padStart(2, '0');
}

function getMonthYear(dStr) {
  if (!dStr) return '';
  const d = new Date(dStr);
  return d.toLocaleDateString('id-ID', { month: 'short', year: 'numeric' });
}
</script>

<template>
  <AppLayout :title="`Detail Isu #${issue.issue_no}`">
    <div class="space-y-6">

      <!-- Tombol Navigasi Kembali -->
      <div>
        <Link 
          href="/issues" 
          class="inline-flex items-center gap-2 text-xs font-semibold text-gray-500 hover:text-blue-600 transition"
        >
          <i class="fas fa-arrow-left"></i> Kembali ke Daftar Eskalasi / Isu
        </Link>
      </div>

      <!-- ==================== BAGIAN ATAS - DETAIL ISSUE ==================== -->
      <div class="card bg-white rounded-xl border border-gray-200 shadow-xs overflow-hidden">
        <!-- Card Header -->
        <div class="bg-slate-50/80 p-6 border-b border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
          <div class="space-y-2">
            <div class="flex flex-wrap items-center gap-2">
              <!-- Nomor Isu -->
              <span class="bg-slate-800 text-white font-mono text-xs font-bold px-2.5 py-1 rounded shadow-xs">
                #{{ issue.issue_no }}
              </span>

              <!-- Prioritas -->
              <span :class="priorityBadgeClass(issue.priority)">
                Prioritas: {{ issue.priority }}
              </span>

              <!-- Level Saat Ini -->
              <span class="bg-blue-100 text-blue-800 border border-blue-200 px-2.5 py-1 rounded text-xs font-bold flex items-center gap-1">
                <i class="fas fa-layer-group text-blue-600"></i> Level Saat Ini: Level {{ issue.current_level }}
              </span>

              <!-- Status -->
              <span :class="statusBadgeClass(issue.status)">
                Status: {{ issue.status }}
              </span>
            </div>

            <!-- Judul Isu -->
            <h1 class="text-xl font-bold text-gray-900 leading-snug">{{ issue.title }}</h1>
          </div>

          <!-- Info Pelapor & Tombol Aksi Edit/Delete -->
          <div class="flex flex-col items-end gap-2 shrink-0">
            <div class="text-right text-xs text-gray-500 bg-white p-3 rounded-lg border border-gray-100 shadow-xs">
              <p>Dilaporkan Oleh: <b class="text-gray-800">{{ issue.reporter?.name || 'Staff Pelaksana' }}</b></p>
              <p class="font-mono text-[11px] text-gray-400 mt-0.5">
                {{ formatDate(issue.created_at) }}
              </p>
            </div>
            <div class="flex items-center gap-2">
              <button 
                type="button" 
                @click="openEditModal"
                class="px-3 py-1.5 bg-amber-50 hover:bg-amber-600 hover:text-white text-amber-700 rounded-lg text-xs font-bold transition border border-amber-200 flex items-center gap-1.5 cursor-pointer shadow-xs"
              >
                <i class="fas fa-edit"></i> Edit Isu
              </button>
              <button 
                type="button" 
                @click="confirmDelete"
                class="px-3 py-1.5 bg-red-50 hover:bg-red-600 hover:text-white text-red-700 rounded-lg text-xs font-bold transition border border-red-200 flex items-center gap-1.5 cursor-pointer shadow-xs"
              >
                <i class="fas fa-trash"></i> Hapus Isu
              </button>
            </div>
          </div>
        </div>

        <!-- Card Body -->
        <div class="p-6 space-y-4">
          <div>
            <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-2 flex items-center gap-1.5">
              <i class="fas fa-align-left text-blue-600"></i> Deskripsi & Uraian Masalah
            </h4>
            <div class="p-4 bg-slate-50 border border-gray-200 rounded-xl text-xs text-gray-700 leading-relaxed whitespace-pre-line font-normal">
              {{ issue.clean_description || issue.description }}
            </div>
          </div>

          <!-- Berkas Lampiran / Bukti Fisik -->
          <div v-if="issue.attachment_url || issue.document_url" class="space-y-3">
            <!-- 1. Foto Bukti Lapangan -->
            <div v-if="issue.attachment_url && issue.is_image_attachment" class="p-4 bg-blue-50/50 border border-blue-200/80 rounded-xl">
              <h4 class="text-xs font-bold text-blue-900 uppercase tracking-wider mb-3 flex items-center gap-2">
                <i class="fas fa-camera text-blue-600"></i> Foto Bukti Lapangan
              </h4>
              <div class="relative group max-w-sm rounded-lg overflow-hidden border border-gray-200 bg-white shadow-xs">
                <img 
                  :src="issue.attachment_url" 
                  alt="Bukti Lampiran" 
                  class="w-full max-h-72 object-contain bg-slate-100 hover:opacity-95 transition cursor-pointer"
                  @click="previewImage = issue.attachment_url"
                />
                <div class="p-2.5 bg-white border-t border-gray-100 flex items-center justify-between text-xs">
                  <span class="text-gray-600 truncate font-mono text-[11px] max-w-[180px]" :title="issue.attachment_name">
                    {{ issue.attachment_name }}
                  </span>
                  <div class="flex items-center gap-2 shrink-0">
                    <button 
                      type="button" 
                      @click="previewImage = issue.attachment_url"
                      class="px-2 py-1 bg-blue-50 hover:bg-blue-600 hover:text-white text-blue-700 rounded text-[11px] font-semibold transition cursor-pointer"
                    >
                      <i class="fas fa-expand mr-1"></i> Perbesar
                    </button>
                    <a 
                      :href="issue.attachment_url" 
                      target="_blank" 
                      download
                      class="px-2 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded text-[11px] font-semibold transition"
                    >
                      <i class="fas fa-download mr-1"></i> Unduh
                    </a>
                  </div>
                </div>
              </div>
            </div>

            <!-- 2. Dokumen Teknis / Pendukung -->
            <div v-if="issue.document_url || (issue.attachment_url && !issue.is_image_attachment)" class="p-4 bg-amber-50/40 border border-amber-200/80 rounded-xl">
              <h4 class="text-xs font-bold text-amber-900 uppercase tracking-wider mb-3 flex items-center gap-2">
                <i class="fas fa-file-lines text-amber-600"></i> Dokumen Pendukung / Laporan Teknis
              </h4>
              <div class="flex items-center justify-between p-3 bg-white border border-amber-200 rounded-lg max-w-md shadow-2xs">
                <div class="flex items-center gap-3">
                  <i class="fas fa-file-pdf text-2xl text-rose-500"></i>
                  <div>
                    <p class="text-xs font-bold text-gray-800">{{ issue.document_name || issue.attachment_name }}</p>
                    <p class="text-[10px] text-gray-400 font-mono">Dokumen Teknis Lapangan</p>
                  </div>
                </div>
                <a 
                  :href="issue.document_url || issue.attachment_url" 
                  target="_blank" 
                  download
                  class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-slate-950 rounded text-xs font-bold flex items-center gap-1.5 transition shadow-xs"
                >
                  <i class="fas fa-download"></i> Unduh Dokumen
                </a>
              </div>
            </div>
          </div>

          <!-- Ringkasan Atribut Tambahan -->
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs pt-1">
            <div class="p-3 bg-white border border-gray-100 rounded-lg">
              <span class="text-gray-400 block text-[10px] uppercase font-bold">Kategori</span>
              <span class="font-semibold text-gray-800">{{ issue.issue_type || 'PROBLEM' }}</span>
            </div>
            <div class="p-3 bg-white border border-gray-100 rounded-lg">
              <span class="text-gray-400 block text-[10px] uppercase font-bold">Target Penyelesaian</span>
              <span class="font-semibold text-gray-800 font-mono">{{ issue.due_date ? formatDateOnly(issue.due_date) : '-' }}</span>
            </div>
            <div class="p-3 bg-white border border-gray-100 rounded-lg">
              <span class="text-gray-400 block text-[10px] uppercase font-bold">Pemilik / PIC</span>
              <span class="font-semibold text-gray-800">{{ issue.owner?.name || issue.reporter?.name || '-' }}</span>
            </div>
            <div class="p-3 bg-white border border-gray-100 rounded-lg">
              <span class="text-gray-400 block text-[10px] uppercase font-bold">Status SLA</span>
              <span :class="slaBadgeClass(issue)">
                {{ slaLabel(issue) }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- ==================== STRUKTUR JALUR KOMANDO & HIERARKI PELAPORAN ==================== -->
      <div class="card bg-white rounded-xl border border-gray-200 shadow-xs overflow-hidden">
        <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-indigo-950 p-4 sm:px-6 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-3">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-blue-500/20 border border-blue-400/30 flex items-center justify-center text-blue-400 text-sm shadow-inner shrink-0">
              <i class="fas fa-sitemap"></i>
            </div>
            <div>
              <h3 class="text-sm font-bold text-white tracking-wide flex items-center gap-2">
                Struktur Jalur Komando & Hierarki Pelaporan
                <span class="text-[10px] font-normal px-2 py-0.5 rounded-full bg-blue-500/30 text-blue-200 border border-blue-400/30 hidden sm:inline-block">
                  Lowest Competent Level
                </span>
              </h3>
              <p class="text-[11px] text-slate-300">
                Alur berjenjang: Staff Penginput ➔ Team Leader (TL) ➔ Asisten Manajer (ASMAN) ➔ Manajer Unit (MUP)
              </p>
            </div>
          </div>
          <div class="flex items-center gap-2 self-start sm:self-auto shrink-0">
            <span class="text-xs text-slate-300 font-medium">Level Berjalan:</span>
            <span class="px-2.5 py-1 bg-amber-400 text-slate-900 rounded-lg text-xs font-black shadow-xs flex items-center gap-1">
              <i class="fas fa-layer-group"></i> Level {{ issue.current_level || 'TL' }}
            </span>
          </div>
        </div>

        <div class="p-6 bg-slate-50/60">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4 relative">

            <!-- STEP 1: STAFF PELAKSANA -->
            <div 
              class="relative bg-white rounded-xl border p-4 transition shadow-xs flex flex-col justify-between"
              :class="{'border-emerald-300 ring-2 ring-emerald-100': issue.current_level === 'TL', 'border-gray-200': issue.current_level !== 'TL'}"
            >
              <div>
                <div class="flex items-center justify-between mb-3">
                  <span class="px-2 py-0.5 text-[10px] font-extrabold uppercase rounded bg-emerald-100 text-emerald-800 border border-emerald-200">
                    1. Staff Pelapor
                  </span>
                  <span class="text-[10px] text-gray-400 font-medium">Input Issue</span>
                </div>
                <div class="flex items-center gap-3">
                  <img 
                    :src="issue.hierarchy_line?.staff?.avatar || issue.reporter?.avatar_url || 'https://ui-avatars.com/api/?name=Staff&background=0284c7&color=fff'" 
                    alt="Staff" 
                    class="w-11 h-11 rounded-full object-cover border-2 border-emerald-400/50 shadow-xs shrink-0" 
                  />
                  <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold text-gray-900 truncate" :title="issue.hierarchy_line?.staff?.name || issue.reporter?.name">
                      {{ issue.hierarchy_line?.staff?.name || issue.reporter?.name || 'Staff Pelapor' }}
                    </p>
                    <p class="text-[11px] text-gray-600 truncate mt-0.5" :title="issue.hierarchy_line?.staff?.position">
                      {{ issue.hierarchy_line?.staff?.position || 'Staff Pelaksana' }}
                    </p>
                    <p class="text-[10px] text-emerald-700 font-medium truncate mt-0.5">
                      <i class="fas fa-building text-[9px] mr-1"></i>{{ issue.hierarchy_line?.staff?.unit || issue.organization_unit?.name || '-' }}
                    </p>
                  </div>
                </div>
              </div>
              <div class="mt-4 pt-3 border-t border-gray-100 text-[11px] text-gray-500 flex items-center justify-between">
                <span>Pelaporan Awal</span>
                <span class="text-emerald-600 font-bold"><i class="fas fa-check-circle"></i> Selesai</span>
              </div>
            </div>

            <!-- STEP 2: TEAM LEADER (TL) -->
            <div 
              class="relative bg-white rounded-xl border p-4 transition shadow-xs flex flex-col justify-between"
              :class="{'border-blue-500 ring-2 ring-blue-200 shadow-md': issue.current_level === 'TL', 'border-gray-200': issue.current_level !== 'TL'}"
            >
              <div>
                <div class="flex items-center justify-between mb-3">
                  <span class="px-2 py-0.5 text-[10px] font-extrabold uppercase rounded bg-blue-100 text-blue-800 border border-blue-200">
                    2. Team Leader (TL)
                  </span>
                  <span v-if="issue.current_level === 'TL'" class="text-[10px] font-bold text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-200 animate-pulse">
                    <i class="fas fa-hourglass-half"></i> Sedang Ditinjau
                  </span>
                  <span v-else-if="['ASMAN', 'MUP'].includes(issue.current_level)" class="text-[10px] text-blue-600 font-bold">
                    <i class="fas fa-check"></i> Dieskalasikan
                  </span>
                </div>
                <div class="flex items-center gap-3">
                  <img 
                    :src="issue.hierarchy_line?.tl?.avatar || 'https://ui-avatars.com/api/?name=TL&background=0284c7&color=fff'" 
                    alt="TL" 
                    class="w-11 h-11 rounded-full object-cover border-2 border-blue-400/50 shadow-xs shrink-0" 
                  />
                  <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold text-gray-900 truncate" :title="issue.hierarchy_line?.tl?.name">
                      {{ issue.hierarchy_line?.tl?.name || 'Belum Ditetapkan' }}
                    </p>
                    <p class="text-[11px] text-gray-600 truncate mt-0.5" :title="issue.hierarchy_line?.tl?.position">
                      {{ issue.hierarchy_line?.tl?.position || 'Team Leader' }}
                    </p>
                    <p class="text-[10px] text-blue-700 font-medium truncate mt-0.5">
                      <i class="fas fa-users-cog text-[9px] mr-1"></i>{{ issue.hierarchy_line?.tl?.unit || '-' }}
                    </p>
                  </div>
                </div>
              </div>
              <div class="mt-4 pt-3 border-t border-gray-100 text-[11px] text-gray-500 flex items-center justify-between">
                <span>Rapat Pagi (Morning Talk)</span>
                <span :class="issue.current_level === 'TL' ? 'text-blue-700 font-bold' : 'text-gray-400'">
                  {{ issue.current_level === 'TL' ? 'Tinjauan Berlangsung' : 'Tahap TL' }}
                </span>
              </div>
            </div>

            <!-- STEP 3: ASISTEN MANAJER (ASMAN) -->
            <div 
              class="relative bg-white rounded-xl border p-4 transition shadow-xs flex flex-col justify-between"
              :class="{'border-indigo-500 ring-2 ring-indigo-200 shadow-md': issue.current_level === 'ASMAN', 'border-gray-200': issue.current_level !== 'ASMAN'}"
            >
              <div>
                <div class="flex items-center justify-between mb-3">
                  <span class="px-2 py-0.5 text-[10px] font-extrabold uppercase rounded bg-indigo-100 text-indigo-800 border border-indigo-200">
                    3. Asisten Manajer
                  </span>
                  <span v-if="issue.current_level === 'ASMAN'" class="text-[10px] font-bold text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-200 animate-pulse">
                    <i class="fas fa-hourglass-half"></i> Sedang Ditinjau
                  </span>
                  <span v-else-if="issue.current_level === 'MUP'" class="text-[10px] text-indigo-600 font-bold">
                    <i class="fas fa-check"></i> Dieskalasikan ke MUP
                  </span>
                </div>
                
                <!-- Jika Ada Asman Bidang -->
                <div v-if="issue.hierarchy_line?.asman" class="flex items-center gap-3">
                  <img 
                    :src="issue.hierarchy_line.asman.avatar || 'https://ui-avatars.com/api/?name=ASMAN&background=0284c7&color=fff'" 
                    alt="Asman" 
                    class="w-11 h-11 rounded-full object-cover border-2 border-indigo-400/50 shadow-xs shrink-0" 
                  />
                  <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold text-gray-900 truncate" :title="issue.hierarchy_line.asman.name">
                      {{ issue.hierarchy_line.asman.name }}
                    </p>
                    <p class="text-[11px] text-gray-600 truncate mt-0.5" :title="issue.hierarchy_line.asman.position">
                      {{ issue.hierarchy_line.asman.position }}
                    </p>
                    <p class="text-[10px] text-indigo-700 font-medium truncate mt-0.5">
                      <i class="fas fa-briefcase text-[9px] mr-1"></i>{{ issue.hierarchy_line.asman.unit }}
                    </p>
                  </div>
                </div>

                <!-- Jika Langsung ke MUP (Unit Khusus tanpa Asman Bidang) -->
                <div v-else class="flex items-center gap-3">
                  <div class="w-11 h-11 rounded-full bg-slate-100 border-2 border-slate-300 flex items-center justify-center text-slate-500 shrink-0 font-bold text-xs">
                    <i class="fas fa-level-up-alt"></i>
                  </div>
                  <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold text-gray-700 truncate">Langsung ke MUP</p>
                    <p class="text-[11px] text-gray-500 truncate mt-0.5">Unit Mandiri Non-Bidang</p>
                    <p class="text-[10px] text-gray-400 font-medium truncate mt-0.5">K3 / Lingkungan</p>
                  </div>
                </div>
              </div>
              <div class="mt-4 pt-3 border-t border-gray-100 text-[11px] text-gray-500 flex items-center justify-between">
                <span>Rapat Siang (Noon Meeting)</span>
                <span :class="issue.current_level === 'ASMAN' ? 'text-indigo-700 font-bold' : 'text-gray-400'">
                  {{ issue.current_level === 'ASMAN' ? 'Tinjauan Berlangsung' : 'Tahap ASMAN' }}
                </span>
              </div>
            </div>

            <!-- STEP 4: MANAJER UNIT (MUP) -->
            <div 
              class="relative bg-white rounded-xl border p-4 transition shadow-xs flex flex-col justify-between"
              :class="{'border-purple-500 ring-2 ring-purple-200 shadow-md': issue.current_level === 'MUP', 'border-gray-200': issue.current_level !== 'MUP'}"
            >
              <div>
                <div class="flex items-center justify-between mb-3">
                  <span class="px-2 py-0.5 text-[10px] font-extrabold uppercase rounded bg-purple-100 text-purple-800 border border-purple-200">
                    4. Manajer Unit (MUP)
                  </span>
                  <span v-if="issue.current_level === 'MUP'" class="text-[10px] font-bold text-purple-700 bg-purple-50 px-1.5 py-0.5 rounded border border-purple-200 animate-pulse">
                    <i class="fas fa-crown text-amber-500"></i> Level Final
                  </span>
                  <span v-else class="text-[10px] text-gray-400">
                    Keputusan Strategis
                  </span>
                </div>
                <div class="flex items-center gap-3">
                  <img 
                    :src="issue.hierarchy_line?.mup?.avatar || 'https://ui-avatars.com/api/?name=MUP&background=0284c7&color=fff'" 
                    alt="MUP" 
                    class="w-11 h-11 rounded-full object-cover border-2 border-purple-400/50 shadow-xs shrink-0" 
                  />
                  <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold text-gray-900 truncate" :title="issue.hierarchy_line?.mup?.name">
                      {{ issue.hierarchy_line?.mup?.name || 'MOH. FURQON AKHSANI' }}
                    </p>
                    <p class="text-[11px] text-gray-600 truncate mt-0.5" :title="issue.hierarchy_line?.mup?.position">
                      {{ issue.hierarchy_line?.mup?.position || 'MANAGER UNIT' }}
                    </p>
                    <p class="text-[10px] text-purple-700 font-medium truncate mt-0.5">
                      <i class="fas fa-landmark text-[9px] mr-1"></i>{{ issue.hierarchy_line?.mup?.unit || 'UP Sengkang' }}
                    </p>
                  </div>
                </div>
              </div>
              <div class="mt-4 pt-3 border-t border-gray-100 text-[11px] text-gray-500 flex items-center justify-between">
                <span>Rapat Manajemen (Sore)</span>
                <span :class="issue.current_level === 'MUP' ? 'text-purple-700 font-bold' : 'text-gray-400'">
                  {{ issue.current_level === 'MUP' ? 'Tinjauan Berlangsung' : 'Tahap MUP' }}
                </span>
              </div>
            </div>

          </div>
        </div>
      </div>

      <!-- ==================== BAGIAN TENGAH - TIMELINE ESKALASI ==================== -->
      <div class="card bg-white rounded-xl border border-gray-200 shadow-xs overflow-hidden">
        <div class="bg-slate-50/80 p-5 border-b border-gray-100 flex items-center justify-between">
          <div class="flex items-center gap-2">
            <i class="fas fa-stream text-blue-600"></i>
            <h3 class="text-sm font-bold text-gray-800">Timeline Riwayat Perjalanan Eskalasi</h3>
          </div>
          <span class="bg-blue-50 text-blue-700 border border-blue-200 px-2.5 py-0.5 rounded text-xs font-bold">
            {{ issue.escalations?.length || 0 }} Kali Rekam Jejak
          </span>
        </div>

        <div class="p-6">
          <div class="relative pl-6 space-y-6 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-blue-200">
            
            <!-- Titik Awal: Pelaporan Pertama -->
            <div class="relative">
              <span class="absolute -left-[27px] top-1.5 w-3.5 h-3.5 rounded-full bg-emerald-500 ring-4 ring-emerald-100"></span>
              <div class="bg-slate-50/80 p-3.5 rounded-xl border border-gray-200 text-xs">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 mb-1">
                  <span class="font-bold text-gray-800 flex items-center gap-1.5">
                    <i class="fas fa-flag text-emerald-600"></i> Isu Dibuat / Dilaporkan Pertama Kali
                  </span>
                  <span class="text-gray-400 font-mono text-[11px]">{{ formatDate(issue.created_at) }}</span>
                </div>
                <p class="text-gray-600">
                  Masalah dicatat oleh <b class="text-gray-800">{{ issue.reporter?.name || 'Staff Pelaksana' }}</b> dan otomatis masuk ke antrean tinjauan awal <b>Level TL</b>.
                </p>
              </div>
            </div>

            <!-- Loop Riwayat Escalation -->
            <template v-if="issue.escalations && issue.escalations.length > 0">
              <div v-for="esc in issue.escalations" :key="esc.id" class="relative">
                <span class="absolute -left-[27px] top-1.5 w-3.5 h-3.5 rounded-full bg-blue-600 ring-4 ring-blue-100"></span>
                <div class="bg-blue-50/40 p-4 rounded-xl border border-blue-100 text-xs space-y-2">
                  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                    <div class="flex items-center gap-2 flex-wrap">
                      <span class="bg-blue-600 text-white font-bold px-2 py-0.5 rounded text-[10px]">
                        Eskalasi ke Level {{ esc.to_level }}
                      </span>
                      <span class="text-gray-600">
                        Jenjang Asal: <b>Level {{ esc.from_level }}</b>
                      </span>
                    </div>
                    <span class="text-gray-400 font-mono text-[11px]">
                      {{ formatDate(esc.created_at) }}
                    </span>
                  </div>

                  <p class="text-gray-800 text-xs leading-relaxed">
                    Dieskalasi oleh <b class="text-blue-900">{{ esc.from_user?.name || 'Pejabat Unit' }}</b> ke level 
                    <span class="font-bold text-blue-700">[{{ esc.to_level }}]</span> pada tanggal 
                    <b class="font-mono text-gray-700">{{ formatDate(esc.created_at) }}</b>.
                  </p>

                  <div class="bg-white p-3 rounded-lg border border-blue-200 text-gray-700 shadow-2xs">
                    <span class="font-bold text-gray-800 block text-[11px] mb-0.5">
                      <i class="fas fa-comment-dots text-blue-500 mr-1"></i> Alasan Eskalasi:
                    </span>
                    <p class="text-xs italic text-gray-700 leading-relaxed">{{ esc.reason }}</p>
                  </div>
                </div>
              </div>
            </template>
            <div v-else class="relative">
              <span class="absolute -left-[27px] top-1.5 w-3.5 h-3.5 rounded-full bg-slate-400 ring-4 ring-slate-100"></span>
              <div class="bg-slate-50 p-4 rounded-xl border border-dashed border-gray-300 text-xs text-gray-500">
                <i class="fas fa-info-circle text-blue-500 mr-1.5"></i> Belum ada riwayat eskalasi. Isu ini masih berada pada level awal penanganan (Level TL).
              </div>
            </div>

            <!-- Loop Putusan / Keputusan Resmi -->
            <template v-if="issue.decisions && issue.decisions.length > 0">
              <div v-for="dec in issue.decisions" :key="dec.id" class="relative">
                <span class="absolute -left-[27px] top-1.5 w-3.5 h-3.5 rounded-full bg-emerald-600 ring-4 ring-emerald-100"></span>
                <div class="bg-emerald-50/50 p-4 rounded-xl border border-emerald-200 text-xs space-y-1.5">
                  <div class="flex items-center justify-between">
                    <span class="bg-emerald-600 text-white font-bold px-2 py-0.5 rounded text-[10px]">
                      Keputusan Diterbitkan
                    </span>
                    <span class="text-gray-400 font-mono text-[11px]">{{ formatDate(dec.created_at) }}</span>
                  </div>
                  <p class="text-gray-800">
                    Ditetapkan oleh <b class="text-emerald-900">{{ dec.decision_maker?.name || 'Pejabat Unit' }}</b> pada kewenangan <span class="font-bold text-emerald-800">Level {{ dec.decision_level }}</span>.
                  </p>
                  <div class="p-3 bg-white border border-emerald-200 rounded-lg text-gray-800 font-medium">
                    "{{ dec.decision }}"
                  </div>
                </div>
              </div>
            </template>

          </div>
        </div>
      </div>

      <!-- ==================== BAGIAN BAWAH - KONTROL AKSI ==================== -->
      <div class="card bg-white rounded-xl border border-gray-200 shadow-xs p-6">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
          <div>
            <h4 class="text-xs font-bold text-gray-800 uppercase tracking-wider flex items-center gap-1.5">
              <i class="fas fa-user-shield text-blue-600"></i> Kontrol Aksi & Wewenang Keputusan
            </h4>
            <p class="text-xs text-gray-500 mt-0.5">
              Role Login: <b class="text-gray-800">{{ userRole || 'Staff' }}</b> | Wewenang Penanganan: <b class="text-blue-600">Level {{ currentLevel }}</b>
            </p>
          </div>

          <!-- Tombol Aksi -->
          <div class="flex items-center gap-3 flex-wrap">
            <template v-if="canAct">
              <!-- Tombol 1: Masuk Ruang Rapat untuk Mengambil Keputusan -->
              <Link 
                href="/rapat"
                class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold shadow-sm transition flex items-center gap-2 cursor-pointer"
              >
                <i class="fas fa-gavel"></i> Bahas & Ambil Keputusan di Ruang Rapat
              </Link>

              <!-- Tombol 2: Eskalasi ke Atasan -->
              <button 
                v-if="['TL', 'ASMAN'].includes(currentLevel)"
                type="button" 
                @click="showEscalateModal = true"
                class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold shadow-sm transition flex items-center gap-2 cursor-pointer"
              >
                <i class="fas fa-arrow-up"></i> Eskalasi ke {{ targetLevelName }}
              </button>
            </template>
            <template v-else-if="isResolved">
              <span class="bg-emerald-100 text-emerald-800 border border-emerald-300 px-3 py-2 rounded-lg text-xs font-bold flex items-center gap-1.5">
                <i class="fas fa-check-circle text-emerald-600"></i> Isu ini telah diselesaikan (Status: {{ issue.status }})
              </span>
            </template>
            <template v-else>
              <span class="text-xs text-gray-500 bg-slate-50 border border-gray-200 px-3.5 py-2 rounded-lg flex items-center gap-2">
                <i class="fas fa-lock text-amber-500"></i> Isu ini sedang berada dalam kewenangan tindakan <b>Level {{ currentLevel }}</b>
              </span>
            </template>
          </div>
        </div>
      </div>

      <!-- ==================== TABS: DISKUSI, KEPUTUSAN & TASK TINDAK LANJUT ==================== -->
      <div class="bg-white rounded-xl border border-gray-200 shadow-xs p-6">
        <!-- Tabs Header -->
        <div class="flex gap-6 border-b border-gray-200 text-xs font-semibold pb-3">
          <button 
            type="button" 
            @click="activeTab = 'diskusi'" 
            :class="activeTab === 'diskusi' ? 'text-blue-600 border-b-2 border-blue-600 font-bold pb-2 flex items-center gap-1.5 cursor-pointer' : 'text-gray-400 hover:text-gray-700 pb-2 flex items-center gap-1.5 cursor-pointer'"
          >
            <i class="far fa-comments"></i> Forum Diskusi ({{ issue.discussions?.length || 0 }})
          </button>
          <button 
            type="button" 
            @click="activeTab = 'keputusan'" 
            :class="activeTab === 'keputusan' ? 'text-blue-600 border-b-2 border-blue-600 font-bold pb-2 flex items-center gap-1.5 cursor-pointer' : 'text-gray-400 hover:text-gray-700 pb-2 flex items-center gap-1.5 cursor-pointer'"
          >
            <i class="fas fa-gavel"></i> Arsip Putusan ({{ issue.decisions?.length || 0 }})
          </button>
          <button 
            type="button" 
            @click="activeTab = 'tindaklanjut'" 
            :class="activeTab === 'tindaklanjut' ? 'text-blue-600 border-b-2 border-blue-600 font-bold pb-2 flex items-center gap-1.5 cursor-pointer' : 'text-gray-400 hover:text-gray-700 pb-2 flex items-center gap-1.5 cursor-pointer'"
          >
            <i class="fas fa-tasks"></i> Task Tindak Lanjut ({{ issue.tasks?.length || 0 }})
          </button>
        </div>

        <!-- Tab 1: Diskusi -->
        <div v-if="activeTab === 'diskusi'" class="pt-5 space-y-4">
          <div class="space-y-3 max-h-[350px] overflow-y-auto pr-2">
            <template v-if="issue.discussions && issue.discussions.length > 0">
              <div 
                v-for="disc in issue.discussions" 
                :key="disc.id" 
                class="flex gap-3 items-start bg-slate-50/70 p-3.5 rounded-xl border border-gray-100 text-xs"
              >
                <div class="w-8 h-8 rounded-full bg-blue-600 text-white font-bold text-xs flex items-center justify-center shrink-0">
                  {{ (disc.user?.name || 'User').substring(0, 2).toUpperCase() }}
                </div>
                <div class="flex-1">
                  <div class="flex items-center justify-between mb-1">
                    <span class="font-bold text-gray-800">{{ disc.user?.name || 'Pegawai' }} ({{ disc.user?.role?.name || 'Staff' }})</span>
                    <span class="text-[10px] text-gray-400 font-mono">{{ formatDate(disc.created_at) }}</span>
                  </div>
                  <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ disc.message }}</p>
                </div>
              </div>
            </template>
            <div v-else class="text-center py-6 text-gray-400 text-xs">
              Belum ada diskusi untuk isu ini. Berikan masukan atau konfirmasi lapangan di bawah.
            </div>
          </div>

          <!-- Form Tambah Diskusi -->
          <form @submit.prevent="submitDiscussion" class="pt-3 border-t border-gray-100">
            <div class="space-y-2">
              <label class="block text-xs font-bold text-gray-700">Tulis Catatan / Tanggapan Tambahan:</label>
              <textarea 
                v-model="discussionForm.message" 
                rows="2" 
                required 
                class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500" 
                placeholder="Ketik catatan teknis atau tanggapan terkait isu ini..."
              ></textarea>
              <div class="flex justify-end">
                <button 
                  type="submit" 
                  :disabled="discussionForm.processing"
                  class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-xs font-semibold transition flex items-center gap-1.5 shadow-xs cursor-pointer disabled:opacity-50 disabled:pointer-events-none disabled:cursor-not-allowed"
                >
                  <i v-if="discussionForm.processing" class="fas fa-spinner fa-spin text-[10px]"></i>
                  <i v-else class="fas fa-paper-plane text-[10px]"></i> 
                  <span>{{ discussionForm.processing ? 'Mengirim...' : 'Kirim Tanggapan' }}</span>
                </button>
              </div>
            </div>
          </form>
        </div>

        <!-- Tab 2: Keputusan Arsip -->
        <div v-if="activeTab === 'keputusan'" class="pt-5 space-y-3">
          <template v-if="issue.decisions && issue.decisions.length > 0">
            <div 
              v-for="dec in issue.decisions" 
              :key="dec.id" 
              class="border border-emerald-100 bg-emerald-50/20 p-4 rounded-xl text-xs space-y-2"
            >
              <div class="flex items-center justify-between">
                <span class="bg-emerald-600 text-white px-2 py-0.5 rounded text-[10px] font-bold">Level {{ dec.decision_level }}</span>
                <span class="text-gray-400 font-mono text-[11px]">{{ formatDate(dec.created_at) }}</span>
              </div>
              <p class="text-gray-800 font-medium">"{{ dec.decision }}"</p>
              <div v-if="dec.rationale" class="text-gray-600 bg-white p-2 rounded border border-emerald-100">
                <span class="font-bold text-gray-700">Pertimbangan:</span> {{ dec.rationale }}
              </div>
              <div v-if="dec.follow_up" class="text-gray-600 bg-white p-2 rounded border border-emerald-100">
                <span class="font-bold text-gray-700">Tindak Lanjut:</span> {{ dec.follow_up }}
              </div>
              <p class="text-gray-500 text-[11px]">Ditetapkan oleh: <b>{{ dec.decision_maker?.name || 'Pejabat Unit' }}</b></p>
            </div>
          </template>
          <div v-else class="text-center py-6 text-gray-400 text-xs">
            Belum ada keputusan formal yang dicatat.
          </div>
        </div>

        <!-- Tab 3: Task Tindak Lanjut -->
        <div v-if="activeTab === 'tindaklanjut'" class="pt-5 space-y-3">
          <template v-if="issue.tasks && issue.tasks.length > 0">
            <div 
              v-for="tsk in issue.tasks" 
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
          <div v-else class="text-center py-6 text-gray-400 text-xs">
            Belum ada task tindak lanjut yang terikat dengan isu ini.
          </div>
        </div>
      </div>

    </div>

    <!-- ==================== MODAL ESKALASI KE ATASAN ==================== -->
    <div v-if="showEscalateModal" class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-xl max-w-lg w-full p-6 shadow-2xl border border-gray-100">
        <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-3">
          <h3 class="text-sm font-bold text-gray-800">Eskalasi Isu ke {{ targetLevelName }}</h3>
          <button @click="showEscalateModal = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold cursor-pointer">&times;</button>
        </div>
        <p class="text-xs text-gray-500 mb-4">Isu akan diteruskan ke kewenangan tingkat <b>{{ targetLevelName }}</b>.</p>
        
        <form @submit.prevent="submitEscalation" class="space-y-4">
          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">
              Alasan Eskalasi & Rekomendasi Putusan <span class="text-red-500">*</span>
            </label>
            <textarea 
              v-model="escalateForm.reason" 
              rows="4" 
              required 
              class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500" 
              placeholder="Jelaskan alasan batas kewenangan teknis, kebutuhan anggaran, atau koordinasi lintas bidang..."
            ></textarea>
            <p v-if="escalateForm.errors.reason" class="text-[10px] text-red-500 mt-1 font-semibold">
              {{ escalateForm.errors.reason }}
            </p>
          </div>
          
          <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
            <button 
              type="button" 
              @click="showEscalateModal = false" 
              class="px-4 py-2 border border-gray-200 text-gray-600 rounded-md text-xs hover:bg-gray-50 cursor-pointer"
            >
              Batal
            </button>
            <button 
              type="submit" 
              :disabled="escalateForm.processing"
              class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-xs font-semibold shadow-xs cursor-pointer disabled:opacity-50 disabled:pointer-events-none disabled:cursor-not-allowed flex items-center gap-1.5"
            >
              <i v-if="escalateForm.processing" class="fas fa-spinner fa-spin text-xs"></i>
              <span>{{ escalateForm.processing ? 'Mengirim...' : 'Kirim Eskalasi' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- ==================== MODAL PENGAMBILAN KEPUTUSAN ==================== -->
    <div v-if="showDecisionModal" class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-xl max-w-lg w-full p-6 shadow-2xl border border-gray-100">
        <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-3">
          <h3 class="text-sm font-bold text-gray-800">Ambil Keputusan (Level {{ currentLevel }})</h3>
          <button @click="showDecisionModal = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold cursor-pointer">&times;</button>
        </div>
        <p class="text-xs text-gray-500 mb-4">Berikan instruksi putusan resmi untuk penyelesaian isu ini.</p>
        
        <form @submit.prevent="submitDecision" class="space-y-4">
          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">
              Pernyataan Keputusan <span class="text-red-500">*</span>
            </label>
            <textarea 
              v-model="decisionForm.decision" 
              rows="3" 
              required 
              class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500" 
              placeholder="Tuliskan keputusan resmi..."
            ></textarea>
            <p v-if="decisionForm.errors.decision" class="text-[10px] text-red-500 mt-1 font-semibold">
              {{ decisionForm.errors.decision }}
            </p>
          </div>
          
          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Dasar Pertimbangan / Rationale (Opsional)</label>
            <textarea 
              v-model="decisionForm.rationale" 
              rows="2" 
              class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500" 
              placeholder="Alasan teknis, keandalan, atau anggaran..."
            ></textarea>
          </div>
          
          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Instruksi Tindak Lanjut (Opsional)</label>
            <textarea 
              v-model="decisionForm.follow_up" 
              rows="2" 
              class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500" 
              placeholder="Tindakan korektif atau perbaikan yang harus dilakukan..."
            ></textarea>
          </div>

          <!-- Otomatisasi Pembuatan Task Tindak Lanjut -->
          <div class="bg-blue-50/60 p-3 rounded-lg border border-blue-100 space-y-3">
            <div class="flex items-center gap-2">
              <input 
                type="checkbox" 
                v-model="decisionForm.create_task" 
                id="create_task" 
                class="rounded text-blue-600 focus:ring-blue-500 cursor-pointer"
              />
              <label for="create_task" class="text-xs text-blue-900 font-bold cursor-pointer">
                <i class="fas fa-magic text-blue-600 mr-1"></i> Buat Task Tindak Lanjut Otomatis
              </label>
            </div>

            <div v-if="decisionForm.create_task" class="space-y-2 pt-1 border-t border-blue-200/60">
              <div class="grid grid-cols-2 gap-2">
                <div>
                  <UserSelect 
                    v-model="decisionForm.task_assignee_id" 
                    :users="users" 
                    label="Tugaskan ke (PIC Pelaksana)" 
                    placeholder="Ketik nama atau NID..." 
                  />
                </div>
                <div>
                  <label class="block text-[11px] font-bold text-blue-900 mb-0.5">Target Selesai (Due Date)</label>
                  <input 
                    type="date" 
                    v-model="decisionForm.task_due_date" 
                    class="w-full text-xs border border-blue-200 rounded p-1.5 bg-white"
                  />
                </div>
              </div>
            </div>
          </div>
          
          <div class="flex items-center gap-2 pt-1">
            <input 
              type="checkbox" 
              v-model="decisionForm.mark_resolved" 
              id="mark_resolved" 
              class="rounded text-emerald-600 cursor-pointer"
            />
            <label for="mark_resolved" class="text-xs text-gray-700 font-semibold cursor-pointer">
              Tandai Masalah Selesai (Status: Resolved)
            </label>
          </div>
          
          <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
            <button 
              type="button" 
              @click="showDecisionModal = false" 
              class="px-4 py-2 border border-gray-200 text-gray-600 rounded-md text-xs hover:bg-gray-50 cursor-pointer"
            >
              Batal
            </button>
            <button 
              type="submit" 
              :disabled="decisionForm.processing"
              class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-md text-xs font-semibold shadow-xs cursor-pointer disabled:opacity-50 disabled:pointer-events-none disabled:cursor-not-allowed flex items-center gap-1.5"
            >
              <i v-if="decisionForm.processing" class="fas fa-spinner fa-spin text-xs"></i>
              <span>{{ decisionForm.processing ? 'Menyimpan...' : 'Simpan Keputusan' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- ==================== MODAL EDIT ISU ==================== -->
    <div v-if="showEditModal" class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-4">
          <h3 class="text-sm font-bold text-gray-800">Edit Data Isu / Kendala</h3>
          <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold cursor-pointer">&times;</button>
        </div>

        <form @submit.prevent="submitEdit" class="space-y-4">
          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Judul Isu <span class="text-rose-500">*</span></label>
            <input 
              type="text" 
              v-model="editForm.title" 
              class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500" 
              required 
            />
          </div>



          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Deskripsi & Uraian Masalah <span class="text-rose-500">*</span></label>
            <textarea 
              v-model="editForm.description" 
              rows="4" 
              class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500 font-mono text-[11px]" 
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
              <i v-if="editForm.processing" class="fas fa-spinner fa-spin text-xs"></i>
              <span>{{ editForm.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Preview Gambar Full Size -->
    <div v-if="previewImage" class="fixed inset-0 bg-slate-900/80 z-50 flex items-center justify-center p-4" @click.self="previewImage = null">
      <div class="relative bg-white rounded-xl max-w-4xl w-full p-4 shadow-2xl overflow-hidden">
        <div class="flex justify-between items-center pb-2 mb-2 border-b border-gray-100">
          <span class="text-xs font-bold text-gray-700 font-mono">{{ issue.attachment_name }}</span>
          <button @click="previewImage = null" class="text-gray-400 hover:text-gray-700 text-xl font-bold cursor-pointer">&times;</button>
        </div>
        <div class="max-h-[75vh] overflow-auto flex items-center justify-center bg-slate-100 rounded-lg p-2">
          <img :src="previewImage" alt="Preview Gambar" class="max-w-full max-h-[70vh] object-contain rounded shadow" />
        </div>
        <div class="flex justify-end gap-2 pt-3 border-t border-gray-100 mt-2">
          <a :href="previewImage" target="_blank" download class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold flex items-center gap-1.5 transition">
            <i class="fas fa-download"></i> Unduh Berkas
          </a>
          <button @click="previewImage = null" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-bold cursor-pointer transition">
            Tutup
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
import UserSelect from '@/Components/UserSelect.vue';
import { useConfirm } from '@/Composables/useConfirm';

const { confirm } = useConfirm();

const props = defineProps({
  issue: Object,
  users: Array,
});

const page = usePage();
const authUser = computed(() => page.props.auth?.user || {});
const userRole = computed(() => String(authUser.value.role?.name || '').toUpperCase());
const currentLevel = computed(() => String(props.issue?.current_level || '').toUpperCase());
const isResolved = computed(() => ['RESOLVED', 'CLOSED'].includes(String(props.issue?.status || '').toUpperCase()));

const canAct = computed(() => {
  return (userRole.value === currentLevel.value || userRole.value === 'MUP' || userRole.value === 'ADMIN') && !isResolved.value;
});

const targetLevelName = computed(() => {
  return currentLevel.value === 'TL' ? 'ASMAN' : 'MUP';
});

const activeTab = ref('diskusi');
const showEscalateModal = ref(false);
const showDecisionModal = ref(false);
const showEditModal = ref(false);
const previewImage = ref(null);

// Edit Form States & File Validation
const editPhotoError = ref('');
const editDocError = ref('');

const editForm = useForm({
  title: props.issue?.title || '',
  description: props.issue?.clean_description || props.issue?.description || '',
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

function openEditModal() {
  editPhotoError.value = '';
  editDocError.value = '';
  editForm.title = props.issue?.title || '';
  editForm.description = props.issue?.clean_description || props.issue?.description || '';
  editForm.current_photo = props.issue?.attachment_name || props.issue?.attachment || null;
  editForm.current_doc = props.issue?.document_name || props.issue?.document || null;
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
    editForm.transform((data) => ({ ...data, _method: 'put' })).post(`/issues/${props.issue.id}`, {
      preserveScroll: true,
      onSuccess: () => {
        showEditModal.value = false;
      },
    });
  } else {
    editForm.put(`/issues/${props.issue.id}`, {
      preserveScroll: true,
      onSuccess: () => {
        showEditModal.value = false;
      },
    });
  }
}

const isDeletingIssue = ref(false);
async function confirmDelete() {
  if (isDeletingIssue.value) return;
  const confirmed = await confirm({
    title: 'Hapus Isu',
    message: `Apakah Anda yakin ingin menghapus issue #${props.issue.issue_no} "${props.issue.title}"? Seluruh riwayat eskalasi, diskusi, dan keputusan terkait akan ikut dihapus permanen.`,
    confirmText: 'Ya, Hapus Isu',
    cancelText: 'Batal',
    type: 'danger',
    icon: 'fas fa-trash-can',
  });
  if (confirmed) {
    isDeletingIssue.value = true;
    router.delete(`/issues/${props.issue.id}`, {
      onFinish: () => {
        isDeletingIssue.value = false;
      },
    });
  }
}

// Forms
const discussionForm = useForm({
  message: '',
});

function submitDiscussion() {
  if (discussionForm.processing) return;
  discussionForm.post(`/issues/${props.issue.id}/discussions`, {
    preserveScroll: true,
    onSuccess: () => {
      discussionForm.reset();
    },
  });
}

const escalateForm = useForm({
  reason: '',
});

function submitEscalation() {
  if (escalateForm.processing) return;
  escalateForm.put(`/issues/${props.issue.id}/escalate`, {
    preserveScroll: true,
    onSuccess: () => {
      showEscalateModal.value = false;
      escalateForm.reset();
    },
  });
}

const defaultDueDate = new Date();
defaultDueDate.setDate(defaultDueDate.getDate() + 3);
const defaultDueDateStr = defaultDueDate.toISOString().split('T')[0];

const decisionForm = useForm({
  decision: '',
  rationale: '',
  follow_up: '',
  create_task: false,
  task_assignee_id: '',
  task_due_date: defaultDueDateStr,
  mark_resolved: false,
});

function submitDecision() {
  if (decisionForm.processing) return;
  decisionForm.put(`/decisions/${props.issue.id}`, {
    preserveScroll: true,
    onSuccess: () => {
      showDecisionModal.value = false;
      decisionForm.reset();
    },
  });
}

function formatDate(dateStr) {
  if (!dateStr) return '-';
  const d = new Date(dateStr);
  return d.toLocaleDateString('id-ID', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  }) + ' WITA';
}

function formatDateOnly(dateStr) {
  if (!dateStr) return '-';
  const d = new Date(dateStr);
  return d.toLocaleDateString('id-ID', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
  });
}

function priorityBadgeClass(p) {
  const priority = String(p || '').toUpperCase();
  if (priority === 'CRITICAL' || priority === 'HIGH') {
    return 'badge bg-red-100 text-red-700 border border-red-200 px-2.5 py-1 rounded text-xs font-bold';
  }
  if (priority === 'MEDIUM') {
    return 'badge bg-amber-100 text-amber-800 border border-amber-200 px-2.5 py-1 rounded text-xs font-bold';
  }
  return 'badge bg-emerald-100 text-emerald-800 border border-emerald-200 px-2.5 py-1 rounded text-xs font-bold';
}

function statusBadgeClass(s) {
  const status = String(s || '').toLowerCase();
  if (status.includes('closed') || status.includes('selesai') || status.includes('resolved') || status.includes('decided')) {
    return 'badge bg-emerald-100 text-emerald-800 border border-emerald-200 px-2.5 py-1 rounded text-xs font-bold';
  }
  if (status.includes('progress') || status.includes('proses')) {
    return 'badge bg-blue-100 text-blue-800 border border-blue-200 px-2.5 py-1 rounded text-xs font-bold';
  }
  if (status.includes('escalated') || status.includes('eskalasi')) {
    return 'badge bg-purple-100 text-purple-800 border border-purple-200 px-2.5 py-1 rounded text-xs font-bold';
  }
  return 'badge bg-amber-100 text-amber-800 border border-amber-200 px-2.5 py-1 rounded text-xs font-bold';
}

function slaBadgeClass(issue) {
  return issue.sla_info?.badge_class || 'bg-blue-50 text-blue-700 border border-blue-200 px-2 py-0.5 rounded text-[10px] inline-flex items-center gap-1';
}

function slaLabel(issue) {
  return issue.sla_info?.label || 'Normal (On-Track)';
}
</script>

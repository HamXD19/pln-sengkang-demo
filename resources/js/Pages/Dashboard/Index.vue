<template>
  <AppLayout title="Dashboard">

    <!-- ========================================================================= -->
    <!-- JIKA ROLE MUP / ADMIN: EXECUTIVE COMMAND CENTER UP SENGKANG -->
    <!-- ========================================================================= -->
    <div v-if="roleName === 'MUP' || roleName === 'ADMIN'" class="space-y-6">

      <!-- ==================== EXECUTIVE HEADER & SIKLUS SELECTOR ==================== -->
      <div class="bg-gradient-to-r from-[#0a192f] via-[#0f2744] to-[#16365c] text-white p-5 sm:p-6 rounded-2xl shadow-lg border border-slate-700/80 relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
          <div>
            <div class="flex flex-wrap items-center gap-2 mb-2">
              <span class="bg-amber-500/20 text-amber-300 border border-amber-400/40 text-[10px] font-black px-2.5 py-0.5 rounded-full uppercase tracking-wider flex items-center gap-1.5 shadow-xs">
                <i class="fas fa-crown text-[9px] text-amber-400"></i> Executive Command Center
              </span>
              <span class="text-[11px] text-emerald-400 font-semibold flex items-center gap-1.5 bg-emerald-950/40 px-2.5 py-0.5 rounded-full border border-emerald-500/30">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> UP Sengkang Live Monitoring
              </span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black uppercase tracking-wider text-white flex items-center gap-2.5">
              <span>DASHBOARD EKSEKUTIF MANAJER UNIT</span>
            </h1>
            <p class="text-xs sm:text-sm text-sky-200/90 mt-1 font-medium max-w-2xl">
              Pusat kendali operasional, evaluasi kesehatan 6 bidang kerja, dan pengambilan keputusan strategis UP Sengkang
            </p>
          </div>

          <!-- Siklus Koordinasi & Filter Waktu -->
          <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 bg-slate-900/80 p-3 rounded-xl border border-slate-700/80 shrink-0 shadow-inner">
            <div class="flex items-center gap-2 text-xs text-slate-300 font-medium mr-1">
              <i class="far fa-calendar-alt text-sky-400 text-sm"></i>
              <span>Siklus Kerja:</span>
              <span class="bg-blue-600/30 text-sky-300 font-mono font-bold px-2 py-0.5 rounded text-xs border border-sky-400/30">
                W{{ manajemenTugas?.minggu_berjalan }}
              </span>
            </div>
            <div class="flex items-center gap-1">
              <button 
                type="button" 
                @click="setSiklus('semua')" 
                :class="siklusClass('semua')"
              >
                Semua
              </button>
              <button 
                type="button" 
                @click="setSiklus('N')" 
                :class="siklusClass('N')"
              >
                Minggu N
              </button>
              <button 
                type="button" 
                @click="setSiklus('N-1')" 
                :class="siklusClass('N-1')"
              >
                Minggu N-1
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- ==================== EXECUTIVE ALERT RIBBON ==================== -->
      <div 
        v-if="executiveStats?.pending_mup_decisions > 0 || executiveStats?.sla_breached_issues > 0"
        class="bg-gradient-to-r from-amber-500/15 via-rose-500/10 to-amber-500/15 border-l-4 border-amber-500 rounded-xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xs bg-white"
      >
        <div class="flex items-start sm:items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 text-lg shadow-xs">
            <i class="fas fa-exclamation-triangle"></i>
          </div>
          <div>
            <h4 class="font-bold text-gray-900 text-sm flex items-center gap-2">
              <span>Perhatian Manajerial Eksekutif</span>
              <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-700 border border-rose-300">
                Tindakan Diperlukan
              </span>
            </h4>
            <p class="text-xs text-gray-600 mt-0.5">
              Terdapat 
              <strong class="text-amber-700 font-black">{{ executiveStats?.pending_mup_decisions || 0 }} isu eskalasi</strong> yang menunggu putusan MUP
              <span v-if="executiveStats?.sla_breached_issues > 0">
                dan <strong class="text-rose-600 font-black">{{ executiveStats?.sla_breached_issues }} kendala</strong> yang telah melampaui batas waktu SLA.
              </span>
            </p>
          </div>
        </div>
        <button 
          type="button"
          @click="issueTab = 'mup_pending'"
          class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold shadow-xs transition shrink-0 cursor-pointer"
        >
          <i class="fas fa-gavel"></i>
          <span>Tinjau Isu Sekarang</span>
        </button>
      </div>

      <div 
        v-else-if="executiveStats?.total_overdue_tasks > 0"
        class="bg-white border-l-4 border-rose-500 rounded-xl p-4 flex items-center justify-between gap-3 shadow-xs"
      >
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center shrink-0 text-lg">
            <i class="fas fa-clock"></i>
          </div>
          <div>
            <h4 class="font-bold text-gray-900 text-sm">Evaluasi Task Terlambat</h4>
            <p class="text-xs text-gray-600">
              Terdapat <strong class="text-rose-600 font-bold">{{ executiveStats.total_overdue_tasks }} task</strong> lewat tenggat waktu (overdue) yang memerlukan koordinasi evaluasi lintas seksi.
            </p>
          </div>
        </div>
        <button 
          type="button"
          @click="activeExecTab = 'overdue'"
          class="text-xs font-bold text-blue-600 hover:underline shrink-0 cursor-pointer"
        >
          Lihat Tunggakan &rarr;
        </button>
      </div>

      <div 
        v-else
        class="bg-white border-l-4 border-emerald-500 rounded-xl p-3.5 flex items-center justify-between gap-3 shadow-xs"
      >
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 text-sm">
            <i class="fas fa-check-circle"></i>
          </div>
          <p class="text-xs text-gray-700 font-medium">
            <strong class="text-emerald-700 font-bold">Kondisi Operasional Normal:</strong> Seluruh eskalasi tertangani dan tidak ada task kritis yang terlambat di seluruh UP Sengkang.
          </p>
        </div>
      </div>

      <!-- ==================== 4 KARTU METRIK EKSEKUTIF UTAMA ==================== -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- KPI 1: Isu Menunggu Keputusan MUP -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between transition hover:shadow-md">
          <div class="flex items-center justify-between mb-3">
            <span class="text-gray-500 text-xs font-semibold uppercase tracking-wider">Menunggu Putusan</span>
            <span 
              :class="(executiveStats?.pending_mup_decisions || 0) > 0 
                ? 'bg-amber-100 text-amber-800 border-amber-300' 
                : 'bg-emerald-100 text-emerald-800 border-emerald-300'"
              class="px-2 py-0.5 rounded-full text-[10px] font-bold border"
            >
              {{ (executiveStats?.pending_mup_decisions || 0) > 0 ? 'Butuh Arahan' : 'Tuntas' }}
            </span>
          </div>
          <div class="flex items-baseline gap-2 mb-1">
            <span class="text-3xl font-black" :class="(executiveStats?.pending_mup_decisions || 0) > 0 ? 'text-amber-600' : 'text-emerald-600'">
              {{ executiveStats?.pending_mup_decisions || 0 }}
            </span>
            <span class="text-xs text-gray-400 font-medium">Isu Strategis</span>
          </div>
          <p class="text-[11px] text-gray-500 font-medium mt-2 pt-2 border-t border-gray-100 flex items-center justify-between">
            <span>Eskalasi wewenang MUP</span>
            <span class="font-bold text-blue-600">Level Unit</span>
          </p>
        </div>

        <!-- KPI 2: Kepatuhan SLA Unit -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between transition hover:shadow-md">
          <div class="flex items-center justify-between mb-3">
            <span class="text-gray-500 text-xs font-semibold uppercase tracking-wider">Kepatuhan SLA</span>
            <span 
              :class="(executiveStats?.sla_compliance_rate || 100) >= 85 
                ? 'bg-emerald-100 text-emerald-800 border-emerald-300' 
                : 'bg-rose-100 text-rose-800 border-rose-300'"
              class="px-2 py-0.5 rounded-full text-[10px] font-bold border"
            >
              {{ (executiveStats?.sla_compliance_rate || 100) >= 85 ? 'Tepat Waktu' : 'Perlu Evaluasi' }}
            </span>
          </div>
          <div class="flex items-baseline gap-2 mb-1">
            <span class="text-3xl font-black text-gray-900">
              {{ executiveStats?.sla_compliance_rate || 100 }}%
            </span>
            <span class="text-xs text-gray-400 font-medium">On-Target</span>
          </div>
          <div class="w-full bg-gray-100 rounded-full h-1.5 mt-2 overflow-hidden">
            <div 
              class="h-1.5 rounded-full transition-all duration-500"
              :class="(executiveStats?.sla_compliance_rate || 100) >= 85 ? 'bg-emerald-500' : 'bg-rose-500'"
              :style="{ width: (executiveStats?.sla_compliance_rate || 100) + '%' }"
            ></div>
          </div>
          <p class="text-[11px] text-gray-400 font-medium mt-2 pt-2 border-t border-gray-100">
            {{ executiveStats?.sla_breached_issues || 0 }} kendala melewati target jam
          </p>
        </div>

        <!-- KPI 3: Total Beban Task Unit -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between transition hover:shadow-md">
          <div class="flex items-center justify-between mb-3">
            <span class="text-gray-500 text-xs font-semibold uppercase tracking-wider">Task Unit</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
              {{ executiveStats?.completion_rate || 0 }}% Tuntas
            </span>
          </div>
          <div class="flex items-baseline gap-2 mb-1">
            <span class="text-3xl font-black text-blue-600">
              {{ executiveStats?.total_unit_tasks || 0 }}
            </span>
            <span class="text-xs text-gray-400 font-medium">Total Terdata</span>
          </div>
          <div class="w-full bg-gray-100 rounded-full h-1.5 mt-2 overflow-hidden">
            <div 
              class="bg-blue-600 h-1.5 rounded-full transition-all duration-500" 
              :style="{ width: (executiveStats?.completion_rate || 0) + '%' }"
            ></div>
          </div>
          <p class="text-[11px] text-gray-500 font-medium mt-2 pt-2 border-t border-gray-100 flex justify-between">
            <span>Selesai: <strong>{{ executiveStats?.total_completed_tasks || 0 }}</strong></span>
            <span>Aktif: <strong>{{ (executiveStats?.total_unit_tasks || 0) - (executiveStats?.total_completed_tasks || 0) }}</strong></span>
          </p>
        </div>

        <!-- KPI 4: Tunggakan Kritis (Overdue) -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between transition hover:shadow-md">
          <div class="flex items-center justify-between mb-3">
            <span class="text-gray-500 text-xs font-semibold uppercase tracking-wider">Tunggakan (Overdue)</span>
            <span 
              :class="(executiveStats?.total_overdue_tasks || 0) > 0 
                ? 'bg-rose-100 text-rose-800 border-rose-300' 
                : 'bg-emerald-100 text-emerald-800 border-emerald-300'"
              class="px-2 py-0.5 rounded-full text-[10px] font-bold border"
            >
              {{ (executiveStats?.total_overdue_tasks || 0) > 0 ? 'Perlu Tindakan' : 'Nol Tunggakan' }}
            </span>
          </div>
          <div class="flex items-baseline gap-2 mb-1">
            <span class="text-3xl font-black" :class="(executiveStats?.total_overdue_tasks || 0) > 0 ? 'text-rose-600' : 'text-emerald-600'">
              {{ executiveStats?.total_overdue_tasks || 0 }}
            </span>
            <span class="text-xs text-gray-400 font-medium">Task Lewat Tenggat</span>
          </div>
          <p class="text-[11px] text-gray-500 font-medium mt-2 pt-2 border-t border-gray-100 flex items-center justify-between">
            <span>Seluruh seksi & bidang</span>
            <span class="text-rose-600 font-bold" v-if="(executiveStats?.total_overdue_tasks || 0) > 0">Segera Evaluasi</span>
            <span class="text-emerald-600 font-bold" v-else>Bersih</span>
          </p>
        </div>

      </div>

      <!-- ==================== MATRIKS KINERJA LINTAS BIDANG (6 DEPARTEMEN) ==================== -->
      <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
          <div>
            <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
              <i class="fas fa-layer-group text-blue-600"></i> Matriks Kinerja & Kesehatan Bidang UP Sengkang
            </h2>
            <p class="text-xs text-gray-500 mt-0.5">
              Klik pada kartu bidang di bawah untuk memfilter data isu dan penugasan secara instan
            </p>
          </div>

          <div class="flex items-center gap-2">
            <button 
              v-if="selectedDepartmentId"
              type="button" 
              @click="selectedDepartmentId = null"
              class="px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 transition flex items-center gap-1.5 cursor-pointer"
            >
              <i class="fas fa-times-circle text-slate-500"></i>
              <span>Reset Filter (Aktif: {{ currentSelectedDept?.name }})</span>
            </button>
            <span v-else class="text-xs text-slate-400 font-medium flex items-center gap-1">
              <i class="fas fa-info-circle text-slate-400"></i> Menampilkan semua 6 bidang
            </span>
          </div>
        </div>

        <!-- 6 Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3.5">
          <div 
            v-for="dept in departmentMetrics" 
            :key="dept.id"
            @click="toggleDepartmentFilter(dept.id)"
            :class="[
              'p-4 rounded-xl border transition-all duration-200 cursor-pointer relative flex flex-col justify-between select-none',
              selectedDepartmentId === dept.id 
                ? 'border-blue-600 bg-blue-50/50 shadow-md ring-2 ring-blue-500/30' 
                : 'border-slate-200/90 bg-slate-50/40 hover:bg-white hover:border-slate-300 hover:shadow-xs'
            ]"
          >
            <!-- Header Bidang & Status Badge -->
            <div>
              <div class="flex items-center justify-between gap-2 mb-2">
                <span :class="healthBadge(dept.health)">
                  <span class="w-1.5 h-1.5 rounded-full" :class="dept.health === 'Kritis' ? 'bg-rose-600' : dept.health === 'Perlu Perhatian' ? 'bg-amber-600' : 'bg-emerald-600'"></span>
                  {{ dept.health }}
                </span>
                <span v-if="selectedDepartmentId === dept.id" class="text-[10px] font-black text-blue-600 uppercase tracking-wider">
                  Dipilih
                </span>
              </div>
              <h4 class="font-bold text-xs text-gray-900 leading-snug line-clamp-2 mb-1.5">
                {{ dept.name }}
              </h4>
            </div>

            <!-- Penanggung Jawab (ASMAN / TL) -->
            <div class="my-3 py-2 border-y border-gray-100 flex items-center gap-2">
              <div class="w-6 h-6 rounded-full overflow-hidden bg-slate-200 shrink-0">
                <img :src="dept.leader?.avatar_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(dept.leader?.name || 'PLN')}&size=60`" class="w-full h-full object-cover" />
              </div>
              <div class="min-w-0 leading-tight">
                <p class="text-[11px] font-bold text-gray-800 truncate">{{ dept.leader?.name || '-' }}</p>
                <p class="text-[9px] text-gray-400 uppercase font-semibold truncate">{{ dept.leader?.role || '-' }}</p>
              </div>
            </div>

            <!-- Mini Metrics Row -->
            <div class="grid grid-cols-3 gap-1 text-center text-[10px]">
              <div class="bg-white/80 p-1 rounded border border-gray-100">
                <span class="text-gray-400 block text-[9px]">Tugas</span>
                <span class="font-bold text-gray-800">{{ dept.completed_tasks }}/{{ dept.total_tasks }}</span>
              </div>
              <div class="bg-white/80 p-1 rounded border border-gray-100" :class="{ 'bg-rose-50/80 border-rose-200': dept.overdue_tasks > 0 }">
                <span class="text-gray-400 block text-[9px]">Telat</span>
                <span class="font-bold" :class="dept.overdue_tasks > 0 ? 'text-rose-600 font-black' : 'text-gray-700'">{{ dept.overdue_tasks }}</span>
              </div>
              <div class="bg-white/80 p-1 rounded border border-gray-100" :class="{ 'bg-amber-50/80 border-amber-200': dept.active_issues > 0 }">
                <span class="text-gray-400 block text-[9px]">Isu</span>
                <span class="font-bold" :class="dept.active_issues > 0 ? 'text-amber-700 font-black' : 'text-gray-700'">{{ dept.active_issues }}</span>
              </div>
            </div>

          </div>
        </div>
      </div>

      <!-- ==================== KOTAK MASUK KEPUTUSAN EKSEKUTIF MUP ==================== -->
      <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        
        <!-- Header & Tab Filter -->
        <div class="p-5 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-50/50">
          <div>
            <h3 class="font-bold text-sm text-gray-900 flex items-center gap-2">
              <i class="fas fa-inbox text-purple-600"></i> Kotak Masuk Keputusan & Arahan MUP
            </h3>
            <p class="text-xs text-gray-500 mt-0.5">
              Kendala operasional dan masalah strategis yang membutuhkan keputusan resmi Manajer Unit
            </p>
          </div>

          <div class="flex flex-wrap items-center gap-2.5">
            <!-- Mode Toggle -->
            <div class="inline-flex rounded-lg bg-gray-200/80 p-0.5 text-xs font-bold">
              <button 
                type="button" 
                @click="issueTab = 'mup_pending'"
                :class="issueTab === 'mup_pending' ? 'bg-white text-purple-700 shadow-xs' : 'text-gray-600 hover:text-gray-900'"
                class="px-3 py-1.5 rounded-md transition cursor-pointer"
              >
                Menunggu Putusan MUP ({{ criticalIssues?.length || 0 }})
              </button>
              <button 
                type="button" 
                @click="issueTab = 'all_unit'"
                :class="issueTab === 'all_unit' ? 'bg-white text-purple-700 shadow-xs' : 'text-gray-600 hover:text-gray-900'"
                class="px-3 py-1.5 rounded-md transition cursor-pointer"
              >
                Semua Isu Unit ({{ allActiveIssuesUnit?.length || 0 }})
              </button>
            </div>

            <!-- Search Field -->
            <div class="relative">
              <i class="fas fa-search absolute left-3 top-2.5 text-gray-400 text-xs"></i>
              <input 
                v-model="searchQuery" 
                type="text" 
                placeholder="Cari no. isu / judul..." 
                class="pl-8 pr-3 py-1.5 rounded-lg border border-gray-200 text-xs text-gray-800 bg-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 w-44 sm:w-52"
              />
            </div>
          </div>
        </div>

        <!-- Filter info strip if filtered by department -->
        <div v-if="selectedDepartmentId" class="px-5 py-2 bg-blue-50/70 border-b border-blue-100 flex items-center justify-between text-xs text-blue-800">
          <span>Memfilter isu untuk bagian: <strong>{{ currentSelectedDept?.name }}</strong></span>
          <button @click="selectedDepartmentId = null" class="text-[11px] font-bold text-blue-700 hover:underline cursor-pointer">Hapus Filter</button>
        </div>

        <!-- Desktop Table Content -->
        <div class="hidden md:block overflow-x-auto">
          <table class="w-full text-left text-xs whitespace-nowrap">
            <thead class="bg-slate-100/70 text-gray-600 font-bold border-b border-gray-200">
              <tr>
                <th class="px-5 py-3">No. Isu</th>
                <th class="px-5 py-3">Judul Kendala & Ringkasan</th>
                <th class="px-5 py-3">Bidang / Unit</th>
                <th class="px-5 py-3">Pelapor & Eskalasi</th>
                <th class="px-5 py-3">Status SLA</th>
                <th class="px-5 py-3">Prioritas</th>
                <th class="px-5 py-3 text-center">Tindakan</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="iss in displayedIssues" :key="iss.id" class="hover:bg-blue-50/30 transition">
                
                <!-- No Isu -->
                <td class="px-5 py-3.5 font-mono font-bold">
                  <Link :href="`/issues/${iss.id}`" class="text-blue-600 hover:underline hover:text-blue-800">
                    #{{ iss.issue_no }}
                  </Link>
                </td>

                <!-- Judul & Keterangan -->
                <td class="px-5 py-3.5 max-w-xs">
                  <p class="font-bold text-gray-900 truncate">{{ iss.title }}</p>
                  <p class="text-[11px] text-gray-400 truncate mt-0.5">
                    {{ iss.clean_description || iss.description || 'Tidak ada deskripsi rinci' }}
                  </p>
                </td>

                <!-- Bidang -->
                <td class="px-5 py-3.5">
                  <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                    {{ iss.organization_unit?.name || 'UP Sengkang' }}
                  </span>
                </td>

                <!-- Pelapor & Eskalasi -->
                <td class="px-5 py-3.5">
                  <p class="font-medium text-gray-800">{{ iss.reporter?.name || '-' }}</p>
                  <p class="text-[10px] text-amber-600 font-semibold" v-if="iss.escalations && iss.escalations.length > 0">
                    <i class="fas fa-level-up-alt"></i> Dari Asman (Eskalasi)
                  </p>
                  <p class="text-[10px] text-gray-400" v-else>
                    Level: {{ iss.current_level }}
                  </p>
                </td>

                <!-- SLA Status -->
                <td class="px-5 py-3.5">
                  <span 
                    v-if="iss.sla_info"
                    :class="[
                      'px-2.5 py-1 rounded-full text-[10px] font-bold border flex items-center gap-1.5 w-fit',
                      iss.sla_info.status === 'Breached' ? 'bg-rose-50 text-rose-700 border-rose-300' :
                      iss.sla_info.status === 'Warning' ? 'bg-amber-50 text-amber-700 border-amber-300' :
                      'bg-emerald-50 text-emerald-700 border-emerald-300'
                    ]"
                  >
                    <i :class="iss.sla_info.icon || 'fas fa-clock'"></i>
                    <span>{{ iss.sla_info.label }}</span>
                  </span>
                  <span v-else class="text-gray-400 font-mono text-[11px]">-</span>
                </td>

                <!-- Prioritas -->
                <td class="px-5 py-3.5">
                  <span :class="priorityBadge(iss.priority)">{{ iss.priority }}</span>
                </td>

                <!-- Aksi MUP -->
                <td class="px-5 py-3.5 text-center">
                  <div class="flex items-center justify-center gap-1.5">
                    <button 
                      type="button" 
                      @click="openDecisionModal(iss)"
                      class="px-3 py-1 bg-purple-700 hover:bg-purple-800 text-white rounded-lg text-xs font-bold shadow-xs transition flex items-center gap-1 cursor-pointer"
                    >
                      <i class="fas fa-gavel text-[10px]"></i>
                      <span>Putusan MUP</span>
                    </button>
                    <Link 
                      :href="`/issues/${iss.id}`" 
                      class="p-1 text-gray-400 hover:text-blue-600 rounded transition"
                      title="Lihat Detail Isu"
                    >
                      <i class="fas fa-external-link-alt text-xs"></i>
                    </Link>
                  </div>
                </td>

              </tr>

              <!-- Empty State -->
              <tr v-if="!displayedIssues || displayedIssues.length === 0">
                <td colspan="7" class="px-6 py-12 text-center">
                  <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-2 text-xl">
                    <i class="fas fa-check-circle"></i>
                  </div>
                  <h4 class="font-bold text-gray-800 text-sm">Tidak ada isu dalam kategori ini</h4>
                  <p class="text-xs text-gray-400 mt-1 max-w-sm mx-auto">
                    {{ issueTab === 'mup_pending' ? 'Semua kendala kritis telah diselesaikan atau berada di jenjang seksi terkait.' : 'Tidak ditemukan isu yang sesuai dengan kriteria filter.' }}
                  </p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Mobile Card Feed View -->
        <div class="md:hidden p-3.5 space-y-3 bg-slate-50/50">
          <template v-if="displayedIssues && displayedIssues.length > 0">
            <div 
              v-for="iss in displayedIssues" 
              :key="iss.id" 
              class="bg-white rounded-xl border border-slate-200/80 p-4 shadow-2xs hover:shadow-xs transition active:scale-[0.99] space-y-3"
            >
              <div class="flex items-center justify-between gap-2">
                <Link :href="`/issues/${iss.id}`" class="font-mono text-xs font-bold text-blue-600 bg-blue-50 border border-blue-200 px-2 py-0.5 rounded-md">
                  #{{ iss.issue_no }}
                </Link>
                <span 
                  v-if="iss.sla_info"
                  :class="[
                    'px-2 py-0.5 rounded-full text-[10px] font-bold border flex items-center gap-1',
                    iss.sla_info.status === 'Breached' ? 'bg-rose-50 text-rose-700 border-rose-300' :
                    iss.sla_info.status === 'Warning' ? 'bg-amber-50 text-amber-700 border-amber-300' :
                    'bg-emerald-50 text-emerald-700 border-emerald-300'
                  ]"
                >
                  <i :class="iss.sla_info.icon || 'fas fa-clock'" class="text-[9px]"></i>
                  <span>{{ iss.sla_info.label }}</span>
                </span>
              </div>

              <Link :href="`/issues/${iss.id}`" class="block font-bold text-slate-800 hover:text-blue-600 text-sm leading-snug">
                {{ iss.title }}
              </Link>
              <p class="text-xs text-slate-500 line-clamp-2">
                {{ iss.clean_description || iss.description || 'Tidak ada deskripsi rinci' }}
              </p>

              <div class="flex flex-wrap items-center gap-2 text-xs pt-1">
                <span :class="priorityBadge(iss.priority)">{{ iss.priority }}</span>
                <span class="text-[11px] text-slate-600 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">
                  {{ iss.organization_unit?.name || 'UP Sengkang' }}
                </span>
                <span v-if="iss.escalations && iss.escalations.length > 0" class="text-[10px] text-amber-700 font-bold bg-amber-50 border border-amber-200 px-2 py-0.5 rounded">
                  <i class="fas fa-level-up-alt"></i> Eskalasi Asman
                </span>
              </div>

              <div class="pt-2 border-t border-slate-100 flex items-center justify-between gap-2">
                <span class="text-[11px] text-slate-400">
                  Pelapor: <b class="text-slate-700">{{ iss.reporter?.name || '-' }}</b>
                </span>
                <div class="flex items-center gap-1.5">
                  <button 
                    type="button" 
                    @click="openDecisionModal(iss)"
                    class="px-3 py-1.5 bg-purple-700 hover:bg-purple-800 text-white rounded-lg text-xs font-bold shadow-xs transition flex items-center gap-1 active:scale-95 cursor-pointer"
                  >
                    <i class="fas fa-gavel text-[10px]"></i>
                    <span>Putusan MUP</span>
                  </button>
                  <Link 
                    :href="`/issues/${iss.id}`" 
                    class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-slate-100 rounded-lg transition"
                    title="Detail"
                  >
                    <i class="fas fa-chevron-right text-xs"></i>
                  </Link>
                </div>
              </div>
            </div>
          </template>
          <div v-else class="text-center py-8 text-slate-400 text-xs bg-white rounded-xl border border-dashed border-slate-200">
            <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-2 text-lg">
              <i class="fas fa-check-circle"></i>
            </div>
            <p class="font-bold text-slate-700">Tidak ada isu dalam kategori ini</p>
          </div>
        </div>

      </div>

      <!-- ==================== 3 TAB EKSEKUTIF (TUNGGAKAN, KEPUTUSAN, RAPAT) ==================== -->
      <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        
        <!-- Tab Navigation Bar -->
        <div class="flex border-b border-gray-200 bg-slate-50/70 px-4 pt-2 gap-2 overflow-x-auto text-xs font-bold">
          
          <button 
            type="button" 
            @click="activeExecTab = 'overdue'"
            :class="activeExecTab === 'overdue' ? 'border-b-2 border-rose-600 text-rose-700 bg-white' : 'text-gray-500 hover:text-gray-800'"
            class="px-4 py-3 rounded-t-lg transition flex items-center gap-2 cursor-pointer shrink-0"
          >
            <i class="fas fa-calendar-times text-rose-600"></i>
            <span>Tunggakan Kritis Unit (Overdue)</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-black" :class="(criticalOverdueTasks?.length || 0) > 0 ? 'bg-rose-100 text-rose-700' : 'bg-gray-100 text-gray-500'">
              {{ criticalOverdueTasks?.length || 0 }}
            </span>
          </button>

          <button 
            type="button" 
            @click="activeExecTab = 'decisions'"
            :class="activeExecTab === 'decisions' ? 'border-b-2 border-blue-600 text-blue-700 bg-white' : 'text-gray-500 hover:text-gray-800'"
            class="px-4 py-3 rounded-t-lg transition flex items-center gap-2 cursor-pointer shrink-0"
          >
            <i class="fas fa-file-signature text-blue-600"></i>
            <span>Riwayat Keputusan & Tindak Lanjut MUP</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-blue-100 text-blue-700">
              {{ recentMupDecisions?.length || 0 }}
            </span>
          </button>

          <button 
            type="button" 
            @click="activeExecTab = 'meetings'"
            :class="activeExecTab === 'meetings' ? 'border-b-2 border-emerald-600 text-emerald-700 bg-white' : 'text-gray-500 hover:text-gray-800'"
            class="px-4 py-3 rounded-t-lg transition flex items-center gap-2 cursor-pointer shrink-0"
          >
            <i class="far fa-calendar-check text-emerald-600"></i>
            <span>Agenda Rapat Koordinasi Mingguan</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-700">
              {{ upcomingMeetings?.length || 0 }}
            </span>
          </button>

        </div>

        <!-- TAB 1 CONTENT: TUNGGAKAN KRITIS (OVERDUE) -->
        <div v-if="activeExecTab === 'overdue'" class="p-0">
          <div class="overflow-x-auto">
            <table class="w-full text-left text-xs whitespace-nowrap">
              <thead class="bg-slate-50 text-gray-500 font-bold border-b border-gray-100">
                <tr>
                  <th class="px-5 py-3">Judul Task</th>
                  <th class="px-5 py-3">PIC / Pelaksana</th>
                  <th class="px-5 py-3">Bidang / Bagian</th>
                  <th class="px-5 py-3">Tenggat Waktu</th>
                  <th class="px-5 py-3">Keterlambatan</th>
                  <th class="px-5 py-3">Progres</th>
                  <th class="px-5 py-3 text-center">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr v-for="ot in displayedOverdueTasks" :key="ot.id" class="hover:bg-rose-50/20 transition">
                  <td class="px-5 py-3 font-bold text-gray-900">{{ ot.title }}</td>
                  <td class="px-5 py-3 text-gray-700 flex items-center gap-2">
                    <div class="w-6 h-6 rounded-full overflow-hidden bg-slate-200 shrink-0">
                      <img :src="ot.assignee?.avatar_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(ot.assignee?.name || 'User')}&size=60`" class="w-full h-full object-cover" />
                    </div>
                    <span>{{ ot.assignee?.name || '-' }}</span>
                  </td>
                  <td class="px-5 py-3 text-gray-600 font-medium">
                    {{ ot.organization_unit?.name || '-' }}
                  </td>
                  <td class="px-5 py-3 font-mono text-gray-600">{{ formatDate(ot.due_date) }}</td>
                  <td class="px-5 py-3">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                      +{{ daysOverdue(ot.due_date) }} hari lewat
                    </span>
                  </td>
                  <td class="px-5 py-3 w-32">
                    <div class="flex items-center gap-2">
                      <div class="w-16 bg-gray-200 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-rose-500 h-1.5 rounded-full" :style="{ width: (ot.progress_percent || 0) + '%' }"></div>
                      </div>
                      <span class="font-bold text-[10px]">{{ ot.progress_percent || 0 }}%</span>
                    </div>
                  </td>
                  <td class="px-5 py-3 text-center">
                    <Link href="/tasks" class="text-xs font-bold text-blue-600 hover:underline">
                      Tinjau Task &rarr;
                    </Link>
                  </td>
                </tr>
                <tr v-if="!displayedOverdueTasks || displayedOverdueTasks.length === 0">
                  <td colspan="7" class="px-6 py-10 text-center text-gray-400">
                    <i class="fas fa-check-circle text-emerald-500 text-xl mb-1.5 block"></i>
                    Tidak ada task terlambat yang terdata pada bidang ini.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- TAB 2 CONTENT: RIWAYAT KEPUTUSAN & TINDAK LANJUT MUP -->
        <div v-else-if="activeExecTab === 'decisions'" class="p-5">
          <div class="space-y-4">
            <div 
              v-for="dec in recentMupDecisions" 
              :key="dec.id"
              class="p-4 rounded-xl border border-gray-100 bg-slate-50/60 hover:bg-white hover:shadow-xs transition"
            >
              <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-2">
                <div class="flex items-center gap-2">
                  <span class="px-2 py-0.5 rounded font-mono font-bold text-[10px] bg-purple-100 text-purple-800 border border-purple-200">
                    Putusan MUP
                  </span>
                  <Link v-if="dec.issue" :href="`/issues/${dec.issue_id}`" class="font-bold text-xs text-blue-700 hover:underline">
                    #{{ dec.issue.issue_no }} - {{ dec.issue.title }}
                  </Link>
                  <span v-else class="font-bold text-xs text-gray-800">Isu #{{ dec.issue_id }}</span>
                </div>
                <span class="text-[11px] text-gray-400 font-mono">{{ formatDateTime(dec.created_at) }}</span>
              </div>

              <!-- Rincian Putusan -->
              <p class="text-xs text-gray-900 font-bold bg-white p-3 rounded-lg border border-gray-100 mb-2">
                "{{ dec.decision }}"
              </p>

              <!-- Pertimbangan & Tindak Lanjut -->
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs text-gray-600">
                <div v-if="dec.rationale" class="bg-white/80 p-2.5 rounded border border-gray-100">
                  <span class="text-[10px] font-bold uppercase text-gray-400 block mb-0.5">Dasar Pertimbangan:</span>
                  <p>{{ dec.rationale }}</p>
                </div>
                <div v-if="dec.follow_up" class="bg-white/80 p-2.5 rounded border border-gray-100">
                  <span class="text-[10px] font-bold uppercase text-gray-400 block mb-0.5">Arahan Tindak Lanjut:</span>
                  <p>{{ dec.follow_up }}</p>
                </div>
              </div>

              <!-- Task Otomatis yang Terkait -->
              <div v-if="dec.tasks && dec.tasks.length > 0" class="mt-3 pt-2.5 border-t border-gray-100 flex flex-wrap items-center gap-3 text-xs">
                <span class="text-[11px] font-bold text-gray-500 flex items-center gap-1">
                  <i class="fas fa-tasks text-blue-600"></i> Tugas Terkait:
                </span>
                <div v-for="dt in dec.tasks" :key="dt.id" class="flex items-center gap-2 bg-blue-50/80 px-2.5 py-1 rounded border border-blue-200">
                  <span class="font-bold text-blue-900 text-[11px]">{{ dt.title }}</span>
                  <span class="text-blue-700 text-[10px]">({{ dt.assignee?.name || '-' }})</span>
                  <span class="font-bold text-[10px] text-emerald-700">{{ dt.progress_percent }}%</span>
                </div>
              </div>
            </div>

            <div v-if="!recentMupDecisions || recentMupDecisions.length === 0" class="py-10 text-center text-gray-400">
              <i class="fas fa-file-signature text-2xl mb-1 block"></i>
              Belum ada riwayat putusan MUP dalam sistem.
            </div>
          </div>
        </div>

        <!-- TAB 3 CONTENT: AGENDA RAPAT MINGGUAN -->
        <div v-else-if="activeExecTab === 'meetings'" class="p-0">
          <div class="overflow-x-auto">
            <table class="w-full text-left text-xs whitespace-nowrap">
              <thead class="bg-slate-50 text-gray-500 font-bold border-b border-gray-100">
                <tr>
                  <th class="px-5 py-3">Nama Agenda Rapat</th>
                  <th class="px-5 py-3">Tanggal & Waktu</th>
                  <th class="px-5 py-3">Pemimpin Rapat</th>
                  <th class="px-5 py-3">Lokasi / Media</th>
                  <th class="px-5 py-3">Status</th>
                  <th class="px-5 py-3 text-center">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr v-for="m in upcomingMeetings" :key="m.id" class="hover:bg-slate-50 transition">
                  <td class="px-5 py-3 font-bold text-gray-900">{{ m.type }}</td>
                  <td class="px-5 py-3 font-mono text-gray-600">
                    {{ formatDate(m.meeting_date) }} ({{ m.time_start }} - {{ m.time_end }})
                  </td>
                  <td class="px-5 py-3 text-gray-700">
                    {{ m.leader?.name || 'Manager Unit' }}
                  </td>
                  <td class="px-5 py-3">
                    <span v-if="m.meeting_mode === 'Online'" class="text-blue-600 font-semibold flex items-center gap-1">
                      <i class="fas fa-video"></i> Video Conference
                    </span>
                    <span v-else class="text-gray-600">
                      {{ m.location || 'Ruang Rapat Utama' }}
                    </span>
                  </td>
                  <td class="px-5 py-3">
                    <span 
                      :class="m.status === 'Berlangsung' ? 'bg-amber-100 text-amber-800 border-amber-300' : 'bg-blue-50 text-blue-700 border-blue-200'"
                      class="px-2 py-0.5 rounded text-[10px] font-bold border"
                    >
                      {{ m.status }}
                    </span>
                  </td>
                  <td class="px-5 py-3 text-center">
                    <Link :href="`/rapat/${m.id}`" class="text-blue-600 font-bold hover:underline">
                      Masuk Rapat &rarr;
                    </Link>
                  </td>
                </tr>
                <tr v-if="!upcomingMeetings || upcomingMeetings.length === 0">
                  <td colspan="6" class="px-6 py-10 text-center text-gray-400">
                    Belum ada agenda rapat mingguan yang terjadwal.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </div>

    </div>

    <!-- ========================================================================= -->
    <!-- JIKA ROLE OPERASIONAL (STAFF, TL, ASMAN): TAMPILAN OPERASIONAL SEIMBANG -->
    <!-- ========================================================================= -->
    <div v-else class="space-y-6">

      <!-- ==================== HEADER BANNER & SIKLUS FILTER ==================== -->
      <div class="bg-[#0f2744] text-white p-5 rounded-2xl shadow-md border border-slate-700 relative overflow-hidden">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
          <div>
            <div class="flex items-center gap-2 mb-1">
              <span class="bg-blue-600/80 text-white text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider">
                {{ roleName }} Workspace
              </span>
              <span class="text-[11px] text-emerald-400 font-medium flex items-center gap-1">
                <i class="fas fa-circle text-emerald-400 text-[8px] animate-pulse"></i> UP Sengkang Monitoring Real-Time
              </span>
            </div>
            <h2 class="text-xl md:text-2xl font-black uppercase tracking-wider text-white">
              DASHBOARD MONITORING & OPERASIONAL {{ roleName }}
            </h2>
            <p class="text-xs text-sky-200 mt-1 font-medium">
              Monitoring terpadu tugas staf, evaluasi isu seksi, dan progres task mingguan
            </p>
          </div>
          
          <!-- Siklus Koordinasi Selector -->
          <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 bg-slate-800/80 p-2.5 rounded-xl border border-slate-600/60 shrink-0">
            <div class="flex items-center gap-1.5 text-xs text-slate-300 font-medium mr-1">
              <i class="far fa-calendar-alt text-sky-400"></i>
              <span>Siklus:</span>
              <span class="bg-blue-500/20 text-sky-300 font-mono font-bold px-2 py-0.5 rounded text-[11px] border border-sky-400/30">
                W{{ manajemenTugas?.minggu_berjalan }}
              </span>
            </div>
            <div class="flex items-center gap-1">
              <button 
                type="button" 
                @click="setSiklus('semua')" 
                :class="siklusClass('semua')"
              >
                Semua
              </button>
              <button 
                type="button" 
                @click="setSiklus('N')" 
                :class="siklusClass('N')"
              >
                Minggu N
              </button>
              <button 
                type="button" 
                @click="setSiklus('N-1')" 
                :class="siklusClass('N-1')"
              >
                Minggu N-1
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- ==================== 5 CARD KPI OPERASIONAL ==================== -->
      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4">
        <!-- Card 1: Total Task -->
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-xs">
          <p class="text-gray-500 text-xs font-medium mb-1">Total Task</p>
          <div class="text-3xl font-bold text-gray-800 mb-1">{{ kpiData?.total_pekerjaan || 0 }}</div>
          <p class="text-emerald-600 text-[11px] font-bold flex items-center gap-1">
            <i class="fas fa-tasks"></i> Terdata
          </p>
        </div>

        <!-- Card 2: On Progress -->
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-xs">
          <p class="text-gray-500 text-xs font-medium mb-1">On Progress</p>
          <div class="text-3xl font-bold text-blue-600 mb-1">{{ kpiData?.on_progress || 0 }}</div>
          <p class="text-gray-400 text-[11px] font-medium">Dalam pengerjaan</p>
        </div>

        <!-- Card 3: Selesai -->
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-xs">
          <p class="text-gray-500 text-xs font-medium mb-1">Selesai</p>
          <div class="text-3xl font-bold text-emerald-600 mb-1">{{ kpiData?.selesai || 0 }}</div>
          <p class="text-emerald-600 text-[11px] font-bold flex items-center gap-1">
            <i class="fas fa-check-circle"></i> Tuntas
          </p>
        </div>

        <!-- Card 4: Terlambat -->
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-xs">
          <p class="text-gray-500 text-xs font-medium mb-1">Terlambat (Overdue)</p>
          <div class="text-3xl font-bold text-rose-600 mb-1">{{ kpiData?.terlambat || 0 }}</div>
          <p class="text-rose-500 text-[11px] font-bold flex items-center gap-1">
            <i class="fas fa-exclamation-circle"></i> Butuh evaluasi
          </p>
        </div>

        <!-- Card 5: Issue Saya -->
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-xs">
          <p class="text-gray-500 text-xs font-medium mb-1">Issue Terkait</p>
          <div class="text-3xl font-bold text-amber-600 mb-1">{{ kpiData?.issue_saya || 0 }}</div>
          <p class="text-amber-600 text-[11px] font-bold flex items-center gap-1">
            <i class="fas fa-level-up-alt"></i> Aktif
          </p>
        </div>
      </div>

      <!-- ==================== RINGKASAN STATUS & PRIORITAS ==================== -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Status Task Breakdown -->
        <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-xs flex flex-col justify-between">
          <h3 class="font-bold text-gray-800 text-sm mb-4 flex items-center gap-2">
            <i class="fas fa-chart-pie text-blue-600"></i> Status Task
          </h3>
          
          <div class="space-y-4 my-auto">
            <div>
              <div class="flex justify-between text-xs font-bold mb-1">
                <span class="text-emerald-700">Selesai ({{ kpiData?.selesai || 0 }})</span>
                <span>{{ percent(kpiData?.selesai, kpiData?.total_pekerjaan) }}%</span>
              </div>
              <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                <div class="bg-emerald-500 h-2.5 rounded-full transition-all duration-500" :style="{ width: percent(kpiData?.selesai, kpiData?.total_pekerjaan) + '%' }"></div>
              </div>
            </div>

            <div>
              <div class="flex justify-between text-xs font-bold mb-1">
                <span class="text-blue-700">Dalam Pengerjaan ({{ kpiData?.on_progress || 0 }})</span>
                <span>{{ percent(kpiData?.on_progress, kpiData?.total_pekerjaan) }}%</span>
              </div>
              <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-500" :style="{ width: percent(kpiData?.on_progress, kpiData?.total_pekerjaan) + '%' }"></div>
              </div>
            </div>

            <div>
              <div class="flex justify-between text-xs font-bold mb-1">
                <span class="text-rose-700">Terlambat ({{ kpiData?.terlambat || 0 }})</span>
                <span>{{ percent(kpiData?.terlambat, kpiData?.total_pekerjaan) }}%</span>
              </div>
              <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                <div class="bg-rose-500 h-2.5 rounded-full transition-all duration-500" :style="{ width: percent(kpiData?.terlambat, kpiData?.total_pekerjaan) + '%' }"></div>
              </div>
            </div>
          </div>

          <div class="mt-4 pt-4 border-t border-gray-100 flex justify-between text-[11px] text-gray-500">
            <span>Total: <strong>{{ kpiData?.total_pekerjaan || 0 }} Task</strong></span>
            <Link href="/tasks" class="text-blue-600 font-semibold hover:underline">Lihat Semua &rarr;</Link>
          </div>
        </div>

        <!-- Prioritas Issue Breakdown -->
        <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-xs flex flex-col justify-between">
          <h3 class="font-bold text-gray-800 text-sm mb-4 flex items-center gap-2">
            <i class="fas fa-signal text-amber-500"></i> Tingkat Prioritas Isu
          </h3>

          <div class="space-y-4 my-auto">
            <div>
              <div class="flex justify-between text-xs font-bold mb-1">
                <span class="text-rose-700">Tinggi / Kritis ({{ kpiData?.chart_priority?.high || 0 }})</span>
                <span class="text-rose-600 font-bold">Prioritas 1</span>
              </div>
              <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                <div class="bg-rose-600 h-2.5 rounded-full" :style="{ width: percent(kpiData?.chart_priority?.high, (kpiData?.chart_priority?.high || 0) + (kpiData?.chart_priority?.medium || 0) + (kpiData?.chart_priority?.low || 0)) + '%' }"></div>
              </div>
            </div>

            <div>
              <div class="flex justify-between text-xs font-bold mb-1">
                <span class="text-amber-700">Sedang ({{ kpiData?.chart_priority?.medium || 0 }})</span>
                <span class="text-amber-600 font-bold">Prioritas 2</span>
              </div>
              <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                <div class="bg-amber-500 h-2.5 rounded-full" :style="{ width: percent(kpiData?.chart_priority?.medium, (kpiData?.chart_priority?.high || 0) + (kpiData?.chart_priority?.medium || 0) + (kpiData?.chart_priority?.low || 0)) + '%' }"></div>
              </div>
            </div>

            <div>
              <div class="flex justify-between text-xs font-bold mb-1">
                <span class="text-emerald-700">Rendah ({{ kpiData?.chart_priority?.low || 0 }})</span>
                <span class="text-emerald-600 font-bold">Prioritas 3</span>
              </div>
              <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                <div class="bg-emerald-500 h-2.5 rounded-full" :style="{ width: percent(kpiData?.chart_priority?.low, (kpiData?.chart_priority?.high || 0) + (kpiData?.chart_priority?.medium || 0) + (kpiData?.chart_priority?.low || 0)) + '%' }"></div>
              </div>
            </div>
          </div>

          <div class="mt-4 pt-4 border-t border-gray-100 flex justify-between text-[11px] text-gray-500">
            <span>Eskalasi Terkelola</span>
            <Link href="/issues" class="text-blue-600 font-semibold hover:underline">Detail Isu &rarr;</Link>
          </div>
        </div>

        <!-- Quick Action Shortcuts -->
        <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-xs flex flex-col justify-between">
          <h3 class="font-bold text-gray-800 text-sm mb-3 flex items-center gap-2">
            <i class="fas fa-bolt text-yellow-500"></i> Aksi Cepat & Navigasi
          </h3>

          <div class="space-y-2.5">
            <Link href="/tasks" class="flex items-center justify-between p-2.5 rounded-lg border border-gray-100 hover:border-blue-300 hover:bg-blue-50/50 transition">
              <div class="flex items-center gap-2.5">
                <span class="w-7 h-7 rounded-md bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">
                  <i class="fas fa-plus"></i>
                </span>
                <span class="text-xs font-bold text-gray-700">Kelola Task</span>
              </div>
              <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
            </Link>

            <Link href="/issues" class="flex items-center justify-between p-2.5 rounded-lg border border-gray-100 hover:border-amber-300 hover:bg-amber-50/50 transition">
              <div class="flex items-center gap-2.5">
                <span class="w-7 h-7 rounded-md bg-amber-100 text-amber-700 flex items-center justify-center text-xs font-bold">
                  <i class="fas fa-bullhorn"></i>
                </span>
                <span class="text-xs font-bold text-gray-700">Lapor Isu & Kendala</span>
              </div>
              <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
            </Link>

            <Link href="/rapat" class="flex items-center justify-between p-2.5 rounded-lg border border-gray-100 hover:border-emerald-300 hover:bg-emerald-50/50 transition">
              <div class="flex items-center gap-2.5">
                <span class="w-7 h-7 rounded-md bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold">
                  <i class="far fa-calendar-check"></i>
                </span>
                <span class="text-xs font-bold text-gray-700">Rapat & Workspace Mingguan</span>
              </div>
              <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
            </Link>
          </div>

          <div class="mt-4 pt-3 border-t border-gray-100 text-[11px] text-gray-400 flex items-center gap-1.5">
            <i class="fas fa-info-circle text-blue-500"></i>
            <span>Data tersinkronisasi otomatis dengan server</span>
          </div>
        </div>

      </div>

      <!-- ==================== TABEL SPESIFIK ROLE OPERASIONAL ==================== -->

      <!-- 1. JIKA ROLE STAFF: TAMPILKAN DAFTAR TUGAS SAYA -->
      <div v-if="roleName === 'STAFF'" class="bg-white rounded-xl border border-gray-100 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
          <div>
            <h3 class="font-bold text-sm text-gray-800">Daftar Task Saya</h3>
            <p class="text-xs text-gray-400">Task operasional yang ditugaskan kepada Anda</p>
          </div>
          <Link href="/tasks" class="text-xs font-bold text-blue-600 hover:underline">
            Buka Modul Task &rarr;
          </Link>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs whitespace-nowrap">
            <thead class="bg-slate-50 text-gray-500 font-semibold border-b border-gray-100">
              <tr>
                <th class="px-6 py-3">Judul Task</th>
                <th class="px-6 py-3">Tenggat Waktu</th>
                <th class="px-6 py-3">Progres</th>
                <th class="px-6 py-3">Status</th>
                <th class="px-6 py-3 text-center">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="t in myTasks" :key="t.id" class="hover:bg-slate-50/50">
                <td class="px-6 py-3 font-bold text-gray-800">{{ t.title }}</td>
                <td class="px-6 py-3 font-mono text-gray-600">{{ t.due_date }}</td>
                <td class="px-6 py-3 w-40">
                  <div class="flex items-center gap-2">
                    <div class="w-24 bg-gray-200 rounded-full h-2">
                      <div class="bg-blue-600 h-2 rounded-full" :style="{ width: t.progress_percent + '%' }"></div>
                    </div>
                    <span class="font-bold text-[11px]">{{ t.progress_percent }}%</span>
                  </div>
                </td>
                <td class="px-6 py-3">
                  <span :class="statusBadge(t.calculated_status || t.status)">
                    {{ t.calculated_status || t.status }}
                  </span>
                </td>
                <td class="px-6 py-3 text-center">
                  <Link href="/tasks" class="text-blue-600 hover:underline font-bold text-[11px]">
                    Perbarui Progres
                  </Link>
                </td>
              </tr>
              <tr v-if="!myTasks || myTasks.length === 0">
                <td colspan="5" class="px-6 py-8 text-center text-gray-400">Belum ada penugasan task untuk Anda.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- 2. JIKA ROLE TL: DAFTAR ISU PERLU DITINJAU TL -->
      <div v-else-if="roleName === 'TL'" class="bg-white rounded-xl border border-gray-100 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
          <div>
            <h3 class="font-bold text-sm text-gray-800 flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span> Isu Menunggu Keputusan TL
            </h3>
            <p class="text-xs text-gray-400">Kendala staf seksi yang memerlukan keputusan teknis atau eskalasi</p>
          </div>
          <Link href="/eskalasi" class="text-xs font-bold text-blue-600 hover:underline">
            Daftar Eskalasi &rarr;
          </Link>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs whitespace-nowrap">
            <thead class="bg-slate-50 text-gray-500 font-semibold border-b border-gray-100">
              <tr>
                <th class="px-6 py-3">No. Isu</th>
                <th class="px-6 py-3">Judul Isu</th>
                <th class="px-6 py-3">Pelapor</th>
                <th class="px-6 py-3">Prioritas</th>
                <th class="px-6 py-3">Status</th>
                <th class="px-6 py-3 text-center">Tindakan</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="iss in issuesUnderReview" :key="iss.id" class="hover:bg-slate-50/50">
                <td class="px-6 py-3 font-mono font-bold text-blue-600">#{{ iss.issue_no }}</td>
                <td class="px-6 py-3 font-bold text-gray-800">{{ iss.title }}</td>
                <td class="px-6 py-3 text-gray-600">{{ iss.reporter?.name || '-' }}</td>
                <td class="px-6 py-3">
                  <span :class="priorityBadge(iss.priority)">{{ iss.priority }}</span>
                </td>
                <td class="px-6 py-3">
                  <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                    {{ iss.status }}
                  </span>
                </td>
                <td class="px-6 py-3 text-center">
                  <Link :href="`/issues/${iss.id}`" class="px-2.5 py-1 bg-blue-600 text-white rounded text-[11px] font-bold shadow-xs hover:bg-blue-700 transition">
                    Tinjau & Putuskan
                  </Link>
                </td>
              </tr>
              <tr v-if="!issuesUnderReview || issuesUnderReview.length === 0">
                <td colspan="6" class="px-6 py-8 text-center text-gray-400">Tidak ada isu aktif yang menunggu keputusan TL saat ini.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- 3. JIKA ROLE ASMAN: DAFTAR ESKALASI MASUK KE ASMAN -->
      <div v-else-if="roleName === 'ASMAN'" class="bg-white rounded-xl border border-gray-100 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
          <div>
            <h3 class="font-bold text-sm text-gray-800 flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Eskalasi Masuk ke Asisten Manajer
            </h3>
            <p class="text-xs text-gray-400">Isu teknis berbiaya / lintas seksi yang dinaikkan oleh TL</p>
          </div>
          <Link href="/eskalasi" class="text-xs font-bold text-amber-600 hover:underline">
            Kelola Eskalasi &rarr;
          </Link>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs whitespace-nowrap">
            <thead class="bg-slate-50 text-gray-500 font-semibold border-b border-gray-100">
              <tr>
                <th class="px-6 py-3">No. Isu</th>
                <th class="px-6 py-3">Judul Isu</th>
                <th class="px-6 py-3">Pelapor Asal</th>
                <th class="px-6 py-3">Prioritas</th>
                <th class="px-6 py-3 text-center">Aksi ASMAN</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="esc in escalatedIssues" :key="esc.id" class="hover:bg-slate-50/50">
                <td class="px-6 py-3 font-mono font-bold text-amber-600">#{{ esc.issue_no }}</td>
                <td class="px-6 py-3 font-bold text-gray-800">{{ esc.title }}</td>
                <td class="px-6 py-3 text-gray-600">{{ esc.reporter?.name || '-' }}</td>
                <td class="px-6 py-3">
                  <span :class="priorityBadge(esc.priority)">{{ esc.priority }}</span>
                </td>
                <td class="px-6 py-3 text-center">
                  <Link :href="`/issues/${esc.id}`" class="px-2.5 py-1 bg-amber-600 text-white rounded text-[11px] font-bold shadow-xs hover:bg-amber-700 transition">
                    Beri Putusan ASMAN
                  </Link>
                </td>
              </tr>
              <tr v-if="!escalatedIssues || escalatedIssues.length === 0">
                <td colspan="5" class="px-6 py-8 text-center text-gray-400">Tidak ada eskalasi pending untuk tingkat Asisten Manajer.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- ==================== MONITORING TUGAS TIM ==================== -->
      <div class="bg-white rounded-xl border border-gray-100 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
          <div>
            <h3 class="font-bold text-sm text-gray-800">Aktivitas Task Terbaru</h3>
            <p class="text-xs text-gray-400">Monitoring progres task seluruh personil di unit UP Sengkang</p>
          </div>
          <Link href="/tasks" class="text-xs font-bold text-blue-600 hover:underline">
            Buka Manajemen Task &rarr;
          </Link>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs whitespace-nowrap">
            <thead class="bg-slate-50 text-gray-500 font-semibold border-b border-gray-100">
              <tr>
                <th class="px-6 py-3">Judul Task</th>
                <th class="px-6 py-3">PIC / Pelaksana</th>
                <th class="px-6 py-3">Tenggat Waktu</th>
                <th class="px-6 py-3">Progres</th>
                <th class="px-6 py-3">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="task in tasks" :key="task.id" class="hover:bg-slate-50/50">
                <td class="px-6 py-3 font-bold text-gray-800">{{ task.title }}</td>
                <td class="px-6 py-3 text-gray-600 flex items-center gap-2">
                  <div class="w-6 h-6 rounded-full overflow-hidden bg-slate-200 shrink-0">
                    <img :src="task.assignee?.avatar_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(task.assignee?.name || 'User')}&size=60`" class="w-full h-full object-cover" />
                  </div>
                  <span>{{ task.assignee?.name || '-' }}</span>
                </td>
                <td class="px-6 py-3 font-mono text-gray-600">{{ task.due_date }}</td>
                <td class="px-6 py-3 w-36">
                  <div class="flex items-center gap-2">
                    <div class="w-20 bg-gray-200 rounded-full h-1.5">
                      <div class="bg-blue-600 h-1.5 rounded-full" :style="{ width: task.progress_percent + '%' }"></div>
                    </div>
                    <span class="font-bold text-[10px]">{{ task.progress_percent }}%</span>
                  </div>
                </td>
                <td class="px-6 py-3">
                  <span :class="statusBadge(task.calculated_status || task.status)">
                    {{ task.calculated_status || task.status }}
                  </span>
                </td>
              </tr>
              <tr v-if="!tasks || tasks.length === 0">
                <td colspan="5" class="px-6 py-8 text-center text-gray-400">Belum ada riwayat task dalam sistem.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>

    <!-- ========================================================================= -->
    <!-- QUICK DECISION MODAL FOR MUP -->
    <!-- ========================================================================= -->
    <div 
      v-if="decisionModalOpen" 
      class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all"
    >
      <div 
        class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-xl overflow-hidden animate-in fade-in zoom-in-95 duration-200"
        @click.stop
      >
        <!-- Modal Header -->
        <div class="bg-[#0f2744] text-white p-5 flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-purple-600 text-white flex items-center justify-center text-lg shadow-sm">
              <i class="fas fa-gavel"></i>
            </div>
            <div>
              <h3 class="font-bold text-sm">Beri Keputusan & Arahan MUP</h3>
              <p class="text-[11px] text-purple-200 font-mono">
                #{{ activeIssueForDecision?.issue_no }} - {{ activeIssueForDecision?.title }}
              </p>
            </div>
          </div>
          <button 
            type="button" 
            @click="closeDecisionModal" 
            class="text-gray-400 hover:text-white p-1 rounded-lg hover:bg-white/10 transition cursor-pointer"
          >
            <i class="fas fa-times text-base"></i>
          </button>
        </div>

        <!-- Issue Brief -->
        <div class="p-4 bg-slate-50 border-b border-gray-100 flex flex-wrap items-center justify-between gap-2 text-xs">
          <div>
            <span class="text-gray-400 block text-[10px] uppercase font-bold">Bagian / Seksi</span>
            <span class="font-bold text-gray-800">{{ activeIssueForDecision?.organization_unit?.name || 'UP Sengkang' }}</span>
          </div>
          <div>
            <span class="text-gray-400 block text-[10px] uppercase font-bold">Pelapor</span>
            <span class="font-bold text-gray-800">{{ activeIssueForDecision?.reporter?.name || '-' }}</span>
          </div>
          <div>
            <span class="text-gray-400 block text-[10px] uppercase font-bold">Prioritas</span>
            <span :class="priorityBadge(activeIssueForDecision?.priority)">{{ activeIssueForDecision?.priority }}</span>
          </div>
          <div>
            <span class="text-gray-400 block text-[10px] uppercase font-bold">Status SLA</span>
            <span class="font-bold text-rose-600" v-if="activeIssueForDecision?.sla_info?.status === 'Breached'">Lewat SLA</span>
            <span class="font-bold text-emerald-600" v-else>Dalam Target</span>
          </div>
        </div>

        <!-- Form Body -->
        <form @submit.prevent="submitDecision" class="p-5 space-y-4">
          
          <!-- Keputusan / Arahan MUP -->
          <div>
            <label class="block text-xs font-bold text-gray-800 mb-1">
              Putusan / Arahan Eksekutif MUP <span class="text-rose-500">*</span>
            </label>
            <textarea 
              v-model="decisionForm.decision"
              rows="3" 
              required
              placeholder="Tuliskan arahan, instruksi resmi, atau putusan manajerial terkait isu ini..." 
              class="w-full text-xs p-3 rounded-lg border border-gray-300 focus:outline-none focus:border-purple-600 focus:ring-1 focus:ring-purple-600 text-gray-800"
            ></textarea>
            <p v-if="decisionForm.errors.decision" class="text-rose-600 text-[11px] mt-1">{{ decisionForm.errors.decision }}</p>
          </div>

          <!-- Dasar Pertimbangan -->
          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">
              Dasar Pertimbangan / Kajian Manajerial (Opsional)
            </label>
            <textarea 
              v-model="decisionForm.rationale"
              rows="2" 
              placeholder="Pertimbangan teknis, anggaran, keandalan operasi, atau regulasi..." 
              class="w-full text-xs p-2.5 rounded-lg border border-gray-200 focus:outline-none focus:border-purple-600 focus:ring-1 focus:ring-purple-600 text-gray-800"
            ></textarea>
          </div>

          <!-- Arahan Tindak Lanjut -->
          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">
              Instruksi Tindak Lanjut Khusus (Opsional)
            </label>
            <input 
              v-model="decisionForm.follow_up"
              type="text" 
              placeholder="Contoh: Lakukan isolasi peralatan dan laporkan hasil pada RTM minggu depan" 
              class="w-full text-xs px-3 py-2 rounded-lg border border-gray-200 focus:outline-none focus:border-purple-600 focus:ring-1 focus:ring-purple-600 text-gray-800"
            />
          </div>

          <!-- Option Checkboxes -->
          <div class="space-y-2 pt-2 border-t border-gray-100">
            
            <label class="flex items-center gap-2.5 cursor-pointer">
              <input 
                v-model="decisionForm.mark_resolved"
                type="checkbox" 
                class="w-4 h-4 rounded text-purple-600 focus:ring-purple-500 border-gray-300 cursor-pointer"
              />
              <span class="text-xs font-bold text-gray-800">
                Tandai Isu Selesai (Resolved)
              </span>
            </label>

            <label class="flex items-center gap-2.5 cursor-pointer">
              <input 
                v-model="decisionForm.create_task"
                type="checkbox" 
                class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-gray-300 cursor-pointer"
              />
              <span class="text-xs font-bold text-gray-800">
                Otomatis Buat Tugas Tindak Lanjut (Task)
              </span>
            </label>

          </div>

          <!-- Extra Task Inputs when create_task is checked -->
          <div v-if="decisionForm.create_task" class="p-3.5 bg-blue-50/60 rounded-xl border border-blue-200 space-y-3 animate-in fade-in duration-150">
            <div>
              <label class="block text-[11px] font-bold text-blue-900 mb-1">Judul Tugas Tindak Lanjut</label>
              <input 
                v-model="decisionForm.task_title"
                type="text" 
                class="w-full text-xs px-2.5 py-1.5 rounded border border-blue-200 bg-white focus:outline-none focus:border-blue-500 text-gray-800 font-semibold"
              />
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
              <div>
                <UserSelect 
                  v-model="decisionForm.task_assignee_id" 
                  :users="assignableUsers" 
                  label="Tugaskan Kepada (PIC)" 
                  placeholder="Ketik nama atau NID..." 
                />
              </div>

              <div>
                <label class="block text-[11px] font-bold text-blue-900 mb-1">Tenggat Waktu</label>
                <input 
                  v-model="decisionForm.task_due_date"
                  type="date" 
                  class="w-full text-xs px-2.5 py-1.5 rounded border border-blue-200 bg-white focus:outline-none focus:border-blue-500 text-gray-800"
                />
              </div>
            </div>
          </div>

          <!-- Modal Footer -->
          <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-2.5">
            <button 
              type="button" 
              @click="closeDecisionModal"
              class="px-4 py-2 rounded-lg text-xs font-bold text-gray-600 hover:bg-gray-100 transition cursor-pointer"
            >
              Batal
            </button>
            <button 
              type="submit" 
              :disabled="decisionForm.processing"
              class="px-5 py-2 rounded-lg text-xs font-bold bg-purple-700 hover:bg-purple-800 text-white shadow-md transition flex items-center gap-2 cursor-pointer disabled:opacity-50 disabled:pointer-events-none disabled:cursor-not-allowed"
            >
              <i v-if="decisionForm.processing" class="fas fa-spinner fa-spin text-xs"></i>
              <i v-else class="fas fa-check"></i>
              <span>{{ decisionForm.processing ? 'Menyimpan...' : 'Simpan Putusan MUP' }}</span>
            </button>
          </div>

        </form>

      </div>
    </div>

    <!-- ============================================================== -->
    <!-- POP-UP BERHASIL MASUK & SELAMAT DATANG [NAMA USER]             -->
    <!-- ============================================================== -->
    <transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div 
        v-if="showWelcomeModal" 
        class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs"
        @click.self="closeWelcomeModal"
        role="dialog"
        aria-modal="true"
      >
        <transition
          enter-active-class="transition duration-300 cubic-bezier(0.16, 1, 0.3, 1)"
          enter-from-class="opacity-0 scale-95 translate-y-3"
          enter-to-class="opacity-100 scale-100 translate-y-0"
          leave-active-class="transition duration-200 ease-in"
          leave-from-class="opacity-100 scale-100 translate-y-0"
          leave-to-class="opacity-0 scale-95 translate-y-3"
        >
          <div 
            v-if="showWelcomeModal"
            class="relative w-full max-w-sm sm:max-w-md bg-white rounded-3xl shadow-2xl border border-slate-100 p-6 sm:p-7 text-center overflow-hidden"
          >
            <!-- Background Glow Ambient Effect -->
            <div class="absolute -top-16 -right-16 w-36 h-36 bg-sky-400/15 rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute -bottom-16 -left-16 w-36 h-36 bg-emerald-400/15 rounded-full blur-2xl pointer-events-none"></div>

            <!-- Tombol Silang (X) di pojok kanan atas -->
            <button 
              type="button" 
              @click="closeWelcomeModal"
              class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-1.5 rounded-full text-xs transition cursor-pointer"
              title="Tutup"
            >
              <i class="fas fa-times"></i>
            </button>

            <!-- Animasi Ikon Berhasil Masuk -->
            <div class="relative mx-auto w-16 h-16 mb-3.5 flex items-center justify-center">
              <div class="absolute inset-0 rounded-2xl bg-emerald-100/70 animate-ping opacity-35"></div>
              <div class="relative w-16 h-16 rounded-2xl bg-gradient-to-tr from-emerald-500 to-teal-400 text-white flex items-center justify-center text-2xl shadow-lg shadow-emerald-500/25">
                <i class="fas fa-check"></i>
              </div>
            </div>

            <!-- Label Pill Berhasil Masuk -->
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-[11px] font-extrabold uppercase tracking-wider mb-2.5">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
              <span>Anda Berhasil Masuk</span>
            </div>

            <!-- Pesan Sambutan & Nama User -->
            <h3 class="text-lg sm:text-xl font-black text-slate-800 tracking-tight leading-snug">
              Selamat Datang,<br />
              <span class="text-sky-600">{{ authUser.name || 'Rekan PLN' }}</span>!
            </h3>

            <!-- Info Profil Ringkas User -->
            <div class="mt-4 p-3 bg-slate-50/90 rounded-2xl border border-slate-100 flex items-center justify-center gap-3 text-xs">
              <div class="w-10 h-10 rounded-xl overflow-hidden bg-sky-100 text-sky-700 flex items-center justify-center font-bold text-xs shrink-0 border border-sky-200">
                <img v-if="authUser.avatar_url || authUser.avatar" :src="authUser.avatar_url || `/storage/${authUser.avatar}`" class="w-full h-full object-cover" />
                <span v-else>{{ (authUser.name || 'U').charAt(0).toUpperCase() }}</span>
              </div>
              <div class="text-left min-w-0">
                <div class="font-bold text-slate-800 truncate text-xs">{{ authUser.name }}</div>
                <div class="flex items-center gap-1.5 text-[11px] text-slate-500 mt-0.5">
                  <span class="inline-flex items-center px-1.5 py-0.2 rounded font-bold text-[10px] bg-sky-100 text-sky-800">
                    {{ authUser.role?.name || roleName || 'STAFF' }}
                  </span>
                  <span class="truncate">{{ authUser.organization_unit?.name || 'PLN UP Sengkang' }}</span>
                </div>
              </div>
            </div>

            <!-- Pesan Motivasi Ringkas -->
            <p class="text-xs text-slate-500 mt-4 leading-relaxed">
              Sistem Koordinasi & Eskalasi UP Sengkang siap mendukung kolaborasi serta keandalan operasi pembangkit hari ini.
            </p>

            <!-- Tombol Aksi "Mulai Bekerja di Dashboard" -->
            <div class="mt-5">
              <button 
                type="button" 
                @click="closeWelcomeModal"
                class="w-full py-3 px-5 rounded-2xl bg-gradient-to-r from-sky-600 via-blue-600 to-sky-700 hover:from-sky-700 hover:to-blue-800 text-white font-bold text-xs shadow-lg shadow-sky-600/25 transition active:scale-[0.98] flex items-center justify-center gap-2 cursor-pointer"
              >
                <span>Mulai Bekerja di Dashboard</span>
                <i class="fas fa-arrow-right text-[11px]"></i>
              </button>
            </div>
          </div>
        </transition>
      </div>
    </transition>

  </AppLayout>
</template>


<script setup>
import { ref, computed, onMounted } from 'vue';
import { router, Link, useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import UserSelect from '@/Components/UserSelect.vue';
import { useToast } from '@/Composables/useToast';

const page = usePage();
const { success: toastSuccess } = useToast();
const authUser = computed(() => page.props.auth?.user || {});

// State Pop-up Welcome Login
const showWelcomeModal = ref(false);

onMounted(() => {
  const fromFlash = page.props.flash?.just_logged_in;
  const fromSession = typeof window !== 'undefined' && sessionStorage.getItem('show_welcome_popup') === 'true';

  if (fromFlash || fromSession) {
    showWelcomeModal.value = true;
    if (typeof window !== 'undefined') {
      sessionStorage.removeItem('show_welcome_popup');
    }
    toastSuccess(`Selamat datang kembali di Sistem Koordinasi UP Sengkang, ${authUser.value.name || 'Rekan PLN'}!`, 'Anda Berhasil Masuk');
  }
});

function closeWelcomeModal() {
  showWelcomeModal.value = false;
}

const props = defineProps({
  roleName: String,
  kpiData: Object,
  manajemenTugas: Object,
  tasks: Array,
  issuesUnderReview: Array,
  escalatedIssues: Array,
  criticalIssues: Array,
  myTasks: Array,
  myTaskStats: Object,
  // Executive Props
  executiveStats: Object,
  departmentMetrics: Array,
  allActiveIssuesUnit: Array,
  criticalOverdueTasks: Array,
  recentMupDecisions: Array,
  upcomingMeetings: Array,
  assignableUsers: Array,
});

// State for MUP Executive Dashboard
const selectedDepartmentId = ref(null);
const issueTab = ref('mup_pending'); // 'mup_pending' | 'all_unit'
const activeExecTab = ref('overdue'); // 'overdue' | 'decisions' | 'meetings'
const searchQuery = ref('');

// Decision Modal State
const decisionModalOpen = ref(false);
const activeIssueForDecision = ref(null);

const decisionForm = useForm({
  decision: '',
  rationale: '',
  follow_up: '',
  mark_resolved: false,
  create_task: false,
  task_assignee_id: '',
  task_due_date: '',
  task_title: '',
});

function openDecisionModal(issue) {
  activeIssueForDecision.value = issue;
  decisionForm.reset();
  decisionForm.decision = '';
  decisionForm.rationale = '';
  decisionForm.follow_up = '';
  decisionForm.mark_resolved = false;
  decisionForm.create_task = false;
  decisionForm.task_assignee_id = '';
  decisionForm.task_due_date = '';
  decisionForm.task_title = `Tindak Lanjut Putusan MUP: ${issue.title}`;
  decisionModalOpen.value = true;
}

function closeDecisionModal() {
  decisionModalOpen.value = false;
  activeIssueForDecision.value = null;
  decisionForm.reset();
}

function submitDecision() {
  if (!activeIssueForDecision.value || decisionForm.processing) return;
  decisionForm.post(`/issues/${activeIssueForDecision.value.id}/decisions`, {
    preserveScroll: true,
    onSuccess: () => {
      closeDecisionModal();
    },
  });
}

const currentSelectedDept = computed(() => {
  if (!selectedDepartmentId.value) return null;
  return (props.departmentMetrics || []).find(d => d.id === selectedDepartmentId.value);
});

function toggleDepartmentFilter(deptId) {
  if (selectedDepartmentId.value === deptId) {
    selectedDepartmentId.value = null;
  } else {
    selectedDepartmentId.value = deptId;
  }
}

const displayedIssues = computed(() => {
  let list = [];
  if (issueTab.value === 'mup_pending') {
    list = props.criticalIssues || [];
  } else {
    list = props.allActiveIssuesUnit || [];
  }

  // Filter by selected department if any
  if (selectedDepartmentId.value && currentSelectedDept.value) {
    const unitIds = currentSelectedDept.value.unit_ids || [];
    list = list.filter(i => unitIds.includes(Number(i.unit_id)));
  }

  // Filter by search query if any
  if (searchQuery.value && searchQuery.value.trim() !== '') {
    const q = searchQuery.value.toLowerCase();
    list = list.filter(i => 
      (i.title && i.title.toLowerCase().includes(q)) ||
      (i.issue_no && i.issue_no.toLowerCase().includes(q)) ||
      (i.reporter?.name && i.reporter.name.toLowerCase().includes(q))
    );
  }

  return list;
});

const displayedOverdueTasks = computed(() => {
  let list = props.criticalOverdueTasks || [];
  if (selectedDepartmentId.value && currentSelectedDept.value) {
    const unitIds = currentSelectedDept.value.unit_ids || [];
    list = list.filter(t => unitIds.includes(Number(t.unit_id)));
  }
  return list;
});

function setSiklus(val) {
  router.get('/dashboard', { siklus: val }, { preserveState: true, preserveScroll: true });
}

function siklusClass(val) {
  const active = (props.manajemenTugas?.siklus_aktif || 'semua') === val;
  if (active) {
    return 'px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-600 text-white shadow-xs transition cursor-pointer';
  }
  return 'px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-700/60 hover:bg-slate-700 text-slate-300 transition cursor-pointer';
}

function percent(count, total) {
  if (!total || total <= 0) return 0;
  return Math.round(((count || 0) / total) * 100);
}

function formatDate(dateStr) {
  if (!dateStr) return '-';
  const d = new Date(dateStr);
  return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
}

function formatDateTime(dateStr) {
  if (!dateStr) return '-';
  const d = new Date(dateStr);
  return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

function daysOverdue(dueDate) {
  if (!dueDate) return 1;
  const due = new Date(dueDate);
  const now = new Date();
  const diffTime = now - due;
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  return Math.max(1, diffDays);
}

function statusBadge(status) {
  const s = String(status || '').toLowerCase();
  if (s.includes('selesai') || s.includes('closed') || s.includes('resolved')) {
    return 'px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200';
  }
  if (s.includes('terlambat') || s.includes('overdue')) {
    return 'px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200';
  }
  if (s.includes('progress')) {
    return 'px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200';
  }
  return 'px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200';
}

function priorityBadge(priority) {
  const p = String(priority || '').toUpperCase();
  if (p === 'CRITICAL' || p === 'HIGH') {
    return 'px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-300';
  }
  if (p === 'MEDIUM') {
    return 'px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300';
  }
  return 'px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300';
}

function healthBadge(health) {
  if (health === 'Kritis') {
    return 'px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-700 border border-rose-300 flex items-center gap-1';
  }
  if (health === 'Perlu Perhatian') {
    return 'px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-700 border border-amber-300 flex items-center gap-1';
  }
  return 'px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-700 border border-emerald-300 flex items-center gap-1';
}
</script>

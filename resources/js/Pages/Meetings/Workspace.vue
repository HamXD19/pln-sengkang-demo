<template>
  <div class="bg-[#f8fafc] flex flex-col h-[100dvh] max-h-[100dvh] overflow-hidden text-sm font-sans antialiased text-gray-800">
    
    <!-- Top Navigation Bar (Anti-Overlap & Clean PLN Theme) -->
    <header class="bg-white border-b border-slate-200 px-3 sm:px-6 py-2 sm:py-2.5 flex items-center justify-between gap-2 sm:gap-3 z-10 shrink-0 shadow-2xs">
      <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-1">
        <Link :href="`/rapat/${meeting.id}`" class="text-slate-400 hover:text-slate-700 p-1 sm:p-1.5 rounded-lg hover:bg-slate-100 transition shrink-0" title="Kembali ke Detail">
          <i class="fas fa-arrow-left text-sm"></i>
        </Link>
        <div class="min-w-0">
          <h2 class="text-xs sm:text-sm font-black text-slate-800 flex items-center gap-1.5 sm:gap-2">
            <span class="truncate max-w-[140px] sm:max-w-xs md:max-w-md" :title="meeting.type">{{ meeting.type }}</span>
            <span class="text-slate-300 font-normal hidden sm:inline">|</span>
            <span class="text-[11px] sm:text-xs font-semibold text-slate-500 hidden sm:inline whitespace-nowrap">
              {{ formatDateFull(meeting.meeting_date) }}
            </span>
          </h2>
          <!-- Subtitle date di layar ponsel -->
          <p class="sm:hidden text-[10px] text-slate-400 font-medium truncate">
            {{ formatDateFull(meeting.meeting_date) }}
          </p>
        </div>

        <span v-if="meeting.status === 'Berlangsung'" class="bg-rose-50 text-rose-700 border border-rose-200 text-[9px] sm:text-[10px] font-bold px-2 py-0.5 rounded-full flex items-center gap-1 shrink-0">
          <span class="w-1.5 h-1.5 bg-rose-500 rounded-full animate-pulse"></span> LIVE
        </span>
        <span v-else class="bg-emerald-50 text-emerald-700 border border-emerald-200 text-[9px] sm:text-[10px] font-bold px-2 py-0.5 rounded-full shrink-0">
          {{ meeting.status }}
        </span>

        <!-- Indikator Kehadiran Real-Time (Desktop) -->
        <div class="hidden lg:flex items-center gap-1.5 bg-emerald-50 border border-emerald-200 text-emerald-800 px-2.5 py-0.5 rounded-full text-[11px] font-semibold shrink-0">
          <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
          <span>Kehadiran: <b class="text-emerald-900">{{ currentStats.total_online || 0 }}</b>/{{ attendees.length }} Hadir</span>
        </div>
      </div>

      <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
        <a 
          :href="`/rapat/${meeting.id}/cetak`" 
          target="_blank" 
          class="border border-blue-200 text-blue-600 hover:bg-blue-50 px-2.5 sm:px-3 py-1.5 rounded-xl text-xs font-semibold flex items-center gap-1.5 transition"
          title="Cetak Notulen PDF"
        >
          <i class="fas fa-print"></i> <span class="hidden sm:inline">Cetak Notulen</span>
        </a>
        <button 
          v-if="meeting.status !== 'Selesai' && canMakeDecision"
          @click="showFinishModal = true" 
          class="bg-blue-600 hover:bg-blue-700 text-white px-2.5 sm:px-4 py-1.5 rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-xs transition cursor-pointer active:scale-95"
        >
          <i class="fas fa-check-double text-xs"></i> <span class="hidden sm:inline">Selesaikan Rapat</span><span class="sm:hidden">Selesai</span>
        </button>
        <Link 
          v-else 
          href="/rapat" 
          class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-1.5 rounded-xl text-xs font-semibold"
        >
          Kembali
        </Link>
      </div>
    </header>

    <!-- Mobile Navigation Tab Bar: Segmented Switcher untuk iOS / Android (< lg) -->
    <div class="lg:hidden bg-slate-950 border-b border-slate-800 p-1.5 flex items-center gap-1.5 shrink-0 z-20 shadow-sm">
      <button 
        type="button" 
        @click="setMobileTab('chat')"
        class="flex-1 py-1.5 px-2 rounded-lg text-xs font-bold transition flex items-center justify-center gap-1.5 cursor-pointer"
        :class="mobileTab === 'chat' ? 'bg-blue-600 text-white shadow-2xs font-extrabold' : 'text-slate-400 hover:text-white'"
      >
        <i class="fas fa-comments text-xs"></i>
        <span>Live Chat</span>
        <span v-if="messagesList && messagesList.length > 0" class="text-[9px] px-1.5 py-0.2 rounded-full font-bold" :class="mobileTab === 'chat' ? 'bg-blue-800 text-white' : 'bg-slate-800 text-slate-300'">
          {{ messagesList.length }}
        </span>
      </button>

      <button 
        type="button" 
        @click="setMobileTab('agenda')"
        class="flex-1 py-1.5 px-2 rounded-lg text-xs font-bold transition flex items-center justify-center gap-1.5 cursor-pointer"
        :class="mobileTab === 'agenda' ? 'bg-blue-600 text-white shadow-2xs font-extrabold' : 'text-slate-400 hover:text-white'"
      >
        <i class="fas fa-tasks text-xs"></i>
        <span>Agenda & Tabel</span>
        <span v-if="activeIssuesList.length + evalTasks.length + plannedTasks.length > 0" class="text-[9px] px-1.5 py-0.2 rounded-full font-bold" :class="mobileTab === 'agenda' ? 'bg-blue-800 text-white' : 'bg-slate-800 text-slate-300'">
          {{ activeIssuesList.length + evalTasks.length + plannedTasks.length }}
        </span>
      </button>

      <button 
        type="button" 
        @click="setMobileTab('participants')"
        class="flex-1 py-1.5 px-2 rounded-lg text-xs font-bold transition flex items-center justify-center gap-1.5 cursor-pointer"
        :class="mobileTab === 'participants' ? 'bg-blue-600 text-white shadow-2xs font-extrabold' : 'text-slate-400 hover:text-white'"
      >
        <i class="fas fa-users-viewfinder text-xs"></i>
        <span>Peserta</span>
        <span class="text-[9px] px-1.5 py-0.2 rounded-full font-bold flex items-center gap-1" :class="mobileTab === 'participants' ? 'bg-blue-800 text-white' : 'bg-slate-800 text-slate-300'">
          <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
          {{ currentStats.total_online || 0 }} Hadir
        </span>
      </button>
    </div>

    <!-- Main Split Workspace -->
    <div class="flex-1 flex flex-col lg:flex-row overflow-hidden min-h-0">
      
      <!-- ========================================== -->
      <!-- SISI KIRI: FORUM CHAT RAPAT & VIDEO HUB    -->
      <!-- ========================================== -->
      <div 
        class="w-full lg:w-[370px] xl:w-[410px] bg-slate-900 flex-col border-b lg:border-b-0 lg:border-r border-slate-800 shrink-0 h-full overflow-hidden"
        :class="[
          mobileTab === 'chat' || mobileTab === 'participants' ? 'flex flex-1' : 'hidden lg:flex'
        ]"
      >
        
        <!-- Tab Bar Sisi Kiri (Hanya Tampil di Desktop >= lg karena Mobile sudah memakai Mobile Segmented Bar) -->
        <div class="hidden lg:flex bg-slate-950 px-3 py-2 border-b border-slate-800 items-center justify-between shrink-0">
          <div class="flex items-center gap-1.5">
            <button 
              type="button" 
              @click="leftTab = 'chat'"
              class="px-2.5 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 cursor-pointer"
              :class="leftTab === 'chat' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-400 hover:text-white'"
            >
              <i class="fas fa-comments text-xs"></i> Live Chat
              <span v-if="messagesList && messagesList.length > 0" class="text-[10px] px-1.5 py-0.2 rounded-full font-bold" :class="leftTab === 'chat' ? 'bg-blue-800 text-white' : 'bg-slate-800 text-slate-300'">
                {{ messagesList.length }}
              </span>
            </button>
            <button 
              type="button" 
              @click="leftTab = 'participants'"
              class="px-2.5 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 cursor-pointer"
              :class="leftTab === 'participants' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-400 hover:text-white'"
            >
              <i class="fas fa-users-viewfinder text-xs"></i> Peserta
              <span class="text-[10px] px-1.5 py-0.5 rounded-full font-bold flex items-center gap-1" :class="leftTab === 'participants' ? 'bg-blue-800 text-white' : 'bg-slate-800 text-slate-300'">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                {{ currentStats.total_online || 0 }} Hadir
              </span>
            </button>
          </div>
          <span class="text-[10px] font-mono text-slate-400 shrink-0">
            {{ meeting.time_start || '08:00' }} - {{ meeting.time_end || '10:00' }}
          </span>
        </div>

        <!-- 1. KONTEN CHAT FORUM RAPAT -->
        <div v-show="leftTab === 'chat'" class="flex-1 flex flex-col overflow-hidden bg-slate-900">
          
          <!-- Notice Info Chat -->
          <div class="px-4 py-2 bg-slate-800/80 border-b border-slate-700/60 flex items-center justify-between text-[11px] text-slate-300">
            <span class="flex items-center gap-1.5">
              <i class="fas fa-bullhorn text-amber-400"></i>
              Forum Rapat Interaktif Staff & Pimpinan
            </span>
            <span class="text-[10px] text-slate-400">Role: <b class="text-blue-300">{{ currentRole }}</b></span>
          </div>

          <!-- Messages Stream -->
          <div ref="chatContainer" class="flex-1 overflow-y-auto p-3 sm:p-4 space-y-3.5 scroll-smooth">
            <template v-if="messagesList && messagesList.length > 0">
              <div 
                v-for="msg in messagesList" 
                :key="msg.id" 
                class="flex flex-col space-y-1.5"
                :class="isMyMessage(msg) ? 'items-end' : 'items-start'"
              >
                <!-- Sender Info -->
                <div class="flex items-center gap-1.5 text-[10px] text-slate-400 px-1">
                  <span class="font-bold text-slate-200">{{ msg.user?.name || 'Karyawan' }}</span>
                  <span class="px-1.5 py-0.2 rounded text-[9px] font-bold" :class="getRoleBadgeClass(msg.user?.role?.name)">
                    {{ msg.user?.role?.name || 'STAFF' }}
                  </span>
                  <span>{{ formatTime(msg.created_at) }}</span>
                </div>

                <!-- Text Bubble -->
                <div 
                  v-if="msg.message" 
                  class="rounded-xl px-3.5 py-2 text-xs leading-relaxed max-w-[85%] break-words shadow-sm"
                  :class="[
                    msg.message.includes('Telah bergabung') 
                      ? 'bg-emerald-950/80 text-emerald-300 border border-emerald-700/80 font-medium' 
                      : (isMyMessage(msg) ? 'bg-blue-600 text-white rounded-tr-none' : 'bg-slate-800 text-slate-100 border border-slate-700 rounded-tl-none')
                  ]"
                >
                  <span v-if="msg.message.includes('Telah bergabung')" class="inline-block w-2 h-2 rounded-full bg-emerald-400 mr-1.5 animate-pulse"></span>
                  {{ msg.message }}
                </div>

                <!-- Embedded Issue Card (Jika Memunculkan Isu) -->
                <div 
                  v-if="msg.issue" 
                  class="w-full max-w-[95%] bg-slate-800/95 border-2 rounded-xl p-3 space-y-2 shadow-lg"
                  :class="msg.issue.priority === 'CRITICAL' ? 'border-red-500/80' : msg.issue.priority === 'HIGH' ? 'border-amber-500/80' : 'border-blue-500/80'"
                >
                  <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-1.5">
                      <span class="bg-rose-950 text-rose-300 border border-rose-800 px-1.5 py-0.5 rounded font-mono text-[10px] font-bold">
                        {{ msg.issue.issue_no }}
                      </span>
                      <span class="text-[10px] font-bold px-1.5 py-0.5 rounded" :class="getPriorityClass(msg.issue.priority)">
                        {{ msg.issue.priority }}
                      </span>
                    </div>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded" :class="getStatusBadgeClass(msg.issue.status)">
                      {{ msg.issue.status }}
                    </span>
                  </div>

                  <h5 class="text-xs font-bold text-white leading-snug">
                    {{ msg.issue.title }}
                  </h5>
                  <p v-if="msg.issue.description" class="text-[11px] text-slate-300 line-clamp-2">
                    {{ msg.issue.description }}
                  </p>

                  <div class="flex items-center justify-between pt-1.5 border-t border-slate-700/80 text-[10px] text-slate-400">
                    <span>Pelapor: <b class="text-slate-200">{{ msg.issue.reporter?.name || 'Staff' }}</b></span>
                    <span>Level: <b class="text-blue-300">{{ msg.issue.current_level }}</b></span>
                  </div>

                  <!-- Aksi Ambil Keputusan untuk Pimpinan (TL, ASMAN, MUP) -->
                  <div v-if="canMakeDecision" class="pt-1">
                    <button 
                      v-if="canDecideIssue(msg.issue)"
                      type="button" 
                      @click="openDecisionModal(msg.issue)"
                      class="w-full bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold py-1.5 px-3 rounded-lg shadow transition flex items-center justify-center gap-1.5 cursor-pointer"
                    >
                      <i class="fas fa-gavel"></i> Bahas & Ambil Keputusan
                    </button>
                    <div 
                      v-else-if="msg.issue.status === 'Escalated'" 
                      class="w-full bg-purple-950/70 border border-purple-800 text-purple-300 text-[11px] font-semibold py-1.5 px-2.5 rounded-lg flex items-center justify-center gap-1.5"
                    >
                      <i class="fas fa-arrow-circle-up text-purple-400"></i>
                      <span>Dieskalasikan ke Jenjang {{ msg.issue.current_level }}</span>
                    </div>
                    <div 
                      v-else-if="['Decided', 'Resolved', 'Closed'].includes(msg.issue.status)" 
                      class="w-full bg-emerald-950/70 border border-emerald-800 text-emerald-300 text-[11px] font-semibold py-1.5 px-2.5 rounded-lg flex items-center justify-center gap-1.5"
                    >
                      <i class="fas fa-check-circle text-emerald-400"></i>
                      <span>Status Isu: {{ msg.issue.status }}</span>
                    </div>
                  </div>
                </div>

              </div>
            </template>
            <div v-else class="text-center py-12 text-slate-500 text-xs">
              <i class="fas fa-comments text-3xl mb-2 block text-slate-600"></i>
              Belum ada percakapan dalam rapat ini.<br />
              <span v-if="isStaff && isStaffTlMeeting">Staff dapat menekan tombol <b>"+ Munculkan Isu Saya"</b> di bawah.</span>
              <span v-else-if="meetingLevel === 'ASMAN'">TL / Asman dapat menekan tombol <b>"+ Munculkan Isu Eskalasi ke Forum"</b> di bawah untuk membawa eskalasi ke meja rapat.</span>
              <span v-else-if="isStaffTlMeeting">Menunggu staf memunculkan kendala lapangan atau koordinasikan via chat.</span>
              <span v-else>Forum rapat koordinasi pimpinan: Bahas progres Task dan Eskalasi via chat.</span>
            </div>
          </div>

          <!-- Bottom Action Bar (Safe Area & Anti Auto-Zoom Mobile) -->
          <div class="p-3 bg-slate-950 border-t border-slate-800 space-y-2 shrink-0 pb-[max(0.75rem,env(safe-area-inset-bottom))]">
            <!-- Tombol Munculkan Isu untuk Staf di Rapat TL, ATAU Tombol Munculkan Eskalasi untuk TL/Asman di Rapat Asman -->
            <div v-if="(isStaff && isStaffTlMeeting) || (['TL', 'ASMAN', 'ADMIN', 'SUPER_ADMIN'].includes(currentRole) && meetingLevel === 'ASMAN')" class="flex items-center justify-between gap-2">
              <button 
                type="button" 
                @click="showSurfaceIssueModal = true"
                class="w-full text-xs font-bold py-1.5 px-3 rounded-lg shadow transition flex items-center justify-center gap-1.5 cursor-pointer"
                :class="meetingLevel === 'ASMAN' ? 'bg-indigo-600 hover:bg-indigo-700 text-white' : 'bg-amber-500 hover:bg-amber-600 text-slate-950'"
              >
                <i :class="meetingLevel === 'ASMAN' ? 'fas fa-level-up-alt' : 'fas fa-exclamation-triangle'"></i>
                <span>{{ meetingLevel === 'ASMAN' ? '+ Munculkan Isu Eskalasi ke Forum' : '+ Munculkan Isu Saya ke Forum' }}</span>
              </button>
            </div>

            <!-- Form Kirim Pesan Cepat -->
            <form @submit.prevent="sendMessage" class="flex items-center gap-2">
              <input 
                type="text" 
                v-model="chatInput" 
                @keydown.enter.prevent="sendMessage"
                placeholder="Ketik catatan / tanggapan rapat..."
                enterkeyhint="send"
                autocomplete="off"
                class="flex-1 bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-base sm:text-xs text-white placeholder-slate-400 outline-none focus:border-blue-500"
              />
              <button 
                type="submit" 
                :disabled="isSending || !chatInput || !chatInput.trim()"
                class="bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none disabled:cursor-not-allowed text-white px-3.5 py-2 rounded-lg text-xs font-bold cursor-pointer transition shrink-0 flex items-center justify-center min-w-[38px] min-h-[34px]"
                title="Kirim Catatan Rapat"
              >
                <i v-if="isSending" class="fas fa-spinner fa-spin text-xs"></i>
                <i v-else class="fas fa-paper-plane"></i>
              </button>
            </form>
          </div>

        </div>

        <!-- 2. KONTEN VIDEO & ROSTER PESERTA -->
        <div v-show="leftTab === 'participants'" class="flex-1 flex flex-col overflow-y-auto p-4 space-y-4 bg-slate-900">
          <!-- Panel Zoom Hub -->
          <div class="w-full bg-slate-800/90 rounded-2xl p-4 border border-slate-700 shadow-xl space-y-3 shrink-0">
            <div class="flex items-center justify-between pb-2 border-b border-slate-700">
              <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full" :class="meeting.status === 'Berlangsung' ? 'bg-red-500 animate-pulse' : 'bg-emerald-400'"></span>
                <h3 class="text-xs font-bold text-white uppercase tracking-wider">
                  {{ meeting.meeting_mode || 'Online (Zoom)' }}
                </h3>
              </div>
              <span class="text-[10px] bg-blue-900/60 text-blue-300 font-semibold px-2 py-0.5 rounded border border-blue-700">
                Pimpinan: {{ meeting.leader?.name || 'Leader' }}
              </span>
            </div>

            <div v-if="meeting.zoom_link" class="space-y-2">
              <a 
                :href="meeting.zoom_link" 
                target="_blank" 
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-xl text-xs flex items-center justify-center gap-2 shadow-lg transition"
              >
                <i class="fas fa-video text-sm"></i> Buka Zoom Video Meeting
              </a>
              <div class="grid grid-cols-2 gap-2 text-[11px] text-gray-300 bg-slate-900/70 p-2 rounded-lg border border-slate-700">
                <div>
                  <span class="text-gray-400 block text-[10px]">Meeting ID</span>
                  <span class="font-mono font-bold text-white">{{ meeting.zoom_meeting_id || '-' }}</span>
                </div>
                <div>
                  <span class="text-gray-400 block text-[10px]">Passcode</span>
                  <span class="font-mono font-bold text-white">{{ meeting.zoom_passcode || '-' }}</span>
                </div>
              </div>
            </div>
            <div v-else class="text-xs text-gray-300 text-center py-2 bg-slate-900/50 rounded-lg p-3">
              <i class="fas fa-building text-blue-400 mr-1.5"></i> Rapat Tatap Muka di {{ meeting.location || 'Ruang Rapat UP Sengkang' }}
            </div>
          </div>

          <!-- Roster Presensi Online Peserta -->
          <div class="space-y-3 flex-1 flex flex-col min-h-0">
            <!-- Header Ringkasan Kehadiran Real-time -->
            <div class="bg-slate-800/90 rounded-xl p-3 border border-slate-700/80 space-y-2 shrink-0">
              <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-white flex items-center gap-1.5">
                  <i class="fas fa-signal text-emerald-400"></i> Presensi Real-Time Ruang Rapat
                </span>
                <span class="text-[10px] text-emerald-400 bg-emerald-950/80 border border-emerald-800 px-2 py-0.5 rounded-full font-bold flex items-center gap-1">
                  <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span> Live Sync
                </span>
              </div>
              <p class="text-[11px] text-slate-300 leading-relaxed">
                <span v-if="canMakeDecision" class="text-blue-300 font-semibold">Monitoring Pimpinan:</span>
                Informasi presensi real-time agar pimpinan mengetahui seluruh peserta yang telah hadir di ruang rapat.
              </p>
              <!-- 3 Counter Bar -->
              <div class="grid grid-cols-3 gap-1.5 pt-1 text-center text-xs">
                <div class="bg-slate-900/80 p-2 rounded-lg border border-slate-700">
                  <span class="text-slate-400 block text-[10px]">Total Peserta</span>
                  <span class="font-bold text-white text-xs">{{ attendees.length }}</span>
                </div>
                <div class="bg-emerald-950/50 p-2 rounded-lg border border-emerald-800/70">
                  <span class="text-emerald-400 block text-[10px]">Peserta Hadir</span>
                  <span class="font-bold text-emerald-300 text-xs">{{ currentStats.total_online || 0 }}</span>
                </div>
                <div class="bg-slate-900/80 p-2 rounded-lg border border-slate-700">
                  <span class="text-slate-400 block text-[10px]">Belum Hadir</span>
                  <span class="font-bold text-slate-300 text-xs">{{ Math.max(0, attendees.length - (currentStats.total_online || 0)) }}</span>
                </div>
              </div>
            </div>

            <!-- Filter Kehadiran -->
            <div class="flex items-center gap-1 bg-slate-950 p-1 rounded-lg border border-slate-800 text-[11px] shrink-0">
              <button 
                type="button" 
                @click="participantFilter = 'all'"
                class="flex-1 py-1 rounded text-center font-bold transition cursor-pointer"
                :class="participantFilter === 'all' ? 'bg-slate-800 text-white shadow-xs' : 'text-slate-400 hover:text-slate-200'"
              >
                Semua ({{ attendees.length }})
              </button>
              <button 
                type="button" 
                @click="participantFilter = 'online'"
                class="flex-1 py-1 rounded text-center font-bold transition cursor-pointer flex items-center justify-center gap-1"
                :class="participantFilter === 'online' ? 'bg-emerald-800 text-emerald-100 shadow-xs' : 'text-slate-400 hover:text-slate-200'"
              >
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Hadir ({{ currentStats.total_online || 0 }})
              </button>
              <button 
                type="button" 
                @click="participantFilter = 'offline'"
                class="flex-1 py-1 rounded text-center font-bold transition cursor-pointer"
                :class="participantFilter === 'offline' ? 'bg-slate-800 text-slate-200 shadow-xs' : 'text-slate-400 hover:text-slate-200'"
              >
                Belum Hadir ({{ Math.max(0, attendees.length - (currentStats.total_online || 0)) }})
              </button>
            </div>

            <!-- List Peserta -->
            <div class="space-y-1.5 flex-1 overflow-y-auto pr-1">
              <template v-if="filteredParticipants.length > 0">
                <div 
                  v-for="u in filteredParticipants" 
                  :key="u.id" 
                  class="flex items-center justify-between p-2.5 rounded-xl border transition"
                  :class="u.is_online ? 'bg-slate-800/90 border-emerald-700/60 shadow-xs' : 'bg-slate-800/40 border-slate-700/40 opacity-80'"
                >
                  <div class="flex items-center gap-2.5 min-w-0">
                    <div class="relative shrink-0">
                      <div class="w-8 h-8 rounded-full bg-blue-600 text-white font-bold text-[10px] flex items-center justify-center overflow-hidden">
                        <img v-if="u.avatar" :src="`/storage/${u.avatar}`" class="w-full h-full object-cover" />
                        <span v-else>{{ (u.name || 'U').substring(0, 2).toUpperCase() }}</span>
                      </div>
                      <!-- Live Indicator Dot on Avatar -->
                      <span 
                        v-if="u.is_online" 
                        class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-emerald-400 border-2 border-slate-900 rounded-full"
                        title="Hadir di Ruang Rapat"
                      ></span>
                      <span 
                        v-else 
                        class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-slate-500 border-2 border-slate-900 rounded-full"
                        title="Belum Hadir"
                      ></span>
                    </div>

                    <div class="min-w-0">
                      <div class="flex items-center gap-1.5 flex-wrap">
                        <p class="text-xs font-semibold text-white truncate max-w-[150px]">{{ u.name }}</p>
                        <span class="text-[9px] font-mono px-1.5 py-0.2 rounded border shrink-0" :class="getRoleBadgeClass(u.role)">
                          {{ getUserRoleName(u) }}
                        </span>
                      </div>
                      <p class="text-[10px] text-slate-400 truncate">
                        <span v-if="u.unit" class="text-slate-300">{{ u.unit }} &bull; </span>
                        <span>{{ u.employee_no || '-' }}</span>
                      </p>
                    </div>
                  </div>

                  <div class="text-right shrink-0 ml-2">
                    <template v-if="u.is_online">
                      <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-300 bg-emerald-950 px-2 py-0.5 rounded border border-emerald-800">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Hadir
                      </span>
                      <span class="text-[9px] text-emerald-400/90 block mt-0.5">
                        {{ u.joined_at ? 'Masuk ' + u.joined_at : 'Aktif sekarang' }}
                      </span>
                    </template>
                    <template v-else-if="u.is_joined">
                      <span class="inline-block text-[10px] font-semibold text-amber-300 bg-amber-950/60 px-2 py-0.5 rounded border border-amber-800/80">
                        Telah Bergabung
                      </span>
                      <span class="text-[9px] text-slate-400 block mt-0.5">
                        {{ u.last_seen || 'offline' }}
                      </span>
                    </template>
                    <template v-else>
                      <span class="inline-block text-[10px] font-normal text-slate-400 bg-slate-900 px-2 py-0.5 rounded border border-slate-800">
                        Belum Masuk
                      </span>
                    </template>
                  </div>
                </div>
              </template>
              <div v-else class="text-center py-6 text-slate-500 text-xs">
                Tidak ada peserta pada filter ini.
              </div>
            </div>
          </div>
        </div>

      </div>

      <!-- ============================================================ -->
      <!-- SISI KANAN: 3 TABEL TERPISAH (TABEL ISSUE, TASK, & ESKALASI)  -->
      <!-- ============================================================ -->
      <div 
        class="flex-1 flex-col bg-white overflow-hidden h-full min-h-0"
        :class="mobileTab === 'agenda' ? 'flex' : 'hidden lg:flex'"
      >
        
        <!-- Tab Selector Bar (Harmonized PLN Corporate Blue Theme) -->
        <div class="bg-white border-b border-slate-200 px-4 sm:px-6 pt-3 flex items-center justify-between gap-4 overflow-x-auto shrink-0">
          <div class="flex gap-4 sm:gap-6 whitespace-nowrap text-xs font-semibold">
            <!-- TAB 1: TABEL ISSUE (HANYA TAMPIL DI RUANG RAPAT STAFF DAN TL) -->
            <button 
              v-if="isStaffTlMeeting"
              type="button" 
              @click="activeAgendaTab = 'issues'"
              :class="activeAgendaTab === 'issues' ? 'border-b-2 border-blue-600 text-blue-600 font-bold pb-3' : 'text-slate-500 hover:text-slate-800 pb-3'"
              class="flex items-center gap-1.5 cursor-pointer transition"
            >
              <i class="fas fa-exclamation-circle text-amber-500"></i>
              <span>Tabel Issue (Staf)</span>
              <span v-if="activeIssuesList.length > 0" class="bg-amber-100 text-amber-800 text-[10px] px-1.5 py-0.2 rounded-full font-bold ml-0.5">
                {{ activeIssuesList.length }}
              </span>
              <span v-if="archivedIssuesList.length > 0" class="bg-slate-100 text-slate-500 text-[9px] px-1.5 py-0.2 rounded-full font-medium ml-0.5" title="Arsip Isu (Sudah Ditindaklanjuti)">
                {{ archivedIssuesList.length }} arsip
              </span>
            </button>

            <!-- TAB 2: TABEL TASK -->
            <button 
              type="button" 
              @click="activeAgendaTab = 'tasks'"
              :class="activeAgendaTab === 'tasks' ? 'border-b-2 border-blue-600 text-blue-600 font-bold pb-3' : 'text-slate-500 hover:text-slate-800 pb-3'"
              class="flex items-center gap-1.5 cursor-pointer transition"
            >
              <i class="fas fa-tasks text-blue-600"></i>
              <span>Tabel Task (TL & Asman)</span>
              <span v-if="evalTasks.length + plannedTasks.length > 0" class="bg-slate-100 text-slate-700 text-[10px] px-1.5 py-0.2 rounded-full font-bold ml-0.5">
                {{ evalTasks.length + plannedTasks.length }}
              </span>
            </button>

            <!-- TAB 3: TABEL ESKALASI -->
            <button 
              type="button" 
              @click="activeAgendaTab = 'escalations'"
              :class="activeAgendaTab === 'escalations' ? 'border-b-2 border-indigo-600 text-indigo-600 font-bold pb-3' : 'text-slate-500 hover:text-slate-800 pb-3'"
              class="flex items-center gap-1.5 cursor-pointer transition relative"
            >
              <i class="fas fa-level-up-alt text-indigo-600"></i>
              <span>Tabel Eskalasi</span>
              <span v-if="escalations && escalations.length > 0" class="bg-indigo-600 text-white text-[10px] px-2 py-0.5 rounded-full font-black ml-0.5 shadow-xs">
                {{ escalations.length }}
              </span>
            </button>

            <!-- TAB 4: ARAHAN & REKAP KEPUTUSAN -->
            <button 
              type="button" 
              @click="activeAgendaTab = 'decisions'"
              :class="activeAgendaTab === 'decisions' ? 'border-b-2 border-blue-600 text-blue-600 font-bold pb-3' : 'text-slate-500 hover:text-slate-800 pb-3'"
              class="flex items-center gap-1.5 cursor-pointer transition"
            >
              <i class="fas fa-gavel text-emerald-600"></i>
              <span>Putusan & Arahan Rapat</span>
              <span v-if="decisionsList && decisionsList.length > 0" class="bg-slate-100 text-slate-700 text-[10px] px-1.5 py-0.2 rounded-full font-bold ml-0.5">
                {{ decisionsList.length }}
              </span>
            </button>
          </div>
        </div>

        <!-- ========================================== -->
        <!-- ISI MASING-MASING TABEL                    -->
        <!-- ========================================== -->
        <div class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-4">
          
          <!-- ============================================== -->
          <!-- 1. TABEL ISSUE (HANYA UNTUK RAPAT STAF & TL)   -->
          <!-- ============================================== -->
          <div v-if="activeAgendaTab === 'issues' && isStaffTlMeeting" class="space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-amber-50/60 border border-amber-200 p-4 rounded-xl">
              <div>
                <h3 class="text-sm font-bold text-amber-900 flex items-center gap-2">
                  <i class="fas fa-exclamation-circle text-amber-600"></i>
                  Tabel Issue Kendala Lapangan (Staff Level)
                </h3>
                <p class="text-xs text-amber-800/80 mt-0.5">
                  Isu kendala teknis & non-teknis dicatat oleh staf dan dimunculkan ke forum rapat untuk dibahas bersama pimpinan.
                </p>
              </div>
              <button 
                v-if="isStaff"
                type="button" 
                @click="showSurfaceIssueModal = true"
                class="px-3.5 py-1.5 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-xs font-bold transition flex items-center gap-1.5 shadow-xs shrink-0 cursor-pointer"
              >
                <i class="fas fa-plus"></i> Munculkan Isu Baru
              </button>
              <div v-else class="text-[11px] bg-amber-100/80 border border-amber-300 text-amber-900 px-3 py-1.5 rounded-lg font-medium">
                <i class="fas fa-user-shield text-amber-700 mr-1"></i>
                Peran {{ currentRole }}: Membahas issue staf & menetapkan keputusan.
              </div>
            </div>

            <!-- Segmented Sub-Tab Switcher: Isu Aktif vs Arsip Isu -->
            <div class="flex items-center gap-2 bg-slate-100 p-1 rounded-xl w-fit border border-slate-200 text-xs">
              <button 
                type="button" 
                @click="workspaceIssueTab = 'active'"
                :class="workspaceIssueTab === 'active' 
                  ? 'bg-white text-amber-800 shadow-xs font-bold' 
                  : 'text-slate-600 hover:text-slate-900 font-medium'"
                class="px-3 py-1.5 rounded-lg transition flex items-center gap-2 cursor-pointer"
              >
                <i class="fas fa-bolt text-amber-500"></i>
                <span>Isu Aktif / Perlu Dibahas</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold"
                  :class="workspaceIssueTab === 'active' ? 'bg-amber-100 text-amber-800' : 'bg-slate-200 text-slate-700'">
                  {{ activeIssuesList.length }}
                </span>
              </button>

              <button 
                type="button" 
                @click="workspaceIssueTab = 'archived'"
                :class="workspaceIssueTab === 'archived' 
                  ? 'bg-white text-slate-800 shadow-xs font-bold' 
                  : 'text-slate-600 hover:text-slate-900 font-medium'"
                class="px-3 py-1.5 rounded-lg transition flex items-center gap-2 cursor-pointer"
              >
                <i class="fas fa-archive text-slate-500"></i>
                <span>Arsip Isu (Task / Eskalasi / Selesai)</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold"
                  :class="workspaceIssueTab === 'archived' ? 'bg-slate-200 text-slate-800' : 'bg-slate-200 text-slate-700'">
                  {{ archivedIssuesList.length }}
                </span>
              </button>
            </div>

            <!-- VIEW 1: TABEL ISU AKTIF -->
            <div v-if="workspaceIssueTab === 'active'" class="border border-gray-200 rounded-xl overflow-hidden shadow-xs bg-white">
              <div class="overflow-x-auto">
                <table class="w-full text-left text-xs whitespace-nowrap">
                  <thead class="bg-slate-50 border-b border-gray-200 text-gray-600 font-bold uppercase tracking-wider text-[11px]">
                    <tr>
                      <th class="px-4 py-3 w-10 text-center">No</th>
                      <th class="px-4 py-3">Kode & Judul Issue</th>
                      <th class="px-4 py-3">Pelapor (Staf)</th>
                      <th class="px-4 py-3">Level Isu</th>
                      <th class="px-4 py-3">Prioritas</th>
                      <th class="px-4 py-3">Status Agenda</th>
                      <th class="px-4 py-3 text-center">Aksi Forum</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-100 text-gray-700 font-medium">
                    <template v-if="activeIssuesList && activeIssuesList.length > 0">
                      <tr v-for="(iss, idx) in activeIssuesList" :key="iss.id" class="hover:bg-amber-50/30 transition">
                        <td class="px-4 py-3 text-center font-bold text-gray-400">{{ idx + 1 }}</td>
                        <td class="px-4 py-3">
                          <Link :href="`/issues/${iss.id}`" class="font-bold text-gray-800 hover:text-blue-600 block">
                            {{ iss.title }}
                          </Link>
                          <span class="text-[10px] font-mono text-gray-400">{{ iss.issue_no }}</span>
                        </td>
                        <td class="px-4 py-3">
                          <div class="font-semibold text-gray-800">{{ iss.reporter?.name || 'Staff' }}</div>
                          <div class="text-[10px] text-gray-400">{{ iss.organization_unit?.name || 'Unit Operasi' }}</div>
                        </td>
                        <td class="px-4 py-3">
                          <span class="bg-blue-50 text-blue-700 border border-blue-200 px-2 py-0.5 rounded text-[10px] font-bold">
                            Level {{ iss.current_level }}
                          </span>
                        </td>
                        <td class="px-4 py-3">
                          <span class="px-2 py-0.5 rounded text-[10px] font-bold" :class="getPriorityClass(iss.priority)">
                            {{ iss.priority }}
                          </span>
                        </td>
                        <td class="px-4 py-3">
                          <span class="px-2 py-0.5 rounded text-[10px] font-bold" :class="getStatusBadgeClass(iss.status)">
                            {{ iss.status }}
                          </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                          <div class="flex items-center justify-center gap-1.5">
                            <button 
                              v-if="canDecideIssue(iss)"
                              type="button" 
                              @click="openDecisionModal(iss)"
                              class="bg-emerald-600 hover:bg-emerald-700 text-white px-2.5 py-1 rounded text-[11px] font-bold shadow-xs transition flex items-center gap-1 cursor-pointer"
                              title="Ambil Keputusan di Rapat"
                            >
                              <i class="fas fa-gavel"></i> Ambil Keputusan
                            </button>
                            <Link 
                              :href="`/issues/${iss.id}`"
                              class="border border-gray-300 text-gray-600 hover:bg-gray-50 px-2 py-1 rounded text-[11px] font-semibold"
                            >
                              Detail
                            </Link>
                          </div>
                        </td>
                      </tr>
                    </template>
                    <tr v-else>
                      <td colspan="7" class="text-center py-10 text-gray-400 text-xs">
                        <i class="fas fa-check-circle text-emerald-500 text-2xl mb-2 block"></i>
                        Semua issue staf telah ditindaklanjuti atau belum ada kendala aktif baru.
                        <div class="mt-1 text-slate-500">
                          Issue yang telah diubah menjadi Task, dieskalasi, atau diputuskan dapat dilihat pada tab <strong>Arsip Isu</strong>.
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- VIEW 2: TABEL ARSIP ISU (SUDAH JADI TASK / ESKALASI / DIPUTUSKAN) -->
            <div v-else class="border border-gray-200 rounded-xl overflow-hidden shadow-xs bg-white">
              <div class="overflow-x-auto">
                <table class="w-full text-left text-xs whitespace-nowrap">
                  <thead class="bg-slate-50 border-b border-gray-200 text-gray-600 font-bold uppercase tracking-wider text-[11px]">
                    <tr>
                      <th class="px-4 py-3 w-10 text-center">No</th>
                      <th class="px-4 py-3">Kode & Judul Issue</th>
                      <th class="px-4 py-3">Pelapor</th>
                      <th class="px-4 py-3">Bentuk Tindak Lanjut</th>
                      <th class="px-4 py-3">Status Isu</th>
                      <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-100 text-gray-700 font-medium">
                    <template v-if="archivedIssuesList && archivedIssuesList.length > 0">
                      <tr v-for="(iss, idx) in archivedIssuesList" :key="iss.id" class="hover:bg-slate-50/60 transition">
                        <td class="px-4 py-3 text-center font-bold text-gray-400">{{ idx + 1 }}</td>
                        <td class="px-4 py-3">
                          <Link :href="`/issues/${iss.id}`" class="font-bold text-gray-800 hover:text-blue-600 block">
                            {{ iss.title }}
                          </Link>
                          <span class="text-[10px] font-mono text-gray-400">{{ iss.issue_no }}</span>
                        </td>
                        <td class="px-4 py-3">
                          <div class="font-semibold text-gray-800">{{ iss.reporter?.name || 'Staff' }}</div>
                          <div class="text-[10px] text-gray-400">{{ iss.organization_unit?.name || 'Unit Operasi' }}</div>
                        </td>
                        <td class="px-4 py-3">
                          <div class="space-y-1">
                            <!-- Badge Task -->
                            <div v-if="iss.tasks && iss.tasks.length > 0" class="flex items-center gap-1 flex-wrap">
                              <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-0.5 rounded-md">
                                <i class="fas fa-tasks text-emerald-600 text-[9px]"></i>
                                <span>Task: {{ iss.tasks[0]?.title }} ({{ iss.tasks[0]?.progress_percent ?? 0 }}%)</span>
                              </span>
                            </div>
                            <!-- Badge Eskalasi -->
                            <div v-else-if="iss.escalations && iss.escalations.length > 0" class="flex items-center gap-1 flex-wrap">
                              <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200 px-2 py-0.5 rounded-md">
                                <i class="fas fa-level-up-alt text-purple-600 text-[9px]"></i>
                                <span>Eskalasi: {{ iss.escalations[0]?.from_level }} &rarr; {{ iss.escalations[0]?.to_level }} ({{ iss.escalations[0]?.status }})</span>
                              </span>
                            </div>
                            <!-- Badge Putusan Rapat -->
                            <div v-else-if="iss.decisions && iss.decisions.length > 0" class="flex items-center gap-1 flex-wrap">
                              <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-sky-50 text-sky-700 border border-sky-200 px-2 py-0.5 rounded-md" :title="iss.decisions[0]?.decision">
                                <i class="fas fa-gavel text-sky-600 text-[9px]"></i>
                                <span class="line-clamp-1 max-w-[200px]">Putusan: {{ iss.decisions[0]?.decision }}</span>
                              </span>
                            </div>
                            <div v-else class="text-[10px] text-slate-400 italic">
                              Ditutup / Selesai Langsung
                            </div>
                          </div>
                        </td>
                        <td class="px-4 py-3">
                          <span class="px-2 py-0.5 rounded text-[10px] font-bold" :class="getStatusBadgeClass(iss.status)">
                            {{ iss.status }}
                          </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                          <Link 
                            :href="`/issues/${iss.id}`"
                            class="border border-gray-300 text-gray-600 hover:bg-gray-50 px-2.5 py-1 rounded text-[11px] font-semibold"
                          >
                            <i class="fas fa-eye mr-1"></i> Detail
                          </Link>
                        </td>
                      </tr>
                    </template>
                    <tr v-else>
                      <td colspan="6" class="text-center py-10 text-gray-400 text-xs">
                        <i class="fas fa-archive text-slate-300 text-2xl mb-2 block"></i>
                        Belum ada arsip issue yang telah ditindaklanjuti.
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- ============================================== -->
          <!-- 2. TABEL TASK (DIBAWA OLEH TL & ASMAN)          -->
          <!-- ============================================== -->
          <div v-if="activeAgendaTab === 'tasks'" class="space-y-4">
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-blue-50/60 border border-blue-200 p-4 rounded-xl">
              <div>
                <h3 class="text-sm font-bold text-blue-950 flex items-center gap-2">
                  <i class="fas fa-tasks text-blue-600"></i>
                  Tabel Task & Action Items (TL & Asman Scope)
                </h3>
                <p class="text-xs text-blue-900/80 mt-0.5">
                  Evaluasi pencapaian Task minggu lalu (N-1) dan perencanaan komitmen Task minggu berjalan (N).
                </p>
              </div>
              <div class="flex items-center gap-2">
                <button 
                  type="button" 
                  @click="taskSubTab = 'eval'"
                  class="px-3 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer"
                  :class="taskSubTab === 'eval' ? 'bg-blue-600 text-white' : 'bg-white text-gray-600 border border-gray-200'"
                >
                  Evaluasi Task N-1 ({{ evalTasks.length }})
                </button>
                <button 
                  type="button" 
                  @click="taskSubTab = 'planned'"
                  class="px-3 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer"
                  :class="taskSubTab === 'planned' ? 'bg-blue-600 text-white' : 'bg-white text-gray-600 border border-gray-200'"
                >
                  Rencana Task Minggu N ({{ plannedTasks.length }})
                </button>
                <button 
                  type="button" 
                  @click="showAddTaskModal = true"
                  class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition flex items-center gap-1 shadow-xs cursor-pointer ml-1"
                >
                  <i class="fas fa-plus"></i> + Tambah Task
                </button>
              </div>
            </div>

            <!-- Tabel Task Content -->
            <div class="border border-gray-200 rounded-xl overflow-hidden shadow-xs bg-white">
              <div class="overflow-x-auto">
                <table class="w-full text-left text-xs whitespace-nowrap">
                  <thead class="bg-slate-50 border-b border-gray-200 text-gray-600 font-bold uppercase tracking-wider text-[11px]">
                    <tr>
                      <th class="px-4 py-3 w-10 text-center">No</th>
                      <th class="px-4 py-3">Uraian Task</th>
                      <th class="px-4 py-3">PIC Pelaksana</th>
                      <th class="px-4 py-3">Target Selesai (Due Date)</th>
                      <th class="px-4 py-3 text-center">Progress</th>
                      <th class="px-4 py-3">Status</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-100 text-gray-700 font-medium">
                    <template v-if="currentDisplayedTasks && currentDisplayedTasks.length > 0">
                      <tr v-for="(tsk, idx) in currentDisplayedTasks" :key="tsk.id" class="hover:bg-blue-50/30 transition">
                        <td class="px-4 py-3 text-center font-bold text-gray-400">{{ idx + 1 }}</td>
                        <td class="px-4 py-3">
                          <div class="font-bold text-gray-800">{{ tsk.title }}</div>
                          <div v-if="tsk.description" class="text-[10px] text-gray-400 truncate max-w-xs">{{ tsk.description }}</div>
                        </td>
                        <td class="px-4 py-3">
                          <div class="font-semibold text-gray-800">{{ tsk.assignee?.name || 'Belum Ditugaskan' }}</div>
                          <div class="text-[10px] text-gray-400">{{ tsk.assignee?.role?.name || 'Staff' }}</div>
                        </td>
                        <td class="px-4 py-3 font-mono">
                          <span :class="isTaskOverdue(tsk) ? 'text-red-600 font-bold' : 'text-gray-600'">
                            {{ tsk.due_date ? formatDateOnly(tsk.due_date) : '-' }}
                          </span>
                          <span v-if="isTaskOverdue(tsk)" class="ml-1 text-[9px] bg-red-100 text-red-700 px-1.5 py-0.2 rounded font-bold">
                            OVERDUE
                          </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                          <div class="flex items-center justify-center gap-2">
                            <div class="w-16 bg-gray-200 rounded-full h-1.5">
                              <div class="bg-blue-600 h-1.5 rounded-full" :style="`width: ${tsk.progress_percent || 0}%`"></div>
                            </div>
                            <span class="font-bold text-gray-800 text-[11px]">{{ tsk.progress_percent || 0 }}%</span>
                          </div>
                        </td>
                        <td class="px-4 py-3">
                          <span class="px-2 py-0.5 rounded text-[10px] font-bold" :class="getTaskStatusClass(tsk.status)">
                            {{ tsk.status }}
                          </span>
                        </td>
                      </tr>
                    </template>
                    <tr v-else>
                      <td colspan="6" class="text-center py-10 text-gray-400 text-xs">
                        Tidak ada data Task pada filter ini. Gunakan tombol '+ Tambah Task' untuk menambah komitmen minggu berjalan.
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

          </div>

          <!-- ============================================== -->
          <!-- 3. TABEL ESKALASI (BERJENJANG TL -> ASMAN -> MUP) -->
          <!-- ============================================== -->
          <div v-if="activeAgendaTab === 'escalations'" class="space-y-4">
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-indigo-50/60 border border-indigo-200 p-4 rounded-xl">
              <div>
                <h3 class="text-sm font-bold text-indigo-950 flex items-center gap-2">
                  <i class="fas fa-level-up-alt text-indigo-600"></i>
                  Tabel Eskalasi Berjenjang (TL & Asman Escalations)
                </h3>
                <p class="text-xs text-indigo-900/80 mt-0.5">
                  Isu-isu yang membutuhkan kewenangan atau alokasi sumber daya di jenjang atas (TL membawa ke Asman, Asman membawa ke MUP).
                </p>
              </div>
              <span class="text-xs font-bold text-indigo-800 bg-indigo-100 px-3 py-1.5 rounded-lg border border-indigo-200">
                Jenjang Rapat: Level {{ meetingLevel }}
              </span>
            </div>

            <!-- Tabel Eskalasi Content -->
            <div class="border border-gray-200 rounded-xl overflow-hidden shadow-xs bg-white">
              <div class="overflow-x-auto">
                <table class="w-full text-left text-xs whitespace-nowrap">
                  <thead class="bg-slate-50 border-b border-gray-200 text-gray-600 font-bold uppercase tracking-wider text-[11px]">
                    <tr>
                      <th class="px-4 py-3 w-10 text-center">No</th>
                      <th class="px-4 py-3">Kode & Judul Isu Terkait</th>
                      <th class="px-4 py-3">Alur Eskalasi</th>
                      <th class="px-4 py-3">Alasan Eskalasi</th>
                      <th class="px-4 py-3">Pengeskalasi</th>
                      <th class="px-4 py-3">Status Eskalasi</th>
                      <th class="px-4 py-3 text-center">Aksi Forum</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-100 text-gray-700 font-medium">
                    <template v-if="escalations && escalations.length > 0">
                      <tr v-for="(esc, idx) in escalations" :key="esc.id" class="hover:bg-indigo-50/30 transition">
                        <td class="px-4 py-3 text-center font-bold text-gray-400">{{ idx + 1 }}</td>
                        <td class="px-4 py-3">
                          <Link v-if="esc.issue" :href="`/issues/${esc.issue.id}`" class="font-bold text-gray-800 hover:text-blue-600 block">
                            {{ esc.issue.title }}
                          </Link>
                          <span class="text-[10px] font-mono text-gray-400">{{ esc.issue?.issue_no || '-' }}</span>
                        </td>
                        <td class="px-4 py-3">
                          <div class="flex items-center gap-1 font-bold text-indigo-900">
                            <span class="bg-slate-100 px-1.5 py-0.5 rounded text-[10px]">{{ esc.from_level }}</span>
                            <i class="fas fa-arrow-right text-[10px] text-indigo-400"></i>
                            <span class="bg-indigo-600 text-white px-1.5 py-0.5 rounded text-[10px]">{{ esc.to_level }}</span>
                          </div>
                        </td>
                        <td class="px-4 py-3">
                          <p class="text-xs text-gray-700 line-clamp-1 max-w-xs" :title="esc.reason">
                            {{ esc.reason }}
                          </p>
                        </td>
                        <td class="px-4 py-3">
                          <div class="font-semibold text-gray-800">{{ esc.from_user?.name || 'Pejabat' }}</div>
                          <div class="text-[10px] text-gray-400">{{ esc.from_user?.role?.name || '' }}</div>
                        </td>
                        <td class="px-4 py-3">
                          <span class="px-2 py-0.5 rounded text-[10px] font-bold" :class="getEscalationStatusClass(esc.status)">
                            {{ esc.status }}
                          </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                          <div class="flex items-center justify-center gap-1.5">
                            <button 
                              v-if="canMakeDecision && esc.issue"
                              type="button" 
                              @click="openDecisionModal(esc.issue)"
                              class="bg-emerald-600 hover:bg-emerald-700 text-white px-2.5 py-1 rounded text-[11px] font-bold shadow-xs transition flex items-center gap-1 cursor-pointer"
                              title="Tetapkan Putusan Eskalasi"
                            >
                              <i class="fas fa-gavel"></i> Ambil Keputusan
                            </button>
                            <Link 
                              v-if="esc.issue"
                              :href="`/issues/${esc.issue.id}`"
                              class="border border-gray-300 text-gray-600 hover:bg-gray-50 px-2 py-1 rounded text-[11px] font-semibold"
                            >
                              Detail
                            </Link>
                          </div>
                        </td>
                      </tr>
                    </template>
                    <tr v-else>
                      <td colspan="7" class="text-center py-10 text-gray-400 text-xs">
                        Tidak ada eskalasi aktif yang ditujukan ke jenjang {{ meetingLevel }} pada rapat ini.
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

          </div>

          <!-- ============================================== -->
          <!-- 4. TABEL REKAP PUTUSAN & INFORMASI ATASAN       -->
          <!-- ============================================== -->
          <div v-if="activeAgendaTab === 'decisions'" class="space-y-4">
            
            <!-- Sub-Header Informasi Cascading -->
            <div class="space-y-3">
              <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider flex items-center gap-1.5">
                <i class="fas fa-info-circle text-blue-600"></i> Arahan & Informasi Cascading Atasan
              </h4>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <template v-if="informations && informations.length > 0">
                  <div 
                    v-for="info in informations" 
                    :key="info.id" 
                    class="bg-slate-50 border border-gray-200 p-3.5 rounded-xl space-y-1.5"
                  >
                    <div class="flex items-center justify-between">
                      <span class="bg-blue-100 text-blue-800 text-[10px] font-bold px-2 py-0.5 rounded">
                        {{ info.category || 'ARAHAN' }}
                      </span>
                      <span class="text-[10px] text-gray-400 font-mono">{{ formatDateOnly(info.published_at) }}</span>
                    </div>
                    <h5 class="text-xs font-bold text-gray-800">{{ info.title }}</h5>
                    <p class="text-xs text-gray-600 line-clamp-2 leading-relaxed">{{ info.content }}</p>
                  </div>
                </template>
                <div v-else class="col-span-2 text-center py-6 text-gray-400 text-xs">
                  Tidak ada instruksi cascading aktif untuk rapat ini.
                </div>
              </div>
            </div>

            <!-- Rekap Keputusan Resmi Rapat -->
            <div class="space-y-3 pt-3 border-t border-gray-200">
              <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider flex items-center gap-1.5">
                <i class="fas fa-gavel text-emerald-600"></i> Rekap Putusan Resmi yang Ditetapkan di Rapat Ini
              </h4>

              <div class="space-y-3">
                <template v-if="decisionsList && decisionsList.length > 0">
                  <div 
                    v-for="dec in decisionsList" 
                    :key="dec.id" 
                    class="bg-emerald-50/40 border border-emerald-200 p-4 rounded-xl text-xs space-y-2 shadow-xs"
                  >
                    <div class="flex items-center justify-between">
                      <span class="bg-emerald-600 text-white font-bold px-2 py-0.5 rounded text-[10px]">
                        Putusan Level {{ dec.decision_level }}
                      </span>
                      <span class="text-gray-400 font-mono text-[11px]">{{ formatDateOnly(dec.created_at) }}</span>
                    </div>
                    <p class="font-bold text-gray-800 text-sm">"{{ dec.decision }}"</p>
                    <div v-if="dec.follow_up" class="text-gray-600">
                      <span class="font-semibold text-gray-700">Tindak Lanjut / Action:</span> {{ dec.follow_up }}
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-gray-500 pt-1 border-t border-emerald-100">
                      <span>Isu Terkait: <b class="text-gray-700">{{ dec.issue?.title || '-' }}</b></span>
                      <span>Ditetapkan oleh: <b class="text-emerald-900">{{ dec.decision_maker?.name || 'Pimpinan' }}</b></span>
                    </div>
                  </div>
                </template>
                <div v-else class="text-center py-8 text-gray-400 text-xs">
                  Belum ada rekap putusan baru yang dicatat dalam sesi rapat ini. Gunakan tombol 'Ambil Keputusan' pada Tabel Issue atau Live Chat.
                </div>
              </div>
            </div>

          </div>

        </div>

      </div>

    </div>

    <!-- ======================================================== -->
    <!-- MODAL 1: PENGAMBILAN KEPUTUSAN DI DALAM RAPAT            -->
    <!-- (Strictly restricted to ongoing meeting room)             -->
    <!-- ======================================================== -->
    <div v-if="showDecisionModal" class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl border border-gray-100 space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center pb-3 border-b border-gray-100">
          <div>
            <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
              <i class="fas fa-gavel text-emerald-600"></i> Form Penetapan Keputusan Rapat
            </h3>
            <p class="text-xs text-gray-500 mt-0.5">
              Ruang Rapat: <b>{{ meeting.type }}</b> (Status: <span class="text-emerald-600 font-bold">Berlangsung</span>)
            </p>
          </div>
          <button @click="showDecisionModal = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold cursor-pointer">&times;</button>
        </div>

        <!-- Issue Preview Card -->
        <div v-if="selectedIssueForDecision" class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 space-y-1 text-xs">
          <div class="flex items-center justify-between text-[11px]">
            <span class="font-mono font-bold text-blue-600">{{ selectedIssueForDecision.issue_no }}</span>
            <span class="px-2 py-0.5 rounded text-[10px] font-bold" :class="getPriorityClass(selectedIssueForDecision.priority)">
              {{ selectedIssueForDecision.priority }}
            </span>
          </div>
          <h4 class="font-bold text-gray-800">{{ selectedIssueForDecision.title }}</h4>
          <p class="text-[11px] text-gray-600 line-clamp-2">{{ selectedIssueForDecision.description }}</p>
        </div>

        <form @submit.prevent="submitDecision" class="space-y-4">
          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">
              Rumusan Keputusan Pimpinan 
              <span v-if="!decisionForm.escalate_issue" class="text-red-500">*</span>
              <span v-else class="text-purple-600 font-normal text-[11px]">(Otomatis saat eskalasi)</span>
            </label>
            <textarea 
              v-model="decisionForm.decision" 
              rows="3" 
              :required="!decisionForm.escalate_issue"
              :placeholder="decisionForm.escalate_issue ? 'Otomatis: Eskalasi isu ke pimpinan level lebih tinggi...' : 'Tuliskan keputusan resmi atasan untuk isu ini...'"
              class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-emerald-500"
            ></textarea>
            <p v-if="decisionForm.errors.decision" class="text-red-500 text-[10px] mt-1">{{ decisionForm.errors.decision }}</p>
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Pertimbangan / Rationale Teknis (Opsional)</label>
            <textarea 
              v-model="decisionForm.rationale" 
              rows="2" 
              placeholder="Alasan teknis atau regulasi yang mendasari keputusan..."
              class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-emerald-500"
            ></textarea>
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Instruksi Tindak Lanjut (Follow-Up Action)</label>
            <textarea 
              v-model="decisionForm.follow_up" 
              rows="2" 
              placeholder="Langkah nyata yang wajib dijalankan oleh pelaksana..."
              class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-emerald-500"
            ></textarea>
          </div>

          <!-- Opsi Eskalasi Isu ke Jenjang Lebih Tinggi (TL -> ASMAN / ASMAN -> MUP) -->
          <div 
            v-if="selectedIssueForDecision && (selectedIssueForDecision.current_level === 'TL' || selectedIssueForDecision.current_level === 'ASMAN')" 
            class="p-3.5 bg-purple-50/80 rounded-xl border border-purple-200 space-y-3 transition"
          >
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <input 
                  type="checkbox" 
                  id="escalateIssue" 
                  v-model="decisionForm.escalate_issue"
                  @change="onEscalateToggle"
                  class="rounded text-purple-600 focus:ring-purple-500 cursor-pointer w-4 h-4"
                />
                <label for="escalateIssue" class="text-xs font-bold text-purple-950 cursor-pointer flex items-center gap-1.5">
                  <i class="fas fa-arrow-up-right-dots text-purple-600"></i>
                  Eskalasikan Isu ke Level Lebih Tinggi ({{ getTargetEscalateLevel(selectedIssueForDecision) }})
                </label>
              </div>
              <span class="text-[10px] bg-purple-200/80 text-purple-900 font-extrabold px-2.5 py-0.5 rounded-full flex items-center gap-1">
                {{ selectedIssueForDecision.current_level }} <i class="fas fa-arrow-right text-[9px]"></i> {{ getTargetEscalateLevel(selectedIssueForDecision) }}
              </span>
            </div>

            <div v-if="decisionForm.escalate_issue" class="space-y-2.5 pt-2 border-t border-purple-200/80 text-xs">
              <p class="text-[11px] text-purple-900 leading-relaxed bg-purple-100/60 p-2 rounded-lg border border-purple-200">
                <i class="fas fa-info-circle text-purple-600 mr-1"></i>
                Isu kendala staf ini akan <b>dinaikkan wewenangnya ke {{ getTargetEscalateLevel(selectedIssueForDecision) }}</b> dan langsung masuk ke agenda rapat koordinasi pimpinan berikutnya (Tabel Eskalasi). Anda tidak diwajibkan membuat task jika penanganan berada di luar wewenang level Anda.
              </p>
              <div>
                <label class="block text-[11px] font-bold text-gray-700 mb-1">
                  Alasan / Justifikasi Eskalasi ke {{ getTargetEscalateLevel(selectedIssueForDecision) }} <span class="text-red-500">*</span>
                </label>
                <textarea 
                  v-model="decisionForm.escalation_reason"
                  rows="2"
                  placeholder="Jelaskan alasan mengapa kendala staf ini membutuhkan wewenang pimpinan tingkat atas (misal: butuh alokasi anggaran, keputusan lintas divisi, kebijakan manajerial)..."
                  class="w-full text-xs border border-purple-200 rounded-lg p-2.5 outline-none focus:border-purple-600 bg-white"
                ></textarea>
              </div>
            </div>
          </div>

          <!-- Opsi Tandai Selesai -->
          <div class="flex items-center gap-2 p-2.5 bg-emerald-50 rounded-lg border border-emerald-200">
            <input 
              type="checkbox" 
              id="markResolved" 
              v-model="decisionForm.mark_resolved"
              @change="onResolvedToggle"
              class="rounded text-emerald-600 focus:ring-emerald-500 cursor-pointer"
            />
            <label for="markResolved" class="text-xs font-bold text-emerald-900 cursor-pointer">
              Tandai Isu Selesai (Resolved) setelah keputusan ini ditetapkan
            </label>
          </div>

          <!-- Opsi Otomatis Buat Task Tindak Lanjut -->
          <div class="p-3 bg-blue-50/70 rounded-xl border border-blue-200 space-y-3">
            <div class="flex items-center gap-2">
              <input 
                type="checkbox" 
                id="createTask" 
                v-model="decisionForm.create_task"
                class="rounded text-blue-600 focus:ring-blue-500 cursor-pointer"
              />
              <label for="createTask" class="text-xs font-bold text-blue-900 cursor-pointer">
                Otomatis Terbitkan Task Tindak Lanjut untuk Tim Pelaksana
              </label>
            </div>

            <div v-if="decisionForm.create_task" class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-blue-200/60">
              <div class="sm:col-span-2">
                <label class="block text-[11px] font-bold text-gray-700 mb-1">Judul Task Tindak Lanjut</label>
                <input 
                  type="text" 
                  v-model="decisionForm.task_title"
                  placeholder="Contoh: Eksekusi perbaikan komponen..."
                  class="w-full text-xs border border-gray-200 rounded-lg p-2 outline-none focus:border-blue-500 bg-white"
                />
              </div>
              <div>
                <label class="block text-[11px] font-bold text-gray-700 mb-1">Tugaskan ke (PIC Pelaksana)</label>
                <select 
                  v-model="decisionForm.task_assignee_id"
                  class="w-full text-xs border border-gray-200 rounded-lg p-2 outline-none focus:border-blue-500 bg-white"
                >
                  <option value="">-- Pilih Karyawan --</option>
                  <option v-for="u in attendees" :key="u.id" :value="u.id">
                    {{ u.name }} ({{ getUserRoleName(u) }})
                  </option>
                </select>
              </div>
              <div>
                <label class="block text-[11px] font-bold text-gray-700 mb-1">Tenggat Selesai (Due Date)</label>
                <input 
                  type="date" 
                  v-model="decisionForm.task_due_date"
                  class="w-full text-xs border border-gray-200 rounded-lg p-2 outline-none focus:border-blue-500 bg-white"
                />
              </div>
            </div>
          </div>

          <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
            <button 
              type="button" 
              @click="showDecisionModal = false" 
              class="px-4 py-2 border border-gray-200 text-gray-600 rounded-lg text-xs hover:bg-gray-50 cursor-pointer"
            >
              Batal
            </button>
            <button 
              type="submit" 
              :disabled="decisionForm.processing"
              class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold shadow-xs cursor-pointer transition disabled:opacity-50 disabled:pointer-events-none disabled:cursor-not-allowed flex items-center gap-1.5"
            >
              <i v-if="decisionForm.processing" class="fas fa-spinner fa-spin text-xs"></i>
              <span>{{ decisionForm.processing ? 'Menyimpan Putusan...' : 'Tetapkan Keputusan Rapat' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL 2: MUNCULKAN ISU STAF KE RUANG RAPAT                -->
    <!-- ======================================================== -->
    <div v-if="showSurfaceIssueModal" class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 space-y-4">
        <div class="flex justify-between items-center pb-3 border-b border-gray-100">
          <div>
            <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
              <i :class="meetingLevel === 'ASMAN' ? 'fas fa-level-up-alt text-indigo-600' : 'fas fa-exclamation-triangle text-amber-500'"></i>
              <span>{{ meetingLevel === 'ASMAN' ? 'Munculkan Isu Eskalasi ke Forum Rapat' : 'Munculkan Isu ke Forum Rapat' }}</span>
            </h3>
            <p class="text-xs text-gray-500 mt-0.5">
              {{ meetingLevel === 'ASMAN' ? 'Pilih isu eskalasi dari seksi yang perlu arahan atau keputusan Asisten Manajer.' : 'Pilih isu lapangan yang telah Anda catat untuk dibahas bersama dalam sesi rapat.' }}
            </p>
          </div>
          <button @click="showSurfaceIssueModal = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold cursor-pointer">&times;</button>
        </div>

        <form @submit.prevent="submitSurfaceIssue" class="space-y-4">
          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">
              {{ meetingLevel === 'ASMAN' ? 'Pilih Isu Eskalasi Bidang' : 'Pilih Isu Kendala Saya' }} <span class="text-red-500">*</span>
            </label>
            <select 
              v-model="surfaceForm.issue_id" 
              required
              class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500 bg-white"
            >
              <option value="">{{ meetingLevel === 'ASMAN' ? '-- Pilih Isu Eskalasi ke Asman --' : '-- Pilih dari Daftar Isu Aktif Anda --' }}</option>
              <option v-for="iss in myPendingIssues" :key="iss.id" :value="iss.id">
                [{{ iss.issue_no }}] {{ iss.title }} ({{ iss.priority }}) - {{ iss.organization_unit?.name || 'Unit' }}
              </option>
            </select>
            <p v-if="!myPendingIssues || myPendingIssues.length === 0" class="text-[11px] text-amber-600 mt-1 font-semibold">
              <span v-if="meetingLevel === 'ASMAN'">Belum ada isu yang dieskalasikan ke jenjang Asman di bidang ini. Isu yang dieskalasikan oleh TL akan otomatis muncul di sini.</span>
              <span v-else>Anda belum memiliki isu terbuka yang dicatat. Buat isu terlebih dahulu dari menu Issue jika ada kendala lapangan.</span>
            </p>
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Pesan Pengantar / Catatan Forum (Opsional)</label>
            <textarea 
              v-model="surfaceForm.message" 
              rows="3" 
              placeholder="Contoh: Mohon arahan pimpinan terkait kendala kebocoran oli pelumas turbin unit 2..."
              class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500"
            ></textarea>
          </div>

          <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
            <button 
              type="button" 
              @click="showSurfaceIssueModal = false" 
              class="px-4 py-2 border border-gray-200 text-gray-600 rounded-lg text-xs hover:bg-gray-50 cursor-pointer"
            >
              Batal
            </button>
            <button 
              type="submit" 
              :disabled="isSubmittingIssue || !surfaceForm.issue_id"
              class="px-5 py-2 bg-amber-500 hover:bg-amber-600 disabled:opacity-50 disabled:pointer-events-none disabled:cursor-not-allowed text-slate-950 rounded-lg text-xs font-bold shadow-xs cursor-pointer transition flex items-center gap-1.5"
            >
              <i v-if="isSubmittingIssue" class="fas fa-spinner fa-spin text-xs"></i>
              <span>{{ isSubmittingIssue ? 'Mengirim...' : 'Munculkan ke Forum' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL 3: TAMBAH TASK MINGGU N                             -->
    <!-- ======================================================== -->
    <div v-if="showAddTaskModal" class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 space-y-4">
        <div class="flex justify-between items-center pb-3 border-b border-gray-100">
          <div>
            <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
              <i class="fas fa-plus text-blue-600"></i> Tambah Task Minggu N
            </h3>
            <p class="text-xs text-gray-500 mt-0.5">Penugasan baru langsung ke personil pelaksana selama sesi rapat berlangsung.</p>
          </div>
          <button @click="showAddTaskModal = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold cursor-pointer">&times;</button>
        </div>

        <form @submit.prevent="submitAddTask" class="space-y-4">
          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Nama / Uraian Task <span class="text-red-500">*</span></label>
            <input 
              type="text" 
              v-model="addTaskForm.title" 
              required 
              placeholder="Contoh: Penggantian filter oli pelumas turbin..."
              class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500"
            />
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">Tugaskan ke (PIC) <span class="text-red-500">*</span></label>
              <select 
                v-model="addTaskForm.assigned_to" 
                required 
                class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500 bg-white"
              >
                <option value="">-- Pilih Karyawan --</option>
                <option v-for="u in attendees" :key="u.id" :value="u.id">
                  {{ u.name }} ({{ getUserRoleName(u) }})
                </option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">Target Selesai (Due Date) <span class="text-red-500">*</span></label>
              <input 
                type="date" 
                v-model="addTaskForm.due_date" 
                required 
                class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500"
              />
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Detail Keterangan</label>
            <textarea 
              v-model="addTaskForm.description" 
              rows="2" 
              placeholder="Catatan teknis atau instruksi khusus..."
              class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500"
            ></textarea>
          </div>

          <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
            <button 
              type="button" 
              @click="showAddTaskModal = false" 
              class="px-4 py-2 border border-gray-200 text-gray-600 rounded-lg text-xs hover:bg-gray-50 cursor-pointer"
            >
              Batal
            </button>
            <button 
              type="submit" 
              :disabled="addTaskForm.processing"
              class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold shadow-xs cursor-pointer transition disabled:opacity-50 disabled:pointer-events-none disabled:cursor-not-allowed flex items-center gap-1.5"
            >
              <i v-if="addTaskForm.processing" class="fas fa-spinner fa-spin text-xs"></i>
              <span>{{ addTaskForm.processing ? 'Menyimpan...' : 'Simpan Task' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL 4: SELESAIKAN RAPAT                                 -->
    <!-- ======================================================== -->
    <div v-if="showFinishModal && canMakeDecision" class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 space-y-4">
        <div class="flex justify-between items-center pb-3 border-b border-gray-100">
          <div>
            <h3 class="text-sm font-bold text-gray-800">Selesaikan Rapat Koordinasi</h3>
            <p class="text-xs text-gray-500 mt-0.5">Tuliskan ringkasan notulen akhir untuk arsip berita acara rapat.</p>
          </div>
          <button @click="showFinishModal = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold cursor-pointer">&times;</button>
        </div>

        <form @submit.prevent="submitFinish" class="space-y-4">
          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Catatan Notulen / Kesimpulan Akhir</label>
            <textarea 
              v-model="finishForm.notes" 
              rows="4" 
              required
              placeholder="Tuliskan kesimpulan evaluasi mingguan dan arahan khusus pimpinan..."
              class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500"
            ></textarea>
          </div>

          <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
            <button 
              type="button" 
              @click="showFinishModal = false" 
              class="px-4 py-2 border border-gray-200 text-gray-600 rounded-lg text-xs hover:bg-gray-50 cursor-pointer"
            >
              Batal
            </button>
            <button 
              type="submit" 
              :disabled="finishForm.processing"
              class="px-5 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-xs font-semibold shadow-xs cursor-pointer transition disabled:opacity-50 disabled:pointer-events-none disabled:cursor-not-allowed flex items-center gap-1.5"
            >
              <i v-if="finishForm.processing" class="fas fa-spinner fa-spin text-xs"></i>
              <span>{{ finishForm.processing ? 'Menyimpan...' : 'Selesaikan & Rekap' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, reactive, computed, nextTick, onMounted, onUnmounted, watch } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
  meeting: Object,
  meetingLevel: String,
  informations: Array,
  evalTasks: Array,
  activeIssues: Array,
  archivedIssues: Array,
  allOpenIssues: Array,
  plannedTasks: Array,
  decisions: Array,
  users: Array,
  messages: Array,
  myPendingIssues: Array,
  escalations: Array,
  userRole: String,
  attendanceStats: Object,
});

const page = usePage();
const authUser = computed(() => page.props.auth?.user || {});
const currentRole = computed(() => {
  return props.userRole || authUser.value.role?.name || 'STAFF';
});

// TL, ASMAN, MUP, dan ADMIN berwenang menetapkan putusan di rapat
const canMakeDecision = computed(() => {
  return ['TL', 'ASMAN', 'MUP', 'ADMIN'].includes(currentRole.value.toUpperCase());
});

function canDecideIssue(issue) {
  if (!issue || !canMakeDecision.value) return false;
  if (['Resolved', 'Closed', 'Decided'].includes(issue.status)) return false;
  const role = currentRole.value.toUpperCase();
  if (role === 'MUP' || role === 'ADMIN') return true;
  return issue.current_level === role;
}

// State Presensi Online Real-Time
const attendees = ref([...(props.users || [])]);
const currentStats = ref(props.attendanceStats || {
  total_expected: attendees.value.length,
  total_joined: 0,
  total_online: 0,
});

// State Real-Time Chat & Agenda Rapat
const messagesList = ref([...(props.messages || [])]);
const activeIssuesList = ref([...(props.activeIssues || [])]);
const archivedIssuesList = ref([...(props.archivedIssues || [])]);
const workspaceIssueTab = ref('active'); // 'active' | 'archived'
const decisionsList = ref([...(props.decisions || [])]);

watch(() => props.users, (newVal) => {
  if (newVal) attendees.value = [...newVal];
});

watch(() => props.attendanceStats, (newVal) => {
  if (newVal) currentStats.value = { ...newVal };
});

watch(() => props.messages, (newVal) => {
  if (newVal) {
    messagesList.value = [...newVal];
    nextTick(scrollToBottom);
  }
});

watch(() => props.activeIssues, (newVal) => {
  if (newVal) activeIssuesList.value = [...newVal];
});

watch(() => props.archivedIssues, (newVal) => {
  if (newVal) archivedIssuesList.value = [...newVal];
});

watch(() => props.decisions, (newVal) => {
  if (newVal) decisionsList.value = [...newVal];
});

const participantFilter = ref('all');

const filteredParticipants = computed(() => {
  if (participantFilter.value === 'online') {
    return attendees.value.filter(u => u.is_online);
  }
  if (participantFilter.value === 'offline') {
    return attendees.value.filter(u => !u.is_online);
  }
  return attendees.value;
});

let pingInterval = null;

async function sendAttendancePing() {
  try {
    const res = await axios.post(`/rapat/${props.meeting.id}/ping`);
    if (res.data && res.data.status === 'ok') {
      currentStats.value.total_online = res.data.online_count;
      currentStats.value.total_joined = res.data.joined_count;
      if (res.data.attendances) {
        attendees.value = attendees.value.map(u => {
          const att = res.data.attendances[u.id];
          if (att) {
            return {
              ...u,
              is_joined: att.is_joined,
              is_online: att.is_online,
              joined_at: att.joined_at || u.joined_at,
              last_seen: att.last_seen || u.last_seen,
            };
          } else {
            return {
              ...u,
              is_online: false,
            };
          }
        });
      }

      // Sinkronisasi Pesan Chat & Kartu Isu Real-Time Tanpa Refresh
      if (res.data.messages) {
        const prevCount = messagesList.value.length;
        const newCount = res.data.messages.length;
        messagesList.value = res.data.messages;
        if (newCount > prevCount) {
          nextTick(scrollToBottom);
        }
      }

      // Sinkronisasi Tabel Isu Aktif & Arsip Real-Time
      if (res.data.active_issues) {
        activeIssuesList.value = res.data.active_issues;
      }
      if (res.data.archived_issues) {
        archivedIssuesList.value = res.data.archived_issues;
      }

      // Sinkronisasi Tabel Putusan Rapat Real-Time
      if (res.data.decisions) {
        decisionsList.value = res.data.decisions;
      }
    }
  } catch (err) {
    // Silent fail on heartbeat
  }
}

onMounted(() => {
  sendAttendancePing();
  // Polling heartbeat & real-time chat sync setiap 2.5 detik
  pingInterval = setInterval(sendAttendancePing, 2500);
});

onUnmounted(() => {
  if (pingInterval) {
    clearInterval(pingInterval);
  }
});

// Hanya staf (dan admin) yang berwenang memunculkan isu
const isStaff = computed(() => {
  const r = currentRole.value.toUpperCase();
  return r === 'STAFF' || r === 'ADMIN';
});

// Penanda Rapat Staff dan TL (Isu hanya tampil di ruang rapat Staff & TL)
const isStaffTlMeeting = computed(() => {
  return props.meetingLevel === 'TL';
});

// Navigasi Tab Sisi Kiri (Chat vs Video/Peserta)
const leftTab = ref('chat');

// Navigasi Tampilan Khusus Mobile Phone (< lg): 'chat' | 'agenda' | 'participants'
const mobileTab = ref('chat');

function setMobileTab(tab) {
  mobileTab.value = tab;
  if (tab === 'chat') {
    leftTab.value = 'chat';
    nextTick(() => {
      scrollToBottom();
    });
  } else if (tab === 'participants') {
    leftTab.value = 'participants';
  }
}

// Navigasi Tab Utama (Jika Rapat Staff & TL buka 'issues', jika ada eskalasi buka 'escalations', selain itu 'tasks')
const activeAgendaTab = ref(
  props.meetingLevel === 'TL' 
    ? 'issues' 
    : (props.escalations && props.escalations.length > 0 ? 'escalations' : 'tasks')
);

// Sub-Tab Task (eval vs planned)
const taskSubTab = ref('eval');

const currentDisplayedTasks = computed(() => {
  return taskSubTab.value === 'eval' ? (props.evalTasks || []) : (props.plannedTasks || []);
});

// Modals State
const showFinishModal = ref(false);
const showAddTaskModal = ref(false);
const showSurfaceIssueModal = ref(false);
const showDecisionModal = ref(false);
const selectedIssueForDecision = ref(null);

const chatContainer = ref(null);

// State Input Chat
const chatInput = ref('');
const isSending = ref(false);

async function sendMessage() {
  const text = (chatInput.value || '').trim();
  if (!text || isSending.value) return;

  isSending.value = true;
  try {
    const res = await axios.post(`/rapat/${props.meeting.id}/messages`, {
      message: text,
    });
    chatInput.value = '';

    if (res.data && res.data.message) {
      if (!messagesList.value.some(m => m.id === res.data.message.id)) {
        messagesList.value.push(res.data.message);
      }
      nextTick(scrollToBottom);
    }
    // Langsung trigger heartbeat polling untuk sync ke seluruh peserta rapat lainnya
    sendAttendancePing();
  } catch (err) {
    console.error('Gagal mengirim pesan chat:', err);
    const errMsg = err.response?.data?.error || err.response?.data?.message || 'Gagal mengirim pesan ke ruang rapat. Silakan coba lagi.';
    alert(errMsg);
  } finally {
    isSending.value = false;
  }
}

// Form Munculkan Isu ke Chat
const surfaceForm = reactive({
  issue_id: '',
  message: '',
});
const isSubmittingIssue = ref(false);

async function submitSurfaceIssue() {
  if (isSubmittingIssue.value || !surfaceForm.issue_id) return;

  isSubmittingIssue.value = true;
  try {
    const res = await axios.post(`/rapat/${props.meeting.id}/messages`, {
      issue_id: surfaceForm.issue_id,
      message: surfaceForm.message || null,
    });
    showSurfaceIssueModal.value = false;
    surfaceForm.issue_id = '';
    surfaceForm.message = '';

    if (res.data && res.data.message) {
      if (!messagesList.value.some(m => m.id === res.data.message.id)) {
        messagesList.value.push(res.data.message);
      }
      nextTick(scrollToBottom);
    }
    // Langsung trigger heartbeat polling untuk sync ke pimpinan & peserta lainnya
    sendAttendancePing();
  } catch (err) {
    console.error('Gagal memunculkan isu:', err);
    const errMsg = err.response?.data?.error || err.response?.data?.message || 'Gagal memunculkan isu ke forum. Silakan periksa kembali.';
    alert(errMsg);
  } finally {
    isSubmittingIssue.value = false;
  }
}

function scrollToBottom() {
  nextTick(() => {
    if (chatContainer.value) {
      chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
    }
  });
}

function isMyMessage(msg) {
  if (!msg || !authUser.value) return false;
  return Number(msg.user_id) === Number(authUser.value.id);
}

// Form Keputusan Rapat
const defaultDueDate = new Date();
defaultDueDate.setDate(defaultDueDate.getDate() + 3);

const decisionForm = useForm({
  meeting_id: props.meeting.id,
  decision: '',
  rationale: '',
  follow_up: '',
  mark_resolved: false,
  create_task: false,
  task_title: '',
  task_assignee_id: '',
  task_due_date: defaultDueDate.toISOString().split('T')[0],
  escalate_issue: false,
  escalation_reason: '',
});

function getTargetEscalateLevel(issue) {
  if (!issue) return 'ASMAN';
  if (issue.current_level === 'TL') {
    const isDirect = issue.hierarchy_line?.is_direct_to_mup || !issue.hierarchy_line?.asman;
    return isDirect ? 'MUP' : 'ASMAN';
  }
  if (issue.current_level === 'ASMAN') {
    return 'MUP';
  }
  return 'MUP';
}

function onEscalateToggle() {
  if (decisionForm.escalate_issue) {
    decisionForm.mark_resolved = false;
    const target = getTargetEscalateLevel(selectedIssueForDecision.value);
    if (!decisionForm.decision || decisionForm.decision.trim().length < 3) {
      decisionForm.decision = `Eskalasi ke ${target}: Mohon arahan dan tindak lanjut pimpinan.`;
    }
    if (!decisionForm.escalation_reason) {
      decisionForm.escalation_reason = decisionForm.rationale || `Memerlukan kewenangan dan penanganan jenjang ${target}.`;
    }
  }
}

function onResolvedToggle() {
  if (decisionForm.mark_resolved) {
    decisionForm.escalate_issue = false;
  }
}

function openDecisionModal(issue) {
  selectedIssueForDecision.value = issue;
  decisionForm.decision = '';
  decisionForm.rationale = '';
  decisionForm.follow_up = '';
  decisionForm.mark_resolved = false;
  decisionForm.create_task = false;
  decisionForm.task_title = `Tindak Lanjut: ${issue.title}`;
  decisionForm.task_assignee_id = issue.reported_by || '';
  decisionForm.escalate_issue = false;
  decisionForm.escalation_reason = '';
  decisionForm.meeting_id = props.meeting.id;
  showDecisionModal.value = true;
}

function submitDecision() {
  if (!selectedIssueForDecision.value || decisionForm.processing) return;
  decisionForm.meeting_id = props.meeting.id;
  decisionForm.post(`/issues/${selectedIssueForDecision.value.id}/decisions`, {
    preserveScroll: true,
    onSuccess: () => {
      showDecisionModal.value = false;
      decisionForm.reset();
      selectedIssueForDecision.value = null;
      sendAttendancePing();
    },
  });
}

// Form Tambah Task
const defaultTaskDueDate = new Date();
defaultTaskDueDate.setDate(defaultTaskDueDate.getDate() + 5);

const addTaskForm = useForm({
  title: '',
  assigned_to: '',
  due_date: defaultTaskDueDate.toISOString().split('T')[0],
  description: '',
});

function submitAddTask() {
  if (addTaskForm.processing) return;
  addTaskForm.post(`/rapat/${props.meeting.id}/tasks`, {
    preserveScroll: true,
    onSuccess: () => {
      showAddTaskModal.value = false;
      addTaskForm.reset();
    },
  });
}

// Form Selesai Rapat
const finishForm = useForm({
  notes: props.meeting?.notes || '',
});

function submitFinish() {
  if (finishForm.processing || !canMakeDecision.value) return;
  finishForm.post(`/rapat/${props.meeting.id}/finish`, {
    onSuccess: () => {
      showFinishModal.value = false;
    },
  });
}

// Helper Formatters
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

function formatTime(dStr) {
  if (!dStr) return '';
  const d = new Date(dStr);
  return d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
}

function isTaskOverdue(task) {
  if (!task.due_date) return false;
  const today = new Date().toISOString().split('T')[0];
  return task.due_date < today && !['Selesai', 'Closed', 'Resolved'].includes(task.status);
}

function getPriorityClass(p) {
  const norm = String(p || '').toUpperCase();
  if (norm === 'CRITICAL') return 'bg-red-500 text-white';
  if (norm === 'HIGH') return 'bg-amber-500 text-white';
  if (norm === 'MEDIUM') return 'bg-blue-500 text-white';
  return 'bg-slate-500 text-white';
}

function getStatusBadgeClass(s) {
  const norm = String(s || '').toLowerCase();
  if (norm.includes('selesai') || norm.includes('resolved') || norm.includes('closed') || norm.includes('diputuskan')) {
    return 'bg-emerald-100 text-emerald-800 border border-emerald-300';
  }
  if (norm.includes('progress') || norm.includes('dibahas') || norm.includes('review')) {
    return 'bg-blue-100 text-blue-800 border border-blue-300';
  }
  return 'bg-amber-100 text-amber-800 border border-amber-300';
}

function getTaskStatusClass(s) {
  const norm = String(s || '').toLowerCase();
  if (norm.includes('selesai') || norm.includes('closed') || norm.includes('resolved')) {
    return 'bg-emerald-100 text-emerald-800 border border-emerald-300';
  }
  if (norm.includes('progress')) {
    return 'bg-blue-100 text-blue-800 border border-blue-300';
  }
  return 'bg-slate-100 text-slate-700 border border-slate-300';
}

function getEscalationStatusClass(s) {
  const norm = String(s || '').toLowerCase();
  if (norm === 'decided' || norm === 'closed') return 'bg-emerald-100 text-emerald-800 border border-emerald-300';
  if (norm === 'accepted') return 'bg-blue-100 text-blue-800 border border-blue-300';
  return 'bg-amber-100 text-amber-800 border border-amber-300';
}

function getUserRoleName(u) {
  if (!u) return 'STAFF';
  if (typeof u.role === 'object') return u.role?.name || 'STAFF';
  return u.role || 'STAFF';
}

function getRoleBadgeClass(r) {
  const roleStr = typeof r === 'object' ? (r?.name || '') : (r || '');
  const norm = String(roleStr).toUpperCase();
  if (norm === 'MUP') return 'bg-purple-900 text-purple-200 border border-purple-700';
  if (norm === 'ASMAN') return 'bg-indigo-900 text-indigo-200 border border-indigo-700';
  if (norm === 'TL') return 'bg-blue-900 text-blue-200 border border-blue-700';
  return 'bg-slate-700 text-slate-300 border border-slate-600';
}
</script>

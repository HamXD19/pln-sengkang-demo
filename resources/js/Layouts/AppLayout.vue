<template>
  <div class="flex h-screen w-full bg-[#f8fafc] text-slate-800 font-sans overflow-hidden">
    
    <!-- ======================================================= -->
    <!-- 1. DESKTOP SIDEBAR (HANYA MUNCUL DI LAYAR LAPTOP / PC)  -->
    <!-- ======================================================= -->
    <aside class="hidden md:flex flex-col w-64 bg-pln-dark text-gray-300 h-full shrink-0 z-40 select-none shadow-xl">
      <!-- Logo Area -->
      <div class="h-20 flex items-center justify-between px-6 border-b border-slate-700/30">
        <div class="flex items-center gap-3">
          <img :src="brandingLogo" alt="Logo PLN" class="w-10 h-10 rounded-md object-contain shadow-sm" />
          <div class="leading-tight">
            <span class="text-white text-xs font-bold tracking-wide">PLN NP</span><br />
            <span class="text-gray-300 text-xs font-bold">UP Sengkang</span>
          </div>
        </div>
      </div>

      <!-- Desktop Navigation Links -->
      <nav class="flex-1 px-3 py-4 space-y-1.5 overflow-y-auto text-xs font-medium">
        <!-- KHUSUS SUPER ADMIN: HANYA KELOLA AKUN, KELOLA IMAGE & LOGO, DAN DIREKTORI AKUN -->
        <template v-if="isSuperAdmin">
          <div class="px-3 pt-1 pb-2 text-[10px] font-bold text-sky-400 uppercase tracking-widest flex items-center gap-1.5 border-b border-slate-700/40 mb-2">
            <i class="fas fa-shield-halved"></i>
            <span>Portal Super Admin</span>
          </div>

          <!-- 1. Kelola Akun Pegawai -->
          <Link 
            href="/master-data?tab=users" 
            :class="navClass('/master-data?tab=users')"
          >
            <i class="fas fa-users-gear w-5 text-center text-base text-sky-400"></i>
            <span>Kelola Akun Pegawai</span>
          </Link>

          <!-- 2. Kelola Image & Logo -->
          <Link 
            href="/master-data?tab=branding" 
            :class="navClass('/master-data?tab=branding')"
          >
            <i class="fas fa-palette w-5 text-center text-base text-amber-400"></i>
            <span>Kelola Image & Logo</span>
          </Link>

          <!-- 3. Direktori Akun -->
          <Link 
            href="/daftar-akun" 
            :class="navClass('/daftar-akun')"
          >
            <div class="flex items-center gap-3">
              <i class="fas fa-address-book w-5 text-center text-base text-emerald-400"></i>
              <span>Direktori Akun</span>
            </div>
            <span class="text-[9px] font-extrabold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 px-1.5 py-0.5 rounded">154</span>
          </Link>
        </template>

        <!-- MENU OPERASIONAL REGULER (STAFF, TL, ASMAN, MUP, ADMIN LAINNYA) -->
        <template v-else>
          <Link 
            href="/dashboard" 
            :class="navClass('/dashboard')"
          >
            <i class="fas fa-home w-5 text-center text-base"></i>
            <span>Dashboard</span>
          </Link>

          <Link 
            href="/tasks" 
            :class="navClass(['/pekerjaan', '/tasks'])"
          >
            <i class="fas fa-tasks w-5 text-center text-base"></i>
            <span>Kelola Task</span>
          </Link>

          <!-- Kelola Issue: Dihapus dari TL, ASMAN, dan MUP (Hanya untuk Staff & Admin) -->
          <Link 
            v-if="!['TL', 'ASMAN', 'MUP'].includes(userRole)"
            href="/issues" 
            :class="navClass('/issues')"
          >
            <i class="fas fa-exclamation-circle w-5 text-center text-base"></i>
            <span>Kelola Issue</span>
          </Link>

          <Link 
            v-if="isLeaderOrAdmin"
            href="/eskalasi" 
            :class="navClass(['/eskalasi', '/escalations'])"
          >
            <i class="fas fa-level-up-alt w-5 text-center text-base"></i>
            <span>Daftar Eskalasi</span>
          </Link>

          <Link 
            href="/rapat" 
            :class="navClass('/rapat')"
          >
            <i class="far fa-calendar-alt w-5 text-center text-base"></i>
            <span>Rapat Mingguan</span>
          </Link>

          <Link 
            href="/informasi" 
            :class="navClass('/informasi')"
          >
            <i class="fas fa-info-circle w-5 text-center text-base"></i>
            <span>Informasi</span>
          </Link>

          <Link 
            href="/keputusan" 
            :class="navClass('/keputusan')"
          >
            <i class="fas fa-file-signature w-5 text-center text-base"></i>
            <span>Keputusan</span>
          </Link>

          <Link 
            href="/laporan" 
            :class="navClass('/laporan')"
          >
            <i class="far fa-file-alt w-5 text-center text-base"></i>
            <span>Laporan</span>
          </Link>

          <!-- Menu Direktori Akun (Semua Role) -->
          <Link 
            href="/daftar-akun" 
            :class="navClass('/daftar-akun')"
          >
            <div class="flex items-center gap-3">
              <i class="fas fa-users-gear w-5 text-center text-base text-emerald-400"></i>
              <span>Daftar Akun</span>
            </div>
            <span class="text-[9px] font-extrabold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 px-1.5 py-0.5 rounded">154</span>
          </Link>

          <Link 
            v-if="isMupOrAdmin" 
            href="/master-data" 
            :class="navClass('/master-data')"
          >
            <div class="flex items-center gap-3">
              <i class="fas fa-cog w-5 text-center text-base"></i>
              <span>Master Data</span>
            </div>
            <i class="fas fa-chevron-right text-[10px]"></i>
          </Link>
        </template>
      </nav>

      <!-- Bottom Profile Bar Desktop -->
      <div class="p-4 border-t border-slate-700/50 bg-slate-900/40">
        <div class="flex items-center justify-between">
          <Link href="/profil" class="flex items-center gap-3 overflow-hidden group hover:opacity-90 transition">
            <div class="w-9 h-9 rounded-full bg-slate-700 group-hover:bg-slate-600 text-white font-bold flex items-center justify-center text-xs shadow-md overflow-hidden shrink-0">
              <img :src="user?.avatar_url" alt="Avatar" class="w-full h-full object-cover" />
            </div>
            <div class="truncate">
              <p class="text-xs font-bold text-white group-hover:text-blue-300 truncate">{{ user?.name }}</p>
              <p class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold truncate">
                {{ user?.position?.name || user?.role?.name || 'Karyawan' }}
              </p>
            </div>
          </Link>
          <button 
            type="button" 
            @click="logout" 
            title="Keluar / Logout" 
            class="text-gray-400 hover:text-red-400 p-1.5 rounded-lg hover:bg-white/10 transition cursor-pointer"
          >
            <i class="fas fa-sign-out-alt"></i>
          </button>
        </div>

        <!-- Sidebar Copyright devATP -->
        <div class="mt-2.5 pt-2 border-t border-slate-800/80 flex items-center justify-between text-[9px] text-slate-500">
          <span>&copy; {{ new Date().getFullYear() }} PLN UP Sengkang</span>
          <span class="font-bold text-slate-400">devATP</span>
        </div>
      </div>
    </aside>

    <!-- ======================================================= -->
    <!-- 2. MAIN APPLICATION CONTENT AREA                        -->
    <!-- ======================================================= -->
    <div class="flex-1 flex flex-col h-full overflow-hidden relative">

      <!-- ======================================================= -->
      <!-- A. NATIVE MOBILE APP TOP BAR (AL-A ANDROID / IOS)       -->
      <!-- ======================================================= -->
      <header 
        class="md:hidden sticky top-0 z-30 bg-white/95 backdrop-blur-md border-b border-slate-200/80 px-3.5 pb-2.5 flex items-center justify-between shadow-2xs transition-all shrink-0"
        style="padding-top: max(0.875rem, calc(env(safe-area-inset-top, 0px) + 0.5rem));"
      >
        <div class="flex items-center gap-2.5 min-w-0 flex-1 mr-2">
          <!-- User Avatar with Online Dot -->
          <div class="relative shrink-0">
            <Link href="/profil" class="block w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-slate-200 overflow-hidden ring-2 ring-sky-500/25 active:scale-95 transition-transform shadow-2xs">
              <img :src="user?.avatar_url" alt="Avatar" class="w-full h-full object-cover" />
            </Link>
            <span class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full bg-emerald-500 ring-2 ring-white"></span>
          </div>

          <!-- User Name & Role Pill -->
          <div class="min-w-0 flex-1">
            <h2 class="text-xs sm:text-sm font-bold text-slate-800 truncate leading-snug py-0.5" :title="user?.name">
              {{ user?.name }}
            </h2>
            <div class="flex items-center gap-1.5 mt-0.5">
              <span class="text-[9px] font-extrabold px-1.5 py-0.5 rounded-md bg-sky-100 text-sky-700 uppercase tracking-wider shrink-0">
                {{ userRole }}
              </span>
              <span class="text-[10px] text-slate-500 truncate font-medium">
                {{ user?.organization_unit?.name || 'UP Sengkang' }}
              </span>
            </div>
          </div>
        </div>

        <!-- Right Side: PLN NP Logo Badge -->
        <div class="flex items-center gap-1.5 shrink-0">
          <div class="flex items-center gap-1 bg-slate-100/80 px-2 py-0.5 rounded-full border border-slate-200/70">
            <img :src="brandingLogo" alt="PLN" class="w-3.5 h-3.5 object-contain" />
            <span class="text-[9px] font-extrabold text-slate-700 tracking-tight">UP Sengkang</span>
          </div>
        </div>
      </header>

      <!-- ======================================================= -->
      <!-- B. SCROLLABLE PAGE BODY                                 -->
      <!-- ======================================================= -->
      <main class="flex-1 overflow-auto p-3.5 sm:p-6 lg:p-8 pb-28 md:pb-8">
        <div class="max-w-[1240px] mx-auto space-y-5 sm:space-y-6">

          <!-- Desktop Page Header (Disembunyikan di HP kecil agar tidak redundan) -->
          <div class="hidden sm:flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-2 pb-2 border-b border-gray-100 sm:border-0 sm:pb-0">
            <div>
              <h1 class="text-xl sm:text-2xl font-extrabold text-gray-800 tracking-tight">{{ title }}</h1>
              <div class="flex items-center gap-3 mt-1.5">
                <Link href="/profil" class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-slate-600 overflow-hidden shadow-sm flex items-center justify-center font-bold text-white hover:ring-2 hover:ring-blue-400 transition shrink-0">
                  <img :src="user?.avatar_url" alt="Avatar" class="w-full h-full object-cover" />
                </Link>
                <div class="min-w-0">
                  <p class="text-xs sm:text-sm text-gray-500 truncate">
                    Selamat datang, <span class="font-bold text-gray-800">{{ user?.name }}</span>
                  </p>
                  <p class="text-[11px] sm:text-xs text-gray-500 truncate flex flex-wrap items-center gap-1.5 mt-0.5">
                    <span>{{ user?.position?.name || user?.role?.name }} &bull; {{ user?.organization_unit?.name || 'UP Sengkang' }}</span>
                    <template v-if="user?.hierarchy_info?.tl && !user.hierarchy_info?.tl?.is_self">
                      <span class="text-gray-300">&bull;</span>
                      <span class="text-blue-600 font-semibold">TL: {{ user.hierarchy_info.tl.name }}</span>
                    </template>
                    <template v-if="user?.hierarchy_info?.asman && !user.hierarchy_info?.asman?.is_self">
                      <span class="text-gray-300">&bull;</span>
                      <span class="text-indigo-600 font-semibold">Asman: {{ user.hierarchy_info.asman.name }}</span>
                    </template>
                  </p>
                </div>
              </div>
            </div>

            <!-- Right Controls Slot -->
            <div class="flex items-center justify-between md:justify-end w-full md:w-auto gap-2.5">
              <slot name="actions" />
              <a 
                href="/download-akun-excel" 
                title="Unduh Data Seluruh 154 Akun Pegawai (Format .xlsx)" 
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-300/80 text-xs font-bold transition shadow-2xs hover:shadow-xs cursor-pointer active:scale-95"
              >
                <i class="fas fa-file-excel text-emerald-600 text-xs"></i>
                <span class="hidden sm:inline">Excel Akun</span>
              </a>
              <div class="hidden sm:flex items-center gap-2 bg-white border border-gray-200 px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 shadow-2xs">
                <i class="far fa-calendar-check text-blue-600"></i>
                <span>{{ currentDateFormatted }}</span>
              </div>
            </div>
          </div>

          <!-- Page Content Slot -->
          <slot />

        </div>
      </main>

      <!-- ======================================================= -->
      <!-- C. NATIVE MOBILE BOTTOM TAB BAR (ALA ANDROID / IOS)     -->
      <!-- ======================================================= -->
      <nav class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-xl border-t border-slate-200/80 shadow-[0_-4px_25px_rgba(0,0,0,0.06)] pb-safe select-none">
        <!-- TAB BAR KHUSUS SUPER ADMIN -->
        <div v-if="isSuperAdmin" class="grid grid-cols-4 items-center h-14 px-1 text-center">
          <!-- 1. Kelola Akun -->
          <Link 
            href="/master-data?tab=users" 
            class="flex flex-col items-center justify-center py-1 transition-all active:scale-90"
            :class="mobileTabClass('/master-data?tab=users')"
          >
            <i class="fas fa-users-gear text-lg"></i>
            <span class="text-[10px] mt-0.5 font-medium tracking-tight">Kelola Akun</span>
          </Link>

          <!-- 2. Image & Logo -->
          <Link 
            href="/master-data?tab=branding" 
            class="flex flex-col items-center justify-center py-1 transition-all active:scale-90"
            :class="mobileTabClass('/master-data?tab=branding')"
          >
            <i class="fas fa-palette text-lg"></i>
            <span class="text-[10px] mt-0.5 font-medium tracking-tight">Image & Logo</span>
          </Link>

          <!-- 3. Direktori Akun -->
          <Link 
            href="/daftar-akun" 
            class="flex flex-col items-center justify-center py-1 transition-all active:scale-90"
            :class="mobileTabClass('/daftar-akun')"
          >
            <i class="fas fa-address-book text-lg"></i>
            <span class="text-[10px] mt-0.5 font-medium tracking-tight">Direktori</span>
          </Link>

          <!-- 4. Menu Lainnya -->
          <button 
            type="button" 
            @click="mobileMoreSheetOpen = true"
            class="flex flex-col items-center justify-center py-1 transition-all active:scale-90 cursor-pointer"
            :class="mobileMoreSheetOpen ? 'text-sky-600 font-bold' : 'text-slate-400 hover:text-slate-600'"
          >
            <i class="fas fa-grip-vertical text-lg"></i>
            <span class="text-[10px] mt-0.5 font-medium tracking-tight">Menu</span>
          </button>
        </div>

        <!-- TAB BAR REGULER -->
        <div v-else class="grid grid-cols-5 items-center h-14 px-1 text-center">
          
          <!-- 1. Beranda / Dashboard -->
          <Link 
            href="/dashboard" 
            class="flex flex-col items-center justify-center py-1 transition-all active:scale-90"
            :class="mobileTabClass('/dashboard')"
          >
            <i class="fas fa-house text-lg"></i>
            <span class="text-[10px] mt-0.5 font-medium tracking-tight">Beranda</span>
          </Link>

          <!-- 2. Tugas / Task -->
          <Link 
            href="/tasks" 
            class="flex flex-col items-center justify-center py-1 transition-all active:scale-90"
            :class="mobileTabClass(['/pekerjaan', '/tasks'])"
          >
            <i class="fas fa-clipboard-check text-lg"></i>
            <span class="text-[10px] mt-0.5 font-medium tracking-tight">Tugas</span>
          </Link>

          <!-- 3. Tab Tengah Dinamis: Eskalasi (Pimpinan) / Lapor Isu (Staf) -->
          <Link 
            v-if="isLeaderOrAdmin"
            href="/eskalasi" 
            class="flex flex-col items-center justify-center py-1 transition-all active:scale-90 relative"
            :class="mobileTabClass(['/eskalasi', '/escalations'])"
          >
            <div class="relative">
              <i class="fas fa-bolt text-lg"></i>
            </div>
            <span class="text-[10px] mt-0.5 font-medium tracking-tight">Eskalasi</span>
          </Link>
          <Link 
            v-else
            href="/issues" 
            class="flex flex-col items-center justify-center py-1 transition-all active:scale-90 relative"
            :class="mobileTabClass('/issues')"
          >
            <div class="relative">
              <i class="fas fa-triangle-exclamation text-lg"></i>
            </div>
            <span class="text-[10px] mt-0.5 font-medium tracking-tight">Isu Staf</span>
          </Link>

          <!-- 4. Rapat Mingguan -->
          <Link 
            href="/rapat" 
            class="flex flex-col items-center justify-center py-1 transition-all active:scale-90"
            :class="mobileTabClass('/rapat')"
          >
            <i class="fas fa-calendar-days text-lg"></i>
            <span class="text-[10px] mt-0.5 font-medium tracking-tight">Rapat</span>
          </Link>

          <!-- 5. Menu Lainnya (Buka iOS Bottom Sheet) -->
          <button 
            type="button" 
            @click="mobileMoreSheetOpen = true"
            class="flex flex-col items-center justify-center py-1 transition-all active:scale-90 cursor-pointer"
            :class="mobileMoreSheetOpen ? 'text-sky-600 font-bold' : 'text-slate-400 hover:text-slate-600'"
          >
            <i class="fas fa-grip-vertical text-lg"></i>
            <span class="text-[10px] mt-0.5 font-medium tracking-tight">Menu</span>
          </button>

        </div>
      </nav>

      <!-- ======================================================= -->
      <!-- D. NATIVE IOS-STYLE BOTTOM SHEET (MENU LAINNYA)         -->
      <!-- ======================================================= -->
      <Teleport to="body">
        <div 
          v-if="mobileMoreSheetOpen" 
          class="fixed inset-0 z-50 md:hidden flex items-end justify-center"
        >
          <!-- Backdrop Blur Hitam Transparan -->
          <div 
            @click="mobileMoreSheetOpen = false" 
            class="absolute inset-0 bg-slate-950/60 backdrop-blur-xs transition-opacity duration-300"
          ></div>

          <!-- Bottom Sheet Card Modal -->
          <div class="relative w-full max-w-lg bg-white rounded-t-[32px] p-5 pb-safe shadow-2xl z-10 space-y-4 max-h-[85vh] overflow-y-auto animate-in slide-in-from-bottom duration-300">
            
            <!-- Drag Grab Handle Pill -->
            <div class="w-12 h-1.5 bg-slate-300 rounded-full mx-auto -mt-1 mb-2"></div>

            <!-- Header Info Profil Singkat -->
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
              <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-full bg-slate-200 overflow-hidden ring-2 ring-sky-500/30">
                  <img :src="user?.avatar_url" alt="Avatar" class="w-full h-full object-cover" />
                </div>
                <div>
                  <h3 class="text-xs font-bold text-slate-800">{{ user?.name }}</h3>
                  <p class="text-[11px] text-slate-500">
                    <span class="font-bold text-sky-600 uppercase">{{ userRole }}</span> &bull; {{ user?.organization_unit?.name || 'UP Sengkang' }}
                  </p>
                </div>
              </div>
              <button 
                @click="mobileMoreSheetOpen = false" 
                class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 flex items-center justify-center text-xs cursor-pointer"
              >
                <i class="fas fa-times"></i>
              </button>
            </div>

            <!-- Grid App Menu Icons ala Native App -->
            <div>
              <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2.5">
                {{ isSuperAdmin ? 'Menu Khusus Super Admin' : 'Menu Fitur Tambahan' }}
              </p>

              <!-- JIKA SUPER ADMIN -->
              <div v-if="isSuperAdmin" class="grid grid-cols-2 gap-3 text-center">
                <!-- Kelola Akun Pegawai -->
                <Link 
                  href="/master-data?tab=users" 
                  @click="mobileMoreSheetOpen = false"
                  class="flex flex-col items-center justify-center p-3.5 rounded-2xl bg-sky-50/70 border border-sky-200/80 active:scale-95 transition-transform"
                >
                  <div class="w-11 h-11 rounded-xl bg-sky-600 text-white flex items-center justify-center text-lg shadow-xs mb-2">
                    <i class="fas fa-users-gear"></i>
                  </div>
                  <span class="text-xs font-bold text-slate-800">Kelola Akun</span>
                  <span class="text-[10px] text-slate-500 mt-0.5">Pegawai & Jabatan</span>
                </Link>

                <!-- Kelola Image & Logo -->
                <Link 
                  href="/master-data?tab=branding" 
                  @click="mobileMoreSheetOpen = false"
                  class="flex flex-col items-center justify-center p-3.5 rounded-2xl bg-amber-50/70 border border-amber-200/80 active:scale-95 transition-transform"
                >
                  <div class="w-11 h-11 rounded-xl bg-amber-500 text-white flex items-center justify-center text-lg shadow-xs mb-2">
                    <i class="fas fa-palette"></i>
                  </div>
                  <span class="text-xs font-bold text-slate-800">Image & Logo</span>
                  <span class="text-[10px] text-slate-500 mt-0.5">Login & Branding</span>
                </Link>

                <!-- Direktori Akun -->
                <Link 
                  href="/daftar-akun" 
                  @click="mobileMoreSheetOpen = false"
                  class="flex flex-col items-center justify-center p-3.5 rounded-2xl bg-teal-50/70 border border-teal-200/80 active:scale-95 transition-transform"
                >
                  <div class="w-11 h-11 rounded-xl bg-teal-600 text-white flex items-center justify-center text-lg shadow-xs mb-2">
                    <i class="fas fa-address-book"></i>
                  </div>
                  <span class="text-xs font-bold text-slate-800">Direktori Akun</span>
                  <span class="text-[10px] text-slate-500 mt-0.5">154 Pegawai</span>
                </Link>

                <!-- Profil Akun -->
                <Link 
                  href="/profil" 
                  @click="mobileMoreSheetOpen = false"
                  class="flex flex-col items-center justify-center p-3.5 rounded-2xl bg-slate-50 border border-slate-200 active:scale-95 transition-transform"
                >
                  <div class="w-11 h-11 rounded-xl bg-slate-700 text-white flex items-center justify-center text-lg shadow-xs mb-2">
                    <i class="fas fa-user-circle"></i>
                  </div>
                  <span class="text-xs font-bold text-slate-800">Profil Saya</span>
                  <span class="text-[10px] text-slate-500 mt-0.5">Pengaturan Akun</span>
                </Link>

                <!-- Tombol Logout (Full width) -->
                <button 
                  type="button" 
                  @click="logout"
                  class="col-span-2 flex items-center justify-center gap-2 p-3 rounded-2xl bg-rose-50/70 border border-rose-200/80 text-rose-600 font-bold text-xs active:scale-95 transition-transform cursor-pointer"
                >
                  <i class="fas fa-arrow-right-from-bracket"></i>
                  <span>Keluar dari Sistem</span>
                </button>
              </div>

              <!-- JIKA REGULER (STAFF, TL, ASMAN, MUP, DLL) -->
              <div v-else class="grid grid-cols-3 gap-2.5 text-center">

                <!-- Keputusan -->
                <Link 
                  href="/keputusan" 
                  @click="mobileMoreSheetOpen = false"
                  class="flex flex-col items-center justify-center p-3 rounded-2xl bg-amber-50/60 border border-amber-200/60 active:scale-95 transition-transform"
                >
                  <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center text-base shadow-xs mb-1.5">
                    <i class="fas fa-file-signature"></i>
                  </div>
                  <span class="text-[11px] font-bold text-slate-700">Keputusan</span>
                </Link>

                <!-- Informasi -->
                <Link 
                  href="/informasi" 
                  @click="mobileMoreSheetOpen = false"
                  class="flex flex-col items-center justify-center p-3 rounded-2xl bg-blue-50/60 border border-blue-200/60 active:scale-95 transition-transform"
                >
                  <div class="w-10 h-10 rounded-xl bg-blue-500 text-white flex items-center justify-center text-base shadow-xs mb-1.5">
                    <i class="fas fa-bullhorn"></i>
                  </div>
                  <span class="text-[11px] font-bold text-slate-700">Informasi</span>
                </Link>

                <!-- Laporan -->
                <Link 
                  href="/laporan" 
                  @click="mobileMoreSheetOpen = false"
                  class="flex flex-col items-center justify-center p-3 rounded-2xl bg-emerald-50/60 border border-emerald-200/60 active:scale-95 transition-transform"
                >
                  <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-base shadow-xs mb-1.5">
                    <i class="fas fa-chart-pie"></i>
                  </div>
                  <span class="text-[11px] font-bold text-slate-700">Laporan</span>
                </Link>

                <!-- Daftar Akun (Semua Role) -->
                <Link 
                  href="/daftar-akun" 
                  @click="mobileMoreSheetOpen = false"
                  class="flex flex-col items-center justify-center p-3 rounded-2xl bg-teal-50/60 border border-teal-200/60 active:scale-95 transition-transform"
                >
                  <div class="w-10 h-10 rounded-xl bg-teal-600 text-white flex items-center justify-center text-base shadow-xs mb-1.5">
                    <i class="fas fa-users-gear"></i>
                  </div>
                  <span class="text-[11px] font-bold text-slate-700">Daftar Akun</span>
                </Link>

                <!-- Master Data (Khusus MUP & Admin) -->
                <Link 
                  v-if="isMupOrAdmin"
                  href="/master-data" 
                  @click="mobileMoreSheetOpen = false"
                  class="flex flex-col items-center justify-center p-3 rounded-2xl bg-purple-50/60 border border-purple-200/60 active:scale-95 transition-transform"
                >
                  <div class="w-10 h-10 rounded-xl bg-purple-600 text-white flex items-center justify-center text-base shadow-xs mb-1.5">
                    <i class="fas fa-sliders-h"></i>
                  </div>
                  <span class="text-[11px] font-bold text-slate-700">Master Data</span>
                </Link>

                <!-- Profil Akun -->
                <Link 
                  href="/profil" 
                  @click="mobileMoreSheetOpen = false"
                  class="flex flex-col items-center justify-center p-3 rounded-2xl bg-slate-50 border border-slate-200 active:scale-95 transition-transform"
                >
                  <div class="w-10 h-10 rounded-xl bg-slate-700 text-white flex items-center justify-center text-base shadow-xs mb-1.5">
                    <i class="fas fa-user-circle"></i>
                  </div>
                  <span class="text-[11px] font-bold text-slate-700">Profil Saya</span>
                </Link>

                <!-- Tombol Logout -->
                <button 
                  type="button" 
                  @click="logout"
                  class="flex flex-col items-center justify-center p-3 rounded-2xl bg-rose-50/60 border border-rose-200/60 active:scale-95 transition-transform cursor-pointer"
                >
                  <div class="w-10 h-10 rounded-xl bg-rose-500 text-white flex items-center justify-center text-base shadow-xs mb-1.5">
                    <i class="fas fa-arrow-right-from-bracket"></i>
                  </div>
                  <span class="text-[11px] font-bold text-rose-600">Keluar</span>
                </button>

              </div>
            </div>

            <!-- Footer Sheet -->
            <div class="pt-2 text-center text-[10px] text-slate-400 border-t border-slate-100 flex items-center justify-between">
              <span>&copy; {{ new Date().getFullYear() }} PLN NP UP Sengkang</span>
              <span class="font-bold text-slate-500">devATP</span>
            </div>

          </div>
        </div>
      </Teleport>

    </div>

  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import { useConfirm } from '@/Composables/useConfirm';

const props = defineProps({
  title: {
    type: String,
    default: 'Dashboard'
  }
});

const page = usePage();
const mobileMoreSheetOpen = ref(false);
const { confirm } = useConfirm();

const user = computed(() => page.props.auth?.user);

const userRole = computed(() => (user.value?.role?.name || '').toUpperCase());

const isSuperAdmin = computed(() => {
  return ['SUPER_ADMIN', 'SUPER ADMIN'].includes(userRole.value);
});

const isMupOrAdmin = computed(() => {
  return ['MUP', 'ADMIN', 'SUPER_ADMIN', 'SUPER ADMIN'].includes(userRole.value);
});

const isLeaderOrAdmin = computed(() => {
  return ['TL', 'ASMAN', 'MUP', 'ADMIN'].includes(userRole.value);
});

const brandingLogo = computed(() => page.props.branding?.logo_url || '/assets/bumn/logo-pln.png');

function isTabActive(pathOrPaths) {
  const currentUrl = page.url;
  const paths = Array.isArray(pathOrPaths) ? pathOrPaths : [pathOrPaths];
  return paths.some(p => {
    if (p === '/master-data?tab=users') {
      return currentUrl === '/master-data' || currentUrl.startsWith('/master-data?tab=users') || currentUrl === '/master-data/';
    }
    if (p === '/master-data?tab=branding') {
      return currentUrl.startsWith('/master-data?tab=branding');
    }
    if (p.includes('?')) {
      return currentUrl === p || currentUrl.startsWith(p);
    }
    return currentUrl === p || (p !== '/' && currentUrl.startsWith(p));
  });
}

function navClass(pathOrPaths) {
  const isActive = isTabActive(pathOrPaths);
  if (isActive) {
    return 'flex items-center gap-3 px-4 py-2.5 bg-pln-blue text-white rounded-lg shadow-md font-bold transition';
  }
  return 'flex items-center gap-3 px-4 py-2.5 text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-colors';
}

function mobileTabClass(pathOrPaths) {
  const active = isTabActive(pathOrPaths);
  if (active) {
    return 'text-sky-600 font-extrabold scale-105 transition-all';
  }
  return 'text-slate-400 hover:text-slate-600 transition-colors';
}

async function logout() {
  mobileMoreSheetOpen.value = false;
  const confirmed = await confirm({
    title: 'Konfirmasi Keluar',
    message: 'Apakah Anda yakin ingin keluar dari sistem Sistem Koordinasi PLN UP Sengkang?',
    confirmText: 'Ya, Keluar',
    cancelText: 'Batal',
    type: 'warning',
    icon: 'fas fa-arrow-right-from-bracket',
  });
  if (confirmed) {
    router.post('/logout');
  }
}

const currentDateFormatted = computed(() => {
  const now = new Date();
  return now.toLocaleDateString('id-ID', {
    weekday: 'long',
    day: 'numeric',
    month: 'short',
    year: 'numeric'
  });
});
</script>

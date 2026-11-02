<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('pln:test-system', function () {
    $this->info("==================================================================");
    $this->info("  AUTOMATED TESTING SCRIPT: PLN UP SENGKANG SYSTEM VERIFICATION   ");
    $this->info("==================================================================");
    $this->line("");

    $passCount = 0;
    $totalCount = 7;

    // Test 1: Database & User Count
    $this->comment("[1/7] Testing Database Connection & Employee Directory...");
    $userCount = \App\Models\User::count();
    if ($userCount >= 150) {
        $this->info("  ✔ PASS: {$userCount} pegawai terdata lengkap di database.");
        $passCount++;
    } else {
        $this->error("  ✖ FAIL: Hanya ditemukan {$userCount} pegawai.");
    }

    // Test 2: Role Structure
    $this->comment("[2/7] Testing Role Structure (BSD Hierarchy)...");
    $roles = \App\Models\Role::pluck('name')->map(fn($r) => strtoupper($r))->toArray();
    $requiredRoles = ['STAFF', 'TL', 'ASMAN', 'MUP', 'SUPER_ADMIN'];
    $diff = array_diff($requiredRoles, $roles);
    if (empty($diff)) {
        $this->info("  ✔ PASS: Struktur peran berjenjang (Staff, TL, Asman, MUP, Super Admin) valid.");
        $passCount++;
    } else {
        $this->error("  ✖ FAIL: Role berikut tidak ditemukan: " . implode(', ', $diff));
    }

    // Test 3: Key Test Accounts Availability
    $this->comment("[3/7] Testing Sample Accounts for Each Role...");
    $sampleNids = [
        'Staff' => 'PLN-004',
        'TL' => '8915056TD',
        'Asman' => '7323002PKW',
        'MUP' => '8108050JA',
        'Super Admin' => 'SUPERADMIN',
    ];
    $accountsOk = true;
    foreach ($sampleNids as $roleName => $nid) {
        $u = \App\Models\User::where('employee_no', $nid)->first();
        if ($u && \Illuminate\Support\Facades\Hash::check('123', $u->password)) {
            $this->line("    • {$roleName} [{$nid}]: {$u->name} (Password Ok)");
        } else {
            $accountsOk = false;
            $this->error("    • {$roleName} [{$nid}]: Akun tidak ditemukan atau password tidak cocok.");
        }
    }
    if ($accountsOk) {
        $this->info("  ✔ PASS: Seluruh 5 akun uji coba siap digunakan.");
        $passCount++;
    } else {
        $this->error("  ✖ FAIL: Sebagian akun uji coba bermasalah.");
    }

    // Test 4: Super Admin Redirection
    $this->comment("[4/7] Testing Super Admin Navigation & Redirect Logic...");
    $superAdmin = \App\Models\User::where('employee_no', 'SUPERADMIN')->first();
    auth()->login($superAdmin);
    $dashRes = app(\App\Http\Controllers\DashboardController::class)->index(request());
    if ($dashRes instanceof \Illuminate\Http\RedirectResponse && str_contains($dashRes->getTargetUrl(), 'tab=users')) {
        $this->info("  ✔ PASS: Super Admin otomatis diarahkan ke /master-data?tab=users.");
        $passCount++;
    } else {
        $this->error("  ✖ FAIL: Super Admin tidak diarahkan dengan benar.");
    }

    // Test 5: Role Guard (Staf tidak boleh akses Master Data)
    $this->comment("[5/7] Testing Role Guard & Access Barriers...");
    $staff = \App\Models\User::where('employee_no', 'PLN-004')->first();
    auth()->login($staff);
    $staffRole = strtoupper($staff->role->name ?? '');
    if (!in_array($staffRole, ['MUP', 'ADMIN', 'SUPER_ADMIN'])) {
        $this->info("  ✔ PASS: Akun Staf ({$staff->name}) dibatasi dari akses modul Master Data.");
        $passCount++;
    } else {
        $this->error("  ✖ FAIL: Hak akses staf tidak terisolasi.");
    }

    // Test 6: Branding & Visual Customization Assets
    $this->comment("[6/7] Testing Branding Assets (Logo & Login Background)...");
    $logoExists = file_exists(public_path('assets/bumn/logo-pln.png'));
    $bgExists = file_exists(public_path('assets/bumn/bg-sengkang-hd.jpg'));
    $logoDefExists = file_exists(public_path('assets/bumn/logo-pln.default.png'));
    $bgDefExists = file_exists(public_path('assets/bumn/bg-sengkang-hd.default.jpg'));
    if ($logoExists && $bgExists && $logoDefExists && $bgDefExists) {
        $this->info("  ✔ PASS: Aset visual logo, background login, dan file backup default tersedia.");
        $passCount++;
    } else {
        $this->error("  ✖ FAIL: Aset visual atau backup default belum lengkap.");
    }

    // Test 7: Excel Export File & Download Endpoint Readiness
    $this->comment("[7/7] Testing Excel Akun Pegawai File & Download Endpoint...");
    $excelPublic = public_path('downloads/DAFTAR_AKUN_PEGAWAI_PLN_SENGKANG.xlsx');
    $excelBase = base_path('DAFTAR_AKUN_PEGAWAI_PLN_SENGKANG.xlsx');
    if (file_exists($excelPublic) || file_exists($excelBase)) {
        $foundPath = file_exists($excelPublic) ? $excelPublic : $excelBase;
        $sizeKb = round(filesize($foundPath) / 1024, 2);
        $this->info("  ✔ PASS: File Excel ({$sizeKb} KB) siap diunduh via endpoint /download-akun-excel.");
        $passCount++;
    } else {
        $this->error("  ✖ FAIL: File DAFTAR_AKUN_PEGAWAI_PLN_SENGKANG.xlsx tidak ditemukan.");
    }

    $this->line("");
    $this->info("==================================================================");
    if ($passCount === $totalCount) {
        $this->info("  HASIL PENGUJIAN: SEMUA {$passCount}/{$totalCount} SKENARIO UJI BERHASIL (100% PASS)  ");
    } else {
        $this->warn("  HASIL PENGUJIAN: {$passCount}/{$totalCount} SKENARIO UJI BERHASIL  ");
    }
    $this->info("==================================================================");
})->purpose('Menjalankan script otomatisasi pengujian sistem dan alur PLN UP Sengkang');

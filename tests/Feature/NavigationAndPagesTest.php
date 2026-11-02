<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class NavigationAndPagesTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;
    protected $mupUser;

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('inertia.testing.ensure_pages_exist', false);

        $this->user = User::where('is_active', 1)
            ->whereHas('role', fn($q) => $q->where('name', 'STAFF'))
            ->first();

        $this->mupUser = User::where('is_active', 1)
            ->whereHas('role', fn($q) => $q->where('name', 'MUP'))
            ->first();
    }

    /**
     * Halaman Dashboard dapat dimuat dengan baik.
     */
    public function test_dashboard_page_loads_successfully(): void
    {
        $response = $this->actingAs($this->user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Dashboard/Index'));
    }

    /**
     * Halaman Kelola Pekerjaan / Task dapat dimuat.
     */
    public function test_tasks_page_loads_successfully(): void
    {
        $response = $this->actingAs($this->user)->get('/tasks');
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Tasks/Index'));
    }

    /**
     * Halaman Kelola Issue dapat dimuat.
     */
    public function test_issues_page_loads_successfully(): void
    {
        $response = $this->actingAs($this->user)->get('/issues');
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Issues/Index'));
    }

    /**
     * Halaman Daftar Eskalasi dapat dimuat.
     */
    public function test_escalations_page_loads_successfully(): void
    {
        $tlUser = User::where('is_active', 1)->whereHas('role', fn($q) => $q->where('name', 'TL'))->first();
        $this->assertNotNull($tlUser);

        $response = $this->actingAs($tlUser)->get('/eskalasi');
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Issues/Index'));
    }

    /**
     * Halaman Rapat Mingguan dapat dimuat.
     */
    public function test_meetings_page_loads_successfully(): void
    {
        $response = $this->actingAs($this->user)->get('/rapat');
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Meetings/Index'));
    }

    /**
     * Halaman Rekap Keputusan dapat dimuat.
     */
    public function test_decisions_page_loads_successfully(): void
    {
        $response = $this->actingAs($this->user)->get('/keputusan');
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Decisions/Index'));
    }

    /**
     * Halaman Informasi & Pengumuman dapat dimuat.
     */
    public function test_information_page_loads_successfully(): void
    {
        $response = $this->actingAs($this->user)->get('/informasi');
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('InfoReports/Informasi'));
    }

    /**
     * Halaman Laporan Operasi dapat dimuat.
     */
    public function test_reports_page_loads_successfully(): void
    {
        $response = $this->actingAs($this->user)->get('/laporan');
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('InfoReports/Laporan'));
    }

    /**
     * Halaman Direktori Akun (Daftar Seluruh Akun Pegawai) dapat dimuat.
     */
    public function test_accounts_directory_page_loads_successfully(): void
    {
        $response = $this->actingAs($this->user)->get('/daftar-akun');
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Accounts/Index'));
    }

    /**
     * Halaman Pengaturan Profil Akun dapat dimuat.
     */
    public function test_profile_page_loads_successfully(): void
    {
        $response = $this->actingAs($this->user)->get('/profil');
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Profile/Index'));
    }

    /**
     * Halaman Master Data dapat dimuat oleh pimpinan (MUP).
     */
    public function test_master_data_page_loads_for_mup(): void
    {
        $response = $this->actingAs($this->mupUser)->get('/master-data');
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Master/Index'));
    }
}

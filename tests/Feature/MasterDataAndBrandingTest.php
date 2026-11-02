<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MasterDataAndBrandingTest extends TestCase
{
    use DatabaseTransactions;

    protected $superAdmin;
    protected $mup;

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('inertia.testing.ensure_pages_exist', false);

        $this->superAdmin = User::where('employee_no', 'SUPERADMIN')->first();
        $this->mup = User::where('is_active', 1)
            ->whereHas('role', fn($q) => $q->where('name', 'MUP'))
            ->first();
    }

    /**
     * Super Admin dapat mengakses Master Data tab users (Kelola Akun Pegawai).
     */
    public function test_super_admin_can_access_master_data_users_tab(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/master-data?tab=users');
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Master/Index')
            ->has('users')
            ->has('roles')
            ->has('branding')
        );
    }

    /**
     * Super Admin dapat menambahkan akun pegawai baru ke dalam sistem.
     */
    public function test_super_admin_can_create_new_user(): void
    {
        $role = Role::where('name', 'STAFF')->first() ?? Role::first();

        $response = $this->actingAs($this->superAdmin)->post('/master-data/users', [
            'name' => '[TEST-QA] Pegawai Baru Uji',
            'email' => 'pegawai_uji_qa@pln.co.id',
            'employee_no' => 'QA-' . mt_rand(1000, 9999),
            'role_id' => $role->id,
            'password' => '123',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'name' => '[TEST-QA] Pegawai Baru Uji',
            'email' => 'pegawai_uji_qa@pln.co.id',
        ]);
    }

    /**
     * Validasi pembuatan akun pegawai menolak kolom kosong.
     */
    public function test_user_creation_requires_name_email_and_role(): void
    {
        $response = $this->actingAs($this->superAdmin)->from('/master-data?tab=users')->post('/master-data/users', [
            'name' => '',
            'email' => 'bukan-email-valid',
            'role_id' => '',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['name', 'email', 'role_id']);
    }

    /**
     * Super Admin dapat me-reset password pegawai kembali ke default ('123').
     */
    public function test_super_admin_can_reset_user_password(): void
    {
        $user = User::where('is_active', 1)
            ->where('employee_no', '!=', 'SUPERADMIN')
            ->first();

        $this->assertNotNull($user);

        $response = $this->actingAs($this->superAdmin)->post("/master-data/users/{$user->id}/reset-password");
        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('123', $user->password));
    }

    /**
     * Super Admin dapat mengembalikan logo dan background login ke default resmi PLN.
     */
    public function test_super_admin_can_reset_branding_to_default(): void
    {
        // 1. Reset Logo
        $responseLogo = $this->actingAs($this->superAdmin)->post('/master-data/branding/reset-logo');
        $responseLogo->assertStatus(302);
        $responseLogo->assertSessionHas('success');

        // 2. Reset Background
        $responseBg = $this->actingAs($this->superAdmin)->post('/master-data/branding/reset-background');
        $responseBg->assertStatus(302);
        $responseBg->assertSessionHas('success');
    }

    /**
     * Endpoint /download-akun-excel mengembalikan file excel lampiran yang valid.
     */
    public function test_download_excel_endpoint_returns_valid_excel_download(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/download-akun-excel');
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }
}

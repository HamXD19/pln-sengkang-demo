<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AuthAndAccessTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('inertia.testing.ensure_pages_exist', false);
    }

    /**
     * Tamu tanpa autentikasi dialihkan ke /login saat membuka /dashboard.
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /**
     * Halaman login publik dapat diakses dan menyertakan data branding PLN.
     */
    public function test_user_can_view_login_page_with_branding(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Auth/Login')
            ->has('branding.logo_url')
            ->has('branding.login_bg_url')
        );
    }

    /**
     * Pengguna aktif dapat masuk menggunakan NID dan password default yang valid.
     */
    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::where('is_active', 1)->whereHas('role', fn($q) => $q->where('name', 'STAFF'))->first();
        $this->assertNotNull($user, 'Harus ada akun staf aktif di database.');

        $response = $this->post('/login', [
            'nid' => $user->employee_no,
            'password' => '123',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Akun yang dinonaktifkan ditolak saat mencoba login.
     */
    public function test_inactive_user_cannot_login(): void
    {
        $inactiveUser = User::where('is_active', 0)->first();
        if ($inactiveUser) {
            $response = $this->from('/login')->post('/login', [
                'nid' => $inactiveUser->employee_no,
                'password' => '123',
            ]);

            $response->assertStatus(302);
            $response->assertRedirect('/login');
            $response->assertSessionHasErrors(['nid']);
            $this->assertGuest();
        } else {
            $this->markTestSkipped('Tidak ada akun nonaktif di database.');
        }
    }

    /**
     * Login ditolak jika NID atau password salah.
     */
    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        $response = $this->from('/login')->post('/login', [
            'nid' => 'PLN-004',
            'password' => 'password_salah_123',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['nid']);
        $this->assertGuest();
    }

    /**
     * Validasi form login menolak pengiriman kolom kosong.
     */
    public function test_login_requires_nid_and_password(): void
    {
        $response = $this->from('/login')->post('/login', [
            'nid' => '',
            'password' => '',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['nid', 'password']);
    }

    /**
     * Super Admin otomatis diarahkan ke /master-data?tab=users saat masuk ke /dashboard.
     */
    public function test_super_admin_redirected_to_master_data(): void
    {
        $superAdmin = User::where('employee_no', 'SUPERADMIN')->first();
        $this->assertNotNull($superAdmin, 'Akun Super Admin harus tersedia di database.');

        $response = $this->actingAs($superAdmin)->get('/dashboard');
        $response->assertStatus(302);
        $response->assertRedirect('/master-data?tab=users');
    }

    /**
     * Role Staf dilarang membuka halaman /master-data (RoleMiddleware 403 Forbidden).
     */
    public function test_staff_cannot_access_master_data(): void
    {
        $staff = User::where('employee_no', 'PLN-004')->first();
        $this->assertNotNull($staff);

        $response = $this->actingAs($staff)->get('/master-data');
        $response->assertStatus(403);
    }

    /**
     * Pengguna terautentikasi dapat logout dengan sukses.
     */
    public function test_authenticated_user_can_logout(): void
    {
        $user = User::first();
        $response = $this->actingAs($user)->post('/logout');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertGuest();
    }
}

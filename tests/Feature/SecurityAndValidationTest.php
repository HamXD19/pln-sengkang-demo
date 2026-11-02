<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SecurityAndValidationTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;
    protected $tl;

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('inertia.testing.ensure_pages_exist', false);

        $this->user = User::where('is_active', 1)
            ->whereHas('role', fn($q) => $q->where('name', 'STAFF'))
            ->first();

        $this->tl = User::where('is_active', 1)
            ->whereHas('role', fn($q) => $q->where('name', 'TL'))
            ->first();
    }

    /**
     * Pembaruan profil menolak format email yang tidak valid.
     */
    public function test_user_cannot_update_profile_with_invalid_email(): void
    {
        $response = $this->actingAs($this->user)->from('/profil')->post('/profil', [
            'name' => 'Nama Baru',
            'email' => 'format-email-tidak-benar',
            'phone' => '08123456789',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/profil');
        $response->assertSessionHasErrors(['email']);
    }

    /**
     * Pengguna dapat memperbarui nama dan nomor telepon pada profil akun.
     */
    public function test_user_can_update_profile_name_and_phone(): void
    {
        $response = $this->actingAs($this->user)->post('/profil', [
            'name' => $this->user->name . ' Updated',
            'email' => $this->user->email,
            'phone' => '081299887766',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $this->user->refresh();
        $this->assertEquals('081299887766', $this->user->phone);
    }

    /**
     * Penggantian password mandiri ditolak jika password saat ini salah.
     */
    public function test_user_cannot_change_password_with_wrong_current_password(): void
    {
        $response = $this->actingAs($this->user)->from('/profil')->post('/profil/password', [
            'current_password' => 'password_salah_total',
            'new_password' => 'passwordBaru123',
            'new_password_confirmation' => 'passwordBaru123',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['current_password']);
    }

    /**
     * Pengguna dapat mengubah password mandiri jika password saat ini benar.
     */
    public function test_user_can_change_password_with_valid_current_password(): void
    {
        $response = $this->actingAs($this->user)->post('/profil/password', [
            'current_password' => '123',
            'new_password' => 'passwordBaru123',
            'new_password_confirmation' => 'passwordBaru123',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $this->user->refresh();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('passwordBaru123', $this->user->password));
    }

    /**
     * Leader (TL/Asman/MUP) berwenang menerbitkan pengumuman/arahan ke sistem.
     */
    public function test_leader_can_publish_information_announcement(): void
    {
        $response = $this->actingAs($this->tl)->post('/informasi', [
            'title' => '[TEST-QA] Arahan K3: Pemakaian APD Lengkap di Turbin Hall',
            'content' => 'Seluruh personil yang bertugas wajib mengenakan safety shoes dan ear protection.',
            'category' => 'ARAHAN',
            'target_level' => 'ALL',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('information', [
            'title' => '[TEST-QA] Arahan K3: Pemakaian APD Lengkap di Turbin Hall',
            'category' => 'ARAHAN',
        ]);
    }

    /**
     * Staf dilarang menerbitkan informasi/arahan (hanya pimpinan).
     */
    public function test_staff_cannot_publish_information_announcement(): void
    {
        $response = $this->actingAs($this->user)->post('/informasi', [
            'title' => '[TEST-QA] Pengumuman Ilegal Staf',
            'content' => 'Konten pengumuman staf.',
            'category' => 'PENGUMUMAN',
            'target_level' => 'ALL',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('error');
    }

    /**
     * Fitur ekspor CSV laporan koordinasi mengembalikan respon file stream yang valid.
     */
    public function test_laporan_export_csv_returns_streamed_csv_response(): void
    {
        $response = $this->actingAs($this->user)->get('/laporan/export-csv');
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }
}

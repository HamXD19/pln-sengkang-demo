<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TaskCrudTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('inertia.testing.ensure_pages_exist', false);

        $this->user = User::where('is_active', 1)
            ->whereHas('role', fn($q) => $q->where('name', 'TL'))
            ->first() ?? User::where('is_active', 1)->first();
    }

    /**
     * Pengguna dapat membuat task baru dengan data yang valid.
     */
    public function test_user_can_create_task_with_valid_data(): void
    {
        $response = $this->actingAs($this->user)->post('/tasks', [
            'title' => '[TEST-QA] Pemeliharaan Rutin Pompa Pendingin',
            'description' => 'Pemeriksaan getaran dan suhu bearing pompa sirkulasi.',
            'assigned_to' => $this->user->id,
            'start_date' => now()->toDateString(),
            'due_date' => now()->addDays(3)->toDateString(),
            'progress_percent' => 10,
            'status' => 'In Progress',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tasks', [
            'title' => '[TEST-QA] Pemeliharaan Rutin Pompa Pendingin',
            'assigned_to' => $this->user->id,
        ]);
    }

    /**
     * Form penambahan task menolak jika kolom wajib (title, assigned_to, due_date) kosong.
     */
    public function test_create_task_fails_with_missing_required_fields(): void
    {
        $response = $this->actingAs($this->user)->from('/tasks')->post('/tasks', [
            'title' => '',
            'assigned_to' => '',
            'due_date' => '',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/tasks');
        $response->assertSessionHasErrors(['title', 'assigned_to', 'due_date']);
    }

    /**
     * Pengguna dapat memperbarui persentase progres pekerjaan.
     */
    public function test_user_can_update_task_progress(): void
    {
        $task = Task::create([
            'title' => '[TEST-QA] Uji Progres Task',
            'assigned_to' => $this->user->id,
            'assigned_by' => $this->user->id,
            'unit_id' => $this->user->unit_id,
            'due_date' => now()->addDays(5)->toDateString(),
            'progress_percent' => 25,
            'status' => 'In Progress',
            'week_number' => now()->isoWeek(),
            'year' => now()->year,
        ]);

        $response = $this->actingAs($this->user)->patch("/tasks/{$task->id}/progress", [
            'progress_percent' => 75,
            'completion_notes' => 'Progres inspeksi 75% selesai.',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $task->refresh();
        $this->assertEquals(75, $task->progress_percent);
        $this->assertEquals('Progres inspeksi 75% selesai.', $task->completion_notes);
    }

    /**
     * Pembaruan progres ke 100% otomatis mengubah status pekerjaan menjadi 'Selesai'.
     */
    public function test_updating_progress_to_100_percent_marks_task_as_selesai(): void
    {
        $task = Task::create([
            'title' => '[TEST-QA] Uji Tuntas 100%',
            'assigned_to' => $this->user->id,
            'assigned_by' => $this->user->id,
            'unit_id' => $this->user->unit_id,
            'due_date' => now()->addDays(2)->toDateString(),
            'progress_percent' => 50,
            'status' => 'In Progress',
            'week_number' => now()->isoWeek(),
            'year' => now()->year,
        ]);

        $response = $this->actingAs($this->user)->patch("/tasks/{$task->id}/progress", [
            'progress_percent' => 100,
            'completion_notes' => 'Pekerjaan telah rampung 100%.',
        ]);

        $response->assertStatus(302);
        $task->refresh();
        $this->assertEquals(100, $task->progress_percent);
        $this->assertEquals('Selesai', $task->status);
    }

    /**
     * Pengguna dapat mengubah rincian data pekerjaan melalui form edit.
     */
    public function test_user_can_update_task_details(): void
    {
        $task = Task::create([
            'title' => '[TEST-QA] Task Sebelum Edit',
            'assigned_to' => $this->user->id,
            'assigned_by' => $this->user->id,
            'unit_id' => $this->user->unit_id,
            'due_date' => now()->addDays(4)->toDateString(),
            'progress_percent' => 0,
            'status' => 'Open',
            'week_number' => now()->isoWeek(),
            'year' => now()->year,
        ]);

        $response = $this->actingAs($this->user)->put("/tasks/{$task->id}", [
            'title' => '[TEST-QA] Task Setelah Diubah',
            'description' => 'Deskripsi baru yang diperbarui.',
            'assigned_to' => $this->user->id,
            'due_date' => now()->addDays(7)->toDateString(),
            'progress_percent' => 30,
            'status' => 'In Progress',
        ]);

        $response->assertStatus(302);
        $task->refresh();
        $this->assertEquals('[TEST-QA] Task Setelah Diubah', $task->title);
        $this->assertEquals(30, $task->progress_percent);
    }

    /**
     * Pengguna dapat menghapus data pekerjaan.
     */
    public function test_user_can_delete_task(): void
    {
        $task = Task::create([
            'title' => '[TEST-QA] Task Untuk Dihapus',
            'assigned_to' => $this->user->id,
            'assigned_by' => $this->user->id,
            'unit_id' => $this->user->unit_id,
            'due_date' => now()->addDays(1)->toDateString(),
            'progress_percent' => 0,
            'status' => 'Open',
            'week_number' => now()->isoWeek(),
            'year' => now()->year,
        ]);

        $response = $this->actingAs($this->user)->delete("/tasks/{$task->id}");
        $response->assertStatus(302);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}

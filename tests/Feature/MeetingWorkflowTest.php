<?php

namespace Tests\Feature;

use App\Models\Issue;
use App\Models\Meeting;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MeetingWorkflowTest extends TestCase
{
    use DatabaseTransactions;

    protected $asman;
    protected $tl;
    protected $staff;

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('inertia.testing.ensure_pages_exist', false);

        $this->asman = User::where('is_active', 1)
            ->whereHas('role', fn($q) => $q->where('name', 'ASMAN'))
            ->first();

        $this->tl = User::where('is_active', 1)
            ->whereHas('role', fn($q) => $q->where('name', 'TL'))
            ->first();

        $this->staff = User::where('is_active', 1)
            ->whereHas('role', fn($q) => $q->where('name', 'STAFF'))
            ->first();
    }

    /**
     * Pimpinan (Asman / TL) dapat membuat agenda rapat baru dengan preset baku.
     */
    public function test_leader_can_create_weekly_meeting(): void
    {
        $response = $this->actingAs($this->asman)->post('/rapat', [
            'type' => 'Rapat Asman + TL',
            'meeting_date' => now()->toDateString(),
            'time_start' => '13:30',
            'time_end' => '15:30',
            'location' => 'Ruang Rapat Bidang Pemeliharaan',
            'meeting_mode' => 'Offline',
            'status' => 'Akan Datang',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('meetings', [
            'type' => 'Rapat Asman + TL',
            'leader_id' => $this->asman->id,
            'meeting_date' => now()->toDateString(),
        ]);
    }

    /**
     * Pembuatan rapat wajib menyertakan tipe rapat dan tanggal pelaksanaan.
     */
    public function test_meeting_creation_requires_type_and_meeting_date(): void
    {
        $response = $this->actingAs($this->asman)->from('/rapat')->post('/rapat', [
            'type' => '',
            'meeting_date' => '',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/rapat');
        $response->assertSessionHasErrors(['type', 'meeting_date']);
    }

    /**
     * Peserta rapat dapat mengakses Workspace Rapat interaktif.
     */
    public function test_user_can_access_meeting_workspace(): void
    {
        $meeting = Meeting::create([
            'type' => 'Rapat TL + Staf',
            'meeting_date' => now()->toDateString(),
            'time_start' => '08:30',
            'time_end' => '10:00',
            'status' => 'Berlangsung',
            'leader_id' => $this->tl->id,
            'unit_id' => $this->tl->unit_id,
            'week_number' => now()->isoWeek(),
            'year' => now()->year,
        ]);

        $response = $this->actingAs($this->staff)->get("/rapat/{$meeting->id}/workspace");
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Meetings/Workspace')
            ->has('meeting')
            ->has('attendanceStats')
        );
    }

    /**
     * Isu kendala dapat dilampirkan dan dilepaskan dari agenda rapat.
     */
    public function test_issue_can_be_attached_and_detached_from_meeting(): void
    {
        $meeting = Meeting::create([
            'type' => 'Rapat Asman + TL',
            'meeting_date' => now()->toDateString(),
            'status' => 'Berlangsung',
            'leader_id' => $this->asman->id,
            'unit_id' => $this->asman->unit_id,
            'week_number' => now()->isoWeek(),
            'year' => now()->year,
        ]);

        $issue = Issue::create([
            'issue_no' => 'ISS-' . now()->format('Y') . '-9994',
            'title' => '[TEST-QA] Issue Agenda Rapat',
            'description' => 'Untuk dibahas di rapat.',
            'reported_by' => $this->staff->id,
            'unit_id' => $this->staff->unit_id,
            'current_level' => 'ASMAN',
            'status' => 'Open',
            'priority' => 'HIGH',
        ]);

        // 1. Lampirkan Issue
        $attachResponse = $this->actingAs($this->asman)->post("/rapat/{$meeting->id}/attach-issue", [
            'issue_id' => $issue->id,
        ]);
        $attachResponse->assertStatus(302);
        $attachResponse->assertSessionHas('success');

        $this->assertDatabaseHas('meeting_issues', [
            'meeting_id' => $meeting->id,
            'issue_id' => $issue->id,
        ]);

        // 2. Lepaskan Issue
        $detachResponse = $this->actingAs($this->asman)->delete("/rapat/{$meeting->id}/detach-issue/{$issue->id}");
        $detachResponse->assertStatus(302);
        $detachResponse->assertSessionHas('success');

        $this->assertDatabaseMissing('meeting_issues', [
            'meeting_id' => $meeting->id,
            'issue_id' => $issue->id,
        ]);
    }

    /**
     * Staf dilarang menyelesaikan rapat pimpinan (Guard Keamanan).
     */
    public function test_staff_cannot_finish_meeting(): void
    {
        $meeting = Meeting::create([
            'type' => 'Rapat Asman + TL',
            'meeting_date' => now()->toDateString(),
            'status' => 'Berlangsung',
            'leader_id' => $this->asman->id,
            'unit_id' => $this->asman->unit_id,
            'week_number' => now()->isoWeek(),
            'year' => now()->year,
        ]);

        $response = $this->actingAs($this->staff)->from("/rapat/{$meeting->id}/workspace")->post("/rapat/{$meeting->id}/finish", [
            'notes' => 'Staf mencoba menyelesaikan rapat.',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('error');

        $meeting->refresh();
        $this->assertNotEquals('Selesai', $meeting->status);
    }

    /**
     * Leader rapat berwenang menyelesaikan rapat dan mencatat notulensi resmi.
     */
    public function test_meeting_leader_can_finish_meeting(): void
    {
        $meeting = Meeting::create([
            'type' => 'Rapat Asman + TL',
            'meeting_date' => now()->toDateString(),
            'status' => 'Berlangsung',
            'leader_id' => $this->asman->id,
            'unit_id' => $this->asman->unit_id,
            'week_number' => now()->isoWeek(),
            'year' => now()->year,
        ]);

        $response = $this->actingAs($this->asman)->post("/rapat/{$meeting->id}/finish", [
            'notes' => 'Rapat selesai dengan 3 butir keputusan operasional.',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $meeting->refresh();
        $this->assertEquals('Selesai', $meeting->status);
        $this->assertEquals('Rapat selesai dengan 3 butir keputusan operasional.', $meeting->notes);
    }

    /**
     * Halaman cetak notulensi resmi format PLN dapat diakses.
     */
    public function test_cetak_notulensi_page_loads_with_meeting_data(): void
    {
        $meeting = Meeting::create([
            'type' => 'Rapat Tinjauan Manajemen (RTM)',
            'meeting_date' => now()->toDateString(),
            'status' => 'Selesai',
            'leader_id' => $this->asman->id,
            'unit_id' => $this->asman->unit_id,
            'notes' => 'Notulen RTM.',
            'week_number' => now()->isoWeek(),
            'year' => now()->year,
        ]);

        $response = $this->actingAs($this->asman)->get("/rapat/{$meeting->id}/cetak");
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Meetings/Cetak')
            ->has('meeting')
        );
    }
}

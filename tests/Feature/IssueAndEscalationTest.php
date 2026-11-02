<?php

namespace Tests\Feature;

use App\Models\Issue;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class IssueAndEscalationTest extends TestCase
{
    use DatabaseTransactions;

    protected $staff;
    protected $tl;
    protected $asman;

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('inertia.testing.ensure_pages_exist', false);

        $this->staff = User::where('is_active', 1)
            ->whereHas('role', fn($q) => $q->where('name', 'STAFF'))
            ->first();

        $this->tl = User::where('is_active', 1)
            ->whereHas('role', fn($q) => $q->where('name', 'TL'))
            ->first();

        $this->asman = User::where('is_active', 1)
            ->whereHas('role', fn($q) => $q->where('name', 'ASMAN'))
            ->first();
    }

    /**
     * Staf pelaksana dapat mencatat issue kendala lapangan dengan data valid.
     */
    public function test_staff_can_report_new_issue_with_valid_data(): void
    {
        $response = $this->actingAs($this->staff)->post('/issues', [
            'title' => '[TEST-QA] Anomali Getaran Pompa Kondensat Unit 1',
            'description' => 'Terdeteksi getaran abnormal pada bearing pompa kondensat melebihi ambang batas aman.',
            'priority' => 'HIGH',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('issues', [
            'title' => '[TEST-QA] Anomali Getaran Pompa Kondensat Unit 1',
            'reported_by' => $this->staff->id,
            'current_level' => 'TL',
        ]);
    }

    /**
     * Pelaporan issue menolak data jika judul atau deskripsi kosong.
     */
    public function test_issue_reporting_requires_title_and_description(): void
    {
        $response = $this->actingAs($this->staff)->from('/issues')->post('/issues', [
            'title' => '',
            'description' => '',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/issues');
        $response->assertSessionHasErrors(['title', 'description']);
    }

    /**
     * Sesuai aturan bisnis, TL tidak dapat menginput issue lapangan langsung (hanya Staf & Admin).
     */
    public function test_tl_cannot_report_issue_directly_due_to_business_rule(): void
    {
        $response = $this->actingAs($this->tl)->from('/issues')->post('/issues', [
            'title' => '[TEST-QA] Percobaan Input Issue oleh TL',
            'description' => 'TL mencoba input issue langsung.',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('error');
    }

    /**
     * Team Leader (TL) dapat mengeskalasi issue ke jenjang Asisten Manajer (ASMAN).
     */
    public function test_tl_can_escalate_issue_to_asman(): void
    {
        $issue = Issue::create([
            'issue_no' => 'ISS-' . now()->format('Y') . '-9991',
            'title' => '[TEST-QA] Issue Untuk Dieskalasi ke Asman',
            'description' => 'Memerlukan koordinasi anggaran antar seksi.',
            'reported_by' => $this->staff->id,
            'unit_id' => $this->staff->unit_id,
            'current_level' => 'TL',
            'status' => 'Open',
            'priority' => 'HIGH',
        ]);

        $response = $this->actingAs($this->tl)->post("/issues/{$issue->id}/escalate", [
            'reason' => 'Perlu persetujuan pembelian komponen cadangan dari Asisten Manajer.',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $issue->refresh();
        $this->assertEquals('Escalated', $issue->status);
        $this->assertContains($issue->current_level, ['ASMAN', 'MUP']);

        $this->assertDatabaseHas('escalations', [
            'issue_id' => $issue->id,
            'from_level' => 'TL',
        ]);
    }

    /**
     * Staf dilarang mengeskalasi issue langsung (RoleMiddleware 403 Forbidden).
     */
    public function test_staff_cannot_escalate_issue(): void
    {
        $issue = Issue::create([
            'issue_no' => 'ISS-' . now()->format('Y') . '-9992',
            'title' => '[TEST-QA] Issue Staf Test',
            'description' => 'Test eskalasi ilegal.',
            'reported_by' => $this->staff->id,
            'unit_id' => $this->staff->unit_id,
            'current_level' => 'TL',
            'status' => 'Open',
            'priority' => 'MEDIUM',
        ]);

        $response = $this->actingAs($this->staff)->post("/issues/{$issue->id}/escalate", [
            'reason' => 'Staf mencoba eskalasi sendiri.',
        ]);

        $response->assertStatus(403);
    }

    /**
     * Pimpinan (Asman / TL) dapat mencatat keputusan penyelesaian issue di dalam rapat berlangsung.
     */
    public function test_decision_maker_can_record_decision_on_issue(): void
    {
        $meeting = \App\Models\Meeting::create([
            'type' => 'Rapat Asman + TL',
            'meeting_date' => now()->toDateString(),
            'time_start' => '13:30',
            'time_end' => '15:30',
            'status' => 'Berlangsung',
            'leader_id' => $this->asman->id,
            'unit_id' => $this->asman->unit_id,
            'week_number' => now()->isoWeek(),
            'year' => now()->year,
        ]);

        $issue = Issue::create([
            'issue_no' => 'ISS-' . now()->format('Y') . '-9993',
            'title' => '[TEST-QA] Issue Untuk Diputuskan',
            'description' => 'Butuh putusan teknis.',
            'reported_by' => $this->staff->id,
            'unit_id' => $this->staff->unit_id,
            'current_level' => 'ASMAN',
            'status' => 'Open',
            'priority' => 'HIGH',
        ]);

        $response = $this->actingAs($this->asman)->post("/issues/{$issue->id}/decisions", [
            'meeting_id' => $meeting->id,
            'decision' => 'Setujui pengadaan suku cadang segera melalui shift pemeliharaan.',
            'mark_resolved' => true,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('decisions', [
            'issue_id' => $issue->id,
            'meeting_id' => $meeting->id,
            'decision_by' => $this->asman->id,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Decision;
use App\Models\Issue;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class DecisionController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $userRole = strtoupper(optional($user->role)->name ?? 'STAFF');

        $query = Decision::with(['issue', 'decisionMaker', 'tasks.assignee'])->latest();

        // 1. Scoping berdasar wewenang & isolasi data unit
        if ($userRole === 'STAFF') {
            $query->where(function ($q) use ($user) {
                $q->whereHas('issue', fn($iq) => $iq->where('reported_by', $user->id))
                  ->orWhereHas('tasks', fn($tq) => $tq->where('assigned_to', $user->id));
            });
        } elseif ($userRole === 'TL' || $userRole === 'ASMAN') {
            $authorizedUnitIds = $user->getAuthorizedUnitIds();
            $query->where(function ($q) use ($user, $authorizedUnitIds) {
                $q->whereHas('issue', fn($iq) => $iq->whereIn('unit_id', $authorizedUnitIds))
                  ->orWhere('decision_by', $user->id)
                  ->orWhereHas('tasks', fn($tq) => $tq->whereIn('unit_id', $authorizedUnitIds));
            });
        }

        if ($request->filled('level') && $request->level !== 'Semua') {
            $query->where('decision_level', $request->level);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('decision', 'like', "%{$search}%")
                  ->orWhere('rationale', 'like', "%{$search}%")
                  ->orWhere('follow_up', 'like', "%{$search}%")
                  ->orWhereHas('issue', function ($iq) use ($search) {
                      $iq->where('issue_no', 'like', "%{$search}%")
                         ->orWhere('title', 'like', "%{$search}%");
                  });
            });
        }

        $decisions = $query->paginate(15)->withQueryString();

        return Inertia::render('Decisions/Index', [
            'decisions' => $decisions,
        ]);
    }

    public function store(Request $request, $issue_id)
    {
        $request->validate([
            'meeting_id' => 'required|exists:meetings,id',
            'decision' => $request->boolean('escalate_issue') ? 'nullable|string' : 'required|string|min:2',
            'rationale' => 'nullable|string',
            'follow_up' => 'nullable|string',
            'mark_resolved' => 'nullable|boolean',
            'create_task' => 'nullable|boolean',
            'task_assignee_id' => 'nullable|exists:users,id',
            'task_due_date' => 'nullable|date',
            'task_title' => 'nullable|string|max:255',
            'escalate_issue' => 'nullable|boolean',
            'escalation_reason' => 'nullable|string',
        ], [
            'decision.required' => 'Rumusan keputusan pimpinan wajib diisi.',
            'decision.min' => 'Rumusan keputusan pimpinan minimal 2 karakter.',
            'meeting_id.required' => 'Ruang rapat wajib dipilih.',
            'meeting_id.exists' => 'Data rapat tidak ditemukan.',
        ]);

        $meeting = \App\Models\Meeting::findOrFail($request->meeting_id);
        if ($meeting->status !== 'Berlangsung') {
            return back()->with('error', 'Pengambilan keputusan hanya dapat dilakukan di dalam forum/ruang rapat yang berstatus Berlangsung.');
        }

        $issue = Issue::findOrFail($issue_id);
        $userRole = strtoupper(auth()->user()->role->name ?? '');

        // Validasi wewenang jenjang (Lowest Competent Level BR-01, BR-02, BR-04)
        // MUP berwenang memutuskan segala level, sedangkan TL dan ASMAN sesuai jenjang isu
        $isAuthorized = ($userRole === 'MUP' || $userRole === 'ADMIN') || 
                        ($issue->current_level === $userRole);

        if (!in_array($userRole, ['TL', 'ASMAN', 'MUP', 'ADMIN']) || !$isAuthorized) {
            return back()->with('error', "Anda ({$userRole}) tidak berwenang memutuskan isu pada jenjang {$issue->current_level}. Prinsip BSD mewajibkan putusan di Lowest Competent Level.");
        }

        $isDirectToMup = !empty($issue->hierarchy_line['is_direct_to_mup']) 
            || empty($issue->hierarchy_line['asman']);

        $targetLevel = 'ASMAN';
        if ($issue->current_level === 'TL') {
            $targetLevel = $isDirectToMup ? 'MUP' : 'ASMAN';
        } elseif ($issue->current_level === 'ASMAN') {
            $targetLevel = 'MUP';
        }

        $decisionText = $request->decision;
        if ($request->boolean('escalate_issue') && (empty($decisionText) || trim($decisionText) === '')) {
            $decisionText = "Eskalasi Isu ke {$targetLevel}: " . ($request->escalation_reason ?: ($request->follow_up ?: ($request->rationale ?: 'Mohon arahan dan tindak lanjut pimpinan level lebih tinggi.')));
        }

        $decision = Decision::create([
            'meeting_id' => $meeting->id,
            'issue_id' => $issue->id,
            'decision_by' => auth()->id(),
            'decision_level' => in_array($userRole, ['TL', 'ASMAN', 'MUP']) ? $userRole : 'TL',
            'decision' => $decisionText,
            'rationale' => $request->rationale,
            'follow_up' => $request->follow_up,
        ]);

        // Otomatisasi Tindak Lanjut Menjadi Task (BR-07 & AT-06)
        $createdTaskMsg = '';
        if ($request->boolean('create_task') || ($request->filled('task_assignee_id') && ($request->filled('follow_up') || $request->filled('task_title')))) {
            $taskTitle = $request->task_title ?: ('Tindak Lanjut: ' . Str::limit($decisionText, 60));
            $dueDate = $request->task_due_date ?: now()->addDays(3)->toDateString();
            $assigneeId = $request->task_assignee_id ?: auth()->id();
            $assignee = User::find($assigneeId);
            $taskUnitId = $assignee?->unit_id ?? $issue->unit_id ?? auth()->user()->unit_id;

            $task = Task::create([
                'issue_id' => $issue->id,
                'decision_id' => $decision->id,
                'title' => $taskTitle,
                'description' => $request->follow_up ?: $decisionText,
                'assigned_to' => $assigneeId,
                'assigned_by' => auth()->id(),
                'unit_id' => $taskUnitId,
                'start_date' => now()->toDateString(),
                'due_date' => $dueDate,
                'progress_percent' => 0,
                'status' => 'In Progress',
                'week_number' => now()->isoWeek(),
                'year' => now()->year,
            ]);

            $createdTaskMsg = " dan otomatis dibuatkan Task #{$task->id} untuk PIC pelaksana";
        }

        $escalatedMsg = '';
        if ($request->boolean('escalate_issue') && in_array($issue->current_level, ['TL', 'ASMAN'])) {
            if ($issue->current_level === 'TL') {
                $fromLevel = 'TL';
                $toLevel = $targetLevel;
                $toUserId = auth()->user()->asman_id ?? User::whereHas('role', fn($q)=>$q->where('name', 'ASMAN'))->first()?->id;
            } else {
                $fromLevel = 'ASMAN';
                $toLevel = 'MUP';
                $toUserId = User::whereHas('role', fn($q)=>$q->where('name', 'MUP'))->first()?->id;
            }

            \App\Models\Escalation::create([
                'issue_id' => $issue->id,
                'from_level' => $fromLevel,
                'from_user_id' => auth()->id() ?? 1,
                'to_level' => $toLevel,
                'to_user_id' => $toUserId,
                'reason' => $request->escalation_reason ?: ($request->follow_up ?: ($request->rationale ?: $decisionText)),
                'status' => 'Pending',
            ]);

            $issue->update([
                'current_level' => $toLevel,
                'status' => 'Escalated',
            ]);

            $meeting->issues()->syncWithoutDetaching([$issue->id => [
                'status' => 'Dieskalasi',
                'notes' => "Putusan rapat: Dieskalasikan ke {$toLevel}",
            ]]);

            $escalatedMsg = " serta berhasil dieskalasikan ke jenjang {$toLevel}";
        } else {
            $newStatus = $request->boolean('mark_resolved') ? 'Closed' : 'In Progress';
            $issue->update([
                'status' => $newStatus,
            ]);

            // Perbarui rekap eskalasi sebelumnya menjadi Decided karena telah diputuskan oleh atasan
            \App\Models\Escalation::where('issue_id', $issue->id)
                ->where('status', 'Pending')
                ->update([
                    'status' => 'Decided',
                    'to_user_id' => auth()->id(),
                ]);

            $meeting->issues()->syncWithoutDetaching([$issue->id => [
                'status' => 'Diputuskan',
                'notes' => 'Putusan rapat: ' . Str::limit($decisionText, 100),
            ]]);
        }

        if ($request->filled('redirect_to')) {
            return redirect($request->redirect_to)->with('success', "Keputusan berhasil disimpan{$createdTaskMsg}{$escalatedMsg}.");
        }

        return back()->with('success', "Keputusan atasan berhasil dicatat{$createdTaskMsg}{$escalatedMsg}.");
    }
}

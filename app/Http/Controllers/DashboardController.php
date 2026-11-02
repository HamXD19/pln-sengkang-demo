<?php

namespace App\Http\Controllers;

use App\Models\Decision;
use App\Models\Issue;
use App\Models\Meeting;
use App\Models\OrganizationUnit;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $roleName = strtoupper(optional($user->role)->name ?? 'STAFF');

        if (in_array($roleName, ['SUPER_ADMIN', 'SUPER ADMIN'])) {
            return redirect()->route('master.index', ['tab' => 'users']);
        }

        $authorizedUnitIds = $user->getAuthorizedUnitIds();
        $currentWeek = now()->isoWeek();
        $siklus = $request->get('siklus', 'semua');

        // Filter siklus mingguan (BR-09 ISO-8601)
        $taskQuery = Task::query();
        $issueQuery = Issue::query();

        // 1. Role-based scoping (Isolasi Unit / Seksi)
        if ($roleName === 'STAFF') {
            $taskQuery->where('assigned_to', $user->id);
            $issueQuery->where('reported_by', $user->id);
        } elseif ($roleName === 'TL') {
            $taskQuery->where(function ($q) use ($authorizedUnitIds, $user) {
                $q->whereIn('unit_id', $authorizedUnitIds)
                  ->orWhere('assigned_to', $user->id);
            });
            $issueQuery->whereIn('unit_id', $authorizedUnitIds);
        } elseif ($roleName === 'ASMAN') {
            $taskQuery->whereIn('unit_id', $authorizedUnitIds);
            // Asman HANYA melihat apa yang DIBAWA oleh TL-nya (sudah dieskalasikan ke ASMAN/MUP atau dilaporkan oleh TL/Asman)
            $subordinateIds = $user->getSubordinateUserIds();
            $issueQuery->whereIn('unit_id', $authorizedUnitIds)
                ->where(function ($q) use ($user, $subordinateIds) {
                    $q->whereIn('current_level', ['ASMAN', 'MUP'])
                      ->orWhereHas('escalations', fn($eq) => $eq->where('to_level', 'ASMAN'))
                      ->orWhere('reported_by', $user->id)
                      ->orWhere(function ($sq) use ($subordinateIds) {
                          $sq->whereIn('reported_by', $subordinateIds)
                             ->whereHas('reporter.role', fn($rq) => $rq->where('name', 'TL'));
                      });
                });
        } elseif ($roleName === 'MUP') {
            // MUP HANYA melihat apa yang DIBAWA oleh ASMAN-nya (sudah dieskalasikan ke MUP atau dilaporkan oleh Asman/MUP)
            $issueQuery->where(function ($q) use ($user) {
                $q->where('current_level', 'MUP')
                  ->orWhereHas('escalations', fn($eq) => $eq->where('to_level', 'MUP'))
                  ->orWhere('reported_by', $user->id)
                  ->orWhereHas('reporter.role', fn($rq) => $rq->whereIn('name', ['ASMAN', 'MUP']));
            });
        }
        // Role ADMIN melihat seluruh data UP Sengkang

        if ($siklus === 'N') {
            $taskQuery->where(function ($q) use ($currentWeek) {
                $q->where('week_number', $currentWeek)
                  ->orWhereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            });
            $issueQuery->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($siklus === 'N-1') {
            $prevWeek = now()->subWeek()->isoWeek();
            $taskQuery->where(function ($q) use ($prevWeek) {
                $q->where('week_number', $prevWeek)
                  ->orWhereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]);
            });
            $issueQuery->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]);
        }

        $today = now()->toDateString();
        $threeDaysLater = now()->addDays(3)->toDateString();

        // =========================================================================
        // TAHAP 1: TASK BACKLOG (WEEKLY CONTINUOUS CYCLE - BSD BUSINESS RULES)
        // Kueri Overdue/Tunggakan TIDAK difilter oleh minggu berjalan ($siklus).
        // Tugas yang belum selesai dari minggu lalu (misal W38) tetap masuk sebagai Overdue.
        // =========================================================================
        $cumulativeOverdueQuery = Task::where(function ($q) use ($today) {
            $q->where(function ($sub) use ($today) {
                $sub->whereNotIn('status', ['Resolved', 'Closed', 'Selesai'])
                    ->where('due_date', '<', $today);
            })->orWhere('status', 'Terlambat')
              ->orWhere('status', 'Overdue');
        })->where(function ($q) {
            $q->whereNull('progress_percent')
              ->orWhere('progress_percent', '<', 100);
        });

        // Scope berdasarkan hak akses role user yang login
        if ($roleName === 'STAFF') {
            $cumulativeOverdueQuery->where('assigned_to', $user->id);
        } elseif ($roleName === 'TL') {
            $cumulativeOverdueQuery->where(function ($q) use ($authorizedUnitIds, $user) {
                $q->whereIn('unit_id', $authorizedUnitIds)
                  ->orWhere('assigned_to', $user->id);
            });
        } elseif ($roleName === 'ASMAN') {
            $cumulativeOverdueQuery->whereIn('unit_id', $authorizedUnitIds);
        }
        // MUP melihat tunggakan seluruh unit

        $overdueTasks = (clone $cumulativeOverdueQuery)->count();

        $totalTasks = (clone $taskQuery)->count();
        $onProgressTasks = (clone $taskQuery)->whereIn('status', ['In Progress', 'On Progress'])->count();
        $completedTasks = (clone $taskQuery)->whereIn('status', ['Resolved', 'Closed', 'Selesai'])->count();

        // Issue Saya
        $myIssuesCount = (clone $issueQuery)->where(function ($q) use ($user) {
            $q->where('reported_by', $user->id)
              ->orWhere('owner_id', $user->id);
        })->count();

        // Data Prioritas Issue
        $highIssues = (clone $issueQuery)->whereIn('priority', ['HIGH', 'CRITICAL'])->count();
        $mediumIssues = (clone $issueQuery)->where('priority', 'MEDIUM')->count();
        $lowIssues = (clone $issueQuery)->where('priority', 'LOW')->count();

        // Paket data KPI untuk Chart.js
        $kpiData = [
            'total_pekerjaan' => max($totalTasks, $completedTasks + $onProgressTasks + $overdueTasks),
            'on_progress' => $onProgressTasks,
            'selesai' => $completedTasks,
            'terlambat' => $overdueTasks,
            'issue_saya' => $myIssuesCount,
            'chart_status' => [
                'selesai' => $completedTasks,
                'on_progress' => $onProgressTasks,
                'terlambat' => $overdueTasks,
            ],
            'chart_priority' => [
                'high' => $highIssues,
                'medium' => $mediumIssues,
                'low' => $lowIssues,
            ]
        ];

        // Isu yang berada di level review sesuai jenjang wewenang & alur eskalasi
        $issuesUnderReviewQuery = Issue::with(['reporter', 'owner', 'organizationUnit'])->latest();
        if ($roleName === 'STAFF') {
            $issuesUnderReviewQuery->where('reported_by', $user->id);
        } elseif ($roleName === 'TL') {
            $issuesUnderReviewQuery->whereIn('unit_id', $authorizedUnitIds)->where('current_level', 'TL');
        } elseif ($roleName === 'ASMAN') {
            $issuesUnderReviewQuery->whereIn('unit_id', $authorizedUnitIds)->where('current_level', 'ASMAN');
        } elseif ($roleName === 'MUP') {
            $issuesUnderReviewQuery->where('current_level', 'MUP');
        } else {
            $issuesUnderReviewQuery->whereIn('current_level', ['TL', 'ASMAN', 'MUP']);
        }
        $issuesUnderReview = $issuesUnderReviewQuery->take(10)->get();

        // Pekerjaan Tim Terkini (Scoped)
        $tasksScopedQuery = Task::with(['assignee', 'issue', 'organizationUnit'])->latest();
        if ($roleName === 'STAFF') {
            $tasksScopedQuery->where('assigned_to', $user->id);
        } elseif ($roleName === 'TL') {
            $tasksScopedQuery->where(fn($q) => $q->whereIn('unit_id', $authorizedUnitIds)->orWhere('assigned_to', $user->id));
        } elseif ($roleName === 'ASMAN') {
            $tasksScopedQuery->whereIn('unit_id', $authorizedUnitIds);
        }
        $tasks = $tasksScopedQuery->take(10)->get();

        $myTasks = null;
        $myTaskStats = null;
        $escalatedIssues = null;
        $criticalIssues = null;

        // Executive / MUP Data Properties
        $executiveStats = null;
        $departmentMetrics = [];
        $allActiveIssuesUnit = [];
        $criticalOverdueTasks = [];
        $recentMupDecisions = [];
        $upcomingMeetings = [];
        $assignableUsers = [];

        if ($roleName === 'STAFF') {
            $myTasks = Task::with('issue')->where('assigned_to', $user->id)->get();
            $myTaskStats = [
                'open' => $myTasks->where('status', 'Open')->count(),
                'in_progress' => $myTasks->whereIn('status', ['In Progress', 'On Progress'])->count(),
                'overdue' => $myTasks->filter(fn($t) => $t->calculated_status === 'Terlambat')->count(),
                'completed' => $myTasks->filter(fn($t) => $t->calculated_status === 'Selesai')->count(),
            ];
        } elseif ($roleName === 'ASMAN') {
            $escalatedIssues = Issue::whereIn('unit_id', $authorizedUnitIds)
                ->where('current_level', 'ASMAN')
                ->with(['reporter', 'escalations.fromUser', 'organizationUnit'])
                ->latest()
                ->get();
        } elseif ($roleName === 'MUP' || $roleName === 'ADMIN') {
            // 1. Isu Kritis menunggu putusan MUP
            $criticalIssues = Issue::where('current_level', 'MUP')
                ->with(['reporter', 'owner', 'organizationUnit', 'escalations.fromUser', 'decisions.decisionMaker'])
                ->latest()
                ->get();

            // 2. Seluruh Isu Aktif Unit (untuk toggle pemantauan menyeluruh MUP)
            $allActiveIssuesUnit = Issue::whereNotIn('status', ['Resolved', 'Closed'])
                ->with(['reporter', 'owner', 'organizationUnit', 'escalations.fromUser', 'decisions.decisionMaker'])
                ->latest()
                ->take(15)
                ->get();

            // 3. Matriks Kinerja Lintas Bidang (6 Departemen Utama UP Sengkang)
            $depts = OrganizationUnit::where('parent_unit_id', 1)->with('children')->get();
            foreach ($depts as $dept) {
                $unitIds = array_merge([$dept->id], $dept->children->pluck('id')->toArray());
                
                $leader = User::whereIn('unit_id', $unitIds)
                    ->whereHas('role', fn($r) => $r->whereIn('name', ['ASMAN', 'TL']))
                    ->join('roles', 'users.role_id', '=', 'roles.id')
                    ->orderByRaw("CASE WHEN roles.name = 'ASMAN' THEN 1 ELSE 2 END")
                    ->select('users.*')
                    ->first();

                $deptTotalTasks = Task::whereIn('unit_id', $unitIds)->count();
                $deptCompletedTasks = Task::whereIn('unit_id', $unitIds)->whereIn('status', ['Resolved', 'Closed', 'Selesai'])->count();
                $deptOnProgressTasks = Task::whereIn('unit_id', $unitIds)->whereIn('status', ['In Progress', 'On Progress'])->count();
                $deptOverdueTasks = Task::whereIn('unit_id', $unitIds)
                    ->where(function ($q) use ($today) {
                        $q->where(function ($sub) use ($today) {
                            $sub->whereNotIn('status', ['Resolved', 'Closed', 'Selesai'])->where('due_date', '<', $today);
                        })->orWhereIn('status', ['Terlambat', 'Overdue']);
                    })
                    ->where(function ($q) {
                        $q->whereNull('progress_percent')->orWhere('progress_percent', '<', 100);
                    })->count();

                $deptActiveIssues = Issue::whereIn('unit_id', $unitIds)->whereNotIn('status', ['Resolved', 'Closed'])->count();
                $deptCriticalIssues = Issue::whereIn('unit_id', $unitIds)->whereNotIn('status', ['Resolved', 'Closed'])->whereIn('priority', ['HIGH', 'CRITICAL'])->count();

                $health = 'Aman';
                if ($deptCriticalIssues > 0 || $deptOverdueTasks > 2) {
                    $health = 'Kritis';
                } elseif ($deptOverdueTasks > 0 || $deptActiveIssues > 0) {
                    $health = 'Perlu Perhatian';
                }

                $departmentMetrics[] = [
                    'id' => $dept->id,
                    'name' => $dept->name,
                    'unit_ids' => $unitIds,
                    'leader' => $leader ? [
                        'name' => $leader->name,
                        'role' => $leader->role?->name,
                        'position' => $leader->position?->name,
                        'avatar_url' => $leader->avatar_url,
                    ] : null,
                    'total_tasks' => $deptTotalTasks,
                    'completed_tasks' => $deptCompletedTasks,
                    'on_progress_tasks' => $deptOnProgressTasks,
                    'overdue_tasks' => $deptOverdueTasks,
                    'active_issues' => $deptActiveIssues,
                    'critical_issues' => $deptCriticalIssues,
                    'health' => $health,
                ];
            }

            // 4. Ringkasan Eksekutif (Executive KPIs)
            $totalUnitTasks = Task::count();
            $totalUnitCompletedTasks = Task::whereIn('status', ['Resolved', 'Closed', 'Selesai'])->count();
            $totalUnitOverdueTasks = Task::where(function ($q) use ($today) {
                $q->where(function ($sub) use ($today) {
                    $sub->whereNotIn('status', ['Resolved', 'Closed', 'Selesai'])->where('due_date', '<', $today);
                })->orWhereIn('status', ['Terlambat', 'Overdue']);
            })->where(function ($q) {
                $q->whereNull('progress_percent')->orWhere('progress_percent', '<', 100);
            })->count();

            $totalUnitActiveIssues = Issue::whereNotIn('status', ['Resolved', 'Closed'])->count();
            $pendingMupDecisions = Issue::where('current_level', 'MUP')->whereNotIn('status', ['Resolved', 'Closed'])->count();
            $slaBreachedIssues = Issue::whereNotIn('status', ['Resolved', 'Closed'])->whereNotNull('due_date')->where('due_date', '<', now())->count();

            $slaComplianceRate = 100;
            if ($totalUnitTasks + $totalUnitActiveIssues > 0) {
                $troubled = $totalUnitOverdueTasks + $slaBreachedIssues;
                $totalItems = $totalUnitTasks + $totalUnitActiveIssues;
                $slaComplianceRate = max(0, min(100, round((($totalItems - $troubled) / $totalItems) * 100)));
            }

            $executiveStats = [
                'pending_mup_decisions' => $pendingMupDecisions,
                'total_active_issues' => $totalUnitActiveIssues,
                'total_overdue_tasks' => $totalUnitOverdueTasks,
                'total_unit_tasks' => $totalUnitTasks,
                'total_completed_tasks' => $totalUnitCompletedTasks,
                'completion_rate' => $totalUnitTasks > 0 ? round(($totalUnitCompletedTasks / $totalUnitTasks) * 100) : 0,
                'sla_breached_issues' => $slaBreachedIssues,
                'sla_compliance_rate' => $slaComplianceRate,
            ];

            // 5. Tugas Overdue / Kritis Lintas Unit (Tunggakan yang butuh evaluasi MUP)
            $criticalOverdueTasks = Task::with(['assignee', 'organizationUnit'])
                ->where(function ($q) use ($today) {
                    $q->where(function ($sub) use ($today) {
                        $sub->whereNotIn('status', ['Resolved', 'Closed', 'Selesai'])->where('due_date', '<', $today);
                    })->orWhereIn('status', ['Terlambat', 'Overdue']);
                })
                ->where(function ($q) {
                    $q->whereNull('progress_percent')->orWhere('progress_percent', '<', 100);
                })
                ->orderBy('due_date', 'asc')
                ->take(10)
                ->get();

            // 6. Riwayat Keputusan MUP & Status Eksekusi Tindak Lanjut
            $recentMupDecisions = Decision::where('decision_level', 'MUP')
                ->orWhere('decision_by', $user->id)
                ->with(['issue.organizationUnit', 'decisionMaker', 'tasks.assignee'])
                ->latest()
                ->take(8)
                ->get();

            // 7. Agenda Rapat Koordinasi & Tinjauan Manajemen
            $upcomingMeetings = Meeting::with('leader')
                ->orderBy('meeting_date', 'desc')
                ->take(5)
                ->get();

            // 8. Daftar user aktif untuk penugasan tindak lanjut cepat
            $assignableUsers = User::with(['role', 'organizationUnit'])
                ->where('is_active', true)
                ->select('id', 'name', 'unit_id', 'role_id', 'position_id')
                ->orderBy('name')
                ->get();
        }

        return Inertia::render('Dashboard/Index', [
            'roleName' => $roleName,
            'kpiData' => $kpiData,
            'manajemenTugas' => [
                'siklus_aktif' => $siklus,
                'minggu_berjalan' => $currentWeek,
            ],
            'tasks' => $tasks,
            'issuesUnderReview' => $issuesUnderReview,
            'escalatedIssues' => $escalatedIssues,
            'criticalIssues' => $criticalIssues,
            'myTasks' => $myTasks,
            'myTaskStats' => $myTaskStats,
            // Executive Data
            'executiveStats' => $executiveStats,
            'departmentMetrics' => $departmentMetrics,
            'allActiveIssuesUnit' => $allActiveIssuesUnit,
            'criticalOverdueTasks' => $criticalOverdueTasks,
            'recentMupDecisions' => $recentMupDecisions,
            'upcomingMeetings' => $upcomingMeetings,
            'assignableUsers' => $assignableUsers,
        ]);
    }
}
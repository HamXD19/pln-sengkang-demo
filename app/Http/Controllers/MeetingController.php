<?php

namespace App\Http\Controllers;

use App\Models\Decision;
use App\Models\Escalation;
use App\Models\Information;
use App\Models\Issue;
use App\Models\Meeting;
use App\Models\MeetingAttendance;
use App\Models\MeetingMessage;
use App\Models\OrganizationUnit;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MeetingController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $userRole = strtoupper(optional($user->role)->name ?? 'STAFF');
        $userUnitId = $user->unit_id;
        $authorizedUnitIds = $user->getAuthorizedUnitIds();

        $tab = $request->get('tab', 'jadwal');
        $query = Meeting::with(['leader.position', 'leader.organizationUnit', 'organizationUnit', 'issues'])
            ->orderByRaw("CASE WHEN status = 'Berlangsung' THEN 0 ELSE 1 END")
            ->latest('meeting_date')
            ->latest('id');

        // Filter Tab (Jadwal vs Riwayat)
        if ($tab === 'riwayat') {
            $query->where('status', 'Selesai');
        } else {
            $query->where(function($q) {
                $q->where('status', '!=', 'Selesai')
                  ->orWhereNull('status');
            });
        }

        // Filter Tipe Rapat
        if ($request->filled('type') && $request->type !== 'semua') {
            $query->where('type', 'like', '%' . $request->type . '%');
        }

        // Filter Bidang / Unit (jika dipilih di antarmuka)
        if ($request->filled('unit_id') && $request->unit_id !== 'semua') {
            $targetUnitId = $request->unit_id;
            $targetChildUnitIds = OrganizationUnit::where('parent_unit_id', $targetUnitId)->pluck('id')->toArray();
            $scopeUnitIds = array_merge([$targetUnitId], $targetChildUnitIds);
            $query->where(function($q) use ($scopeUnitIds) {
                $q->whereIn('unit_id', $scopeUnitIds)
                  ->orWhereHas('leader', fn($lq) => $lq->whereIn('unit_id', $scopeUnitIds));
            });
        }

        // =========================================================================
        // ATURAN ISOLASI RAPAT PER BIDANG & PERAN (ROLE-BASED SCOPING)
        // Sesuai permintaan: Rapat Asman + TL tidak boleh muncul di akun Staff!
        // =========================================================================
        if ($userRole === 'STAFF') {
            // 1. STAFF:
            // - Dilarang keras melihat Rapat Asman + TL, Rapat Tinjauan Manajemen (RTM), dan rapat level pimpinan
            // - Hanya melihat Rapat TL + Staf di seksinya sendiri, yang dipimpin TL-nya, atau seksi dalam bidang yang sama
            $parentUnitId = $user->organizationUnit?->parent_unit_id;
            $siblingUnitIds = $parentUnitId 
                ? OrganizationUnit::where('parent_unit_id', $parentUnitId)->pluck('id')->toArray() 
                : [];
            $relatedUnitIds = array_values(array_unique(array_filter(array_merge([$userUnitId, $parentUnitId], $siblingUnitIds))));

            $query->where(function ($q) {
                $q->where('type', 'not like', '%Asman%')
                  ->where('type', 'not like', '%Manajemen%')
                  ->where('type', 'not like', '%RTM%');
            })->where(function ($q) use ($user, $userUnitId, $relatedUnitIds) {
                if (!empty($relatedUnitIds)) {
                    $q->whereIn('unit_id', $relatedUnitIds)
                      ->orWhereHas('leader', fn($lq) => $lq->whereIn('unit_id', $relatedUnitIds));
                } elseif ($userUnitId) {
                    $q->where('unit_id', $userUnitId)
                      ->orWhereHas('leader', fn($lq) => $lq->where('unit_id', $userUnitId));
                }
                if ($user->tl_id) {
                    $q->orWhere('leader_id', $user->tl_id);
                }
                $q->orWhereHas('attendances', fn($aq) => $aq->where('user_id', $user->id));
            });
        } elseif ($userRole === 'TL') {
            // 2. TEAM LEADER (TL):
            // - Melihat Rapat TL + Staf seksinya
            // - Melihat Rapat Asman + TL pada bidang yang menaungi seksinya
            // - Dilarang melihat rapat seksi/bidang lain yang tidak berkaitan
            $parentUnitId = $user->organizationUnit?->parent_unit_id;
            $query->where(function ($q) use ($user, $userUnitId, $parentUnitId) {
                // Rapat seksinya
                $q->where(function ($sq) use ($user, $userUnitId) {
                    $sq->where(function($tq) {
                        $tq->where('type', 'like', '%TL + Staf%')
                           ->orWhere(function($subTq) {
                               $subTq->where('type', 'not like', '%Asman%')
                                     ->where('type', 'not like', '%Manajemen%')
                                     ->where('type', 'not like', '%RTM%');
                           });
                    });
                    if ($userUnitId) {
                        $sq->where(function($uq) use ($user, $userUnitId) {
                            $uq->where('unit_id', $userUnitId)
                               ->orWhere('leader_id', $user->id)
                               ->orWhereHas('leader', fn($lq) => $lq->where('unit_id', $userUnitId));
                        });
                    } else {
                        $sq->where('leader_id', $user->id);
                    }
                });

                // Rapat Asman + TL di bidang induknya
                if ($parentUnitId || $user->asman_id) {
                    $q->orWhere(function ($sq) use ($user, $parentUnitId) {
                        $sq->where('type', 'like', '%Asman%');
                        if ($parentUnitId) {
                            $sq->where(function($uq) use ($user, $parentUnitId) {
                                $uq->where('unit_id', $parentUnitId)
                                   ->orWhere('leader_id', $user->asman_id)
                                   ->orWhereHas('leader', fn($lq) => $lq->where('unit_id', $parentUnitId));
                            });
                        } elseif ($user->asman_id) {
                            $sq->where('leader_id', $user->asman_id);
                        }
                    });
                }

                // Terdaftar sebagai peserta
                $q->orWhereHas('attendances', fn($aq) => $aq->where('user_id', $user->id));
            });
        } elseif ($userRole === 'ASMAN') {
            // 3. ASISTEN MANAJER (ASMAN):
            // - Melihat Rapat Asman + TL bidangnya
            // - Melihat Rapat TL + Staf seluruh seksi di bawah bidangnya
            // - Melihat Rapat Tinjauan Manajemen (RTM) dari MUP
            // - Dilarang melihat rapat operasional bidang Asman lain
            $query->where(function ($q) use ($user, $authorizedUnitIds) {
                if (!empty($authorizedUnitIds)) {
                    $q->whereIn('unit_id', $authorizedUnitIds)
                      ->orWhere('leader_id', $user->id)
                      ->orWhereHas('leader', fn($lq) => $lq->whereIn('unit_id', $authorizedUnitIds));
                }
                $q->orWhere('type', 'like', '%Manajemen%')
                  ->orWhere('type', 'like', '%RTM%')
                  ->orWhere('unit_id', 1)
                  ->orWhereHas('attendances', fn($aq) => $aq->where('user_id', $user->id));
            });
        }
        // 4. MUP & ADMIN: Akses penuh ke seluruh bidang

        $meetings = $query->get();

        // Siapkan daftar bidang / departemen utama untuk filter dan formulir
        $departments = OrganizationUnit::where('parent_unit_id', 1)
            ->orWhereNull('parent_unit_id')
            ->orderBy('id')
            ->get(['id', 'name', 'parent_unit_id']);

        $allUnits = OrganizationUnit::orderBy('name')->get(['id', 'name', 'parent_unit_id']);

        $subordinateIds = $user->getSubordinateUserIds();
        $leadersQuery = in_array($userRole, ['MUP', 'ADMIN'])
            ? User::where('is_active', true)
            : User::whereIn('id', array_merge([$user->id], $subordinateIds))->where('is_active', true);

        $leaders = $leadersQuery->select('id', 'name', 'role_id', 'unit_id')->with(['role:id,name', 'organizationUnit:id,name'])->orderBy('name')->get()->makeHidden(['avatar_url']);
        if ($leaders->isEmpty()) {
            $leaders = User::where('id', $user->id)->select('id', 'name', 'role_id', 'unit_id')->with(['role:id,name', 'organizationUnit:id,name'])->get()->makeHidden(['avatar_url']);
        }

        $issueQuery = Issue::whereNotIn('status', ['Resolved', 'Closed'])->latest();
        if ($userRole === 'STAFF') {
            $issueQuery->where('reported_by', $user->id);
        } elseif ($userRole === 'TL') {
            $issueQuery->whereIn('unit_id', $authorizedUnitIds)
                ->where(function ($q) {
                    $q->where('current_level', 'TL')
                      ->orWhere('status', 'Escalated')
                      ->orWhereHas('escalations', fn($eq) => $eq->where('to_level', 'ASMAN'));
                });
        } elseif ($userRole === 'ASMAN') {
            $issueQuery->whereIn('unit_id', $authorizedUnitIds)
                ->where(function ($q) use ($user, $subordinateIds) {
                    $q->whereIn('current_level', ['ASMAN', 'MUP'])
                      ->orWhereHas('escalations', fn($eq) => $eq->where('to_level', 'ASMAN'))
                      ->orWhere('reported_by', $user->id);
                });
        } elseif ($userRole === 'MUP') {
            $issueQuery->where(function ($q) use ($user) {
                $q->where('current_level', 'MUP')
                  ->orWhereHas('escalations', fn($eq) => $eq->where('to_level', 'MUP'))
                  ->orWhere('reported_by', $user->id);
            });
        }
        $availableIssues = $issueQuery->get();

        return Inertia::render('Meetings/Index', [
            'meetings' => $meetings,
            'leaders' => $leaders,
            'availableIssues' => $availableIssues,
            'departments' => $departments,
            'allUnits' => $allUnits,
            'tab' => $tab,
            'selectedUnitId' => $request->get('unit_id', 'semua'),
            'selectedType' => $request->get('type', 'semua'),
            'userRole' => $userRole,
            'userUnitId' => $userUnitId,
            'userUnitName' => $user->organizationUnit?->name ?? 'UP Sengkang',
        ]);
    }

    public function show($id)
    {
        $meeting = Meeting::with(['leader.position', 'leader.organizationUnit', 'organizationUnit', 'issues'])->find($id);
        if (!$meeting) {
            $meeting = Meeting::first() ?? (object)[
                'id' => $id,
                'type' => 'Rapat TL + Staf',
                'meeting_date' => now()->toDateString(),
                'time_start' => '08:00',
                'time_end' => '10:00',
                'location' => 'Ruang Operasi',
                'status' => 'Akan Datang',
                'leader' => auth()->user(),
            ];
        }

        $issues = $meeting->issues->isNotEmpty() ? $meeting->issues : Issue::where('current_level', 'TL')->take(5)->get();
        $tasks = Task::with('assignee')->take(6)->get();
        $users = User::where('is_active', true)->take(12)->get();

        return Inertia::render('Meetings/Show', [
            'meeting' => $meeting,
            'issues' => $issues,
            'tasks' => $tasks,
            'users' => $users,
        ]);
    }

    public function workspace($id)
    {
        $meeting = Meeting::with(['leader.position', 'leader.organizationUnit', 'organizationUnit', 'issues.reporter', 'issues.owner', 'issues.decisions'])->find($id);
        if (!$meeting) {
            $meeting = Meeting::first() ?? (object)[
                'id' => $id,
                'type' => 'Rapat TL + Staf',
                'meeting_date' => now()->toDateString(),
                'time_start' => '08:00',
                'time_end' => '10:00',
                'location' => 'Ruang Operasi',
                'meeting_mode' => 'Offline',
                'status' => 'Berlangsung',
                'leader' => auth()->user(),
            ];
        }

        $user = auth()->user();
        $userRole = strtoupper(optional($user->role)->name ?? 'STAFF');

        // Proteksi Ruang Rapat: Staf dilarang masuk ke workspace rapat Asman/MUP
        if ($userRole === 'STAFF') {
            $meetingType = $meeting->type ?? '';
            if (str_contains($meetingType, 'Asman') || str_contains($meetingType, 'Manajemen') || str_contains($meetingType, 'RTM')) {
                return redirect()->route('rapat.index')->with('error', 'Akses dibatasi. Anda tidak berwenang mengakses ruang rapat koordinasi level pimpinan (Asman / Manajemen).');
            }
        }

        // Tentukan level rapat sesuai tipe
        $type = $meeting->type ?? 'Rapat TL + Staf';
        if (str_contains($type, 'Asman')) {
            $meetingLevel = 'ASMAN';
        } elseif (str_contains($type, 'Manajemen') || str_contains($type, 'RTM')) {
            $meetingLevel = 'MUP';
        } else {
            $meetingLevel = 'TL';
        }

        // Agenda 1: Informasi Cascading dari level atas
        $informations = Information::with('publisher')
            ->where('is_active', true)
            ->whereIn('target_level', ['ALL', $meetingLevel, 'STAFF'])
            ->latest('published_at')
            ->take(6)
            ->get();

        // =========================================================================
        // TAHAP 2: MEETING AGENDA QUERY (OTOMATISASI REVIEW N-1 & TASK OUTSTANDING)
        // Menarik seluruh pekerjaan yang belum selesai (backlog) dari minggu sebelumnya
        // Terisolasi ketat sesuai lingkup pimpinan rapat (TL vs Asman vs MUP)
        // =========================================================================
        $today = now()->toDateString();
        $outstandingQuery = Task::with(['assignee', 'issue'])
            ->where(function ($q) {
                $q->whereNull('progress_percent')
                  ->orWhere('progress_percent', '<', 100);
            })
            ->where(function ($q) use ($today) {
                $q->whereIn('status', ['Open', 'Assigned', 'In Progress', 'On Progress', 'Blocked', 'Overdue', 'Terlambat'])
                  ->orWhere('due_date', '<', $today);
            });

        $leader = $meeting->leader ?? auth()->user();
        $leaderUnits = $leader ? $leader->getAuthorizedUnitIds() : [];

        // Gabungan cakupan unit meeting + unit leader
        $meetingUnitId = $meeting->unit_id;
        $meetingScopeUnits = [];
        if ($meetingUnitId) {
            $meetingScopeUnits = OrganizationUnit::where('parent_unit_id', $meetingUnitId)
                ->orWhere('id', $meetingUnitId)
                ->pluck('id')
                ->toArray();
        }
        $targetScopeUnits = array_values(array_unique(array_merge($leaderUnits, $meetingScopeUnits)));

        if ($meetingLevel === 'TL') {
            // Untuk rapat TL: Pekerjaan staf di bawah seksinya atau yang ditugaskan ke TL
            $outstandingQuery->where(function ($q) use ($leader, $leaderUnits) {
                if (!empty($leaderUnits)) {
                    $q->whereIn('unit_id', $leaderUnits)
                      ->orWhereHas('assignee', fn($sq) => $sq->whereIn('unit_id', $leaderUnits))
                      ->orWhere('assigned_to', $leader->id)
                      ->orWhere('assigned_by', $leader->id);
                } else {
                    $q->where('assigned_to', $leader->id ?? auth()->id());
                }
            });
        } elseif ($meetingLevel === 'ASMAN') {
            // Untuk rapat ASMAN: Pekerjaan seluruh seksi di bawah bidang Asman
            if (!empty($leaderUnits)) {
                $outstandingQuery->where(function ($q) use ($leader, $leaderUnits) {
                    $q->whereIn('unit_id', $leaderUnits)
                      ->orWhereHas('assignee', fn($sq) => $sq->whereIn('unit_id', $leaderUnits))
                      ->orWhere('assigned_by', $leader->id);
                });
            }
        }
        // Untuk MUP / RTM: Memuat seluruh tunggakan pekerjaan UP Sengkang secara komprehensif

        $evalTasks = $outstandingQuery->orderBy('due_date', 'asc')->get();
        if ($evalTasks->isEmpty()) {
            $fallbackQuery = Task::with('assignee');
            if (($meetingLevel === 'TL' || $meetingLevel === 'ASMAN') && !empty($leaderUnits)) {
                $fallbackQuery->where(function($q) use ($leaderUnits, $leader) {
                    $q->whereIn('unit_id', $leaderUnits)
                      ->orWhereHas('assignee', fn($sq) => $sq->whereIn('unit_id', $leaderUnits))
                      ->orWhere('assigned_to', $leader->id);
                });
            }
            $evalTasks = $fallbackQuery->latest()->take(10)->get();
        }

        // Aturan BSD: Isu HANYA tampil di ruang rapat Staff dan TL (meetingLevel === 'TL').
        // Untuk rapat TL + Asman dan Asman + MUP, isu mentah staf tidak tampil lagi (fokus pada Task & Eskalasi).
        if ($meetingLevel === 'TL') {
            if ($meeting->issues && $meeting->issues->isNotEmpty()) {
                $eagerMeetingIssues = $meeting->issues()->with(['reporter', 'owner', 'organizationUnit', 'tasks', 'escalations', 'decisions'])->get();
                $activeIssues = $eagerMeetingIssues->filter(function ($iss) {
                    return !in_array($iss->status, ['Resolved', 'Closed', 'Decided', 'Escalated']) 
                        && $iss->current_level === 'TL'
                        && $iss->tasks->isEmpty();
                })->values();

                $archivedIssues = $eagerMeetingIssues->filter(function ($iss) {
                    return in_array($iss->status, ['Resolved', 'Closed', 'Decided', 'Escalated']) 
                        || $iss->current_level !== 'TL'
                        || $iss->tasks->isNotEmpty()
                        || $iss->escalations->isNotEmpty();
                })->values();
            } else {
                $baseIssuesQuery = Issue::with(['reporter', 'owner', 'organizationUnit', 'tasks', 'escalations', 'decisions']);
                if (!empty($leaderUnits)) {
                    $baseIssuesQuery->whereIn('unit_id', $leaderUnits);
                }

                $activeIssues = (clone $baseIssuesQuery)
                    ->where('current_level', 'TL')
                    ->whereNotIn('status', ['Resolved', 'Closed', 'Decided', 'Escalated'])
                    ->whereDoesntHave('tasks')
                    ->latest()
                    ->get();

                $archivedIssues = (clone $baseIssuesQuery)
                    ->where(function ($q) {
                        $q->whereIn('status', ['Resolved', 'Closed', 'Decided', 'Escalated'])
                          ->orWhereHas('tasks')
                          ->orWhereHas('escalations');
                    })
                    ->latest()
                    ->take(20)
                    ->get();
            }

            $allOpenIssuesQuery = Issue::whereNotIn('status', ['Resolved', 'Closed', 'Decided', 'Escalated'])->where('current_level', 'TL');
            if (!empty($leaderUnits)) {
                $allOpenIssuesQuery->whereIn('unit_id', $leaderUnits);
            }
            $allOpenIssues = $allOpenIssuesQuery->latest()->get();
        } else {
            // Rapat TL + Asman dan Asman + MUP tidak menampilkan issue mentah staf
            $activeIssues = collect([]);
            $archivedIssues = collect([]);
            $allOpenIssues = collect([]);
        }

        // Agenda 3: Perencanaan Pekerjaan Minggu N
        $plannedQuery = Task::with('assignee');
        if (($meetingLevel === 'TL' || $meetingLevel === 'ASMAN') && !empty($leaderUnits)) {
            $plannedQuery->where(function($q) use ($leaderUnits, $leader) {
                $q->whereIn('unit_id', $leaderUnits)
                  ->orWhereHas('assignee', fn($sq) => $sq->whereIn('unit_id', $leaderUnits))
                  ->orWhere('assigned_to', $leader->id);
            });
        }
        $plannedTasks = $plannedQuery->latest()->take(12)->get();

        // Agenda 4: Rekap Keputusan & Tindak Lanjut
        $decisionsQuery = Decision::with(['decisionMaker', 'issue', 'tasks.assignee']);
        if (($meetingLevel === 'TL' || $meetingLevel === 'ASMAN') && !empty($leaderUnits)) {
            $decisionsQuery->whereHas('issue', fn($iq) => $iq->whereIn('unit_id', $leaderUnits));
        }
        $decisions = $decisionsQuery->latest()->take(10)->get();

        $user = auth()->user();
        $userRole = strtoupper(optional($user->role)->name ?? 'STAFF');

        // Otomatis catat kehadiran / join online saat pengguna masuk ruang rapat
        $attendance = MeetingAttendance::firstOrNew([
            'meeting_id' => $meeting->id,
            'user_id' => $user->id,
        ]);
        $isFirstJoin = !$attendance->exists;
        $attendance->status = 'Joined';
        if ($isFirstJoin || !$attendance->joined_at) {
            $attendance->joined_at = now();
        }
        $attendance->last_seen_at = now();
        $attendance->save();

        if ($isFirstJoin) {
            MeetingMessage::create([
                'meeting_id' => $meeting->id,
                'user_id' => $user->id,
                'message' => '🟢 Telah bergabung ke ruang rapat online.',
            ]);
        }

        // Query seluruh calon peserta rapat sesuai jenjang:
        $subordinateIds = $leader ? $leader->getSubordinateUserIds() : [];
        if ($meetingLevel === 'MUP') {
            $usersQuery = User::where('is_active', true);
        } elseif ($meetingLevel === 'ASMAN') {
            $usersQuery = User::whereIn('id', array_merge([$leader->id], $subordinateIds))->where('is_active', true);
        } else {
            $usersQuery = User::whereIn('id', array_merge([$leader->id], $subordinateIds))->where('is_active', true);
        }

        $rawUsers = $usersQuery->with(['role:id,name', 'organizationUnit:id,name'])->orderBy('name')->get();
        if ($rawUsers->isEmpty() && $leader) {
            $rawUsers = User::where('id', $leader->id)->with(['role:id,name', 'organizationUnit:id,name'])->get();
        }

        // Ambil data absensi online untuk seluruh peserta rapat ini
        $attendances = MeetingAttendance::where('meeting_id', $meeting->id)
            ->get()
            ->keyBy('user_id');

        $users = $rawUsers->map(function ($u) use ($attendances) {
            $att = $attendances->get($u->id);
            $isOnline = $att ? $att->isOnline() : false;
            $isJoined = $att ? true : false;
            return [
                'id' => $u->id,
                'name' => $u->name,
                'employee_no' => $u->employee_no,
                'avatar' => $u->avatar,
                'role' => $u->role?->name ?? 'STAFF',
                'unit' => $u->organizationUnit?->name ?? '',
                'is_joined' => $isJoined,
                'is_online' => $isOnline,
                'joined_at' => $att?->joined_at ? $att->joined_at->timezone('Asia/Makassar')->format('H:i') . ' WITA' : null,
                'last_seen' => $att?->last_seen_at ? $att->last_seen_at->diffForHumans() : null,
            ];
        });

        // Pastikan pengguna yang sedang login selalu terdaftar di roster peserta
        if (!$users->contains('id', $user->id)) {
            $att = $attendances->get($user->id);
            $users->prepend([
                'id' => $user->id,
                'name' => $user->name,
                'employee_no' => $user->employee_no,
                'avatar' => $user->avatar,
                'role' => $user->role?->name ?? $userRole,
                'unit' => $user->organizationUnit?->name ?? '',
                'is_joined' => true,
                'is_online' => true,
                'joined_at' => $att?->joined_at ? $att->joined_at->timezone('Asia/Makassar')->format('H:i') . ' WITA' : now()->timezone('Asia/Makassar')->format('H:i') . ' WITA',
                'last_seen' => 'baru saja',
            ]);
        }

        $attendanceStats = [
            'total_expected' => $users->count(),
            'total_joined' => $users->where('is_joined', true)->count(),
            'total_online' => $users->where('is_online', true)->count(),
        ];

        $messages = MeetingMessage::with(['user.role', 'issue.reporter', 'issue.organizationUnit'])
            ->where('meeting_id', $meeting->id)
            ->oldest()
            ->get();

        $myPendingIssues = [];
        if ($meetingLevel === 'TL' && in_array($userRole, ['STAFF', 'ADMIN', 'SUPER_ADMIN'])) {
            $myPendingIssues = Issue::with(['reporter', 'organizationUnit'])
                ->whereNotIn('status', ['Resolved', 'Closed'])
                ->where('reported_by', $user->id)
                ->latest()
                ->get();
        } elseif ($meetingLevel === 'ASMAN' && in_array($userRole, ['TL', 'ASMAN', 'ADMIN', 'SUPER_ADMIN'])) {
            // Pada Rapat Asman: TL & Asman dapat memunculkan isu eskalasi ke forum live chat
            $myPendingQuery = Issue::with(['reporter', 'organizationUnit'])
                ->whereNotIn('status', ['Resolved', 'Closed'])
                ->where(function ($q) {
                    $q->where('current_level', 'ASMAN')
                      ->orWhereHas('escalations', fn($eq) => $eq->where('to_level', 'ASMAN'));
                });

            if (!empty($targetScopeUnits)) {
                $myPendingQuery->whereIn('unit_id', $targetScopeUnits);
            } elseif ($user->unit_id) {
                $myPendingQuery->where('unit_id', $user->unit_id);
            }

            $myPendingIssues = $myPendingQuery->latest()->get();
        }

        // 3. Query data untuk Tabel Eskalasi (terpisah dari Tabel Issue & Tabel Task)
        $escalationsQuery = Escalation::with(['issue.reporter', 'fromUser.role', 'toUser.role'])->latest();
        if ($meetingLevel === 'TL') {
            $escalationsQuery->where(function ($q) use ($targetScopeUnits) {
                $q->where(function ($sq) {
                    $sq->where('to_level', 'TL')->orWhere('from_level', 'TL');
                });
                if (!empty($targetScopeUnits)) {
                    $q->whereHas('issue', fn($iq) => $iq->whereIn('unit_id', $targetScopeUnits));
                }
            });
        } elseif ($meetingLevel === 'ASMAN') {
            $escalationsQuery->where(function ($q) use ($targetScopeUnits) {
                $q->where(function ($sq) {
                    $sq->where('to_level', 'ASMAN')->orWhere('from_level', 'ASMAN');
                });
                if (!empty($targetScopeUnits)) {
                    $q->whereHas('issue', fn($iq) => $iq->whereIn('unit_id', $targetScopeUnits));
                }
            });
        } elseif ($meetingLevel === 'MUP') {
            $escalationsQuery->where('to_level', 'MUP');
        }
        $escalations = $escalationsQuery->take(20)->get();

        return Inertia::render('Meetings/Workspace', [
            'meeting' => $meeting,
            'meetingLevel' => $meetingLevel,
            'informations' => $informations,
            'evalTasks' => $evalTasks,
            'activeIssues' => $activeIssues,
            'archivedIssues' => $archivedIssues,
            'allOpenIssues' => $allOpenIssues,
            'plannedTasks' => $plannedTasks,
            'decisions' => $decisions,
            'users' => $users,
            'messages' => $messages,
            'myPendingIssues' => $myPendingIssues,
            'escalations' => $escalations,
            'attendanceStats' => $attendanceStats,
            'userRole' => $userRole,
        ]);
    }

    public function storeMessage(Request $request, $id)
    {
        $meeting = Meeting::findOrFail($id);

        $validated = $request->validate([
            'message' => 'nullable|string|max:2000',
            'issue_id' => 'nullable|exists:issues,id',
        ]);

        if (empty($validated['message']) && empty($validated['issue_id'])) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['error' => 'Silakan ketik pesan atau pilih isu yang ingin dimunculkan.'], 422);
            }
            return back()->with('error', 'Silakan ketik pesan atau pilih isu yang ingin dimunculkan.');
        }

        $userRole = strtoupper(optional(auth()->user()->role)->name ?? 'STAFF');
        if (!empty($validated['issue_id'])) {
            $type = $meeting->type ?? 'Rapat TL + Staf';
            $isStaffTlMeeting = !str_contains($type, 'Asman') && !str_contains($type, 'Manajemen') && !str_contains($type, 'RTM') && !str_contains($type, 'MUP');
            $isAsmanMeeting = str_contains($type, 'Asman');

            if (!$isStaffTlMeeting && !$isAsmanMeeting) {
                if ($request->expectsJson() || $request->wantsJson()) {
                    return response()->json(['error' => 'Isu kendala hanya dapat dimunculkan pada Rapat TL + Staf atau Rapat Asman + TL.'], 422);
                }
                return back()->with('error', 'Isu kendala hanya dapat dimunculkan pada Rapat TL + Staf atau Rapat Asman + TL.');
            }

            if ($isStaffTlMeeting && !in_array($userRole, ['STAFF', 'ADMIN', 'SUPER_ADMIN'])) {
                if ($request->expectsJson() || $request->wantsJson()) {
                    return response()->json(['error' => 'Pada rapat TL + Staf, staf pelapor yang berwenang memunculkan kendala lapangan.'], 422);
                }
                return back()->with('error', 'Pada rapat TL + Staf, staf pelapor yang berwenang memunculkan kendala lapangan.');
            }

            if ($isAsmanMeeting && !in_array($userRole, ['TL', 'ASMAN', 'ADMIN', 'SUPER_ADMIN'])) {
                if ($request->expectsJson() || $request->wantsJson()) {
                    return response()->json(['error' => 'Pada rapat Asman, hanya TL atau Asman yang berwenang memunculkan isu eskalasi ke forum.'], 422);
                }
                return back()->with('error', 'Pada rapat Asman, hanya TL atau Asman yang berwenang memunculkan isu eskalasi ke forum.');
            }
        }

        $msg = MeetingMessage::create([
            'meeting_id' => $meeting->id,
            'user_id' => auth()->id(),
            'issue_id' => $validated['issue_id'] ?? null,
            'message' => $validated['message'] ?? null,
        ]);

        // Jika staf memunculkan isu, otomatis kaitkan ke agenda rapat
        if (!empty($validated['issue_id'])) {
            $issue = Issue::find($validated['issue_id']);
            if ($issue) {
                $meeting->issues()->syncWithoutDetaching([
                    $issue->id => [
                        'status' => 'Dibahas',
                        'notes' => 'Dimunculkan oleh staf ' . auth()->user()->name . ' di live chat ruang rapat.',
                    ]
                ]);
            }
        }

        if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $msg->load(['user.role', 'issue.reporter', 'issue.organizationUnit']),
            ]);
        }

        return redirect()->route('rapat.workspace', $id)->with('success', 'Pesan / isu berhasil dikirim ke ruang rapat.');
    }

    public function attachIssue(Request $request, $id)
    {
        $meeting = Meeting::findOrFail($id);
        $request->validate(['issue_id' => 'required|exists:issues,id']);
        $meeting->issues()->syncWithoutDetaching([$request->issue_id]);
        return back()->with('success', 'Isu terpilih berhasil dimasukkan ke agenda rapat.');
    }

    public function detachIssue(Request $request, $id, $issueId)
    {
        $meeting = Meeting::findOrFail($id);
        $meeting->issues()->detach($issueId);
        return back()->with('success', 'Isu berhasil dikeluarkan dari agenda rapat.');
    }

    public function storeWorkspaceTask(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'assigned_to' => 'required|exists:users,id',
            'due_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $assignee = User::find($request->assigned_to);

        Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'assigned_to' => $request->assigned_to,
            'assigned_by' => auth()->id(),
            'unit_id' => $assignee?->unit_id ?? auth()->user()->unit_id,
            'start_date' => now()->toDateString(),
            'due_date' => $request->due_date,
            'progress_percent' => 0,
            'status' => 'Open',
            'week_number' => now()->isoWeek(),
            'year' => now()->year,
        ]);

        return redirect()->route('rapat.workspace', $id)->with('success', 'Pekerjaan baru untuk Minggu N berhasil ditambahkan langsung dari Workspace Rapat.');
    }

    public function finishMeeting(Request $request, $id)
    {
        $meeting = Meeting::findOrFail($id);
        $user = auth()->user();
        $userRole = strtoupper(optional($user->role)->name ?? 'STAFF');

        if ($userRole === 'STAFF') {
            return back()->with('error', 'Akses ditolak. Staf tidak memiliki wewenang untuk menyelesaikan rapat pimpinan.');
        }

        $isLeader = $meeting->leader_id && $meeting->leader_id == $user->id;
        $isAuthorizedRole = in_array($userRole, ['TL', 'ASMAN', 'MUP', 'ADMIN']);

        if (!$isLeader && !$isAuthorizedRole) {
            return back()->with('error', 'Anda tidak berwenang menyelesaikan rapat ini.');
        }

        $notes = $request->input('notes');

        $meeting->update([
            'status' => 'Selesai',
            'notes' => $notes ?: 'Rapat koordinasi mingguan telah selesai dilaksanakan dengan baik.',
        ]);

        return redirect()->route('rapat.show', $meeting->id)->with('success', 'Rapat koordinasi berhasil diselesaikan dan notulen otomatis tercatat.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'custom_type' => 'nullable|string|max:255',
            'unit_id' => 'nullable|exists:organization_units,id',
            'meeting_date' => 'required|date',
            'time_start' => 'nullable|string',
            'time_end' => 'nullable|string',
            'location' => 'nullable|string',
            'meeting_mode' => 'nullable|string',
            'zoom_link' => 'nullable|string',
            'zoom_meeting_id' => 'nullable|string',
            'zoom_passcode' => 'nullable|string',
            'leader_id' => 'nullable|exists:users,id',
            'status' => 'nullable|string',
            'notes' => 'nullable|string',
            'issue_ids' => 'nullable|array',
            'issue_ids.*' => 'exists:issues,id',
        ]);

        $meetingType = $validated['type'];
        if ($meetingType === 'CUSTOM' && !empty($validated['custom_type'])) {
            $meetingType = $validated['custom_type'];
        }

        $status = $validated['status'] ?? 'Akan Datang';
        $leaderId = $validated['leader_id'] ?? auth()->id();

        // Tentukan unit_id penyelenggara
        $unitId = $validated['unit_id'] ?? null;
        if (!$unitId) {
            $leaderUser = User::find($leaderId);
            $unitId = $leaderUser?->unit_id ?? (auth()->user()->unit_id ?? 1);
        }

        $timeStart = $validated['time_start'] ?? null;
        $timeEnd = $validated['time_end'] ?? null;
        if (empty($timeStart) || empty($timeEnd)) {
            if (str_contains($meetingType, 'Asman') && str_contains($meetingType, 'TL')) {
                // Senin Siang
                $timeStart = $timeStart ?: '13:30';
                $timeEnd = $timeEnd ?: '15:30';
            } elseif (str_contains($meetingType, 'Manajemen') || str_contains($meetingType, 'RTM')) {
                // Selasa Pagi
                $timeStart = $timeStart ?: '08:30';
                $timeEnd = $timeEnd ?: '11:00';
            } else {
                // Senin Pagi (TL + Staf)
                $timeStart = $timeStart ?: '08:30';
                $timeEnd = $timeEnd ?: '10:00';
            }
        }

        $meeting = Meeting::create([
            'type' => $meetingType,
            'unit_id' => $unitId,
            'meeting_date' => $validated['meeting_date'],
            'time_start' => $timeStart,
            'time_end' => $timeEnd,
            'location' => $validated['location'] ?? (str_contains($meetingType, 'RTM') || str_contains($meetingType, 'Manajemen') ? 'Ruang Rapat Utama UP Sengkang' : (str_contains($meetingType, 'Asman') ? 'Ruang Rapat Bidang' : 'Ruang Rapat Seksi / Operasi')),
            'meeting_mode' => $validated['meeting_mode'] ?? 'Offline',
            'zoom_link' => $validated['zoom_link'] ?? null,
            'zoom_meeting_id' => $validated['zoom_meeting_id'] ?? null,
            'zoom_passcode' => $validated['zoom_passcode'] ?? null,
            'leader_id' => $leaderId,
            'status' => $status,
            'notes' => $validated['notes'] ?? null,
            'week_number' => \Carbon\Carbon::parse($validated['meeting_date'])->isoWeek(),
            'year' => \Carbon\Carbon::parse($validated['meeting_date'])->year,
        ]);

        // Hubungkan isu terpilih ke agenda rapat
        if (!empty($request->input('issue_ids'))) {
            $meeting->issues()->sync($request->input('issue_ids'));
        }

        return redirect()->route('rapat.index')->with('success', "Agenda rapat '{$meetingType}' berhasil dibuat.");
    }

    public function cetak($id)
    {
        $meeting = Meeting::with('leader')->find($id);
        if (!$meeting) {
            $meeting = (object)[
                'id' => $id,
                'type' => 'Rapat TL + Staf',
                'meeting_date' => now()->toDateString(),
                'status' => 'Selesai',
                'leader' => auth()->user(),
                'notes' => 'Rapat koordinasi mingguan telah dilaksanakan sesuai siklus N-1 dan perencanaan N.',
            ];
        }

        $type = $meeting->type ?? 'Rapat TL + Staf';
        $level = str_contains($type, 'Asman') ? 'ASMAN' : (str_contains($type, 'Manajemen') || str_contains($type, 'RTM') ? 'MUP' : 'TL');

        $issues = $meeting->issues()->with(['reporter', 'owner'])->get();
        if ($issues->isEmpty()) {
            $issues = Issue::with(['reporter', 'owner'])->where('current_level', $level)->take(10)->get();
        }

        $meetingDecisions = Decision::with(['decisionMaker', 'issue'])->where('meeting_id', $meeting->id)->latest()->get();
        $decisions = $meetingDecisions->isNotEmpty() ? $meetingDecisions : Decision::with(['decisionMaker', 'issue'])->latest()->take(10)->get();

        $meetingTasks = Task::with('assignee')->whereHas('decision', fn($dq) => $dq->where('meeting_id', $meeting->id))->get();
        $tasks = $meetingTasks->isNotEmpty() ? $meetingTasks : Task::with('assignee')->latest()->take(15)->get();
        $users = User::where('is_active', true)->take(12)->get();

        $asman = User::whereHas('role', fn($q) => $q->where('name', 'ASMAN'))->first();
        $mup = User::whereHas('role', fn($q) => $q->where('name', 'MUP'))->first();

        return Inertia::render('Meetings/Cetak', compact('meeting', 'issues', 'tasks', 'decisions', 'users', 'level', 'asman', 'mup'));
    }

    public function pingAttendance(Request $request, $id)
    {
        $meeting = Meeting::findOrFail($id);
        $user = auth()->user();

        $attendance = MeetingAttendance::firstOrNew([
            'meeting_id' => $meeting->id,
            'user_id' => $user->id,
        ]);
        if (!$attendance->joined_at) {
            $attendance->joined_at = now();
        }
        $attendance->status = 'Joined';
        $attendance->last_seen_at = now();
        $attendance->save();

        $attendances = MeetingAttendance::where('meeting_id', $meeting->id)->get();

        $usersStatus = [];
        foreach ($attendances as $att) {
            $usersStatus[$att->user_id] = [
                'is_joined' => true,
                'is_online' => $att->isOnline(),
                'joined_at' => $att->joined_at ? $att->joined_at->timezone('Asia/Makassar')->format('H:i') . ' WITA' : null,
                'last_seen' => $att->last_seen_at ? $att->last_seen_at->diffForHumans() : null,
            ];
        }

        $onlineCount = collect($usersStatus)->where('is_online', true)->count();
        $joinedCount = collect($usersStatus)->where('is_joined', true)->count();

        // Sinkronisasi Pesan & Isu Rapat Real-Time
        $messages = MeetingMessage::with(['user.role', 'issue.reporter', 'issue.organizationUnit'])
            ->where('meeting_id', $meeting->id)
            ->oldest()
            ->get();

        // Sinkronisasi Isu Aktif & Arsip Agenda Rapat Real-Time (Oracle-safe)
        $type = $meeting->type ?? 'Rapat TL + Staf';
        if (str_contains($type, 'Asman')) {
            $meetingLevel = 'ASMAN';
        } elseif (str_contains($type, 'Manajemen') || str_contains($type, 'RTM')) {
            $meetingLevel = 'MUP';
        } else {
            $meetingLevel = 'TL';
        }

        if ($meetingLevel === 'TL') {
            $meetingIssues = $meeting->issues()
                ->with(['reporter', 'owner', 'organizationUnit', 'tasks', 'escalations', 'decisions'])
                ->get();

            if ($meetingIssues->isNotEmpty()) {
                $activeIssues = $meetingIssues
                    ->filter(function ($iss) {
                        return !in_array($iss->status, ['Resolved', 'Closed', 'Decided', 'Escalated']) 
                            && $iss->current_level === 'TL'
                            && $iss->tasks->isEmpty();
                    })
                    ->values();

                $archivedIssues = $meetingIssues
                    ->filter(function ($iss) {
                        return in_array($iss->status, ['Resolved', 'Closed', 'Decided', 'Escalated']) 
                            || $iss->current_level !== 'TL'
                            || $iss->tasks->isNotEmpty()
                            || $iss->escalations->isNotEmpty();
                    })
                    ->values();
            } else {
                $leader = $meeting->leader ?? auth()->user();
                $leaderUnits = $leader ? $leader->getAuthorizedUnitIds() : [];

                $baseIssuesQuery = Issue::with(['reporter', 'owner', 'organizationUnit', 'tasks', 'escalations', 'decisions']);
                if (!empty($leaderUnits)) {
                    $baseIssuesQuery->whereIn('unit_id', $leaderUnits);
                }

                $activeIssues = (clone $baseIssuesQuery)
                    ->where('current_level', 'TL')
                    ->whereNotIn('status', ['Resolved', 'Closed', 'Decided', 'Escalated'])
                    ->whereDoesntHave('tasks')
                    ->latest()
                    ->get();

                $archivedIssues = (clone $baseIssuesQuery)
                    ->where(function ($q) {
                        $q->whereIn('status', ['Resolved', 'Closed', 'Decided', 'Escalated'])
                          ->orWhereHas('tasks')
                          ->orWhereHas('escalations');
                    })
                    ->latest()
                    ->take(20)
                    ->get();
            }
        } else {
            $activeIssues = collect([]);
            $archivedIssues = collect([]);
        }

        // Sinkronisasi Putusan Rapat Real-Time
        $decisions = Decision::with(['decisionMaker', 'issue', 'tasks.assignee'])
            ->where('meeting_id', $meeting->id)
            ->latest()
            ->take(15)
            ->get();

        return response()->json([
            'status' => 'ok',
            'online_count' => $onlineCount,
            'joined_count' => $joinedCount,
            'attendances' => $usersStatus,
            'messages' => $messages,
            'active_issues' => $activeIssues,
            'archived_issues' => $archivedIssues,
            'decisions' => $decisions,
        ]);
    }
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guarded = [];

    protected $appends = [
        'avatar_url',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Accessor dinamis avatar URL (Mendukung Hosting & Local Storage serta Fallback).
     */
    public function getAvatarUrlAttribute(): string
    {
        if (!empty($this->avatar)) {
            if (str_starts_with($this->avatar, 'http://') || str_starts_with($this->avatar, 'https://')) {
                return $this->avatar;
            }

            $cleanPath = ltrim($this->avatar, '/');

            // Cek ketersediaan file di storage atau public
            if (file_exists(storage_path('app/public/' . $cleanPath)) || file_exists(public_path('storage/' . $cleanPath))) {
                return asset('storage/' . $cleanPath);
            }
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name ?? 'User') . '&background=0284c7&color=fff&size=120';
    }

    /**
     * Cek apakah user memiliki avatar kustom yang ada fisiknya di storage.
     */
    public function getHasCustomAvatarAttribute(): bool
    {
        if (empty($this->avatar)) {
            return false;
        }

        $cleanPath = ltrim($this->avatar, '/');
        return file_exists(storage_path('app/public/' . $cleanPath)) || file_exists(public_path('storage/' . $cleanPath));
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function organizationUnit(): BelongsTo
    {
        return $this->belongsTo(OrganizationUnit::class, 'unit_id');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function issuesReported(): HasMany
    {
        return $this->hasMany(Issue::class, 'reported_by');
    }

    public function issuesOwned(): HasMany
    {
        return $this->hasMany(Issue::class, 'owner_id');
    }

    public function tasksAssigned(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function tasksCreated(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_by');
    }

    public function teamLeader(): BelongsTo
    {
        return $this->belongsTo(self::class, 'tl_id');
    }

    public function asistenManajer(): BelongsTo
    {
        return $this->belongsTo(self::class, 'asman_id');
    }

    /**
     * Dapatkan daftar ID unit kerja yang berada di dalam lingkup wewenang user ini.
     * - MUP & ADMIN: Seluruh unit UP Sengkang
     * - ASMAN: Unit bidangnya beserta seluruh sub-unit/seksi (child units) di bawahnya
     * - TL & STAFF: Unit seksinya sendiri
     */
    protected ?array $memoizedAuthorizedUnitIds = null;
    protected ?array $memoizedSubordinateUserIds = null;

    /**
     * Dapatkan unit ID yang berwenang diakses berdasarkan peran dan posisi dalam struktur organisasi.
     */
    public function getAuthorizedUnitIds(): array
    {
        if ($this->memoizedAuthorizedUnitIds !== null) {
            return $this->memoizedAuthorizedUnitIds;
        }

        $roleName = strtoupper($this->role->name ?? '');

        if (in_array($roleName, ['MUP', 'ADMIN', 'SUPER_ADMIN', 'SUPER ADMIN'], true)) {
            return $this->memoizedAuthorizedUnitIds = OrganizationUnit::pluck('id')->toArray();
        }

        if ($roleName === 'ASMAN') {
            $myUnitId = $this->unit_id;
            if (!$myUnitId) {
                return $this->memoizedAuthorizedUnitIds = [];
            }

            // Ambil unit bidang ini dan seluruh unit anak (seksi-seksi) di bawahnya
            return $this->memoizedAuthorizedUnitIds = OrganizationUnit::where('parent_unit_id', $myUnitId)
                ->orWhere('id', $myUnitId)
                ->pluck('id')
                ->toArray();
        }

        // Untuk TL dan STAFF: hanya unit seksinya sendiri
        return $this->memoizedAuthorizedUnitIds = ($this->unit_id ? [$this->unit_id] : []);
    }

    /**
     * Dapatkan daftar ID user bawahan (dan diri sendiri) dalam lingkup kerja.
     */
    public function getSubordinateUserIds(): array
    {
        if ($this->memoizedSubordinateUserIds !== null) {
            return $this->memoizedSubordinateUserIds;
        }

        $roleName = strtoupper($this->role->name ?? '');

        if (in_array($roleName, ['MUP', 'ADMIN', 'SUPER_ADMIN', 'SUPER ADMIN'], true)) {
            return $this->memoizedSubordinateUserIds = self::pluck('id')->toArray();
        }

        $unitIds = $this->getAuthorizedUnitIds();
        if (empty($unitIds)) {
            return $this->memoizedSubordinateUserIds = [$this->id];
        }

        return $this->memoizedSubordinateUserIds = self::whereIn('unit_id', $unitIds)->pluck('id')->toArray();
    }

    /**
     * Jalur hierarki komando struktural: Staff -> TL -> ASMAN -> MUP
     */
    public function getHierarchyInfoAttribute(): array
    {
        static $cachedMup = null;
        static $cachedTlsByUnit = [];
        static $cachedAsmansByUnit = [];
        static $cachedUsersById = [];
        static $allRoles = null;
        static $allPositions = null;
        static $allUnits = null;
        static $allUnitParents = null;

        if ($allRoles === null) {
            $allRoles = Role::pluck('name', 'id')->toArray();
        }
        if ($allPositions === null) {
            $allPositions = Position::pluck('name', 'id')->toArray();
        }
        if ($allUnits === null) {
            $allUnits = OrganizationUnit::pluck('name', 'id')->toArray();
            $allUnitParents = OrganizationUnit::pluck('parent_unit_id', 'id')->toArray();
        }

        $roleName = strtoupper($this->relationLoaded('role') ? ($this->role->name ?? 'STAFF') : (($this->role_id && isset($allRoles[$this->role_id])) ? $allRoles[$this->role_id] : 'STAFF'));
        $positionName = $this->relationLoaded('position') ? ($this->position?->name) : (($this->position_id && isset($allPositions[$this->position_id])) ? $allPositions[$this->position_id] : null);
        $positionName = $positionName ?? ($roleName === 'STAFF' ? 'Staff' : $roleName);
        $unitName = $this->relationLoaded('organizationUnit') ? ($this->organizationUnit?->name) : (($this->unit_id && isset($allUnits[$this->unit_id])) ? $allUnits[$this->unit_id] : '-');

        // 1. Cari TL
        $tl = null;
        if ($roleName === 'TL') {
            $tl = $this;
        } elseif ($this->tl_id) {
            if (!isset($cachedUsersById[$this->tl_id])) {
                $cachedUsersById[$this->tl_id] = $this->teamLeader ?? self::with(['position', 'organizationUnit'])->find($this->tl_id);
            }
            $tl = $cachedUsersById[$this->tl_id];
        } elseif ($this->unit_id) {
            if (!array_key_exists($this->unit_id, $cachedTlsByUnit)) {
                $cachedTlsByUnit[$this->unit_id] = self::with(['position', 'organizationUnit'])
                    ->where('unit_id', $this->unit_id)
                    ->whereHas('role', fn($q) => $q->where('name', 'TL'))
                    ->first();
            }
            $tl = $cachedTlsByUnit[$this->unit_id];
        }

        // 2. Cari ASMAN
        $asman = null;
        if ($roleName === 'ASMAN') {
            $asman = $this;
        } elseif ($this->asman_id) {
            if (!isset($cachedUsersById[$this->asman_id])) {
                $cachedUsersById[$this->asman_id] = $this->asistenManajer ?? self::with(['position', 'organizationUnit'])->find($this->asman_id);
            }
            $asman = $cachedUsersById[$this->asman_id];
        } else {
            $parentUnitId = $this->relationLoaded('organizationUnit') ? ($this->organizationUnit?->parent_unit_id) : (($this->unit_id && isset($allUnitParents[$this->unit_id])) ? $allUnitParents[$this->unit_id] : null);
            if ($parentUnitId) {
                if (!array_key_exists($parentUnitId, $cachedAsmansByUnit)) {
                    $cachedAsmansByUnit[$parentUnitId] = self::with(['position', 'organizationUnit'])
                        ->where('unit_id', $parentUnitId)
                        ->whereHas('role', fn($q) => $q->where('name', 'ASMAN'))
                        ->first();
                }
                $asman = $cachedAsmansByUnit[$parentUnitId];
            }
        }

        // 3. Cari MUP (Hanya query 1x per request lifecycle)
        if ($cachedMup === null) {
            $cachedMup = self::with(['position', 'organizationUnit'])
                ->whereHas('role', fn($q) => $q->where('name', 'MUP'))
                ->first();
        }
        $mup = $cachedMup;

        return [
            'staff' => [
                'id' => $this->id,
                'name' => $this->name,
                'position' => $positionName,
                'unit' => $unitName,
                'avatar' => $this->avatar_url,
            ],
            'tl' => $tl ? [
                'id' => $tl->id,
                'name' => $tl->name,
                'position' => $tl->position?->name ?? 'Team Leader',
                'unit' => $tl->organizationUnit?->name ?? '-',
                'avatar' => $tl->avatar_url,
                'is_self' => $tl->id === $this->id,
            ] : null,
            'asman' => $asman ? [
                'id' => $asman->id,
                'name' => $asman->name,
                'position' => $asman->position?->name ?? 'Asisten Manajer',
                'unit' => $asman->organizationUnit?->name ?? '-',
                'avatar' => $asman->avatar_url,
                'is_self' => $asman->id === $this->id,
            ] : null,
            'mup' => $mup ? [
                'id' => $mup->id,
                'name' => $mup->name,
                'position' => $mup->position?->name ?? 'Manager Unit',
                'unit' => $mup->organizationUnit?->name ?? 'UP Sengkang',
                'avatar' => $mup->avatar_url,
                'is_self' => $mup->id === $this->id,
            ] : null,
            'is_direct_to_mup' => !$asman && $this->organizationUnit && ($this->organizationUnit->parent_unit_id == 1 || str_contains(strtolower($this->organizationUnit->name ?? ''), 'k3') || str_contains(strtolower($this->organizationUnit->name ?? ''), 'lingkungan')),
        ];
    }
}

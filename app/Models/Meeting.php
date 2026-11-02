<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Meeting extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function leader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    public function organizationUnit(): BelongsTo
    {
        return $this->belongsTo(OrganizationUnit::class, 'unit_id');
    }

    public function issues()
    {
        return $this->belongsToMany(Issue::class, 'meeting_issues')->withPivot(['status', 'notes'])->withTimestamps();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(MeetingMessage::class, 'meeting_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(MeetingAttendance::class, 'meeting_id');
    }
}

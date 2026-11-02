<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\User;
use App\Models\OrganizationUnit;

return new class extends Migration
{
    public function up(): void
    {
        $asmansByUnit = User::whereHas('role', fn($q) => $q->where('name', 'ASMAN'))->pluck('id', 'unit_id')->toArray();
        $tlsByUnit = User::whereHas('role', fn($q) => $q->where('name', 'TL'))->pluck('id', 'unit_id')->toArray();
        $units = OrganizationUnit::pluck('parent_unit_id', 'id')->toArray();

        // 1. Petakan TL -> ASMAN per bidang
        $tls = User::whereHas('role', fn($q) => $q->where('name', 'TL'))->get();
        foreach ($tls as $tl) {
            $parentUnitId = $units[$tl->unit_id] ?? null;
            if ($parentUnitId && isset($asmansByUnit[$parentUnitId])) {
                $tl->asman_id = $asmansByUnit[$parentUnitId];
                $tl->save();
            } elseif ($parentUnitId == 1) {
                // Direct to MUP
                $tl->asman_id = 1;
                $tl->save();
            }
        }

        // 2. Petakan Staff -> TL dan Staff -> ASMAN per bidang
        $staffs = User::whereHas('role', fn($q) => $q->where('name', 'STAFF'))->get();
        foreach ($staffs as $s) {
            if (isset($tlsByUnit[$s->unit_id])) {
                $s->tl_id = $tlsByUnit[$s->unit_id];
            }
            $parentUnitId = $units[$s->unit_id] ?? null;
            if ($parentUnitId && isset($asmansByUnit[$parentUnitId])) {
                $s->asman_id = $asmansByUnit[$parentUnitId];
            } elseif ($parentUnitId == 1) {
                $s->asman_id = 1;
            } elseif (isset($asmansByUnit[$s->unit_id])) {
                $s->asman_id = $asmansByUnit[$s->unit_id];
            }
            $s->save();
        }
    }

    public function down(): void
    {
        // No-op
    }
};

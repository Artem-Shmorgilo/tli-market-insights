<?php

namespace App\Services;

use App\Models\League;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class LeagueService
{
    public function leagues(): Collection
    {
        return League::query()
            ->orderByDesc('started_at')
            ->orderByDesc('id')
            ->get(['id', 'name', 'system_name', 'started_at']);
    }

    public function selected(Request $request, ?Collection $leagues = null): ?League
    {
        $leagues ??= $this->leagues();

        $defaultLeague = $leagues->first(
            fn (League $league) => $league->started_at !== null
        );

        return $leagues->firstWhere(
            'id',
            $request->integer('league', $defaultLeague?->id ?? 0),
        ) ?? $defaultLeague;
    }
}

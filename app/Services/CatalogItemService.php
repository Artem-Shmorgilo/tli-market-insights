<?php

namespace App\Services;

use App\Models\Item;
use App\Models\Price;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;

class CatalogItemService
{
    public function query(
        ?int    $leagueId,
        ?int    $categoryId,
        ?string $search,
    ): Builder
    {
        $latestObservations = Price::query()
            ->select('item_id')
            ->selectRaw('MAX(observed_at) AS latest_observed_at')
            ->where('league_id', $leagueId ?? 0)
            ->groupBy('item_id');

        $lowestPrices = Price::query()
            ->joinSub(
                $latestObservations,
                'latest_observations',
                function (JoinClause $join): void {
                    $join->on(
                        'prices.item_id',
                        '=',
                        'latest_observations.item_id',
                    )->on(
                        'prices.observed_at',
                        '=',
                        'latest_observations.latest_observed_at',
                    );
                },
            )
            ->where('prices.league_id', $leagueId ?? 0)
            ->select('prices.item_id')
            ->selectRaw('MIN(prices.value) AS lowest_price')
            ->groupBy('prices.item_id');

        return Item::query()
            ->leftJoinSub(
                $lowestPrices,
                'current_prices',
                fn(JoinClause $join) => $join->on(
                    'items.id',
                    '=',
                    'current_prices.item_id',
                ),
            )
            ->select('items.*')
            ->addSelect('current_prices.lowest_price')
            ->with('category:id,name,system_name,image_path')
            ->when(
                $categoryId > 0,
                fn($query) => $query->where(
                    'items.category_id',
                    $categoryId,
                ),
            )
            ->when(
                $search,
                fn($query) => $query->where(
                    'items.name',
                    'ilike',
                    '%' . $search . '%',
                ),
            )
            ->orderByRaw(
                'CASE WHEN current_prices.lowest_price IS NULL '
                . 'THEN 1 ELSE 0 END',
            )
            ->orderBy('items.name');
    }
}

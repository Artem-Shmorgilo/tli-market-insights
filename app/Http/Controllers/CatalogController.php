<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\League;
use App\Models\Price;
use App\Services\LeagueService;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CatalogController extends Controller
{
    public function __construct(
        private readonly LeagueService $leagueService,
    ) {}
    private const int PER_PAGE = 20;

    public function index(Request $request): Response
    {
        $leagues = $this->leagueService->leagues();

        $selectedLeague = $this->leagueService->selected(
            $request,
            $leagues,
        );

        $categories = Category::query()
            ->where('is_hidden', false)
            ->orderBy('display_order')
            ->get(['id', 'name', 'system_name', 'image_path']);

        $items = $this->itemsQuery(
            $selectedLeague?->id,
            $request,
        )->paginate(self::PER_PAGE)->withQueryString();

        return Inertia::render('Catalog/Index', [
            'leagues' => $leagues,
            'selectedLeagueId' => $selectedLeague?->id,
            'categories' => $categories,
            'items' => $items,
            'filters' => [
                'category' => $request->integer('category') ?: null,
                'search' => $request->string('search')->toString(),
            ],
        ]);
    }

    public function items(Request $request): JsonResponse
    {
        $leagueId = $request->integer('league');

        abort_unless(
            $leagueId > 0 && League::whereKey($leagueId)->exists(),
            404,
        );

        $items = $this->itemsQuery($leagueId, $request)
            ->simplePaginate(self::PER_PAGE)
            ->through(fn (Item $item) => [
                'id' => $item->id,
                'name' => $item->name,
                'image_path' => $item->image_path,
                'category_id' => $item->category_id,
                'lowest_price' => $item->lowest_price,
                'category' => $item->category ? [
                    'id' => $item->category->id,
                    'name' => $item->category->name,
                    'system_name' => $item->category->system_name,
                    'image_path' => $item->category->image_path,
                ] : null,
            ]);

        return response()->json($items);
    }

    private function itemsQuery(
        ?int $leagueId,
        Request $request,
    ) {
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
                fn (JoinClause $join) => $join->on(
                    'items.id',
                    '=',
                    'current_prices.item_id',
                ),
            )
            ->select('items.*')
            ->addSelect('current_prices.lowest_price')
            ->with('category:id,name,system_name,image_path')
            ->when(
                $request->integer('category') > 0,
                fn ($query) => $query->where(
                    'items.category_id',
                    $request->integer('category'),
                ),
            )
            ->when(
                $request->filled('search'),
                fn ($query) => $query->where(
                    'items.name',
                    'ilike',
                    '%' . $request->string('search')->toString() . '%',
                ),
            )
            ->orderByRaw(
                'CASE WHEN current_prices.lowest_price IS NULL '
                . 'THEN 1 ELSE 0 END',
            )
            ->orderBy('items.name');
    }
}

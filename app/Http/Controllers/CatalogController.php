<?php

namespace App\Http\Controllers;

use App\Http\Requests\Catalog\IndexRequest;
use App\Http\Requests\Catalog\ItemsRequest;
use App\Http\Resources\ItemResource;
use App\Models\Category;
use App\Services\CatalogItemService;
use App\Services\LeagueService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Inertia\Inertia;
use Inertia\Response;

class CatalogController extends Controller
{
    public function __construct(
        private readonly LeagueService      $leagueService,
        private readonly CatalogItemService $catalogItemService,
    )
    {
    }

    private const int PER_PAGE = 20;

    public function index(IndexRequest $request): Response
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

        $items = $this->catalogItemService->query(
            $selectedLeague?->id,
            $request->integer('category') ?: null,
            $request->validated('search'),
        )->paginate(self::PER_PAGE)->withQueryString();

        return Inertia::render('Catalog/Index', [
            'categories' => $categories,
            'items' => $items,
            'filters' => [
                'category' => $request->integer('category') ?: null,
                'search' => $request->string('search')->toString(),
            ],
        ]);
    }

    public function items(ItemsRequest $request): AnonymousResourceCollection
    {
        $items = $this->catalogItemService->query(
            $request->integer('league'),
            $request->integer('category') ?: null,
            $request->validated('search'),
        )
            ->simplePaginate(self::PER_PAGE);

        return ItemResource::collection($items);
    }
}

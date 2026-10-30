<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StorePricesRequest;
use App\Models\Item;
use App\Models\Price;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PriceCollectionController extends Controller
{
    public function store(StorePricesRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $item = Item::query()
            ->where('external_id', $validated['item_id'])
            ->firstOrFail();

        $timestamp = $validated['timestamp'];

        $prices = array_map(
            fn (int|float|string $value): array => [
                'item_id' => $item->id,
                'league_id' => $validated['league_id'],
                'value' => $value,
                'observed_at' => $timestamp,
                'created_at' => now(),
            ],
            $validated['prices'],
        );

        DB::transaction(function () use ($prices): void {
            Price::query()->insert($prices);
        });

        return response()->json([
            'message' => 'Prices collected successfully.',
            'data' => [
                'item_id' => $item->external_id,
                'league_id' => $validated['league_id'],
                'received' => count($prices),
                'saved' => count($prices),
            ],
        ], 201);
    }
}

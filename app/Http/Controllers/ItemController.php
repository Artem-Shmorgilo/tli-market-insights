<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Inertia\Inertia;
use Inertia\Response;

class ItemController extends Controller
{
    public function show(Item $item): Response
    {
        return Inertia::render('Items/Show', [
            'item' => $item,
        ]);
    }
}

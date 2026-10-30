<?php

namespace App\Http\Resources;

    use Illuminate\Http\Request;
    use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'image_path' => $this->image_path,
            'category_id' => $this->category_id,
            'lowest_price' => $this->lowest_price,

            'category' => $this->whenLoaded('category', fn () =>
            $this->category ? [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'system_name' => $this->category->system_name,
                'image_path' => $this->category->image_path,
            ] : null
            ),
        ];
    }
}

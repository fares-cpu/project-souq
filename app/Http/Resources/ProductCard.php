<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\UserCard;

class ProductCard extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'category' => $this->category,
            'price' => $this->price,
            'mainimage' => env('APP_URL') . Storage::url($this->mainimage),
            'user' => new UserCard($this->user),
            'rate' => $this->rate()
        ];
    }
}

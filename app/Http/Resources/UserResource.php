<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\ProductCard;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'username' => $this->username,
            'bio' => $this->bio,
            'phone' => $this->publicphone,
            'profileimage' => env('APP_URL') . Storage::url($this->profileImage),
            'products' => ProductCard::collection($this->products),
            'follows' => $this->followsCount()
        ];
    }
}

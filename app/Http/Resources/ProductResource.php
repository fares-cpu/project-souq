<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserCard;
use Illuminate\Support\Facades\Storage;
use App\Models\Like;

class ProductResource extends JsonResource
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
            'description' => $this->description,
            'category' => $this->category,
            'price' => $this->price,
            'instock' => $this->instock,
            'mainimage' => env('APP_URL') . Storage::url($this->mainimage),
            'image2' =>  env('APP_URL') . Storage::url($this->image2),
            'image3' =>  env('APP_URL') . Storage::url($this->image3),
            'image4' =>  env('APP_URL') . Storage::url($this->image4),
            'user' => new UserCard($this->user),
            'likes' => $this->likesCount(),
            'comments' => new CommentResource($this->comment),
            'rate' => $this->rate()
            
        ];
    }
}

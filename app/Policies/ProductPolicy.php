<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use App\Models\Buy;

class ProductPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function update(User $user, Product $product){
        return $user->id === $product->user_id;
    }

    public function rate(User $user, Product $product){
        //as user->buys is one to many, here is the thing:
        $buyCollection = Buy::where('user_id', $user->id)->where('product_id', $product->id)->get();
        return $buyCollection->isNotEmpty();
    }

}

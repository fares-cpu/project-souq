<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    /**
     * Get the user that owns the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all of the rates for the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rates(): HasMany
    {
        return $this->hasMany(Rate::class, 'product_id', 'id');
    }

    public function rate(){
        $rates = Rate::select('value')
            ->where('product_id', $this->id)
            ->get();
        $all = 0;
        $counter = 0;
        foreach($rates as $rate){
            $all += $rate->value;
            $counter++;
        }
        return ($all/$counter);
    }

    public function likesCount(){
        return Like::where('product_id', $this->id)->count();
    }
}

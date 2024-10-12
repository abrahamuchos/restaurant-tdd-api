<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 *
 *
 * @property int                              $id
 * @property int                              $restaurant_id
 * @property string                           $name
 * @property string|null                      $description
 * @property float                            $price
 * @property bool                             $is_available
 * @property \Illuminate\Support\Carbon|null  $created_at
 * @property \Illuminate\Support\Carbon|null  $updated_at
 * @property-read \App\Models\Restaurant|null $restaurant
 * @method static \Database\Factories\DishFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Dish newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Dish newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Dish query()
 * @method static \Illuminate\Database\Eloquent\Builder|Dish whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dish whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dish whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dish whereIsAvailable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dish whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dish wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dish whereRestaurantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dish whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Dish extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'restaurant_id',
        'name',
        'description',
        'price',
        'is_available',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }
}

<?php

namespace App\Models;

use App\Models\Traits\HasSearch;
use App\Models\Traits\HasSort;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

/**
 *
 *
 * @property int                                                                  $id
 * @property int                                                                  $restaurant_id
 * @property string                                                               $name
 * @property string                                                               $description
 * @property \Illuminate\Support\Carbon|null                                      $created_at
 * @property \Illuminate\Support\Carbon|null                                      $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Dish> $dishes
 * @property-read int|null                                                        $dishes_count
 * @property-read \App\Models\Restaurant                                          $restaurant
 * @method static \Database\Factories\MenuFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Menu newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Menu newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Menu query()
 * @method static \Illuminate\Database\Eloquent\Builder|Menu whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Menu whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Menu whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Menu whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Menu whereRestaurantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Menu whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant search($search = '')
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant sort($sortBy = '', $sortDirection = '')
 * @mixin \Eloquent
 */
class Menu extends Model
{
    use HasFactory, HasSearch, HasSort;

    protected $fillable = [
        'name',
        'description',
        'qr'
    ];

    /**
     * @return BelongsTo
     */
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    /**
     * @return BelongsToMany
     */
    public function dishes(): BelongsToMany
    {
        return $this->belongsToMany(Dish::class, 'dish_menu');
    }

    /**
     * @return string[]
     */
    public function searchFields(): array
    {
        return ['name', 'description', 'code'];
    }

    /**
     * Fields to be sorted
     * @return string[]
     */
    public function sortFields(): array
    {
        return ['id', 'name', 'description'];
    }

    public function qr(): Attribute
    {
        return Attribute::get(fn($attr) => $attr ? Storage::disk('public-qr')->url($attr) : null);
    }
}

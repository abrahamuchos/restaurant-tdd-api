<?php

namespace App\Models;

use App\Models\Traits\HasSearch;
use App\Models\Traits\HasSort;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 
 *
 * @property int                                                                  $id
 * @property int                                                                  $user_id
 * @property string                                                               $code
 * @property string                                                               $name
 * @property string                                                               $description
 * @property string                                                               $address
 * @property string                                                               $phone
 * @property string                                                               $email
 * @property string                                                               $website
 * @property string|null                                                          $logo
 * @property string|null                                                          $image
 * @property \Illuminate\Support\Carbon|null                                      $created_at
 * @property \Illuminate\Support\Carbon|null                                      $updated_at
 * @method static \Database\Factories\RestaurantFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant query()
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant whereClosingHour($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant whereOpeningHour($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant whereWebsite($value)
 * @property-read \App\Models\User                                                $user
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant whereCode($value)
 * @property string                                                               $opening_hour
 * @property string                                                               $closing_hour
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Dish> $dishes
 * @property-read int|null                                                        $dishes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Menu> $menus
 * @property-read int|null                                                        $menus_count
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant search($search = '')
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant sort($sortBy ='', $sortDirection = '')
 * @mixin \Eloquent
 */
class Restaurant extends Model
{
    use HasFactory, HasSearch, HasSort;

    protected $fillable = [
        'user_id',
        'code',
        'name',
        'description',
        'address',
        'phone',
        'email',
        'website',
        'opening_hour',
        'closing_hour',
        'logo',
        'image',
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany
     */
    public function dishes(): HasMany
    {
        return $this->hasMany(Dish::class);
    }

    /**
     * @return HasMany
     */
    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
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
}

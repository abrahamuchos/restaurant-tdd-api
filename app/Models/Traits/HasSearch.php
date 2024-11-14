<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

trait HasSearch
{
    public function searchFields(): array
    {
        return [];
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function scopeSearch(Builder $query, $search = ''): void
    {
        $search = $search ?: request()->get('search');
        if (!$search) {
            return;
        }

        $fields = $this->searchFields();

        $query->where(function (Builder $query) use ($search, $fields) {
            foreach (explode(' ', $search) as $word) {
                 $query->whereAny($fields, 'like', "%{$word}%");
            }
        });
    }
}

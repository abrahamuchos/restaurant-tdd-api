<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasSort
{
    /**
     * @return string[]
     */
    public function sortFields(): array
    {
        return ['id'];
    }

    /**
     * @param Builder $builder
     * @param string  $sortBy
     * @param string  $sortDirection
     *
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function scopeSort(Builder $builder, string $sortBy = '', string $sortDirection = ''): void
    {
        $sortBy = $sortBy ?: request()->get('sortBy');
        $sortDirection = $sortDirection ?: request()->get('sortDirection');
        $sortDirection = $sortDirection === 'asc' ? 'asc' : 'desc';

        if(!$sortBy) return;
        if (!in_array($sortBy, $this->sortFields())) {
            abort(400, 'Invalid sortBy');
        }

        $builder->orderBy($sortBy, $sortDirection);
    }

}

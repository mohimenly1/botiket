<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;
use App\Models\User;

class UserCustomSort implements Sort
{
    public function __invoke(Builder $query, $descending, string $property) : Builder
    {
        $direction = $descending ? 'DESC' : 'ASC';

        return  $query->orderBy(
            User::select(['id','name'])
                ->whereColumn('users.id', 'order.user_id')
        );
    }
}

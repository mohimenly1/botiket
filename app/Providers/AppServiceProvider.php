<?php

namespace App\Providers;

use Illuminate\Contracts\Pagination\Paginator as PaginationPaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->resolving(PaginationPaginator::class, function ($paginator) {
            return $paginator->appends(Request::except('page'));
        });
    }
}

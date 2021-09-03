<?php

namespace App\Providers;

use App\Models\CastMember;
use App\Models\Category;
use App\Models\Genre;
use App\Observers\CastMemberObserver;
use App\Observers\CategoryObserver;
use App\Observers\GenreObserver;
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
        // Adiciona um ou mais observers na inicialização
        Category::observe(CategoryObserver::class);
        Genre::observe(GenreObserver::class);
        CastMember::observe(CastMemberObserver::class);
    }
}

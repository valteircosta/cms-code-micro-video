<?php

namespace App\Providers;

use App\Models\CastMember;
use App\Models\Category;
use App\Models\Genre;
use App\Observers\SyncModelObserver;
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
        // Usando SyncModelObserver que é a abstração (Generico) da classe Observer
        Category::observe(SyncModelObserver::class);
        Genre::observe(SyncModelObserver::class);
        CastMember::observe(SyncModelObserver::class);
    }
}

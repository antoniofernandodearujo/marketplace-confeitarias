<?php

namespace App\Providers;

use App\Models\Product;
use App\Observers\ProductObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     * 
     * Este método registra os observers e outras configurações da aplicação
     * que devem ser carregadas durante o boot do Laravel.
     */
    public function boot(): void
    {
        // Registra o observer para processos relacionados ao ciclo de vida dos produtos
        Product::observe(ProductObserver::class);
    }
}

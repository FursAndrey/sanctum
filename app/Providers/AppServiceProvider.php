<?php

namespace App\Providers;

use App\MyFaker\FakerImageProvider;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(
            Generator::class,
            function () {
                $faker = Factory::create();
                $faker->addProvider(new FakerImageProvider($faker));

                return $faker;
            }
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //для пагинаций
        Paginator::useBootstrap();
    }
}

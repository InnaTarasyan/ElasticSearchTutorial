<?php

namespace App\Providers;

use Elasticsearch\Client;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Articles\ArticlesRepository;
use App\Articles\EloquentArticlesRepository;
use App\Articles\ElasticsearchArticlesRepository;

use Elasticsearch\ClientBuilder;




class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Schema::defaultStringLength(191);


    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
//        $this->app->bind(ArticlesRepository::class, function () {
//            return new EloquentArticlesRepository();
//        });
        $this->app->singleton(ArticlesRepository::class, function($app) {
            // This is useful in case we want to turn-off our
            // search cluster or when deploying the search
            // to a live, running application at first.
            if (!config('services.search.enabled')) {
                return new EloquentArticlesRepository();
            }

            return new ElasticsearchArticlesRepository(
                ClientBuilder::create()->build()
            );
        });

//        $this->app->bind(Client::class, function () {
//            return ClientBuilder::create()->build();
//        });




        //$this->bindSearchClient();
    }

    private function bindSearchClient()
    {
        $this->app->bind(Client::class, function ($app) {
            return ClientBuilder::create()
                ->setHosts(config('services.search.hosts'))
                ->build();
        });
    }
}

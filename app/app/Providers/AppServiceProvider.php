<?php

namespace App\Providers;

use App\Services\AIAgentService;
use App\Services\AIPlannerService;
use App\Services\SamplePlanService;
use App\Services\SwipeService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SwipeService::class, function ($app) {
            $config = $app['config']['services.swipe'];

            return new SwipeService(
                baseUrl: $config['base_url'],
                clientId: $config['client_id'],
                clientSecret: $config['client_secret'],
                mock: (bool) $config['mock'],
            );
        });

        $this->app->singleton(AIAgentService::class, function ($app) {
            $config = $app['config']['services.anthropic'];

            return new AIAgentService(
                apiKey: $config['api_key'],
                model: $config['model'],
                mock: (bool) $config['mock'],
            );
        });

        $this->app->singleton(SamplePlanService::class);

        $this->app->singleton(AIPlannerService::class, function ($app) {
            $config = $app['config']['services.anthropic'];

            return new AIPlannerService(
                samplePlans: $app->make(SamplePlanService::class),
                apiKey: $config['api_key'],
                model: $config['model'],
                mock: (bool) $config['mock'],
            );
        });
    }

    public function boot(): void
    {
        //
    }
}

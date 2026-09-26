<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \App\Models\Tontine::class => \App\Policies\TontinePolicy::class,
        \App\Models\CollectiveContribution::class => \App\Policies\CollectiveContributionPolicy::class,
        \App\Models\IndividualContribution::class => \App\Policies\IndividualContributionPolicy::class,
        \App\Models\RotatingContribution::class => \App\Policies\RotatingContributionPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}

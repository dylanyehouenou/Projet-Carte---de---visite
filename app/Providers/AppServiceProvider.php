<?php

namespace App\Providers;

use App\Models\Employee;
use App\Policies\EmployeePolicy;
use App\Services\Wallet\Apple\AppleWalletProvider;
use App\Services\Wallet\Apple\PassBuilder;
use App\Services\Wallet\Google\GoogleWalletProvider;
use App\Services\Wallet\Google\ObjectBuilder;
use App\Services\Wallet\WalletManager;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(WalletManager::class, function () {
            return new WalletManager(
                new AppleWalletProvider(new PassBuilder()),
                new GoogleWalletProvider(new ObjectBuilder()),
            );
        });
    }

    public function boot(): void
    {
        Gate::policy(Employee::class, EmployeePolicy::class);

        // Inject wallet availability flags into the public card view
        // without touching PublicCardController
        View::composer('public.card', function ($view) {
            $manager = $this->app->make(WalletManager::class);
            $view->with([
                'walletAppleEnabled'  => $manager->isAppleConfigured(),
                'walletGoogleEnabled' => $manager->isGoogleConfigured(),
            ]);
        });
    }
}

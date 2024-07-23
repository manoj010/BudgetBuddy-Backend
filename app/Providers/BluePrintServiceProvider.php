<?php

namespace App\Providers;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;

class BluePrintServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Blueprint::macro('defaultInfos', function () {
            $this->foreignId('created_by')->constrained('users')->onUpdate('cascade');
            $this->foreignId('updated_by')->nullable()->constrained('users')->onUpdate('cascade');
            $this->softDeletes('archived_at');
        });
    }
}

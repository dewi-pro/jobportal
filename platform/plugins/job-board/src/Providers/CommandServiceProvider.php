<?php

namespace Botble\JobBoard\Providers;

use Botble\JobBoard\Commands\CheckExpiredJobsSoonCommand;
use Botble\JobBoard\Commands\RenewJobsCommand;
use Illuminate\Support\ServiceProvider;

class CommandServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->commands([
            RenewJobsCommand::class,
            CheckExpiredJobsSoonCommand::class,
        ]);
    }
}

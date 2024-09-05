<?php

namespace Botble\JobBoard\Providers;

use Botble\Base\Events\UpdatedContentEvent;
use Botble\JobBoard\Events\JobPublishedEvent;
use Botble\JobBoard\Listeners\RenderingSiteMapListener;
use Botble\JobBoard\Listeners\SaveFavoriteTagAndSkillsListener;
use Botble\JobBoard\Listeners\SendJobAlertListener;
use Botble\JobBoard\Listeners\UpdatedContentListener;
use Botble\Theme\Events\RenderingSiteMapEvent;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        UpdatedContentEvent::class => [
            UpdatedContentListener::class,
            SaveFavoriteTagAndSkillsListener::class,
        ],
        RenderingSiteMapEvent::class => [
            RenderingSiteMapListener::class,
        ],
        JobPublishedEvent::class => [
            SendJobAlertListener::class,
        ],
    ];
}

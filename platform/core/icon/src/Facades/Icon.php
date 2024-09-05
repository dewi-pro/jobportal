<?php

namespace Botble\Icon\Facades;

use Botble\Icon\IconManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static string getDefaultDriver()
 * @method static \Botble\Icon\IconDriver createSvgDriver()
 * @method static mixed driver(string|null $driver = null)
 * @method static \Botble\Icon\IconManager extend(string $driver, \Closure $callback)
 * @method static array getDrivers()
 * @method static \Illuminate\Contracts\Container\Container getContainer()
 * @method static \Botble\Icon\IconManager setContainer(\Illuminate\Contracts\Container\Container $container)
 * @method static \Botble\Icon\IconManager forgetDrivers()
 *
 * @see \Botble\Icon\IconManager
 */
class Icon extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return IconManager::class;
    }
}

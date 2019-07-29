<?php

namespace IN10\Multilanguage;

use IN10\Multilanguage\Router;
use IN10\Multilanguage\SetLanguageFromRoute;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

class ServiceProvider extends IlluminateServiceProvider
{
    public function boot() : void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/languages.php', 'languages');

        $this->publishes([
            __DIR__ . '/../config/languages.php' => config_path('languages.php'),
        ], 'config');
    }

    public function register() : void
    {
        $router = app('router');
        $router->aliasMiddleware('set-language-from-route', SetLanguageFromRoute::class);
        $router->aliasMiddleware('detect-user-language', DetectLanguage::class);
        $router->mixin(new Router());
    }
}

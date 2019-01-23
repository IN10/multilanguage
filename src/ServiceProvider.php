<?php

namespace IN10\LaravelMultilanguage;

class ServiceProvider
{
    public function register() : void
    {
        app(Router::class)->mixin(\IN10\Multilanguage\Router::class);
    }
}

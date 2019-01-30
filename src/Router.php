<?php

namespace IN10\Multilanguage;

use Closure;

class Router
{
    public function multilanguage()
    {
        return function (array $attributes, Closure $callback) {
            // Define the route group again, but without an language, which
            // defaults to the default language
            $this->group(array_merge($attributes, [
                'middleware' => 'set-language-from-route',
            ]), $callback);

            // Define a route group for every language used with that language
            // code as the route prefix
            $languages = config('languages.supported-languages');
            foreach ($languages as $language) {
                $this->group(array_merge($attributes, [
                    'prefix' => $language,
                    'as' => $language . '.',
                    'middleware' => 'set-language-from-route',
                ]), $callback);
            }
        };
    }
}

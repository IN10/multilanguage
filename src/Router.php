<?php

namespace IN10\Multilanguage;

use Closure;

class Router
{
    public function multilanguage()
    {
        return function (array $attributes, Closure $callback) {
            // Define a route group for every language used with that language
            // code as the route prefix
            $languages = config('languages.supported-languages');
            foreach ($languages as $language) {
                $attributes = array_merge($attributes, [
                    'prefix' => $language,
                    'as' => $language . '.',
                    'middleware' => 'set-language-from-route',
                ]);
                $this->group($attributes, $callback);
            }
        };
    }
}

<?php

namespace IN10\Multilanguage;

use Closure;
use DomainException;
use Illuminate\Translation\Translator;
use OutOfRangeException;

class Router
{
    /**
     * Define a multilanguage route group in which all contained routes are
     * prefixed with the correct language
     */
    public function multilanguage() : Closure
    {
        return function (array $attributes, Closure $callback) {

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

            // Define the route group again, but without an language, which
            // defaults to the default language
            $this->group(array_merge($attributes, [
                'middleware' => 'set-language-from-route',
            ]), $callback);
        };
    }

    /**
     * Register a new translated GET route with the router.
     */
    public function transGet() : Closure
    {
        return function ($key, $action) {

            // Grab the current language from the group stack
            $lastMiddleware = end($this->groupStack);
            $language = substr($lastMiddleware['as'] ?? '', 0, 2);

            // If language is not set (like in the default group), use the default language
            if (!$language) {
                $language = config('languages.default');
            }

            // Check if the language is valid
            if (!in_array($language, config('languages.supported-languages', []))) {
                throw new DomainException('Route::transGet() can only be used in a ::multilanguage() route group, which must be a root-group (first part of the path)');
            }

            $translator = app(Translator::class);

            // Check if we have a translation
            $translationKey = 'routes.' . $key;
            if (!$translator->hasForLocale($translationKey, $language)) {
                throw new OutOfRangeException("Missing translation for \"{$translationKey}\" in language {$language}");
            }

            // Create a GET-route with the translated string
            $actualRoute = $translator->trans($translationKey, [], $language);
            return $this->get($actualRoute, $action);
        };
    }
}

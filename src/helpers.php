<?php

if (!function_exists('translatedRoute')) {
    /**
     * Generate a URL to a route, taken into account translations
     */
    function translatedRoute(
        string $route,
        array $parameters = [],
        bool $absolute = true,
        ?string $language = null
    ) : string {
        if ($language === null) {
            $language = \Illuminate\Support\Facades\App::getLocale();
        }
        $actualRoute = "{$language}.{$route}";
        return route($actualRoute, $parameters, $absolute);
    }
}

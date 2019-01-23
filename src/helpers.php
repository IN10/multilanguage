<?php

if (!function_exists('translatedRoute')) {
    /**
     * Generate a URL to a route, taken into account translations
     */
    function translatedRoute(
        string $language,
        string $route,
        array $parameters = [],
        bool $absolute = true
    ) : string {
        $actualRoute = "{$language}.{$route}";
        return route($actualRoute, $parameters, $absolute);
    }
}

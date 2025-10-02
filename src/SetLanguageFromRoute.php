<?php

namespace IN10\Multilanguage;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLanguageFromRoute
{
    public function handle($request, Closure $next)
    {
        $path = $request->path();

        //set language based on browser language
        $browserLanguage = substr(preg_split('/,|;/', $request->server('HTTP_ACCEPT_LANGUAGE'))[0], 0, 2);
        if (!in_array($browserLanguage, config('languages.supported-languages')) && Session::get('language') === null) {
            setcookie('language', config('languages.fallback-language'), time() + 3600 * 24 * 30, '/');
            Session::put('language', config('languages.fallback-language'));
            return redirect( config(['app.url']) . '/' . config('languages.fallback-language') . $path, 301);
        }

        // The root path "/" is a special case in Laravel, e.g. the root route
        // is "/", while "/de/arthur" would be "de/arthur" as the path
        if ($path === '/') {
            App::setLocale(config('languages.default'));
            return $next($request);
        }

        // Find the language
        $parts = collect(explode('/', $path));
        if ($parts->count() === 0) {
            App::setLocale(config('languages.default'));
        }

        // Check if the language is 2 letters
        $language = $parts->first();
        if (strlen($language) !== 2) {
            App::setLocale(config('languages.default'));
            return $next($request);
        }

        // Check for supported languages
        $languages = config('languages.supported-languages', []);
        if (!in_array($language, $languages)) {
            abort(404, 'No such language');
        }

        // Redirect the default language to the naked route
        if ($language === config('languages.default')) {
            $parts->shift(); // Chop off the language part
            $newPath = $parts->implode('/');
            $queryString = http_build_query($request->query());
            if ($queryString) {
                $newPath = "{$newPath}?{$queryString}";
            }
            return redirect($newPath, 301);
        }

        App::setLocale($language);
        return $next($request);
    }
}

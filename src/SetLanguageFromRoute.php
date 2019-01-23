<?php

namespace IN10\Multilanguage;

use Closure;
use Illuminate\Support\Facades\App;

class SetLanguageFromRoute
{
    public function handle($request, Closure $next)
    {
        $path = $request->path();

        // The root path "/" is a special case in Laravel, e.g. the root route
        // is "/", while "/de/arthur" would be "de/arthur" as the path
        if ($path === '/') {
            return $next($request);
        }

        // Find the language
        $parts = collect(explode('/', $path));
        if ($parts->count() === 0) {
            return $next($request);
        }

        // Check if the language is 2 letters
        $language = $parts->first();
        if (strlen($language) !== 2) {
            return $next($request);
        }

        // Check for supported languages
        $languages = config('languages.supported-languages', []);
        if (!in_array($language, $languages)) {
            abort(404, 'No such language');
        }

        // Redirect the default language to the naked route
        if ($language === config('languages.default')) {
            // Chop off the language part
            $parts->shift();
            $newPath = $parts->implode('/');
            return redirect($newPath, 301);
        }

        App::setLocale($language);
        return $next($request);
    }
}

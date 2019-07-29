<?php

namespace IN10\Multilanguage;

use Closure;
use Illuminate\Support\Facades\App;

/**
 * Detects the desired language of the Client from the Accept-Language header
 */
class DetectLanguage
{
    public function handle($request, Closure $next)
    {
        // Only operate on the homepage of the website
        // otherwise, a page like example.com/news with an Accept-Header specifying Dutch
        // would get redirected to example.com/nl, because the middleware is agnostic
        // as to how URLs are constructed on the website.
        if ($request->path() !== '/') {
            return $next($request);
        }

        // Only operate when an Accept-Language header is sent
        if (!$request->hasHeader('Accept-Language')) {
            return $next($request);
        }

        // Only operate when the current language is the default language
        if (App::getLocale() !== config('languages.default')) {
            return $next($request);
        }

        $desiredLanguage = $this->parseHeader($request->header('Accept-Language'));
        if ($desiredLanguage === null) {
            return $next($request);
        }

        // If the desired language is the current (default) language, do nothing
        if ($desiredLanguage === App::getLocale()) {
            return $next($request);
        }

        // Redirect to the homepage in the desired language
        return redirect("/{$desiredLanguage}", 307);
    }

    /**
     * Parse the Accept-Language header in accordance with RFC-2616
     * https://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     */
    private function parseHeader($header) : ?string
    {
        if ($header === null) {
            return null;
        }

        if (is_array($header)) {
            if (count($header) >= 1) {
                $header = (string) $header[0];
            } else {
                return null;
            }
        }

        // Explode by comma's
        $entries = collect(explode(',', $header));

        // Parse each entry, normalising q-values
        $languages = $entries->mapwithKeys(function ($entry) {

            // Split by ;
            if (strpos($entry, ';') !== false) {
                $parts = explode(';q=', $entry);
                $language = $parts[0];
                $q = (float) trim($parts[1]);
            } else {
                $language = $entry;
                $q = 1.0;
            }

            // Split the language, using only the prefix
            if (strpos($language, '-') !== false) {
                $language = explode('-', $language)[0];
            }

            return [trim($language) => $q];
        });

        // Return the item with the largest q-value that we support on this website
        return $languages->sort()->reverse()->keys()->first(function ($language) {
            return in_array($language, config('languages.supported-languages', []));
        });
    }
}

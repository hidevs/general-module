<?php

namespace Modules\General\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AcceptLanguageMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next)
    {
        if ($locale = $this->parseHttpLocale($request)) {
            app()->setLocale($locale == '*' ? $this->defaultLocale() : $locale);
        }

        return $next($request);
    }

    public function defaultLocale()
    {
        return config('app.locale', 'fa-IR');
    }

    private function parseHttpLocale(Request $request): string
    {
        $list = explode(',', $request->server('HTTP_ACCEPT_LANGUAGE', $this->defaultLocale()));

        $locales = collect($list)
            ->map(function ($locale) {
                $parts = explode(';', $locale);

                $mapping['locale'] = trim($parts[0]);

                if (isset($parts[1])) {
                    $factorParts = explode('=', $parts[1]);

                    $mapping['factor'] = $factorParts[1];
                } else {
                    $mapping['factor'] = 1;
                }

                return $mapping;
            })
            ->sortByDesc(function ($locale) {
                return $locale['factor'];
            });

        return $locales->first()['locale'];
    }
}

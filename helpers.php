<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Uri;

if (! function_exists('frontend_url')) {
    function frontend_url(): Uri
    {
        throw_if(
            empty($frontendUrl = config('app.frontend_url')),
            new \Exception('Frontend URL is not defined in app config')
        );

        return Uri::to($frontendUrl);
    }
}

if (! function_exists('try_catch')) {
    function try_catch(\Closure $closure, $default = null, bool $throw = false)
    {
        try {
            return $closure();
        } catch (\Throwable $e) {
            throw_if($throw, $e);

            return $default;
        }
    }
}

if (! function_exists('try_catch_transaction')) {
    function try_catch_transaction(\Closure $closure, $default = null, bool $throw = false)
    {
        try {
            DB::beginTransaction();
            $result = $closure();
            DB::commit();

            return $result;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw_if($throw, $e);

            return $default;
        }
    }
}

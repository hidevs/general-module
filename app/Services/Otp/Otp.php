<?php

namespace Modules\General\Services\Otp;

use Illuminate\Support\Facades\Facade;

/**
 * @method static OtpService digits(int $digits)
 * @method static OtpService expiry(int $digits)
 * @method static OtpService bypass(bool $bypass)
 * @method static int getDigits()
 * @method static int getExpiry()
 * @method static bool getBypass()
 * @method static string make(string $key)
 * @method static string create(string $key)
 * @method static string generate(string $key, $data = null)
 * @method static string getData(string $key)
 * @method static bool match(mixed $otp, string $key)
 * @method static bool check(mixed $otp, string $key)
 * @method static bool verify(mixed $otp, string $key)
 *
 * @see OtpService
 */
class Otp extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'otp';
    }
}

<?php

namespace Modules\General\Services\Otp;

use Closure;
use Exception;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Cache;
use Rawilk\Settings\Facades\Settings;

class OtpService
{
    protected Repository $store;

    protected int $expiry;

    protected int $digits;

    protected bool $bypass;

    public function __construct()
    {
        $this->store = Cache::store();
        $this->digits = intval(Settings::get('otp.digits', 6));
        $this->expiry = intval(Settings::get('otp.expiry', 60));
        $this->bypass = boolval(Settings::get('otp.bypass', false));
    }

    public function getDigits(): int
    {
        return $this->digits;
    }

    public function getExpiry(): int
    {
        return $this->expiry;
    }

    public function getBypass(): bool
    {
        return $this->bypass;
    }

    public function expiry(int $expiry): self
    {
        if ($expiry * 60 > 0) {
            $this->expiry = $expiry * 60;
        }

        return $this;
    }

    public function digits(int $digits): self
    {
        if ($digits > 0) {
            $this->digits = $digits;
        }

        return $this;
    }

    public function bypass(bool $bypass): self
    {
        $this->bypass = $bypass;

        return $this;
    }

    public function generate($key, $data = null): string
    {
        $format = implode('', array_map(fn ($_) => '#', range(1, $this->digits)));
        $code = fake()->unique()->numerify($format);
        $ttl = now()->addMinutes($this->expiry);

        $this->store->put($this->keyFor($key), $code, $ttl);
        if (! is_null($data)) {
            $this->store->put($this->keyFor($key), $data, $ttl);
        }

        return $code;
    }

    public function getData($key): mixed
    {
        return $this->store->get($this->keyFor($key));
    }

    public function check($code, $key): bool
    {
        if ($this->bypass) {
            return true;
        }

        if (! $this->store->has($this->keyFor($key))) {
            return false;
        }

        return $code == $this->store->get($this->keyFor($key));
    }

    public function forget($key): bool
    {
        return $this->store->forget($this->keyFor($key));
    }

    protected function keyFor(string $key, string $suffix = ''): string
    {
        return md5(sprintf('%s.%s.%s', 'otp', $key, $suffix));
    }

    protected function alias($key): ?Closure
    {
        $aliases = [
            'make' => fn (array $args) => $this->generate(...$args),
            'create' => fn (array $args) => $this->generate(...$args),
            'verify' => fn (array $args) => $this->check(...$args),
            'match' => fn (array $args) => $this->check(...$args),
        ];

        return data_get($aliases, $key);
    }

    public function __call($method, $args)
    {
        $alias = $this->alias($method);
        if ($alias) {
            return call_user_func($alias, $args);
        }

        throw new Exception('Method does not exist');
    }
}

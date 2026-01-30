<?php

namespace Modules\General\Contracts\Seeder;

use Illuminate\Database\Seeder;

abstract class BaseSeeder extends Seeder
{
    public static bool $fake_seeding;

    public function run(): void
    {
        $this->init();

        if (isset(static::$fake_seeding) && static::$fake_seeding) {
            $this->fake();
        } else {
            $this->askForFakeData();
        }
    }

    protected function askForFakeData(): void
    {
        if (! isset(static::$fake_seeding)) {
            static::$fake_seeding = $this->command->confirm('Would you like to seed fake data?');
        }
    }

    abstract public function init(): void;

    abstract public function fake(): void;
}

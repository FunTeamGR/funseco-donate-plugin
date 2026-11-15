<?php

namespace Funseco\Donate\Tests;

use Funseco\Core\Testing\TestCase as CoreTestCase;
use Funseco\Chat\ChatCommandsServiceProvider;
use Funseco\Donate\DonateServiceProvider;

abstract class TestCase extends CoreTestCase
{
    /** Dependencies first, then this plugin (isolated runs load only these). */
    protected function plugins(): array
    {
        return [
            ChatCommandsServiceProvider::class,
            DonateServiceProvider::class,
        ];
    }
}

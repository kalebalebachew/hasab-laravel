<?php

namespace Hasab\Tests;

use Hasab\HasabServiceProvider as HasabHasabServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            HasabHasabServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Use a fake API key for testing
        $app['config']->set('hasab.api_key', 'test_key');
    }
}

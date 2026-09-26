<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Http;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    public function createApplication()
    {
        $app = parent::createApplication();
        $connection = $app['config']->get('database.default');
        $database = $app['config']->get("database.connections.$connection");
        if (! $app->environment('testing') || $app->configurationIsCached()
            || ($database['driver'] ?? null) !== 'sqlite'
            || ($database['database'] ?? null) !== ':memory:'
            || ! empty($database['url'])) {
            throw new RuntimeException('Las pruebas requieren configuración sin caché y SQLite en memoria.');
        }

        return $app;
    }

    protected function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests();
        $this->withoutVite();
    }
}

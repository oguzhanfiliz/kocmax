<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        // Sadece sqlite kullanılırken gerekli ayarları koşullu uygula
        if (config('database.default') === 'sqlite') {
            // CI ortamında dosya tabanlı sqlite kullanılıyorsa mevcut ayarı bozma
            $configuredDatabase = config('database.connections.sqlite.database');
            if (empty($configuredDatabase) || $configuredDatabase === ':memory:') {
                // Lokal hızlı testler için bellek içi veritabanı
                config()->set('database.connections.sqlite.database', ':memory:');
            }

            config()->set('database.connections.sqlite.foreign_key_constraints', true);
        }
    }
}

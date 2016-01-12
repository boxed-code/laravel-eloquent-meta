<?php

namespace BoxedCode\Tests\Eloquent\Meta\Support;

use BoxedCode\Eloquent\Meta\MetaItem;
use BoxedCode\Eloquent\Meta\MetaServiceProvider;
use BoxedCode\Tests\Eloquent\Meta\Support\Stubs\MetableModel;
use Orchestra\Testbench\TestCase;

class AbstractTestCase extends TestCase
{
    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        if (! class_exists('MetaMigration')) {
            static::makeMigration();
        } else {
            static::migrate();
        }

        parent::getEnvironmentSetUp($app);
    }

    protected function getPackageProviders($app)
    {
        return [MetaServiceProvider::class];
    }

    protected static function makeMigration($args = [])
    {
        $artisan = app()->make('Illuminate\Contracts\Console\Kernel');

        $artisan->call('make:meta-migration', $args);

        static::migrate();
    }

    protected static function migrate()
    {
        $artisan = app()->make('Illuminate\Contracts\Console\Kernel');

        // Models
        $artisan->call('migrate', [
            '--database' => 'testbench',
            '--realpath' => realpath(__DIR__.'/../Support/migrations'),
        ]);

        // Meta
        $artisan->call('migrate', [
            '--database' => 'testbench',
            '--realpath' => app()->databasePath().'/migrations',
        ]);
    }

    protected function createMetableStub($attributes = [])
    {
        $m = $this->getMetableStub();

        $attributes = array_merge(['key' => 'foo', 'value' => 'bar'], $attributes);

        $item = new MetaItem($attributes);

        $m->meta->add($item);

        $m->save();

        return $m::first();
    }

    protected function getMetableStub()
    {
        return new MetableModel;
    }

    protected function getMetaItemStub($attributes = [])
    {
        $attributes = array_merge(['key' => 'foo', 'value' => 'bar'], $attributes);

        return new MetaItem($attributes);
    }
}

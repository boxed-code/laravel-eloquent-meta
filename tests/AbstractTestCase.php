<?php

namespace BoxedCode\Tests\Eloquent\Meta;

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

        parent::getEnvironmentSetUp($app);
    }

    public function tearDown()
    {
        $files = glob($this->app->databasePath().'/migrations/*');

        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        parent::tearDown();
    }

    protected function migrate($args = [])
    {
        $this->assertEquals(0, $this->artisan('make:meta-migration', $args));

        $this->assertEquals(0, $this->artisan('migrate', [
            '--database' => 'testbench',
            '--realpath' => $this->app->databasePath().'/migrations',
        ]));
    }
}

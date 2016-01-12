<?php

namespace BoxedCode\Tests\Eloquent\Meta;

use BoxedCode\Eloquent\Meta\Types\Registry;
use BoxedCode\Tests\Eloquent\Meta\Support\AbstractTestCase;

class MetaServiceProviderTest extends AbstractTestCase
{
    public function testMigrationCommandExists()
    {
        $this->assertSame(0, $this->artisan('make:meta-migration', ['model_name' => 'sp_test']));
    }

    public function testTypeRegistryInjectable()
    {
        $this->assertInstanceOf(Registry::class, $this->app->make(Registry::class));
    }

    public function testDefaultTypesRegistered()
    {
        $registry = $this->app->make(Registry::class);

        $this->assertCount(5, $registry->registered());
    }
}

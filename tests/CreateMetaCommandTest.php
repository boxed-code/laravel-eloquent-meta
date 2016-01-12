<?php

namespace BoxedCode\Tests\Eloquent\Meta;

class CreateMetaCommandTest extends AbstractTestCase
{
    public function testTableCreation()
    {
        $this->assertTrue(\Schema::hasTable('meta'));

        $this->assertEquals([
            'id',
            'key',
            'tag',
            'model_id',
            'model_type',
            'type',
            'value',
            'created_at',
            'updated_at',
        ], \Schema::getColumnListing('meta'));
    }

    public function testCustomModelName()
    {
        static::makeMigration(['model_name' => 'test']);

        $this->assertTrue(\Schema::hasTable('test_meta'));
    }

    public function testCustomPath()
    {
        static::makeMigration(['--path' => 'storage']);

        $migration = head(glob($this->app->storagePath().'/*migration.php'));

        $this->assertFileExists($migration);
    }
}

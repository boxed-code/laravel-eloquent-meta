<?php

/*
 * This file is part of Mailable.
 *
 * (c) Oliver Green <oliver@mailable.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BoxedCode\Eloquent\Meta;

use Illuminate\Support\ServiceProvider;
use BoxedCode\Eloquent\Meta\Contracts\MetaItem as MetaItemContract;
use BoxedCode\Eloquent\Meta\Migrations\CreateMetaMigrationCommand;
use BoxedCode\Eloquent\Meta\Types\Registry as TypeRegistry;

class MetaServiceProvider extends ServiceProvider
{
    /**
     * Register application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerMigrationCommand();

        $this->registerTypeRegistry();
    }

    /**
     * Register the create meta migration console command.
     *
     * @return void
     */
    public function registerMigrationCommand()
    {
        $this->commands([CreateMetaMigrationCommand::class]);
    }

    /**
     * Register the meta item value type registry.
     *
     * @return void
     */
    public function registerTypeRegistry()
    {
        $this->app->singleton(TypeRegistry::class, function() {
            $registry = new TypeRegistry;

            $this->registerDefaultTypes($registry);

            return $registry;
        });
    }

    /**
     * Register the default item value types with the registry.
     *
     * @param  \BoxedCode\Eloquent\Meta\Types\Registry $registry
     * @return void
     */
    public function registerDefaultTypes(TypeRegistry $registry)
    {
        $types = [
            new \BoxedCode\Eloquent\Meta\Types\StringType,
            new \BoxedCode\Eloquent\Meta\Types\IntegerType,
            new \BoxedCode\Eloquent\Meta\Types\BooleanType,
            new \BoxedCode\Eloquent\Meta\Types\ArrayType,
        ];

        $registry->register($types);
    }
}
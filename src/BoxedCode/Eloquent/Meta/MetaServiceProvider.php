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

        $this->bindContracts();

        $this->registerTypeRegistry();
    }

    /**
     * Bind the meta contracts to concrete instances.
     * 
     * @return void
     */
    public function bindContracts()
    {
        $rebound = function ($app) {
            $instance = $app[MetaItemContract::class];
            $this->app['meta.model'] = get_class($instance);
        };

        $this->app->rebinding(MetaItemContract::class, $rebound);

        $this->app->bind(
            MetaItemContract::class, 
            \BoxedCode\Eloquent\Meta\MetaItem::class
        );

        $rebound($this->app);
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
        $this->app->singleton(TypeRegistry::class, function () {
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
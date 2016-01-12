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

trait FluentMeta
{
    use Metable;

    /**
     * Model table columns.
     *
     * @var array
     */
    public static $model_table_columns = [];

    /**
     * Get a models table columns.
     *
     * @param $class
     * @return mixed
     */
    protected function getClassColumns($class)
    {
        if (! isset(static::$model_table_columns[$class])) {
            static::$model_table_columns[$class] = $this->getConnection()
                ->getSchemaBuilder()
                ->getColumnListing($this->getTable());
        }

        return static::$model_table_columns[$class];
    }

    /**
     * Check whether a method, property or attribute
     * name exists on the model.
     *
     * @param $name
     * @return bool
     */
    protected function existsOnParent($name)
    {
        $columns = $this->getClassColumns(static::class);

        return property_exists($this, $name)
            || method_exists($this, $name)
            || in_array($name, $columns);
    }

    /**
     * Dynamically determine whether a meta item or model property isset.
     *
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        if ($this->existsOnParent($name)) {
            return parent::__isset($name);
        }

        return isset($this->meta->$name);
    }

    /**
     * Dynamically get a model property / attribute or meta item.
     *
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        if ($this->existsOnParent($name)) {
            return parent::__get($name);
        }

        if ($meta = $this->meta->$name) {
            return $this->meta->$name;
        }
    }

    /**
     * Dynamically set a meta item or model property / attribute.
     *
     * @param $name
     * @param $value
     * @return mixed
     */
    public function __set($name, $value)
    {
        if ($this->existsOnParent($name)) {
            return parent::__set($name, $value);
        }

        $this->meta->$name = $value;
    }

    /**
     * Dynamically unset a meta item or model property / attribute.
     *
     * @param $name
     */
    public function __unset($name)
    {
        if ($this->existsOnParent($name)) {
            unset($this->$name);

            return;
        }

        if ($meta = $this->meta->$name) {
            unset($this->meta->$name);
        }
    }
}

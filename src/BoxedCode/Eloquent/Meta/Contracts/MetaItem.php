<?php

/*
 * This file is part of Mailable.
 *
 * (c) Oliver Green <oliver@mailable.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BoxedCode\Eloquent\Meta\Contracts;

interface MetaItem
{
    /**
     * Get the value of the model's primary key.
     *
     * @return mixed
     */
    public function getKey();

    /**
     * Parent model relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function model();

    /**
     * Parse and get the value attribute.
     *
     * @return mixed
     */
    public function getValueAttribute();

    /**
     * Parse and set the value attribute.
     *
     * @param mixed $value
     * @param string $type
     */
    public function setValueAttribute($value, $type = null);

    /**
     * Get the value attribute by-passing any accessors.
     *
     * @return mixed
     */
    public function getRawValue();

    /**
     * Set the value attribute by-passing the mutators.
     *
     * @param mixed $value
     */
    public function setRawValue($value);

    /**
     * Get the string value of the meta item.
     *
     * @return string
     */
    public function __toString();

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array  $models
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function newCollection(array $models = []);

    /**
     * Register a deleted model event with the dispatcher.
     *
     * @param  \Closure|string  $callback
     * @param  int  $priority
     * @return void
     */
    public static function deleted($callback, $priority = 0);
}

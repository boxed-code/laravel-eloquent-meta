<?php

/*
 * This file is part of Mailable.
 *
 * (c) Oliver Green <oliver@mailable.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BoxedCode\Eloquent\Meta\Types;

use BoxedCode\Eloquent\Meta\Contracts\MetaItem as MetaItemContract;
use BoxedCode\Eloquent\Meta\Contracts\Type as TypeContract;

abstract class Type implements TypeContract
{
    /**
     * MetaItem model instance.
     *
     * @var \BoxedCode\Eloquent\Meta\Contracts\MetaItem
     */
    protected $model;

    /**
     * Constructor.
     *
     * @param \BoxedCode\Eloquent\Meta\Contracts\MetaItem|null $model
     */
    public function __construct(MetaItemContract $model = null)
    {
        $this->model = $model;
    }

    /**
     * Parse & return the meta item value.
     *
     * @return mixed
     */
    public function get()
    {
        return $this->model->getRawValue();
    }

    /**
     * Parse & set the meta item value.
     *
     * @param mixed $value
     */
    public function set($value)
    {
        $this->model->setRawValue($value);
    }

    /**
     * Assertain whether we can handle the
     * type of variable passed.
     *
     * @param  mixed  $value
     * @return bool
     */
    abstract public function isType($value);

    /**
     * Get the types class name.
     *
     * @return string
     */
    public function getClass()
    {
        return get_class($this);
    }

    /**
     * Output value to string.
     *
     * @return string
     */
    public function __toString()
    {
        return serialize($this->get());
    }
}

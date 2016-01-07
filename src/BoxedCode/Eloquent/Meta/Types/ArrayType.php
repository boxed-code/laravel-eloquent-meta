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

class ArrayType extends Type
{
    /**
     * Parse & return the meta item value.
     *
     * @return array
     */
    public function get()
    {
        return unserialize(parent::get());
    }

    /**
     * Parse & set the meta item value.
     *
     * @param array $value
     */
    public function set($value)
    {
        $array = (array) $value;

        parent::set(serialize($array));
    }

    /**
     * Assertain whether we can handle the
     * type of variable passed.
     *
     * @param  mixed  $value
     * @return bool
     */
    public function isType($value)
    {
        return is_array($value);
    }
}

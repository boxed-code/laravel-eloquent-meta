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

class StringType extends Type
{
    /**
     * Parse & set the meta item value.
     *
     * @param string $value
     */
    public function set($value)
    {
        parent::set((string) $value);
    }

    /**
     * Assertain whether we can handle the
     * type of variable passed.
     *
     * @param  mixed  $value
     * @return boolean
     */
    public function isType($value)
    {
        return is_string($value);
    }

    /**
     * Output value to string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->get();
    }
}
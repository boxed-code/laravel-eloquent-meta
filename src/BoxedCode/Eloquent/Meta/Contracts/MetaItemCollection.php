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

interface MetaItemCollection
{
    public function modelKeys();
    public function originalModelKeys();
    public function add($item);
    public function findItem($key, $tag = null);
    public function getDefaultTag();
    public function setDefaultTag($name);
    public function where($key, $value, $strict = true);
    public function get($key, $default = null);
    public function forget($keys);
    public function keys();
}

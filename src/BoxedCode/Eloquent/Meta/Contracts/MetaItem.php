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
    public function model();
    public function getValueAttribute();
    public function setValueAttribute($value, $type = null);
    public function setRawValue($value);
    public function getRawValue();
    public function __toString();
    public function newCollection(array $models = []);
}
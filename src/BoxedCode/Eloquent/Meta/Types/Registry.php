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

use InvalidArgumentException;
use ArrayAccess;
use BoxedCode\Eloquent\Meta\Contracts\Type as TypeContract;

class Registry implements ArrayAccess
{
    protected $registered = [];

    public function register($mixed)
    {
        if ($mixed instanceof TypeContract) {
            $this->registerClass(get_class($mixed), $mixed);

            return true;
        } elseif (is_array($mixed)) {
            foreach ($mixed as $type) {
                $this->register($type);
            }

            return true;
        }

        throw new InvalidArgumentException(
            'The register() input must either be a Type or array of Type.'
        );
    }

    protected function registerClass($class, $instance)
    {
        if (! isset($this->registered[$class])) {
            $this->registered[$class] = $instance;

            return true;
        }

        throw new InvalidArgumentException(
            "The type is already registered. [$class]"
        );
    }

    public function findTypeFor($value)
    {
        $type_str = gettype($value);

        $registered = array_reverse($this->registered);

        foreach ($registered as $type) {
            if ($type->isType($value)) {
                return $type;
            }
        }

        throw new InvalidArgumentException(
            "There is no type registered for the variable type. [$type_str]."
        );
    }

    public function registered()
    {
        return $this->registered;
    }

    public function offsetExists($offset)
    {
        return in_array($offset, $this->registered);
    }

    public function offsetGet($offset)
    {
        return $this->registered[$offset];
    }

    public function offsetSet($offset, $value)
    {
        return $this->registered[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->registered[$offset]);
    }
}

<?php

namespace BoxedCode\Tests\Eloquent\Meta\Support\Stubs;

use BoxedCode\Eloquent\Meta\Contracts\Type as TypeContract;
use BoxedCode\Eloquent\Meta\Types\Type;

class TypeStub extends Type implements TypeContract
{
    public function isType($value)
    {
        return true;
    }
}

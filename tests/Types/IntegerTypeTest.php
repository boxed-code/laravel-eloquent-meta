<?php

namespace BoxedCode\Tests\Eloquent\Meta\Types;

use BoxedCode\Eloquent\Meta\Types\IntegerType;
use BoxedCode\Tests\Eloquent\Meta\Support\AbstractTestCase;

class IntegerTypeTest extends AbstractTestCase
{
    public function testGetSet()
    {
        $t = new IntegerType($this->getMetaItemStub());

        $t->set('123');

        $this->assertSame(123, $t->get());
    }

    public function testIsType()
    {
        $t = new IntegerType($this->getMetaItemStub());

        $this->assertTrue($t->isType(123));
    }

    public function testToString()
    {
        $t = new IntegerType($this->getMetaItemStub());

        $t->set(123);

        $this->assertSame('123', $t->__toString());
    }
}

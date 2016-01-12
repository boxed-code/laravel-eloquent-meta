<?php

namespace BoxedCode\Tests\Eloquent\Meta\Types;

use BoxedCode\Eloquent\Meta\Types\DoubleType;
use BoxedCode\Tests\Eloquent\Meta\Support\AbstractTestCase;

class DoubleTypeTest extends AbstractTestCase
{
    public function testGetSet()
    {
        $t = new DoubleType($this->getMetaItemStub());

        $t->set('123.99');

        $this->assertSame(123.99, $t->get());
    }

    public function testIsType()
    {
        $t = new DoubleType($this->getMetaItemStub());

        $this->assertTrue($t->isType(123.99));
    }

    public function testToString()
    {
        $t = new DoubleType($this->getMetaItemStub());

        $t->set(123.99);

        $this->assertSame('123.99', $t->__toString());
    }
}

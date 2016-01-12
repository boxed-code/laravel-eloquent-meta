<?php

namespace BoxedCode\Tests\Eloquent\Meta\Types;

use BoxedCode\Eloquent\Meta\Types\BooleanType;
use BoxedCode\Tests\Eloquent\Meta\Support\AbstractTestCase;

class BooleanTypeTest extends AbstractTestCase
{
    public function testGetSet()
    {
        $t = new BooleanType($this->getMetaItemStub());

        $t->set('1');

        $this->assertTrue($t->get());

        $t->set('0');

        $this->assertFalse($t->get());
    }

    public function testIsType()
    {
        $t = new BooleanType($this->getMetaItemStub());

        $this->assertTrue($t->isType(true));
    }

    public function testToString()
    {
        $t = new BooleanType($this->getMetaItemStub());

        $t->set(true);

        $this->assertSame('1', $t->__toString());
    }
}

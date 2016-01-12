<?php

namespace BoxedCode\Tests\Eloquent\Meta\Types;

use BoxedCode\Eloquent\Meta\Types\ArrayType;
use BoxedCode\Tests\Eloquent\Meta\Support\AbstractTestCase;

class ArrayTypeTest extends AbstractTestCase
{
    public function testGetSet()
    {
        $t = new ArrayType($this->getMetaItemStub());

        $t->set(['foo' => 'bar']);

        $this->assertSame(['foo' => 'bar'], $t->get());
    }

    public function testIsType()
    {
        $t = new ArrayType($this->getMetaItemStub());

        $this->assertTrue($t->isType(['foo' => 'bar']));
    }
}

<?php

namespace BoxedCode\Tests\Eloquent\Meta\Types;

use BoxedCode\Eloquent\Meta\Types\StringType;
use BoxedCode\Tests\Eloquent\Meta\Support\AbstractTestCase;

class StringTypeTest extends AbstractTestCase
{
    public function testSet()
    {
        $t = new StringType($this->getMetaItemStub());

        $t->set(123);

        $this->assertSame('123', $t->get());
    }

    public function testIsType()
    {
        $t = new StringType($this->getMetaItemStub());

        $this->assertTrue($t->isType('string'));
    }

    public function testToString()
    {
        $t = new StringType($this->getMetaItemStub());

        $t->set(123);

        $this->assertSame('123', $t->__toString());
    }
}

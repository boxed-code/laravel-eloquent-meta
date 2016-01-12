<?php

namespace BoxedCode\Tests\Eloquent\Meta\Types;

use BoxedCode\Tests\Eloquent\Meta\Support\AbstractTestCase;
use BoxedCode\Tests\Eloquent\Meta\Support\Stubs\TypeStub;

class TypeTest extends AbstractTestCase
{
    public function testConstructorGetModel()
    {
        $m = $this->getMetaItemStub();

        $s = new TypeStub($m);

        $this->assertSame($m, $s->getModel());
    }

    public function testSetGet()
    {
        $s = new TypeStub($this->getMetaItemStub());

        $s->set('bar');

        $this->assertSame('bar', $s->get());
    }

    public function testGetClass()
    {
        $s = new TypeStub($this->getMetaItemStub());

        $this->assertSame(TypeStub::class, $s->getClass());
    }

    public function testToString()
    {
        $s = new TypeStub($this->getMetaItemStub());

        $this->assertSame(serialize('bar'), $s->__toString());
    }
}

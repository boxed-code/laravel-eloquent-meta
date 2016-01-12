<?php

namespace BoxedCode\Tests\Eloquent\Meta;

use BoxedCode\Eloquent\Meta\MetaItem;
use BoxedCode\Eloquent\Meta\MetaItemCollection;
use BoxedCode\Eloquent\Meta\Types\IntegerType;
use BoxedCode\Tests\Eloquent\Meta\Support\AbstractTestCase;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MetaItemTest extends AbstractTestCase
{
    public function testModel()
    {
        $m = $this->getMetaItemStub();

        $this->assertInstanceOf(MorphTo::class, $m->model());
    }

    public function testGetSetValueAttribute()
    {
        $m = $this->getMetaItemStub();

        $m->setValueAttribute('bar');

        $this->assertSame('bar', $m->getValueAttribute());
    }

    public function testSetValueAttributeWithType()
    {
        $m = $this->getMetaItemStub();

        $m->setValueAttribute('123', IntegerType::class);

        $this->assertSame('integer', gettype($m->getValueAttribute()));
    }

    public function testGetSetRawValue()
    {
        $m = $this->getMetaItemStub();

        $m->setValueAttribute('bar');

        $this->assertSame('bar', $m->getRawValue());
    }

    public function testNewCollection()
    {
        $m = $this->getMetaItemStub();

        $models = [
            new MetaItem(['key' => 'foo', 'value' => 'bar']),
            new MetaItem(['key' => 'baz', 'value' => 'qux']),
        ];

        $c = $m->newCollection($models);

        $this->assertInstanceOf(MetaItemCollection::class, $c);

        $this->assertSame($models[0], $c[0]);
        $this->assertSame($models[1], $c[1]);
    }

    public function testToString()
    {
        $m = $this->getMetaItemStub();

        $m->fill(['key' => 'foo', 'value' => 'bar']);

        $this->assertEquals('bar', $m);
    }
}

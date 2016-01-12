<?php

namespace BoxedCode\Tests\Eloquent\Meta;

use BoxedCode\Eloquent\Meta\MetaItemCollection;
use BoxedCode\Tests\Eloquent\Meta\Support\AbstractTestCase;
use BoxedCode\Tests\Eloquent\Meta\Support\Stubs\FluentMetaModel;
use DateTime;

class FluentMetaTest extends AbstractTestCase
{
    public function testSetGetParentAttribute()
    {
        $m = new FluentMetaModel();

        $date = DateTime::createFromFormat('Y-m-d H:i:s', '2015-01-01 23:59:59');

        $m->created_at = $date;

        $this->assertEquals($date, $m->created_at);

        $this->assertEquals($date, $m->getAttribute('created_at'));

        $this->assertTrue($m->incrementing);

        $this->assertInstanceOf(MetaItemCollection::class, $m->meta);
    }

    public function testSet()
    {
        $m = new FluentMetaModel();

        $m->foo = 'bar';

        $this->assertSame('bar', $m->meta->foo);
    }

    public function testGet()
    {
        $m = new FluentMetaModel();

        $m->meta->foo = 'bar';

        $this->assertSame('bar', $m->foo);
    }

    public function testIsset()
    {
        $m = new FluentMetaModel();

        $m->meta->foo = 'bar';

        $this->assertTrue(isset($m->foo));
    }

    public function testUnset()
    {
        $m = new FluentMetaModel();

        $m->meta->foo = 'bar';

        unset($m->foo);

        $this->assertFalse(isset($m->foo));
    }
}

<?php

namespace BoxedCode\Tests\Eloquent\Meta;

use BoxedCode\Eloquent\Meta\MetaItem;
use BoxedCode\Eloquent\Meta\MetaItemCollection;
use BoxedCode\Tests\Eloquent\Meta\Support\AbstractTestCase;
use InvalidArgumentException;

class MetaItemCollectionTest extends AbstractTestCase
{
    public function testConstructor()
    {
        $c = $this->getMetaItemCollectionStub();

        $this->assertInstanceOf(MetaItemCollection::class, $c);
    }

    public function testConstructorFillsOriginal()
    {
        $m = $this->getMetaItemStub();

        $c = $this->getMetaItemCollectionStub([$m]);

        $this->assertSame([$m->id], $c->originalModelKeys());
    }

    public function testConstructorObservesDeletions()
    {
        $m = with($this->createMetableStub())->meta[0];

        $c = $this->getMetaItemCollectionStub([$m]);

        $m->delete();

        $this->assertCount(0, $c);
    }

    public function testModelKeys()
    {
        $m = $this->getMetaItemStub();

        $c = $this->getMetaItemCollectionStub([$m]);

        $this->assertSame([$m->id], $c->modelKeys());
    }

    public function testAdd()
    {
        $c = $this->getMetaItemCollectionStub();

        $item = ['foo', 'bar', 'baz', 'qux'];

        $c->add($item);

        $this->assertSame($item, $c[0]);
    }

    public function testAddMetaItem()
    {
        $c = $this->getMetaItemCollectionStub();

        $m = $this->getMetaItemStub();

        $c->add($m);

        $this->assertSame($m, $c[0]);
    }

    public function testAddMetaItemObservesDeletions()
    {
        $c = $this->getMetaItemCollectionStub();

        $m = with($this->createMetableStub())->meta[0];

        $c->add($m);

        $m->delete();

        $this->assertCount(0, $c);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testAddMetaItemsThrowsOnDuplicate()
    {
        $c = $this->getMetaItemCollectionStub();

        $m = with($this->createMetableStub())->meta[0];

        $c->add($m);

        $c->add($m);
    }

    public function testFindItem()
    {
        $m = $this->getMetaItemStub();

        $c = $this->getMetaItemCollectionStub([$m]);

        $this->assertSame(0, $c->findItem('foo'));
    }

    public function testFindItemMiss()
    {
        $m = $this->getMetaItemStub();

        $c = $this->getMetaItemCollectionStub([$m]);

        $this->assertNull($c->findItem('foo2'));
    }

    public function testFindItemCustomTag()
    {
        $m = $this->getMetaItemStub();

        $mc = $this->getMetaItemStub(['key' => 'foo', 'value' => 'qux', 'tag' => 'phpunit']);

        $c = $this->getMetaItemCollectionStub([$m, $mc]);

        $this->assertSame(1, $c->findItem('foo', 'phpunit'));
    }

    public function testGetSetMetaItemClass()
    {
        $m = $this->getMetaItemCollectionStub();

        $m->setMetaItemClass('FooClass');

        $this->assertSame('FooClass', $m->getMetaItemClass());

        $m->setMetaItemClass(MetaItem::class);
    }

    public function testGetSetDefaultTag()
    {
        $m = $this->getMetaItemCollectionStub();

        $m->setDefaultTag('phpunit');

        $this->assertSame('phpunit', $m->getDefaultTag());
    }

    public function testCall()
    {
        $m = $this->getMetaItemStub();

        $c = $this->getMetaItemCollectionStub([$m]);

        $this->assertSame($m, $c->whereKey($m->key)[0]);
    }

    public function testCallMiss()
    {
        $m = $this->getMetaItemStub();

        $c = $this->getMetaItemCollectionStub([$m]);

        $this->assertCount(0, $c->whereKey('foo2'));
    }

    public function testIsset()
    {
        $m = $this->getMetaItemStub();

        $c = $this->getMetaItemCollectionStub([$m]);

        $this->assertTrue(isset($c->foo));
    }

    public function testUnset()
    {
        $m = $this->getMetaItemStub();

        $c = $this->getMetaItemCollectionStub([$m]);

        unset($c->foo);

        $this->assertFalse(isset($c->foo));
    }

    public function testUnsetMiss()
    {
        $m = $this->getMetaItemStub();

        $c = $this->getMetaItemCollectionStub([$m]);

        unset($c->foo2);

        $this->assertFalse(isset($c->foo2));
    }

    public function testGetKey()
    {
        $m = $this->getMetaItemStub();

        $c = $this->getMetaItemCollectionStub([$m]);

        $this->assertSame('bar', $c->foo);
    }

    public function testGetTag()
    {
        $m = $this->getMetaItemStub();

        $c = $this->getMetaItemCollectionStub([$m]);

        $this->assertCount(1, $c->default);
    }

    public function testGetMiss()
    {
        $m = $this->getMetaItemStub();

        $c = $this->getMetaItemCollectionStub([$m]);

        $this->assertNull($c->not_set);
    }

    public function testSetNew()
    {
        $m = $this->getMetaItemStub();

        $c = $this->getMetaItemCollectionStub([$m]);

        $this->assertNull($c->baz);

        $c->baz = 'qux';

        $this->assertSame('qux', $c->baz);
    }

    public function testSetExisting()
    {
        $m = $this->getMetaItemStub();

        $c = $this->getMetaItemCollectionStub([$m]);

        $this->assertSame('bar', $c->foo);

        $c->foo = 'qux';

        $this->assertSame('qux', $c->foo);
    }

    protected function getMetaItemCollectionStub($models = [])
    {
        return new MetaItemCollection($models);
    }
}

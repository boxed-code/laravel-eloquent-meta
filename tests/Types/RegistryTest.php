<?php

namespace BoxedCode\Tests\Eloquent\Meta\Types;

use BoxedCode\Eloquent\Meta\Types\Registry;
use BoxedCode\Eloquent\Meta\Types\StringType;
use BoxedCode\Tests\Eloquent\Meta\Support\AbstractTestCase;
use BoxedCode\Tests\Eloquent\Meta\Support\Stubs\TypeStub;
use InvalidArgumentException;
use stdClass;

class RegistryTest extends AbstractTestCase
{
    public function testRegisterTypeContract()
    {
        $r = $this->getRegistry();

        $c = new TypeStub($this->getMetaItemStub());

        $r->register($c);

        $this->assertSame($c, $r[TypeStub::class]);
    }

    public function testRegisterArrayOfTypeContracts()
    {
        $r = $this->getRegistry();

        $st = new TypeStub($this->getMetaItemStub());

        $it = new StringType($this->getMetaItemStub());

        $r->register([$st, $it]);

        $this->assertSame($st, $r[TypeStub::class]);

        $this->assertSame($it, $r[StringType::class]);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testRegisterInvalidTypeThrows()
    {
        $r = $this->getRegistry();

        $r->register(new stdClass);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testRegisterDuplicateTypeThrows()
    {
        $r = $this->getRegistry();

        $st = new TypeStub($this->getMetaItemStub());

        $r->register([$st, $st]);
    }

    public function testFindType()
    {
        $r = $this->getRegistry();

        $st = new TypeStub($this->getMetaItemStub());

        $r->register($st);

        $this->assertSame($st, $r->findTypeFor('a string value'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testFindTypeMissThrows()
    {
        $r = $this->getRegistry();

        $r->findTypeFor('a string value');
    }


    public function testRegistered()
    {
        $r = $this->getRegistry();

        $st = new TypeStub($this->getMetaItemStub());

        $r->register($st);

        $this->assertSame([TypeStub::class => $st], $r->registered());
    }

    public function testOffsetSetExists()
    {
        $r = $this->getRegistry();

        $st = new TypeStub($this->getMetaItemStub());

        $r->offsetSet(TypeStub::class, $st);

        $this->assertTrue($r->offsetExists(TypeStub::class));
    }

    public function testOffsetUnset()
    {
        $r = $this->getRegistry();

        $st = new TypeStub;

        $r->register($st);

        $r->offsetUnset(TypeStub::class);

        $this->assertFalse(isset($r[TypeStub::class]));
    }

    protected function getRegistry()
    {
        return new Registry;
    }
}

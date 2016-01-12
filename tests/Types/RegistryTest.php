<?php

namespace BoxedCode\Tests\Eloquent\Meta\Types;

use BoxedCode\Eloquent\Meta\Types\IntegerType;
use BoxedCode\Eloquent\Meta\Types\Registry;
use BoxedCode\Eloquent\Meta\Types\StringType;
use BoxedCode\Tests\Eloquent\Meta\Support\AbstractTestCase;
use InvalidArgumentException;
use stdClass;

class RegistryTest extends AbstractTestCase
{
    public function testRegisterTypeContract()
    {
        $r = $this->getRegistry();

        $c = new StringType($this->getMetaItemStub());

        $r->register($c);

        $this->assertSame($c, $r[StringType::class]);
    }

    public function testRegisterArrayOfTypeContracts()
    {
        $r = $this->getRegistry();

        $st = new StringType($this->getMetaItemStub());

        $it = new IntegerType($this->getMetaItemStub());

        $r->register([$st, $it]);

        $this->assertSame($st, $r[StringType::class]);

        $this->assertSame($it, $r[IntegerType::class]);
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

        $st = new StringType($this->getMetaItemStub());

        $r->register([$st, $st]);
    }

    public function testFindType()
    {
        $r = $this->getRegistry();

        $st = new StringType($this->getMetaItemStub());

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

        $st = new StringType($this->getMetaItemStub());

        $r->register($st);

        $this->assertSame([StringType::class => $st], $r->registered());
    }

    public function testOffsetSetExists()
    {
        $r = $this->getRegistry();

        $st = new StringType($this->getMetaItemStub());

        $r->offsetSet(StringType::class, $st);

        $this->assertTrue($r->offsetExists(StringType::class));
    }

    public function testOffsetUnset()
    {
        $r = $this->getRegistry();

        $st = new StringType;

        $r->register($st);

        $r->offsetUnset(StringType::class);

        $this->assertFalse(isset($r[StringType::class]));
    }

    protected function getRegistry()
    {
        return new Registry;
    }
}

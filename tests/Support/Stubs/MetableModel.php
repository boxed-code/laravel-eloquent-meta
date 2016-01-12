<?php

namespace BoxedCode\Tests\Eloquent\Meta\Support\Stubs;

use BoxedCode\Eloquent\Meta\Metable;
use Illuminate\Database\Eloquent\Model;

class MetableModel extends Model
{
    use Metable;

    protected $table = 'models';

    public function setProperty($name, $value)
    {
        $this->$name = $value;
    }
}

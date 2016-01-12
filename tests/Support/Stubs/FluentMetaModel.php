<?php

namespace BoxedCode\Tests\Eloquent\Meta\Support\Stubs;

use BoxedCode\Eloquent\Meta\FluentMeta;
use Illuminate\Database\Eloquent\Model;

class FluentMetaModel extends Model
{
    use FluentMeta;

    protected $table = 'models';

    public function setProperty($name, $value)
    {
        $this->$name = $value;
    }
}

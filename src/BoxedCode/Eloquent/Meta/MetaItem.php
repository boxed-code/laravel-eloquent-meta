<?php

/*
 * This file is part of Mailable.
 *
 * (c) Oliver Green <oliver@mailable.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BoxedCode\Eloquent\Meta;

use Illuminate\Database\Eloquent\Model;
use BoxedCode\Eloquent\Meta\Contracts\MetaItem as MetaItemContract;
use BoxedCode\Eloquent\Meta\Types\Registry;

class MetaItem extends Model implements MetaItemContract
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['key', 'tag', 'value'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'meta';

    /**
     * Parent model relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function model()
    {
        return $this->morphTo();
    }

    /**
     * Get the value type registry.
     *
     * @return \BoxedCode\Eloquent\Meta\Types\Registry
     */
    protected function getTypeRegistry()
    {
        return app(Registry::class);
    }

    /**
     * Get the models value type class name.
     *
     * @return string
     */
    protected function getTypeClass()
    {
        return $this->getTypeRegistry()[$this->type];
    }

    /**
     * Get the models value type instance.
     *
     * @return \BoxedCode\Eloquent\Meta\Contracts\Type
     */
    protected function getTypeInstance()
    {
        $class = $this->getTypeClass();

        return new $class($this);
    }

    /**
     * Parse and get the value attribute.
     *
     * @return mixed
     */
    public function getValueAttribute()
    {
        return $this->getTypeInstance()->get();
    }

    /**
     * Parse and set the value attribute.
     *
     * @param mixed $value
     * @param string $type
     */
    public function setValueAttribute($value, $type = null)
    {
        if (is_null($type) && ! isset($this->attributes['type'])) {
            $registry = $this->getTypeRegistry();
            $this->attributes['type'] = $registry->findTypeFor($value)->getClass();
        } elseif (isset($type)) {
            $this->attributes['type'] = $type;
        }

        return $this->getTypeInstance()->set($value);
    }

    /**
     * Get the value attribute by-passing any accessors.
     *
     * @return mixed
     */
    public function getRawValue()
    {
        return $this->attributes['value'];
    }

    /**
     * Set the value attribute by-passing the mutators.
     *
     * @param mixed $value
     */
    public function setRawValue($value)
    {
        $this->attributes['value'] = $value;
    }

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array  $models
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function newCollection(array $models = [])
    {
        $collection = new MetaItemCollection($models);

        $collection::setMetaItemClass($this);

        return $collection;
    }

    /**
     * Get the string value of the meta item.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getTypeInstance()->__toString();
    }
}

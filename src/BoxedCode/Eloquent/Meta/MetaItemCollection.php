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

use Illuminate\Database\Eloquent\Collection as CollectionBase;
use BoxedCode\Eloquent\Meta\Contracts\MetaItem as ItemContract;

class MetaItemCollection extends CollectionBase
{
    protected static $item_class;

    protected $default_tag = 'default';

    protected $original_model_keys = [];

    public function __construct($items = [])
    {
        $this->items = is_array($items) ? $items : $this->getArrayableItems($items);

        $this->original_model_keys = $this->modelKeys();

        $this->observeDeletions($this->items);
    }

    public function getOriginalModelKeys()
    {
        return $this->original_model_keys;
    }

    public function modelKeys()
    {
        $keys = [];

        foreach ($this->items as $item) {
            if ($item instanceof ItemContract) {
                $keys[] = $item->getKey();
            }
        }

        return $keys;
    }

    public function add($item)
    {
        if ($item instanceof ItemContract) {

            if (! is_null($this->find($item->key, $item->tag))) {
                throw new \InvalidArgumentException("Unique key / tag index constraint failed. [$item->key/$item->tag]");
            }

            $this->observeDeletions([$item]);
        }

        $this->items[] = $item;

        return $this;
    }

    public function find($key, $tag = null)
    {
        $collection = $this->whereKey($key);

        if (! is_null($tag)) {
            $collection = $collection->whereTag($tag);
        }

        if ($collection->count() > 0) {
            return $collection->keys()->first();
        }
    }

    protected function observeDeletions(array $items)
    {
        foreach ($items as $item) {
            if ($item instanceof ItemContract) {
                $this->observeDeletion($item);
            }
        }
    }

    protected function observeDeletion(ItemContract $item)
    {
        $item::deleted(function ($model) {
            $key = $this->find($model->key, $model->tag);

            if (! is_null($key)) {
                $this->forget($key);
            }
        });
    }

    public static function getMetaItemClass()
    {
        return static::$item_class;
    }

    public static function setMetaItemClass($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        static::$item_class = $class;
    }

    public function getDefaultTag()
    {
        return $this->default_tag;
    }

    public function setDefaultTag($name)
    {
        $this->default_tag = $name;

        return $this;
    }

    public function __call($name, $arguments)
    {
        if (starts_with($name, 'where') && 1 === count($arguments)) {
            $key = snake_case(substr($name, 5));
            return $this->where($key, $arguments[0]);
        }
    }

    public function __isset($name)
    {
        return ! is_null($this->find($name, $this->default_tag));
    }

    public function __unset($name)
    {
        $key = $this->find($name, $this->default_tag);

        if (! is_null($key)) {
            $this->forget($key);
        }
    }

    public function __get($name)
    {
        $key = $this->find($name, $this->default_tag);

        if (! is_null($key)) {
            return $this->get($key)->value;
        }

        $tag = $this->whereTag($name);

        if ($tag->count() > 0) {
            return $tag->setDefaultTag($name);
        }
    }

    public function __set($name, $value)
    {
        $key = $this->find($name, $this->default_tag);

        if (! is_null($key)) {
            $this->get($key)->value = $value;
        }
        else {
            $attr = [
                'key'   => $name,
                'value' => $value,
                'tag'   => $this->default_tag
            ];

            $class = static::$item_class;

            $this->add(new $class($attr));
        }
    }
}
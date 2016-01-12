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
use BoxedCode\Eloquent\Meta\Contracts\MetaItem as MetaItemContract;
use BoxedCode\Eloquent\Meta\Contracts\MetaItemCollection as CollectionContract;
use InvalidArgumentException;

class MetaItemCollection extends CollectionBase implements CollectionContract
{
    /**
     * Fully qualified class name to use when creating new items via magic methods.
     *
     * @var string
     */
    protected static $item_class;

    /**
     * The default tag name to use when using magic methods.
     *
     * @var string
     */
    protected $default_tag = 'default';

    /**
     * Keys of the models that the collection was constructed with.
     *
     * @var array
     */
    protected $original_model_keys = [];

    /**
     * MetaItemCollection constructor.
     *
     * @param array $items
     */
    public function __construct($items = [])
    {
        parent::__construct($items);

        $this->original_model_keys = $this->modelKeys();

        $this->setTags($this->items);

        $this->observeDeletions($this->items);
    }

    /**
     * Sets the default tag on any 'tag-less' items.
     *
     * @param array $items
     */
    protected function setTags(array $items)
    {
        array_map(function ($item) {
            if ($item instanceof MetaItemContract) {
                $item->tag = $item->tag ?: $this->default_tag;
            }

            return $item;
        }, $items);
    }

    /**
     * Get the array of primary keys.
     *
     * @return array
     */
    public function modelKeys()
    {
        $keys = [];

        foreach ($this->items as $item) {
            if ($item instanceof MetaItemContract) {
                $keys[] = $item->getKey();
            }
        }

        return $keys;
    }

    /**
     * Get the array of primary keys the collection was constructed with.
     *
     * @return array
     */
    public function originalModelKeys()
    {
        return $this->original_model_keys;
    }

    /**
     * Add an item to the collection.
     *
     * @param mixed $item
     * @return $this
     * @throws InvalidArgumentException
     */
    public function add($item)
    {
        if ($item instanceof MetaItemContract) {
            if (! is_null($this->findItem($item->key, $item->tag))) {
                $tag = $item->tag ?: $this->default_tag;

                $key = $item->key;

                throw new InvalidArgumentException("Unique key / tag constraint failed. [$key/$tag]");
            }

            $this->observeDeletions([$item]);
        }

        $this->items[] = $item;

        return $this;
    }

    /**
     * Get the collection key form an item key and tag.
     *
     * @param mixed $key
     * @param null $tag
     * @return mixed
     */
    public function findItem($key, $tag = null)
    {
        $collection = $this->whereKey($key);

        if (! is_null($tag)) {
            $collection = $collection->whereTag($tag);
        }

        if ($collection->count() > 0) {
            return $collection->keys()->first();
        }
    }

    /**
     * Set deletion listeners on an array of items.
     *
     * @param array $items
     */
    protected function observeDeletions(array $items)
    {
        foreach ($items as $item) {
            if ($item instanceof MetaItemContract) {
                $this->observeDeletion($item);
            }
        }
    }

    /**
     * Set a deletion listener on an item.
     *
     * @param \BoxedCode\Eloquent\Meta\Contracts\MetaItem $item
     */
    protected function observeDeletion(MetaItemContract $item)
    {
        $item::deleted(function ($model) {
            $key = $this->findItem($model->key, $model->tag);

            if (! is_null($key)) {
                $this->forget($key);
            }
        });
    }

    /**
     * Get the class name that will be used to construct new
     * items via the magic methods.
     *
     * @return string
     */
    public static function getMetaItemClass()
    {
        return static::$item_class;
    }

    /**
     * Set the class name that will be used to construct new
     * items via the magic methods.
     *
     * @param $class
     */
    public static function setMetaItemClass($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        static::$item_class = $class;
    }

    /**
     * Get the default tag name that will be used to construct new
     * items via the magic methods.
     *
     * @return string
     */
    public function getDefaultTag()
    {
        return $this->default_tag;
    }

    /**
     * Set the default tag name that will be used to construct new
     * items via the magic methods.
     *
     * @param $name
     * @return $this
     */
    public function setDefaultTag($name)
    {
        $this->default_tag = $name;

        return $this;
    }

    /**
     * Resolve calls to filter the collection by item attributes.
     *
     * @param string $name
     * @param array $arguments
     * @return static
     */
    public function __call($name, $arguments)
    {
        if (starts_with($name, 'where') && 1 === count($arguments)) {
            $key = snake_case(substr($name, 5));

            return $this->where($key, $arguments[0]);
        }
    }

    /**
     * Resolve calls to check whether an item with a specific key name exists.
     *
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return ! is_null($this->findItem($name, $this->default_tag));
    }

    /**
     * Resolve calls to unset an item with a specific key name.
     *
     * @param $name
     */
    public function __unset($name)
    {
        $key = $this->findItem($name, $this->default_tag);

        if (! is_null($key)) {
            $this->forget($key);
        }
    }

    /**
     * Resolve calls to get an item with a specific key name or a
     * collection of items with a specific tag name.
     *
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        $key = $this->findItem($name, $this->default_tag);

        if (! is_null($key)) {
            return $this->get($key)->value;
        }

        $tag = $this->where('tag', $name);

        if ($tag->count() > 0) {
            return $tag->setDefaultTag($name);
        }
    }

    /**
     * Resolve calls to set a new item to the collection or
     * update an existing key.
     *
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $key = $this->findItem($name, $this->default_tag);

        if (! is_null($key)) {
            $this->get($key)->value = $value;
        } else {
            $attr = [
                'key'   => $name,
                'value' => $value,
                'tag'   => $this->default_tag,
            ];

            $class = static::$item_class;

            $this->add(new $class($attr));
        }
    }
}

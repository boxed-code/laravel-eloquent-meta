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
    /**
     * Class name of collection items.
     * Used to create new items via the magic method.
     *
     * @var string
     */
    protected $item_class = ItemContract::class;

    protected $original = [];

    protected $default_tag = 'default';

    public function setDefaultTag($tag)
    {
        $this->default_tag = $tag;

        return $this;
    }

    public function getDefaultTag()
    {
        return $this->default_tag;
    }

    public function __construct($items = [])
    {
        $this->items = is_array($items) ? $items : $this->getArrayableItems($items);

        $this->original = $items;

        $this->observeDeletions($this->items);
    }

    public function getOriginal()
    {
        return new static($this->original);
    }

    protected function observeDeletions(array $items)
    {
        foreach ($items as $item) {
            
            if ($item instanceof ItemContract) {

                $item::deleted(function ($model) {
                    $key = $this->getKeyByName($model->key);

                    if ($key) {
                        $this->forget($this->items[$key]);
                    }
                });

            }
        }
    }

    /**
     * Get the class name of collection items.
     *
     * @return string
     */
    public function getItemClass()
    {
        return $this->item_class;
    }

    /**
     * Add an item to the collection.
     *
     * @param  mixed  $item
     * @return $this
     */
    public function add($item)
    {
        if (is_array($item)) {
            $instance = app($this->getItemClass());
            $instance->fill($item);
            $item = $instance;
        }

        $this->observeDeletions([$item]);

        if (! $item->tag) {
            $item->tag = $this->default_tag;
        }

        $this->items[] = $item;

        return $this;
    }

    /**
     * Get an items collection key by name.
     *
     * @param  string $name
     * @param  string $tag
     * @return \BoxedCode\Eloquent\Meta\Contracts\MetaItem
     */
    public function getKeyByName($name, $tag = null)
    {
        if (! $tag) {
            $tag = $this->default_tag;
        }

        $search = function ($item, $key) use ($name, $tag) {
            if ($name === $item->key && $tag === $item->tag) {
                return true;
            }

            return false;
        };

        return $this->search($search);
    }

    /**
     * Get an item by name.
     *
     * @param  string $name
     * @param  string $tag
     * @return \BoxedCode\Eloquent\Meta\Contracts\MetaItem
     */
    public function getByKey($name, $tag = null)
    {
        $key = $this->getKeyByName($name, $tag);

        if (false !== $key) {
            return $this[$key];
        }
    }

    public function forgetByKey($name, $tag = null)
    {
        $key = $this->getKeyByName($name, $tag);

        if (false !== $key) {
            $this->forget($key);
        }
    }

    public function __call($name, $arguments)
    {
        if (starts_with($name, 'where') && 1 === count($arguments)) {
            $key = snake_case(substr($name, 5));
            return $this->where($key, $arguments[0]);
        }
    }

    /**
     * Getter.
     *
     * @param  string $name
     * @return \BoxedCode\Eloquent\Meta\Contracts\MetaItem|null
     */
    public function __get($name)
    {
        if ($item = $this->getByKey($name)) {
            return $item->value;
        }

        elseif (($tag = $this->where('tag', $name)) && $tag->count() > 0) {
            return $tag->setDefaultTag($name);
        }
    }

    /**
     * Setter.
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        if ($item = $this->getByKey($name)) {
            $item->value = $value;
        } else {
            $this->add(['key'   => $name, 'value' => $value]);
        }
    }
}
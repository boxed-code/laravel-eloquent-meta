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

trait Metable
{
    /**
     * Boot the trait.
     *
     * @return void
     */
    public static function bootMetable()
    {
        static::observeSaveAndCascade();

        static::observeDeleteAndCascade();
    }

    /**
     * Get the primary key for the model.
     *
     * @return string
     */
    abstract public function getKeyName();

    /**
     * Meta item relationship.
     *
     * @return \BoxedCode\Eloquent\Meta\HasMeta
     */
    public function meta()
    {
        return $this->hasMeta($this->getMetaItemClassName(), 'model');
    }

    /**
     * Determine if the model or given attribute(s) have been modified.
     *
     * @param  array|string|null  $attributes
     * @return bool
     */
    public function isDirty($attributes = null)
    {
        if (parent::isDirty($attributes)) {
            return true;
        }

        else {
            foreach ($this->meta as $item) {
                if ($item->isDirty($attributes)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get the class name of meta relationship items.
     *
     * @return string
     */
    public function getMetaItemClassName()
    {
        if (isset($this->metaItemClassname)) {
            $class = $this->metaItemClassname;
        } else {
            $class = MetaItem::class;
        }

        return $class;
    }

    /**
     * Get an instance of the meta relationship item class.
     *
     * @param array $attr
     * @return MetaItem
     */
    public function getMetaItemInstance($attr = [])
    {
        $class = $this->getMetaItemClassName();

        return new $class($attr);
    }

    /**
     * Get the polymorphic relationship columns.
     *
     * @param  string  $name
     * @param  string  $type
     * @param  string  $id
     * @return array
     */
    abstract protected function getMorphs($name, $type, $id);

    /**
     * Define the polymorphic one-to-many relationship with the meta data.
     *
     * @param  string  $related
     * @param  string  $name
     * @param  string  $type
     * @param  string  $id
     * @param  string  $localKey
     * @return \BoxedCode\Eloquent\Meta\HasMeta
     */
    public function hasMeta($related, $name, $type = null, $id = null, $localKey = null)
    {
        $instance = new $related;

        // Here we will gather up the morph type and ID for the relationship so that we
        // can properly query the intermediate table of a relation. Finally, we will
        // get the table and create the relationship instances for the developers.
        list($type, $id) = $this->getMorphs($name, $type, $id);

        $table = $instance->getTable();

        $localKey = $localKey ?: $this->getKeyName();

        return new HasMeta($instance->newQuery(), $this, $table.'.'.$type, $table.'.'.$id, $localKey);
    }

    /**
     * Observes the model and saves dirty meta data on save.
     *
     * @return void
     */
    public static function observeSaveAndCascade()
    {
        $onSave = function($model) {

            /*
             * Remove any keys not present in the collection
             */
            $key = $model->getMetaItemInstance()->getKeyName();

            $to_remove = array_diff($model->meta->originalModelKeys(), $model->meta->modelKeys());

            $model->meta()->whereIn($key, $to_remove)->delete();

            /*
             * Save dirty meta items
             */
            foreach ($model->meta as $meta) {

                if ($meta->isDirty()) {
                    if ($meta->exists) {
                        $meta->save();
                    } else {
                        $model->meta()->save($meta);
                    }
                }

            }

        };

        static::saved($onSave);
    }

    /**
     * Observes the model and deletes meta entries on delete.
     *
     * @return void
     */
    public static function observeDeleteAndCascade()
    {
        $onDelete = function($model) {
            foreach ($model->meta as $meta) {
                $meta->delete();
            }
        };

        static::deleted($onDelete);
    }

}

<?php

namespace rms\db;

use rms\Util;

/**
 * Entity Collection abstraction
 * Contents can be iterated or accessed in array-style
 * @package rms\db
 */
class EntityCollection implements \Iterator, \ArrayAccess
{
    /** @var array */
    private $items = [];

    /**
     * Adds entity to collection
     * @param Entity $entity
     */
    public function add(Entity $entity)
    {
        $this->items[] = $entity;
    }

    /**
     * Removes entity from collection
     * @param $key string
     */
    public function delete($key)
    {
        unset($this->items[$key]);
    }

    /**
     * Returns collection size
     * @return int
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * Returns internal storage in associative array format
     * @return array
     */
    public function asArray()
    {
        $result = [];
        foreach ($this->items as $id => $item) {
            $result[$id] = $item->asArray();
        }
        return $result;
    }

    /**
     * Saves all entities in collection
     */
    public function save()
    {
        /** @var Entity $entity */
        foreach ($this->items as $entity) {
            $entity->save();
        }
    }

    // Iterator interface

    public function current()
    {
        return current($this->items);
    }

    public function next()
    {
        next($this->items);
    }

    public function key()
    {
        /** @var Entity $currentItem */
        $currentItem = current($this->items);
        return $currentItem->getId();
    }

    public function valid()
    {
        return current($this->items) !== false;
    }

    public function rewind()
    {
        reset($this->items);
    }

    // ArrayAccess interface

    public function offsetExists($offset)
    {
        return isset($this->items[$offset]);
    }

    public function offsetGet($offset)
    {
        return Util::arrayGet($this->items, $offset);
    }

    public function offsetSet($offset, $value)
    {
        if ($value instanceof Entity) {
            $this->items[] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }
}
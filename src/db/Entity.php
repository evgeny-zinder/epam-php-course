<?php

namespace rms\db;

use rms\Util;
use rms\helpers\NameTranslateHelper;

/**
 * Class Entity
 * ActiveRecord-based tool for DB data presentation
 * @package rms\db
 */
class Entity
{
    /** @var string */
    private $tableName;

    /** @var integer|null */
    private $id = null;

    /** @var array */
    private $data = [];

    /** @var SqliteWrapper */
    private $sqliteWrapper;

    /** @var NameTranslateHelper */
    private $translateHelper;

    /**
     * Entity constructor.
     * @param SqliteWrapper $sqliteWrapper
     * @param $tableName
     */
    public function __construct(SqliteWrapper $sqliteWrapper, $tableName = null)
    {
        $this->sqliteWrapper = $sqliteWrapper;
        $this->tableName = $tableName;
        $this->translateHelper = new NameTranslateHelper();
    }

    /**
     * Loads entity data from DB (by ID) or from array
     * @param $data int|array
     */
    public function load($data)
    {
        if (is_array($data)) {
            $this->data = $data;
        } else {
            if (null === $this->tableName) {
                return;
            }
            $this->data = $this->sqliteWrapper->load($this->tableName, $data);
        }
        if (count($this->data) > 0) {
            $this->id = Util::arrayGet($this->data, 'id');
            unset($this->data['id']);
        }
    }

    /**
     * Saves entity to DB (either by inserting new row or updating exising one)
     */
    public function save()
    {
        if (null === $this->tableName) {
            return;
        }

        if (null === $this->id) {
            $this->id = $this->sqliteWrapper->insert(
                $this->tableName,
                $this->data
            );
        } else {
            $this->sqliteWrapper->update(
                $this->tableName,
                $this->id,
                $this->data
            );
        }
    }

    /**
     * Returns entity field
     * @param $key
     * @return mixed
     */
    public function __get($key)
    {
        return Util::arrayGet($this->data, $key);
    }

    /**
     * Sets entity field
     * @param $key
     * @param $value
     */
    public function __set($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * Checks if field exists
     * @param $key
     * @return bool
     */
    public function __isset($key)
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * Get/setter methods magic
     * @param $function
     * @param $parameters
     * @return mixed
     */
    public function __call($function, $parameters)
    {
        $type = strtolower(substr($function, 0, 3));
        $fieldName = $this->translateHelper->functionToField($function);
        if ('get' === $type) {
            return $this->__get($fieldName);
        } elseif ('set' === $type) {
            $this->__set($fieldName, current($parameters));
            return $this;
        }
        return null;
    }

    /**
     * Returns id
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Checks if internal storage is empty (=== data is not loaded)
     * @return bool
     */
    public function isEmpty()
    {
        return null === $this->getId();
    }

    /**
     * Returns internal storage as associative array
     * @return array
     */
    public function asArray()
    {
        $data = $this->data;
        if (null !== $this->getId()) {
            $data = array_merge(['id' => $this->getId()], $data);
        }
        return $data;
    }
}

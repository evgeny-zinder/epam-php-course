<?php

namespace rms\db;

use rms\helpers\NameTranslateHelper;
use rms\helpers\SqlConditionHelper;

/**
 * Class Repository
 * Holds basic queries for entity CRUD operations
 * @package rms\db
 */
class Repository
{
    /** @var SqliteWrapper */
    protected $sqliteWrapper;

    /** @var string */
    protected $entityName;

    /** @var string */
    protected $tableName;

    /** @var SqlConditionHelper */
    protected $conditionBuilder;

    /** @var NameTranslateHelper */
    protected $nameTranslator;

    /**
     * Repository constructor.
     * @param SqliteWrapper $sqliteWrapper
     * @param string $entityName
     * @param string|null $tableName
     */
    public function __construct(SqliteWrapper $sqliteWrapper, $entityName, $tableName = null)
    {
        $this->sqliteWrapper = $sqliteWrapper;
        $this->entityName = $entityName;
        $this->tableName = (null !== $tableName) ? $tableName : $entityName;
        $this->conditionBuilder = new SqlConditionHelper();
        $this->nameTranslator = new NameTranslateHelper();
    }

    /**
     * Looks up single entity using given condition(-s)
     * @param array $conditions
     * @return Entity
     */
    public function findOneBy(array $conditions)
    {
        $sql = $this->generateFindSql($conditions);
        $data = $this->sqliteWrapper->getRow($sql);
        return $this->createEntity($data);
    }

    /**
     * Looks up one or more entities using given condition(-s) and returns collection
     * @param array $conditions
     * @return EntityCollection
     */
    public function findBy(array $conditions)
    {
        $sql = $this->generateFindSql($conditions);
        $data = $this->sqliteWrapper->select($sql);
        return $this->createCollection($data);
    }

    /**
     * Entity factory method
     * @param $data
     * @return Entity
     */
    public function createEntity($data = [])
    {
        $entityClassName = '\\rms\\db\\entities\\' . $this->nameTranslator->fieldToFunction($this->entityName) . 'Entity';
        /** @var Entity $entityClass */
        $entityClass = new $entityClassName($this->sqliteWrapper);
        if (count($data) > 0) {
            $entityClass->load($data);
        }
        return $entityClass;
    }

    /**
     * Collection factory method
     * @param array $data
     * @return EntityCollection
     */
    public function createCollection(array $data)
    {
        $collection = new EntityCollection();
        foreach ($data as $row) {
            if (is_array($row)) {
                $collection->add($this->createEntity($row));
            }
        }
        return $collection;
    }

    /**
     * Returns single entity by ID
     * @param int $id
     * @return Entity
     */
    public function findOneById($id)
    {
        return $this->findOneBy(['id' => $id]);
    }

    /**
     * Gets all table records
     * @return EntityCollection
     */
    public function getAll()
    {
        return $this->findBy([1 => 1]);
    }

    /**
     * Deletes row(s) by their ID, entity or entities collection
     * @param mixed $data
     */
    public function delete($data)
    {
        if ($data instanceof EntityCollection) {
            $this->deleteCollection($data);
        } elseif ($data instanceof Entity){
            $this->deleteEntity($data);
        } else {
            $this->deleteById((int) $data);
        }
    }

    /**
     * Delete row by id
     * @param int $id row id
     */
    private function deleteById($id)
    {
        $this->sqliteWrapper->delete($this->tableName, $id);
    }

    /**
     * Delete row by entity
     * @param Entity $entity
     */
    private function deleteEntity(Entity $entity)
    {
        $this->sqliteWrapper->delete($this->tableName, $entity->getId());
    }

    /**
     * Delete rows by their entities
     * @param EntityCollection $collection
     */
    private function deleteCollection(EntityCollection $collection)
    {
        foreach ($collection as $entity) {
            $this->sqliteWrapper->delete($this->tableName, $entity->getId());
        }
    }

    /**
     * Generates SQL string by condition(-s) list
     * @param array $conditions
     * @return string
     */
    private function generateFindSql(array $conditions)
    {
        return sprintf(
            'SELECT ' . '* FROM %s WHERE %s',
            $this->tableName,
            $this->conditionBuilder->build($conditions)
        );
    }
}

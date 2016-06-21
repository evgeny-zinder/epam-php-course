<?php

namespace rms\db;

use rms\Util;

/**
 * Class EntityManager
 * Manages repositories
 * @package rms\db
 */
class EntityManager
{
    /** @var array */
    private $repositories;

    /** @var SqliteWrapper */
    private $sqliteWrapper;

    /**
     * EntityManager constructor.
     * @param SqliteWrapper $sqliteWrapper
     */
    public function __construct(SqliteWrapper $sqliteWrapper)
    {
        $this->sqliteWrapper = $sqliteWrapper;
    }

    /**
     * Get repository by entities name
     * @param string $entityName
     * @return Repository|null
     */
    public function getRepository($entityName)
    {
        if (null === Util::arrayGet($this->repositories, $entityName)) {
            $repositoryClassName = '\\rms\\db\\repositories\\' . ucfirst($entityName) . 'Repository';
            $this->repositories[$entityName] = new $repositoryClassName($this->sqliteWrapper, $entityName);
        }
        return Util::arrayGet($this->repositories,$entityName);
    }

    /**
     * Shortcut entity factory method
     * @param string $name entity name
     * @param array $data optional entity data
     * @return Entity
     */
    public function createEntity($name, array $data = [])
    {
        return $this->getRepository($name)->createEntity($data);
    }
}

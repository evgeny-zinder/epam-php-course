<?php

namespace rms\db\repositories;

use rms\db\Repository;
use rms\db\SqliteWrapper;

/**
 * Class MembersRepository
 * @package rms\db\repositories
 */
class MembersRepository extends Repository
{
    /**
     * MembersRepository constructor.
     * @param SqliteWrapper $sqliteWrapper
     */
    public function __construct(SqliteWrapper $sqliteWrapper)
    {
        parent::__construct($sqliteWrapper, 'members');
    }

    /**
     * Looks up single member by any of it's data fields
     * @param $data
     * @return \rms\db\Entity
     */
    public function getOneByAnyAccount($data)
    {
        return $this->findOneBy(
            [
                'or' => [
                    ['id' => $data],
                    ['eid' => $data],
                    ['name' => $data],
                    ['email' => $data],
                    ['jenkins' => $data],
                    ['jira' => $data],
                    ['slack' => $data]
                ]
            ]
        );
    }
}
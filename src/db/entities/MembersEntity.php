<?php

namespace rms\db\entities;

use rms\db\Entity;
use rms\db\SqliteWrapper;

/**
 * Class MembersEntity
 * @package rms\db\entities
 *
 * @method string getName()
 * @method MembersEntity setName($name)
 * @method string getEid()
 * @method MembersEntity setEid($eid)
 * @method string getEmail()
 * @method MembersEntity setEmail($email)
 * @method string getJira()
 * @method MembersEntity setJira($jira)
 * @method string getSlack()
 * @method MembersEntity setSlack($slack)
 * @method string getJenkins()
 * @method MembersEntity setJenkins($jenkins)
 * @method string getCellular()
 * @method MembersEntity setCellular($cellular)
 * @method string getPhoto()
 * @method MembersEntity setPhoto($photo)
 */
class MembersEntity extends Entity
{
    /**
     * MembersEntity constructor.
     * @param SqliteWrapper $sqliteWrapper
     */
    public function __construct(SqliteWrapper $sqliteWrapper)
    {
        parent::__construct($sqliteWrapper, 'members');
    }
}
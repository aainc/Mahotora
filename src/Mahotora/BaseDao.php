<?php
/**
 * Date: 15/10/07
 * Time: 13:23
 */

namespace Mahotora;

abstract class BaseDao
{
    /**
     * @var DatabaseSession
     */
    private $databaseSession = null;

    public function __construct($databaseSession)
    {
        $this->databaseSession = $databaseSession;
    }

    public function save($object)
    {
        $this->databaseSession->executeNoResult(new SaveQuery($this->getTableName(), $object));
    }

    public abstract function getTableName();
    public abstract function find($id);

    /**
     * @return DatabaseSession
     */
    public function getDatabaseSession()
    {
        return $this->databaseSession;
    }
}

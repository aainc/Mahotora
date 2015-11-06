<?php
namespace Mahotora;
/**
 * Class MultiDatabaseSession
 *
 * database session wrapper for read replication, master database style
 * @package Mahotora
 */
class MultiDatabaseSession implements DatabaseSession
{
    private $read = null;
    private $write = null;
    private $trans = false;

    public function __construct($read, $write)
    {
        $this->read = $read;
        $this->write = $write;
    }

    public function open()
    {
        $this->read->open();
        $this->write->open();
    }

    public function executeNoResult($query, $marker = null, array $params = null)
    {
        $this->write->executeNoResult($query, $marker, $params);
    }

    public function traverse($query, \Closure $runner = null, $marker = null, array $params = null, \Closure $fetcher = null)
    {
        return $this->getStore()->traverse($query, $runner, $marker, $params, $fetcher);
    }

    public function find($query, $marker = null, $params = null, $className = null)
    {
        return $this->getStore()->find($query, $marker, $params, $className);
    }

    public function getStore()
    {
        return $this->trans ? $this->write : $this->read;
    }

    public function begin()
    {
        if ($this->trans) {
            $this->rollback();
            throw new \Exception('already in transaction');
        }
        $this->write->begin();
        $this->trans = true;
    }

    public function commit()
    {
        $this->write->commit();
        $this->trans = false;
    }

    public function rollback()
    {
        $this->write->rollback();
        $this->trans = false;
    }

    public function close()
    {
        $this->read->close();
        $this->write->close();
    }

    public function lastInsertId()
    {
        $this->write->lastInsertId();
    }
}
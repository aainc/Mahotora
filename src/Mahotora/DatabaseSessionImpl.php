<?php
namespace Mahotora;
/**
 * Wrapper class of \mysqli
 *
 * Class DatabaseSessionImpl
 * @package Mahotora
 */
class DatabaseSessionImpl implements DatabaseSession
{
    private $config = null;
    /**
     * @var \mysqli
     */
    private $connection = null;
    private $driver = null;

    public function __construct($config)
    {
        if (!is_array($config) && !($config instanceof \ArrayAccess)) {
            throw new \InvalidArgumentException('wants array');
        }
        $this->config = $config;
    }

    /**
     * connect database
     */
    public function open()
    {
        if ($this->connection === null) {
            $this->driver = new \mysqli_driver();
            $this->driver->report_mode = MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT;
            $this->connection = new \mysqli($this->config['host'], $this->config['user'], $this->config['pass']);
            $this->connection->select_db($this->config['database']);
        }
    }

    /**
     * begin transaction
     */
    public function begin()
    {
        if ($this->connection === null) {
            $this->open();
        }
        $this->connection->autocommit(false);
    }

    /**
     * commit transaction
     */
    public function commit()
    {
        if ($this->connection === null) {
            $this->open();
        }
        $this->connection->commit();
        $this->connection->autocommit(true);
    }

    /**
     * rollback transaction
     */
    public function rollback()
    {
        if ($this->connection === null) {
            $this->open();
        }
        $this->connection->rollback();
        $this->connection->autocommit(true);
    }

    /**
     * execute sql that need not resultSet
     * @param string $query SQL
     * @param null $marker marker of binding parameters (s = string, d = double, i = integer, b = blob) example: integer, string, double sequence "isd"
     * @param array $params binding parameters
     * @return int affected_rows
     * @throws \InvalidArgumentException
     */
    public function executeNoResult($query, $marker = null, array $params = null)
    {
        if ($marker XOR $params) {
            throw new \InvalidArgumentException('$marker xor $params');
        }
        if ($this->connection === null) {
            $this->open();
        }
        if ($query instanceof BaseQuery) {
            $params = $query->getParameters();
            $marker = $query->getMarker();
            $query = $query->getQuery();
        }
        $stmt = $this->connection->prepare($query);
        if ($marker && $params) {
            array_unshift($params, $marker);
            foreach ($params as $key => $val)  $tmp[$key] = &$params[$key];
            call_user_func_array(array($stmt, 'bind_param'), $tmp);
        }
        $stmt->execute();
        $rows = $this->getAffectedRows();
        $stmt->close();
        return $rows;
    }

    public function getAffectedRows()
    {
        return $this->connection->affected_rows;
    }

    public function lastInsertId()
    {
        return $this->connection->insert_id;
    }

    /**
     * traverse resultSet with closure
     * @param string $query SQL
     * @param \Closure $runner called for each fetched row
     * @param null $marker marker of binding parameters (s = string, d = double, i = integer, b = blob) example: integer, string, double sequence "isd"
     * @param array $params binding parameters
     * @param \Closure $fetcher called for each row pre fetch
     * @return array if $runner return a value, it push this array.
     * @throws \InvalidArgumentException
     */
    public function traverse($query, \Closure $runner = null, $marker = null, array $params = null, \Closure $fetcher = null)
    {
        if ($marker XOR $params) {
            throw new \InvalidArgumentException('$marker xor $params');
        }
        if ($this->connection === null) {
            $this->open();
        }
        $stmt = $this->connection->prepare($query);
        if ($marker && $params) {
            array_unshift($params, $marker);
            foreach ($params as $key => $val)  $tmp[$key] = &$params[$key];
            call_user_func_array(array($stmt, 'bind_param'), $tmp);
        }
        $stmt->execute();
        $rs = $stmt->get_result();
        $result = array();
        while ($obj = ($fetcher ? $fetcher($rs) : $rs->fetch_object())) {
            $tmp = $runner ? $runner($obj) : $obj;
            if ($tmp !== null) {
                $result[] = $tmp;
            }
        }
        $rs->close();
        $stmt->close();
        return $result;

    }

    /**
     * get the resultSet by array
     * @param $query
     * @param null $marker
     * @param null $params
     * @param null $className
     * @return \InvalidArgumentException
     */
    public function find($query, $marker = null, $params = null, $className = null)
    {
        return $this->traverse($query,
            null,
            $marker,
            $params,
            $className !== null ? function ($resultSet) use ($className) {
                return $resultSet->fetch_object($className);
            } : null
        );

    }

    /**
     *  close connection
     */
    public function close()
    {
        if ($this->connection === null) {
            $this->open();
        }
        $this->connection->close();
    }

    /**
     * @param \mysqli $connection
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return \mysqli
     */
    public function getConnection()
    {
        return $this->connection;
    }
}
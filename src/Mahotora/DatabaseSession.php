<?php
namespace Mahotora;
interface DatabaseSession
{
    public function open();
    public function begin();
    public function commit();
    public function rollback();
    public function executeNoResult($query, $marker = null, array $params = null);
    public function traverse($query, \Closure $runner = null, $marker = null, array $params = null, \Closure $fetcher = null);
    public function find($query, $marker = null, $params = null, $className = null);
    public function close();
    public function lastInsertId();
}
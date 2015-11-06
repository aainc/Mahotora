<?php
/**
 * Date: 15/10/07
 * Time: 11:32
 */

namespace Mahotora;


abstract class BaseQuery {
    protected  $query = null;
    protected  $marker = null;
    protected  $parameters = null;


    public function __construct ($tableName, $object)
    {
        if ($object && $object instanceof \stdClass) {
            $object = (array)$object;
        }
        if (!$tableName || !$object || !is_array($object)) {
            throw new \InvalidArgumentException('need $tableName. $object wants array or stdClass');
        }
        $this->fill($tableName, $object);
    }

    public function backQuote ($val)
    {
       return  '`' . str_replace(array('\\', '`'), array('\\\\', '\`'), $val) . '`';
    }

    abstract public function fill($tableName, $object);

    /**
     * @return null
     */
    public function getMarker()
    {
        return $this->marker;
    }

    /**
     * @return null
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @return null
     */
    public function getQuery()
    {
        return $this->query;
    }
}
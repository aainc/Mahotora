<?php
/**
 * Date: 15/10/07
 * Time: 13:10
 */

namespace Mahotora;


class InsertQuery extends BaseQuery
{

    public function fill($tableName, $object)
    {
        $values    = array_values($object);
        $tableName = $this->backQuote($tableName);
        $self = $this;
        $columnBlock = implode(',', array_map(
            function ($column) use ($self){return $self->backQuote($column);},
            array_keys($object)
        ));

        $valuesBlock  = implode(',', array_pad(array(), count($values), '?'));
        $marker  = implode('',  array_map(function ($column) {
            return strval(intval($column)) === strval($column) ? 'i' : 's';
        }, $values));

        $this->query  =  "INSERT INTO $tableName ($columnBlock) VALUES ($valuesBlock)";
        $this->marker = $marker;
        $this->parameters =  $values;
    }
}
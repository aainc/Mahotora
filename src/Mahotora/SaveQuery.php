<?php
/**
 * Date: 15/10/07
 * Time: 13:11
 */

namespace Mahotora;


class SaveQuery extends InsertQuery
{
    public function fill ($tableName, $object)
    {
        parent::fill($tableName, $object);
        $marker = $this->getMarker();
        $parameters = $this->getParameters();
        $columns = array();
        foreach ($object as $key => $val) {
            $columns[] = $this->backQuote($key) . ' = ?';
            $marker .= strval(intval($val)) === strval($val) ? 'i' : 's';
            $parameters[] = $val;
        }
        $this->marker = $marker;
        $this->parameters = $parameters;
        $this->query =  "$this->query ON DUPLICATE KEY UPDATE " . implode(',', $columns);
    }
}
<?php
/**
 * DatabaseSessionFactory.php@gadget_enquete
 * User: ishidatakeshi
 * Date: 15/09/10
 * Time: 17:57
 */
namespace Mahotora;


class DatabaseSessionFactory
{
    public static function build($config)
    {
        $result = null;
        if (isset($config['read'])) {
            if (isset($config['write'])) {
                $result = new MultiDatabaseSession(
                    new DatabaseSessionImpl($config['read']),
                    new DatabaseSessionImpl($config['write'])
                );
            } else {
                throw new \InvalidArgumentException('need DB_READ, DB_WRITE');
            }
        } else {
            $result = new DatabaseSessionImpl($config);
        }
        return $result;
    }
}
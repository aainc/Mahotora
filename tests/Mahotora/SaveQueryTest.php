<?php
/**
 * Date: 15/10/07
 * Time: 13:34
 */

namespace Mahotora;


class SaveQueryTest extends \PHPUnit_Framework_TestCase
{
    public function testFill ()
    {
        $query = new SaveQuery('tbl1', array('col1' => 'col1Value', 'col2' => 'col2Value', 'col3' => 1));
        $this->assertSame('INSERT INTO `tbl1` (`col1`,`col2`,`col3`) VALUES (?,?,?) ON DUPLICATE KEY UPDATE `col1` = ?,`col2` = ?,`col3` = ?', $query->getQuery());
        $this->assertSame('ssissi', $query->getMarker());
        $this->assertSame(array('col1Value', 'col2Value', 1, 'col1Value', 'col2Value', 1), $query->getParameters());
    }

    public function testFillByObject ()
    {
        $query = new SaveQuery('tbl1', (object)array('col1' => 'col1Value', 'col2' => 'col2Value', 'col3' => 1));
        $this->assertSame('INSERT INTO `tbl1` (`col1`,`col2`,`col3`) VALUES (?,?,?) ON DUPLICATE KEY UPDATE `col1` = ?,`col2` = ?,`col3` = ?', $query->getQuery());
        $this->assertSame('ssissi', $query->getMarker());
        $this->assertSame(array('col1Value', 'col2Value', 1, 'col1Value', 'col2Value', 1), $query->getParameters());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNoObject ()
    {
        $query = new InsertQuery('tbl1', null);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNoTable ()
    {
        $query = new InsertQuery(null, (object)array('hoge' => 'fuga'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidObject ()
    {
        $query = new InsertQuery(null, new \DateTime());
    }
}
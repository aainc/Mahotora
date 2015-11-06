<?php
/**
 * Date: 15/10/07
 * Time: 13:34
 */

namespace Mahotora;


class InsertQueryTest extends \PHPUnit_Framework_TestCase
{
    public function testFill ()
    {
        $query = new InsertQuery('tbl1', array('col1' => 'col1Value', 'col2' => 'col2Value', 'col3' => 1));
        $this->assertSame('INSERT INTO `tbl1` (`col1`,`col2`,`col3`) VALUES (?,?,?)', $query->getQuery());
        $this->assertSame('ssi', $query->getMarker());
        $this->assertSame(array('col1Value', 'col2Value', 1), $query->getParameters());
    }

    public function testFillByObject ()
    {
        $query = new InsertQuery('tbl1', (object)array('col1' => 'col1Value', 'col2' => 'col2Value', 'col3' => 1));
        $this->assertSame('INSERT INTO `tbl1` (`col1`,`col2`,`col3`) VALUES (?,?,?)', $query->getQuery());
        $this->assertSame('ssi', $query->getMarker());
        $this->assertSame(array('col1Value', 'col2Value', 1), $query->getParameters());
    }

    public function testBackQuote ()
    {
        $query = new InsertQuery('hoge', array('fuga' => 'piyo'));
        $this->assertEquals('`tbl1`', $query->backQuote('tbl1'));
        $this->assertEquals('`tbl\`1`', $query->backQuote('tbl`1'));
        $this->assertEquals('`tbl1\\\\`', $query->backQuote('tbl1\\'));
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
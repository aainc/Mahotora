<?php
namespace Mahotora;


class DatabaseSessionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Mahotora\DatabaseSessionImpl
     */
    private $target = null;
    private $statement = null;
    private $connection = null;
    private $resultSet = null;

    public function setUp()
    {
        $this->target = \Phake::partialMock('Mahotora\DatabaseSessionImpl',array());
        $this->connection = \Phake::mock('\mysqli');
        $this->statement = \Phake::mock('\mysqli_stmt');
        $this->resultSet = \Phake::mock('\mysqli_result');
        $this->target->setConnection($this->connection);
    }

    public function testBegin()
    {
        $this->target->begin();
        \Phake::verify($this->connection)->autocommit(false);
    }

    public function testCommit()
    {
        $this->target->commit();
        \Phake::verify($this->connection)->commit();
        \Phake::verify($this->connection)->autocommit(true);
    }

    public function testRollback()
    {
        $this->target->rollback();
        \Phake::verify($this->connection)->rollback();
        \Phake::verify($this->connection)->autocommit(true);
    }

    public function testTraverse()
    {
        \Phake::when($this->connection)->prepare('SELECT * FROM t1 WHERE p1 = ? AND p2 = ? AND p3 = ?')->thenReturn($this->statement);
        \Phake::when($this->statement)->get_result()->thenReturn($this->resultSet);
        \Phake::when($this->resultSet)->fetch_object()
            ->thenReturn((object)array('hoge' => 'fuga'))
            ->thenReturn(null);
        $result = $this->target->traverse(
            'SELECT * FROM t1 WHERE p1 = ? AND p2 = ? AND p3 = ?',
            function ($obj) {
                return $obj;
            },
            'ssd', array('hoge', 'fuga', 1234)
        );
        $this->assertSame(1, count($result));
        $this->assertEquals((object)array('hoge' => 'fuga'), $result[0]);
        \Phake::verify($this->statement)->bind_param('ssd', 'hoge', 'fuga', 1234);
        \Phake::verify($this->statement)->close();
        \Phake::verify($this->resultSet)->close();
    }

    public function testTraverseNoParameter()
    {
        \Phake::when($this->connection)->prepare('SELECT * FROM t1')->thenReturn($this->statement);
        \Phake::when($this->statement)->get_result()->thenReturn($this->resultSet);
        \Phake::when($this->resultSet)->fetch_object()
            ->thenReturn((object)array('hoge' => 'fuga'))
            ->thenReturn(null);
        $result = $this->target->traverse('SELECT * FROM t1', function ($obj) {
            return $obj;
        });
        $this->assertSame(1, count($result));
        $this->assertEquals((object)array('hoge' => 'fuga'), $result[0]);
        \Phake::verify($this->statement, \Phake::never())->bind_param(\Phake::anyParameters());
        \Phake::verify($this->statement)->close();
        \Phake::verify($this->resultSet)->close();
    }

    public function testExecuteNoResult()
    {
        \Phake::when($this->target)->getAffectedRows()->thenReturn(1);
        \Phake::when($this->connection)->prepare('CREATE TABLE t1 (id BIGINT NOT NULL PRIMARY KEY)')->thenReturn($this->statement);
        $result = $this->target->executeNoResult('CREATE TABLE t1 (id BIGINT NOT NULL PRIMARY KEY)', 'ssd', array('hoge', 'fuga', 1234));
        $this->assertSame(1, $result);
        \Phake::verify($this->statement)->bind_param('ssd', 'hoge', 'fuga', 1234);
        \Phake::verify($this->statement)->close();
    }

    public function testExecuteNoResultNoParameter()
    {
        \Phake::when($this->target)->getAffectedRows()->thenReturn(0);
        \Phake::when($this->connection)->prepare('DROP TABLE')->thenReturn($this->statement);
        $this->target->executeNoResult('DROP TABLE');
        \Phake::verify($this->statement, \Phake::never())->bind_param(\Phake::anyParameters());
        \Phake::verify($this->statement)->close();
    }

}

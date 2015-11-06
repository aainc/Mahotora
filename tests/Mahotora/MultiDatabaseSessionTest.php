<?php
namespace Mahotora;
class MultiDatabaseSessionTest extends \PHPUnit_Framework_TestCase
{
    private $read = null;
    private $write = null;
    /**
     * @var \Mahotora\DatabaseSession
     */
    private $target = null;

    public function setUp()
    {
        $this->read = \Phake::mock('\Mahotora\DatabaseSessionImpl');
        $this->write = \Phake::mock('\Mahotora\DatabaseSessionImpl');
        $this->target = new MultiDatabaseSession($this->read, $this->write);
    }

    public function testBegin()
    {
        $this->target->begin();
        \Phake::verify($this->write)->begin();
        \Phake::verify($this->read, \Phake::never())->begin();
    }

    public function testCommit()
    {
        $this->target->commit();
        \Phake::verify($this->write)->commit();
        \Phake::verify($this->read, \Phake::never())->commit();

    }

    public function testRollback()
    {
        $this->target->rollback();
        \Phake::verify($this->write)->rollback();
        \Phake::verify($this->read, \Phake::never())->rollback();
    }

    public function testTraverse()
    {
        $this->target->begin();
        $this->target->commit();
        $this->target->traverse('hoge', function ($obj) {
        }, 'ssd', array('a', 'b', 3), function ($rs) {
        });
        \Phake::verify($this->read)->traverse('hoge', function ($obj) {
        }, 'ssd', array('a', 'b', 3), function ($rs) {
        });
        \Phake::verify($this->write, \Phake::never())->traverse('hoge', function ($obj) {
        }, 'ssd', array('a', 'b', 3), function ($rs) {
        });
    }

    public function testTraverseIntrans()
    {
        $this->target->begin();
        $this->target->traverse('hoge', function ($obj) {
        }, 'ssd', array('a', 'b', 3), function ($rs) {
        });
        \Phake::verify($this->write)->traverse('hoge', function ($obj) {
        }, 'ssd', array('a', 'b', 3), function ($rs) {
        });
        \Phake::verify($this->read, \Phake::never())->traverse('hoge', function ($obj) {
        }, 'ssd', array('a', 'b', 3), function ($rs) {
        });
    }

    public function testFind()
    {
        $this->target->begin();
        $this->target->commit();
        $this->target->find('hoge', 'ssd', array('a', 'b', 3), 'fuga');
        \Phake::verify($this->read)->find('hoge', 'ssd', array('a', 'b', 3), 'fuga');
        \Phake::verify($this->write, \Phake::never())->find('hoge', 'ssd', array('a', 'b', 3), 'fuga');
    }

    public function testFindInTrans()
    {
        $this->target->begin();
        $this->target->find('hoge', 'ssd', array('a', 'b', 3), 'fuga');
        \Phake::verify($this->write)->find('hoge', 'ssd', array('a', 'b', 3), 'fuga');
        \Phake::verify($this->read, \Phake::never())->find('hoge', 'ssd', array('a', 'b', 3), 'fuga');
    }
}
<?php
namespace TestLSYSDB;
use PHPUnit\Framework\TestCase;
use LSYS\Database;
use LSYS\Database\DI;
use LSYS\Database\EventManager\SlaveQueryObserver;
use LSYS\SlaveQuery;
class SlaveQueryTest extends TestCase
{
    public function testredis(){
        \LSYS\SlaveQuery\DI::get()->slave_query(new \LSYS\DI\SingletonCallback(function () {
            return new SlaveQuery(new \LSYS\SlaveQuery\Cache\Redis(1000/*缓存时间*/));
        }));
        $this->runsalve();
    }
    public function testmemcache(){
        \LSYS\SlaveQuery\DI::get()->slave_query(new \LSYS\DI\SingletonCallback(function () {
            return new SlaveQuery(new \LSYS\SlaveQuery\Cache\Memcache(1000/*缓存时间*/));
        }));
        $this->runsalve();
    }
    public function testmemcached(){
        \LSYS\SlaveQuery\DI::get()->slave_query(new \LSYS\DI\SingletonCallback(function () {
            return new SlaveQuery(new \LSYS\SlaveQuery\Cache\Memcached(1000/*缓存时间*/));
        }));
        $this->runsalve();
    }
    protected function runsalve() {
        $db =DI::get()->db("database.mysqli");
        $db = Database::factory(\LSYS\Config\DI::get()->config("database.mysqli"));
        $sqc=\LSYS\SlaveQuery\DI::get()->slave_query();
        $eventmanager=\LSYS\EventManager\DI::get()->eventManager();
        $eventmanager->attach(new SlaveQueryObserver($sqc));
        $db->setEventManager($eventmanager);
        $db->getMasterConnect()->exec("INSERT INTO test.l_order(sn, title, add_time)VALUES('', '', 0);");
        $sql="select * from l_order";
        $this->assertFalse($sqc->allowSlave($sql));
    }
}
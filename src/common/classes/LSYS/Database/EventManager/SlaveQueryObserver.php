<?php
/**
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LSYS\Database\EventManager;
use LSYS\EventManager\Event;
use LSYS\EventManager\EventObserver;
class SlaveQueryObserver implements EventObserver
{
    protected $slave_query_check;
    public function __construct(\LSYS\SlaveQuery $sqc=null){
        $this->slave_query_check=$sqc?$sqc:\LSYS\SlaveQuery\DI::get()->slave_query();
    }
    public function eventNotify(Event $event)
    {
        switch ($event->getName()) {
            case DBEvent::SQL_OK:
				list($prepare)=(array)$event->getData();
                $this->slave_query_check->execNotify($prepare);
            break;
        }
    }
    public function eventName()
    {
        return [
            DBEvent::SQL_OK,
        ];
    }
}

<?php
namespace LSYS\SlaveQuery;
/**
 * @method \LSYS\SlaveQuery slave_query() 
 */
class DI extends \LSYS\DI{
    /**
     * @return static
     */
    public static function get(){
        $di=parent::get();
        !isset($di->slave_query)&&$di->slave_query(new \LSYS\DI\VirtualCallback(\LSYS\SlaveQuery::class));
        return $di;
    }
}



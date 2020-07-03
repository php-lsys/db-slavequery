<?php
/**
 * lsys database
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LSYS\SlaveQuery\Cache;
use LSYS\SlaveQuery\Cache;
class Memcache implements Cache{
    protected $memcache;
    protected $prefix;
    protected $delayed;
    protected $log;
    /**
     * @param number $delayed
     * @param \LSYS\Memcache $memcache
     * @param string $prefix
     * @param callable $log error report callback (\LSYS\Exception $e)
     */
    public function __construct($delayed=10,\LSYS\Memcache $memcache=null,$prefix='db_master',callable $log=null){
        $this->memcache=$memcache;
        $this->prefix=$prefix;
        $this->delayed=$delayed;
        $this->log=$log;
    }
    protected function memcache(){
        if (!is_object($this->memcache))$this->memcache=\LSYS\Memcache\DI::get()->memcache();
        try{
            $this->memcache->configServers();
        }catch (\LSYS\Exception $e){
            is_callable($this->log)&&call_user_func($this->log,$e);
            return;
        }
        return $this->memcache;
    }
    public function time(array $table){
        $memcache=$this->memcache();
        if (!is_object($memcache)) return true;
        foreach ($table as $v){
            try{
                if(intval($memcache->get($this->prefix.$v))>time())return true;
            }catch (\LSYS\Exception $e){
                is_callable($this->log)&&call_user_func($this->log,$e);
                return true;
            }
        }
    }
    public function save(array $table){
        $memcache=$this->memcache();
        if (!is_object($memcache)) return;
        $delayed=$this->delayed();
        foreach ($table as $v){
            try{
                $memcache->set($this->prefix.$v,time()+$delayed,0,$delayed);
            }catch (\LSYS\Exception $e){
                is_callable($this->log)&&call_user_func($this->log,$e);
            }
        }
    }
    public function delayed(){
        return $this->delayed;
    }
}
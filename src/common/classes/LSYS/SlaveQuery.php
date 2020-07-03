<?php
/**
 * 检测是否可以从 slave库查询
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LSYS;
use LSYS\SlaveQuery\Parse;
use LSYS\SlaveQuery\Cache;
use LSYS\SlaveQuery\Parse\Simple;
use LSYS\Database\Connect\MYSQLi\Prepare;
use LSYS\Database\ConnectSchema;
class SlaveQuery{
    protected $cache;
    protected $parse;
    public function __construct(Cache $cache,Parse $parse=null){
        $this->cache=$cache;
        $this->parse=$parse?$parse:new Simple();
    }
    /**
     * 检测当前SQL是否可以通过从库查询
     * @param string $sql
     */
    public function allowSlave($sql){
        if($this->cache->delayed()<=0)return false;
        $table=$this->parse->queryParseTable($sql);
        if(empty($table))return false;
        if($this->cache->time($table)){
            return false;
        }
        return true;
    }
    /**
     * sql连接对象获取
     * @param Database $db
     * @param string $sql
     * @return \LSYS\Database\ConnectSlave|\LSYS\Database\ConnectMaster
     */
    public function connect(Database $db,$sql){
        if ($this->allowSlave($sql)) {
            return $db->getSlaveConnect();
        }else{
            return $db->getMasterConnect();
        }
    }
    /**
     * sql执行方法
     * @param Database $db
     * @param string $sql
     * @param array $value
     * @param array $value_type
     * @return \LSYS\Database\Result
     */
    public function query(Database $db,$sql,array $value=[],array $value_type=[]) {
        return $this->connect($db, $sql)->query($sql,$value,$value_type);
    }
    /**
     * SQL更改告知
     * @param string $table_schema 默认数据库名
     * @param string $sql
     */
    public function execNotify(Prepare $prepare){
        if($this->cache->delayed()<=0
            ||$prepare->affectedRows()==0
        )return;
        $connect=$prepare->connect();
        if(!$connect instanceof ConnectSchema){
            return ;
        }
        $sql=$prepare->querySQL();
        $table=$this->parse->execParseTable($sql);
        if(empty($table))return;
        $table_schema=$connect->schema();
        if(!empty($table_schema)){
            $add=[];
            foreach ($table as $v){
                $p=strpos($v, '.');
                if($p===false){
                    $add[]=$table_schema.'.'.$v;
                }else{
                    if(substr($v, 0,$p)==$table_schema){
                        $add[]=substr($v, $p+1);
                    }
                }
            }
            $table=array_merge($table,$add);
        }
        return $this->cache->save($table);
    }
}

<?php
use LSYS\Config\File;
use LSYS\SlaveQuery;
include_once __DIR__."/../vendor/autoload.php";
File::dirs(array(
	__DIR__."/config",
));

LSYS\SlaveQuery\DI::get()->slave_query(new \LSYS\DI\SingletonCallback(function () {
    return new SlaveQuery(new \LSYS\SlaveQuery\Cache\Redis(1000/*缓存时间*/));
}));
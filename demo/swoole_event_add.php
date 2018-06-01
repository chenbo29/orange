<?php
/**
 * Created by PhpStorm.
 * User: chenbo
 * Date: 18-6-1
 * Time: 下午3:34
 */
$fp = stream_socket_client("tcp://www.qq.com:80", $errno, $errstr, 30);
fwrite($fp,"GET / HTTP/1.1\r\nHost: www.qq.com\r\n\r\n");

swoole_event_add($fp, function($fp) {
    $resp = fread($fp, 8192);
    var_dump($resp);
    //socket处理完成后，从epoll事件中移除socket
    swoole_event_del($fp);
    fclose($fp);
});
echo "Finish\n";  //swoole_event_add不会阻塞进程，这行代码会顺序执行





swoole_event_add($worker['process']->pipe, function ($pipe) use ($worker, $logger) {
    $logger->info('read from worker:'.$worker['process']->read());
});
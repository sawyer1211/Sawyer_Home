<?php

namespace app\common\tools;

use think\Db;

/**
 * Created by 1211.withsawyer.
 * User: Sawyer Yang
 * Date: 2017/4/27
 * Time: 15:19
 */
class LogUtils
{
    public static function log($msg, $_path = '', $base_path = './log/')
    {
        if ($_path) {
            $base_path .= $_path . '/';
        }
        //判断路径是否存在
        _createFolder($base_path);
        $fp = fopen($base_path . date('Ymd') . ".txt", "a");
        flock($fp, LOCK_EX);
        fwrite($fp, "执行日期：" . strftime("%Y%m%d%H%M%S", time()) . "\n" . $msg . "\r\n\r\n");
        flock($fp, LOCK_UN);
        fclose($fp);
    }

    /**
     * 用户日志
     * @param $data
     * @return int|string
     */
    public static function userRunningLog($data)
    {
        return Db::name('user_login_running')->insert($data);
    }
}
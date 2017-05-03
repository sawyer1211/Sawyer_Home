<?php
/**
 * Created by thinkphp5.
 * User: Sawyer Yang
 * Date: 2017/3/14
 * Time: 14:11
 */
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]

// 定义应用目录
define('APP_PATH', str_replace("\\", '/', __DIR__ . '/app/'));
define('APP_ROOT', str_replace("\\", '/', dirname(__FILE__) . '/'));
define('HTTP_HOST', 'http://' . $_SERVER['HTTP_HOST'] . '');
define('APP_URL', HTTP_HOST . substr($_SERVER['SCRIPT_NAME'], 0, -9) . '');
define('__PUBLIC__', APP_URL . 'public/');
// 加载框架引导文件
require __DIR__ . '/thinkphp/start.php';
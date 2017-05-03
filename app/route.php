<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;

return [
    '__pattern__'   => [
        'name' => '\w+',
    ],

    // 首页
    'index'         => 'home/index/index',
    // 注册页面
    'enroll/:visit' => ['home/user/register', ['method' => 'get'], ['visit' => '\w*']],
    // 提交注册地址
    'doRgt/:visit'  => ['home/user/register', ['method' => 'get|post'], ['visit' => '\w*']],
];

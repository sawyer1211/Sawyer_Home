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

// 我的简历
Route::rule('Sawyer_resume', 'home/user/resume');
// 舒克懒懒的简历
Route::rule('Shuke_resume', 'home/user/shuKeResume');
// 舒克懒懒费事，还要发邮件
Route::rule('send_shuke', 'home/user/shuKeResumeSendEmail');

return [
    '__pattern__'       => [
        'name' => '\w+',
    ],


    // 首页
    '/'                 => 'home/index/index',
    'index'             => 'home/index/index',
    // 文章详情
    'details/:id'       => ['home/index/articleDetails', ['method' => 'get'], ['id' => '^[1-9][0-9]*$']],
    // 登陆页面
    'login/:visit'      => ['home/user/login', ['method' => 'get'], ['visit' => '\w*']],
    // 提交登录登陆页面
    'doLogin/:visit'    => ['home/user/login', ['method' => 'get|post'], ['visit' => '\w*']],
    // 注册页面
    'enroll/:visit'     => ['home/user/register', ['method' => 'get'], ['visit' => '\w*']],
    // 提交注册地址
    'doRgt/:visit'      => ['home/user/register', ['method' => 'get|post'], ['visit' => '\w*']],
    // 发送验证码
    'sendVerify'        => 'home/user/sendVerify',
    // 图形验证码
    'GraphicVerifyCode' => 'home/user/validateCodeImage',
    // 注销登录
    'log-on'            => 'home/user/logout',
];

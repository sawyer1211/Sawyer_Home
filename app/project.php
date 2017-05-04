<?php
/**
 * 项目所需配置文件
 * Created by 1211.withsawyer.
 * User: Sawyer Yang
 * Date: 2017/4/26
 * Time: 16:09
 */
if (!defined('THINK_PATH')) exit();
return [
    'APP_NAME'                             => "Sawyer'Home",                                // 项目名称
    'VERSION_NUMBER'                       => '1.0',                                        // 版本号
    'PASS_ENCRYPT_TIMES'                   => 3,                                            // 密码加密次数（不可更改）
    'tokenRefresh'                         => 600000,                                       // token过期时间
    'isTest'                               => 1,                                            // 0-生产模式 1-开发模式
    'SESSION_USER_INFO_NAME'               => 'withsawyer_user_info',                       // 用户session名称
    'SEND_VERIFY_TYPE'                     => 'EMAIL',                                      // 发送验证码类型【MOBILE-手机 EMAIL-邮箱】
    'SEND_VERIFY_TYPE_CORRESPONDING_VALUE' => [                                             // 发送验证码类型对应状态
        'MOBILE' => 1,
        'EMAIL'  => 3,
    ],
    'VERIFY_SEND_INTERVAL_TIMES'           => 60,                                           // 验证码发送间隔时间（秒）
    'MOBILE_VERIFY_SEND_TIMES'             => 20,                                           // 手机发送限制（条）
    'EMAIL_VERIFY_SEND_TIMES'              => 10,                                           // 邮箱发送限制（条）
    'VERIFY_CODE_VALID_TIME'               => 120,                                          // 验证码过期时间(秒)
    'UPLOAD_IMAGES_SIZE_LIMIT'             => 2097152,                                      // 上传图片大小限制（字节）

];
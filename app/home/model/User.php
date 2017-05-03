<?php

namespace app\home\model;

use think\Model;

/**
 * Created by 1211.withsawyer.
 * User: Sawyer Yang
 * Date: 2017/4/26
 * Time: 16:43
 */
class User extends Model
{
    protected $pk = 'uid';
    protected $name = 'user_manage';

    //自定义初始化
    protected function initialize()
    {
        //需要调用`Model`的`initialize`方法
        parent::initialize();
        //TODO:自定义的初始化
    }

    public function doRegister($nickname, $user_name, $password, $re_password, $email, $verify)
    {

    }



}
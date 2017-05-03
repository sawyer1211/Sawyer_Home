<?php

namespace app\home\model;

use app\common\consts\MsgConst;
use app\common\consts\UserConst;
use app\common\model\BaseModel;

/**
 * Created by 1211.withsawyer.
 * User: Sawyer Yang
 * Date: 2017/4/26
 * Time: 16:43
 */
class User extends BaseModel
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


    /**
     * 提交注册信息
     * @param $data
     */
    public function doRegister($data)
    {
        $checkUserRes = $this->fetchSql(true)->where('u_state', 'NEQ', MsgConst::DELETE_CODE)->whereOr('u_email', $data['u_email'])->where("u_user_name", $data['u_user_name'])->count();
        dump($checkUserRes);die;

        $result = $this->insert($data);
        if (false === $result) {
            $this->arrayReturn(MsgConst::FAIL_CODE, '注册失败');
        } else {
            $this->arrayReturn(MsgConst::SUCCESS_CODE, '注册成功');
        }
    }


}
<?php

namespace app\home\controller;


use app\common\consts\MsgConst;
use app\common\controller\BaseController;
use think\Loader;

class Index extends BaseController
{
    public function index()
    {
        $IndexModel = Loader::model('Index');
        $articleListRes = $IndexModel->getArticleList();
        $articleListData = [];
        if ($articleListRes['retCode'] == MsgConst::SUCCESS_CODE) {
            $articleListData = $articleListRes['data'];
        }
        return $this->view->fetch('index/index', ['articleListData' => $articleListData]);
    }

    public function articleDetails()
    {
        return $this->view->fetch('index/detail');
    }


}

<?php

namespace app\home\controller;


use app\common\controller\BaseController;
use think\Loader;

class Index extends BaseController
{
    public function index()
    {
        $IndexModel = Loader::model('Index');
        $IndexModel->getArticleList();
        return $this->view->fetch('index/index');
    }

    public function articleDetails()
    {
        return $this->view->fetch('index/detail');
    }


}

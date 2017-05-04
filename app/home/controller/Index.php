<?php

namespace app\home\controller;


use app\common\controller\BaseController;

class Index extends BaseController
{
    public function index()
    {
        return $this->view->fetch('index/index');
    }


}

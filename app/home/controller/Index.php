<?php

namespace app\home\controller;


use app\common\consts\MsgConst;
use app\common\controller\BaseController;
use think\Db;
use think\Loader;
use think\Request;

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
        $articleId = Request::instance()->param('id');
        $flip = Request::instance()->param('flip');
        if ($flip == 'turn_left') {
            $flipRes = Db::name('article_publish')->field('id')->where([
                'id'        => ['LT', $articleId],
                'ac_status' => ['NEQ', MsgConst::DELETE_CODE],
            ])->order('id DESC')->find();
            if (empty($flipRes)) {
                $this->ajaxReturn(MsgConst::DATA_EMPTY, '左边翻不动了哟，试试右边呢 ？');
            }
            $articleId = $flipRes['id'];
            if (Request::instance()->isAjax()) {
                $this->ajaxReturn(MsgConst::SUCCESS_CODE, MsgConst::SUCCESS_MSG, ['page' => $articleId]);
            }
        }
        if ($flip == 'turn_right') {
            $flipRes = Db::name('article_publish')->field('id')->where([
                'id'        => ['GT', $articleId],
                'ac_status' => ['NEQ', MsgConst::DELETE_CODE],
            ])->order('id ASC')->find();
            if (empty($flipRes)) {
                $this->ajaxReturn(MsgConst::DATA_EMPTY, '右边翻不动了哟，试试左边呢 ？');
            }
            $articleId = $flipRes['id'];
            if (Request::instance()->isAjax()) {
                $this->ajaxReturn(MsgConst::SUCCESS_CODE, MsgConst::SUCCESS_MSG, ['page' => $articleId]);
            }
        }
        if (!_checkInt($articleId)) {
            $this->_empty();
        }
        $IndexModel = Loader::model('Index');
        $articleDetails = $IndexModel->getArticleDetails($articleId);
        if ($articleDetails['retCode'] != MsgConst::SUCCESS_CODE) {
            $this->_empty();
        }
        $articleDetails = $articleDetails['data'];
        return $this->view->fetch('index/detail', ['articleDetails' => $articleDetails]);
    }


}

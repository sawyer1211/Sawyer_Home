<?php
/**
 * Created by Sawyer_Home.
 * User: Sawyer Yang
 * Date: 2017/5/8
 * Time: 17:02
 */

namespace app\home\model;


use app\common\consts\MsgConst;
use app\common\model\BaseModel;
use think\Db;

class Index extends BaseModel
{
    protected $name = 'article_publish';
    private $ArticleModel;

    //自定义初始化
    protected function initialize()
    {
        //需要调用`Model`的`initialize`方法
        parent::initialize();
        //TODO:自定义的初始化
        $this->ArticleModel = Db::name($this->name);
    }


    public function getArticleList($where = [], $orderBy = [])
    {
        if (empty($where) || !is_array($where)) {
            $where = [
                'ac.ac_status' => ['NEQ', MsgConst::DELETE_CODE],
            ];
        }
        if (empty($orderBy) || !is_array($orderBy)) {
            $orderBy = [
                'ac.ac_create_time' => 'DESC',
                'ac.ac_sort'        => 'ASC',
                'ac.id'             => 'DESC',
            ];
        }
        $result = $this->ArticleModel->fetchSql(false)->alias('ac')->field('ac.id AS id,ac.ac_title AS title,ac.ac_content AS content,lb.lb_name AS label_name,ac.ac_create_time AS create_time')->join("__ARTICLE_LABEL__ lb", "lb.id=ac.ac_label", 'LEFT')->where($where)->order($orderBy)->select();
//        dump($result);die;
        if (empty($result)) {
            return $this->arrayReturn(MsgConst::DATA_EMPTY, '暂无数据');
        } else {
            return $this->arrayReturn(MsgConst::SUCCESS_CODE, '查询成功', $result);
        }
    }

    public function getArticleDetails($id)
    {
        $result = $this->ArticleModel->fetchSql(false)->alias('ac')->field('ac.id AS id,ac.ac_title AS title,ac.ac_content AS content,lb.lb_name AS label_name,ac.ac_create_time AS create_time')->join("__ARTICLE_LABEL__ lb", "lb.id=ac.ac_label", 'LEFT')->where([
            'ac.id'        => ['EQ', $id],
            'ac.ac_status' => ['NEQ', MsgConst::DELETE_CODE],
        ])->find();
        if (empty($result)) {
            return $this->arrayReturn(MsgConst::DATA_EMPTY, '暂无数据');
        } else {
            return $this->arrayReturn(MsgConst::SUCCESS_CODE, '查询成功', $result);
        }
    }

}
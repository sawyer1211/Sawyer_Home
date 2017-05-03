<?php

namespace app\common\controller;

use app\common\tools\LogUtils;
use think\Cache;
use think\Config;
use think\Controller;
use think\Db;
use think\Session;
use think\View;

class BaseController extends Controller
{
    protected $START_TIME = ''; // 程序开始执行的时间
    protected $arrUserInfo = [];
    private $current_action_name;
    private $current_controller_name;

    private $access_permission = [
        'Index' => [
            'index',
        ],
        'User'  => [

        ],
    ];

    protected function createToken($uid, $apiId)
    {
        $time = time();
        $str = $apiId . $uid . $this->apikey . $time;
        $token = _myMd5($str, 4);

        return $token;
    }

    public function _initialize()
    {
        $this->START_TIME = explode(' ', microtime());
        $view = new View();
        $this->current_action_name = $this->request->action();
        $this->current_controller_name = $this->request->controller();
        $arrWebPage = $this->getWebPageInfo();                              // 获取网站信息
        $this->assign('arrWebPage', $arrWebPage);                    // 定义变量
        $this->checkAuth();                                                 // 自动验证登陆信息
    }

    private function checkAuth()
    {
        $this->arrUserInfo = Session::get('withsawyer_user_info');
        if (array_key_exists($this->current_controller_name, $this->access_permission)) {
            if (in_array($this->current_action_name, $this->access_permission[$this->current_controller_name])) {
                if (false === Session::has('withsawyer_user_info') || empty($this->arrUserInfo['uid'])) {
                    $this->redirect("User/login");
                }
            }
        } else {
            $this->_empty();
        }
    }

    /**
     * 获取网站信息
     * @param bool $refresh 是否刷新缓存
     * @return array|mixed
     */
    private function getWebPageInfo($refresh = false)
    {
        Cache::connect(Config::get('cache'));
        $arrWebPage = Cache::get('arrWebPage');
        if (empty($arrWebPage) || false === $arrWebPage || true === $refresh) {
            $arrWebPage = [];
            $objParamSettingModel = Db::name('system_param_setting');
            $objWebPageRes = $objParamSettingModel->field('set_name,set_value')->where('set_type', 'webpage')->select();
            foreach ($objWebPageRes AS $setValue) {
                $arrWebPage[$setValue['set_name']] = $setValue['set_value'];
            }
            Cache::set('arrWebPage', $arrWebPage);
        }
        return $arrWebPage;
    }

    /**
     * @param mixed  $retCode 错误代码
     * @param string $retMsg 错误信息
     * @param array  $data 返回参数
     * @param array  $explain 开发模式才返回【参数说明】
     */
    protected function ajaxReturn($retCode, $retMsg, $data = [], $explain = [])
    {
        $returnData = [
            'retCode' => $retCode,
            'retMsg'  => urlencode($retMsg),
        ];
        if ($data) {
            $returnData['data'] = _dataToUrlEncode(_arrayFilterNull($data));
        }
        if (Config::get('isTest') == 1) {
            // 开发模式才返回【参数说明】
            if ($explain) {
                $returnData['explain'] = _dataToUrlEncode(_arrayFilterNull($explain));
            }
            // 程序总共执行多少时间
            $END_TIME = explode(' ', microtime());
            $USE_TIME = $END_TIME[0] + $END_TIME[1] - ($this->START_TIME[0] + $this->START_TIME[1]);
            $returnData['use_time'] = '总耗时' . round($USE_TIME, 5) . '秒';
        }
        LogUtils::log("接口响应：【" . urldecode(json_encode($returnData)) . "】", '', './log/response/');
        // 返回JSON数据格式到客户端 包含状态信息
        header('Content-Type:application/json; charset=utf-8');
        exit(urldecode(json_encode($returnData)));
    }

    public function _empty()
    {
        return $this->view->fetch('common/404');
    }


}
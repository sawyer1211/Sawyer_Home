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
    protected $START_TIME = '';             // 程序开始执行的时间
    protected $arrWebPage = [];             // 网站基本信息
    protected $session_user_info_name = []; // 用户信息session名称
    protected $arrUserInfo = [];            // 用户登陆信息
    private $current_controller_name;       // 当前操作的控制器
    private $current_action_name;           // 当前操作的方法名
    protected $nowTime = '';
    protected $test = 0;                    // 当前项目模式【0-生产模式 1-开发模式】

    private $access_permission = [
        'Index' => [
            '',
        ],
        'User'  => [
            'login', 'register', 'sendVerify', 'validateCodeImage', 'resume', 'shuKeResume', 'shuKeResumeSendEmail',
        ],
    ];

    protected function createToken($uid, $apiId)
    {
        $time = time();
        $str = $apiId . $uid . $this->apikey . $time;
        $token = _myMd5($str, 4);

        return $token;
    }


    /**
     * 先执行一些配置的方法
     */
    public function _initialize()
    {
        // 当前项目模式【0-生产模式 1-开发模式】
        $this->test = Config::get('isTest');
        // 记录一下网站访问量
        if ($this->test != 1) {
            $this->_resumeRecord('withsawyer');
        }
        // 当前时间戳
        $this->nowTime = time();
        // 程序开始的微秒
        $this->START_TIME = explode(' ', microtime());
        $this->current_controller_name = $this->request->controller();
        $this->current_action_name = $this->request->action();
        // 获取网站信息
        $this->arrWebPage = $this->getWebPageInfo(true);
        // 定义网站信息变量
        $this->assign('arrWebPage', $this->arrWebPage);
        // 自动验证登陆信息
        $this->checkAuth();
    }

    /**
     * 检查权限信息
     */
    private function checkAuth()
    {
        // 获取用户session信息
        $this->session_user_info_name = Config::get('SESSION_USER_INFO_NAME');
        $this->arrUserInfo = Session::get($this->session_user_info_name);
        // 检查当前访问的控制器是否存在
        if (array_key_exists($this->current_controller_name, $this->access_permission)) {
            // 检查当前访问的方法名如果不在定义中就需要登陆
            if (!in_array($this->current_action_name, $this->access_permission[$this->current_controller_name])) {
                if (false === Session::has($this->session_user_info_name) || !_checkInt($this->arrUserInfo['uid'])) {
                    if ($this->test != 1) {
                        $this->redirect(url('/login/view'));
                    }
                }
            } elseif (in_array($this->current_action_name, $this->access_permission[$this->current_controller_name])) {
                if (true === Session::has($this->session_user_info_name) || _checkInt($this->arrUserInfo['uid'])) {
                    if ($this->test != 1) {
                        $this->redirect(url('/index'));
                    }
                }
            }
        } else {
            // 否则404
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
        $this->arrWebPage = Cache::get('arrWebPage');
        if (empty($this->arrWebPage) || false === $this->arrWebPage || true === $refresh) {
            $this->arrWebPage = [];
            $objParamSettingModel = Db::name('system_param_setting');
            $objWebPageRes = $objParamSettingModel->field('set_name,set_key,set_value')->where('set_type', 'webpage')->select();
            foreach ($objWebPageRes AS $setValue) {
                $this->arrWebPage[$setValue['set_key']] = $setValue['set_value'];
            }
            Cache::set('arrWebPage', $this->arrWebPage);
        }
        return $this->arrWebPage;
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
        if ($this->test == 1) {
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


    /**
     * 记录一下下访问量
     * @param $name
     * @return int|string
     */
    protected function _resumeRecord($name)
    {
        $result = Db::name('resume_record')->fetchSql(false)->where([
            'rs_name' => $name,
        ])->inc('rs_times')->exp('rs_time', 'now()')->update();
        return $result;

    }


}
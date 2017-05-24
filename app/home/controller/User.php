<?php
/**
 * Created by 1211.withsawyer.
 * User: Sawyer Yang
 * Date: 2017/4/18
 * Time: 16:01
 */

namespace app\home\controller;


use app\common\consts\UserConst;
use app\common\controller\BaseController;
use app\common\consts\MsgConst;
use app\common\tools\LogUtils;
use app\common\tools\ValidateCode;
use think\Config;
use think\Cookie;
use think\Db;
use think\Loader;
use think\Request;
use think\Session;

class User extends BaseController
{

//    public function __destruct()
//    {
//        if (Config::get('isTest') == 1) {
//            // 程序总共执行多少时间
//            $END_TIME = explode(' ', microtime());
//            $USE_TIME = $END_TIME[0] + $END_TIME[1] - ($this->START_TIME[0] + $this->START_TIME[1]);
//            echo '总耗时' . round($USE_TIME, 5) . '秒';
//        }
//    }

    public function login()
    {
        $visitType = Request::instance()->param('visit');
        $errRetryNum = Cookie::get('errRetryNum') ?: 0;
        if ($visitType == 'view') {
            $footer_text = [
                0 => [
                    'en' => 'If it is not too concerned about, and how they will lose their temper.',
                    'cn' => '若不是太在意，又怎会乱发脾气',
                ],
                1 => [
                    'en' => 'If we can only encounter each other rather than stay with each other,then I wish we had never encountered.',
                    'cn' => '如果只是遇见，不能停留，不如不遇见。',
                ],
                2 => [
                    'en' => 'People always can not because of the beautiful moonlight, and has been wandering in the dark.',
                    'cn' => '人总不能因为贪恋月色的美好，而一直徘徊于黑夜之中。',
                ],
            ];
            $footer_text = $footer_text[mt_rand(0, 2)];
            return $this->view->fetch('user/login', ['footer_text' => $footer_text, 'errRetryNum' => $errRetryNum]);
        } elseif ($visitType == 'doLogin') {
//            $errRetryNum = Cookie::get('errRetryNum') ?: 0;
            $account = Request::instance()->post('account');              // 帐号（用户名或邮箱）
            $password = Request::instance()->post('password');            // 密码
            // 验证数据正确性
            if (!_checkUserName($account) && !_checkEmail($account)) {
                $this->ajaxReturn(MsgConst::LEGAL_CODE, '请检查帐号格式是否正确！');
            }
            if (empty($password)) {
                $this->ajaxReturn(MsgConst::LEGAL_CODE, '请输入密码');
            }
            if (mb_strlen($password) < 8 || mb_strlen($password) > 20) {
                $this->ajaxReturn(MsgConst::LEGAL_CODE, '密码不能小于8位或大于20位');
            }
            // 获取到登陆失败的次数如果大于三次就要输入验证码
            if ($errRetryNum > 3) {
                $verify_code = Request::instance()->post('verify_code');      // 验证码
                // 判断是否填写验证码
                if (empty($verify_code)) {
                    $this->ajaxReturn(MsgConst::LEGAL_CODE, '请输入验证码');
                }
                // 判断验证码是否正确
                if ($verify_code != Session::get('GraphicValidateCode')) {
                    $this->ajaxReturn(MsgConst::LEGAL_CODE, '验证码不正确');
                }
            }
            // 密码加密一下
            $password = _myMd5($password, Config::get('PASS_ENCRYPT_TIMES'));
            $checkUserRes = Db::name('user_manage')->fetchSql(false)->where('u_state', 'NEQ', MsgConst::DELETE_CODE)->where('u_password', 'EQ', $password)->whereOr('u_email', 'EQ', $account)->where("u_user_name", 'EQ', $account)->find();
            if (!$checkUserRes) {
                // 登录出错 重试次数+1 等于3的时候就需要验证码了
                $errRetryNum += 1;
                Cookie::set('errRetryNum', $errRetryNum);
                $this->ajaxReturn(MsgConst::FAIL_CODE, '帐号或密码错误，请重试...', [
                    'errRetryNum' => $errRetryNum,
                ]);
            }

            $userRunningData = [
                'ur_uid'        => $checkUserRes['uid'],
                'ur_user_name'  => $checkUserRes['u_user_name'],
                'ur_client_ip'  => _getClientIp(),
                'ur_login_time' => $this->nowTime,
                'ur_note'       => '用户登陆',
            ];
            LogUtils::userRunningLog($userRunningData);
            $sessionData = [
                'uid'       => $checkUserRes['uid'],
                'user_name' => $checkUserRes['u_user_name'],
            ];
            // 存入登陆信息
            Session::set($this->session_user_info_name, $sessionData);
            // 登录成功后重试次数就清零
            Cookie::set('errRetryNum', 0);
            $this->ajaxReturn(MsgConst::SUCCESS_CODE, '注册成功，页面即将跳转...');
        } else {
            $this->_empty();
        }

    }

    public function sendVerify()
    {
        // 发送验证码类型
        $sendVerifyType = Config::get('SEND_VERIFY_TYPE');

        // 发送验证码类型对应状态
        $sendVerifyTypeCorrespondingValue = Config::get('SEND_VERIFY_TYPE_CORRESPONDING_VALUE');
        // 获取验证码模板名称
        $BtnVerifyCode = Request::instance()->post('btn_verify_code');
        $verifyTemplateName = _paramHandle($BtnVerifyCode, 'DECODE');
        if (empty($verifyTemplateName)) {
            $this->ajaxReturn(MsgConst::LEGAL_CODE, '模板信息不存在');
        }
        // 查询出验证码模板
        $verityTemplateWhere = [
            'verify_name'  => ['EQ', $verifyTemplateName],
            'verify_type'  => ['EQ', $sendVerifyTypeCorrespondingValue[$sendVerifyType]],
            'verify_state' => ['NEQ', MsgConst::DELETE_CODE],
        ];
        $verityTemplateResult = Db::name('verify_templates')->where($verityTemplateWhere)->find();
        if (!$verityTemplateResult) {
            $this->ajaxReturn(MsgConst::LEGAL_CODE, '模板不存在');
        }
        // 生成六位数的验证码
        $verifyCode = _randString(6);
        // 生成十位数的校验码
        $checkCode = _randString(10, 99);
        if ($sendVerifyType == 'MOBILE') {
            //TODO 发送手机短信验证码
        } elseif ($sendVerifyType == 'EMAIL') {
            $emailArr = [];
            // 获取发送邮件的地址并判断邮箱的合法性
            $sendAddress = Request::instance()->post('email_address');
            if (!_checkEmail($sendAddress)) {
                $this->ajaxReturn(MsgConst::LEGAL_CODE, '请输入正确的邮箱地址');
            }
            // 获取模板HTML代码并替换掉里面对应的内容
            $emailContent = file_get_contents(__PUBLIC__ . '/' . $verityTemplateResult['verify_template_url']);
            $emailContent = str_replace('{background_image}', __PUBLIC__ . 'images/email_template/background_image.jpg?' . mt_rand(1, 99), $emailContent);
            $emailContent = str_replace('{verify_code}', $verifyCode, $emailContent);
            // 邮件主题
            $emailArr['addAddress'] = $sendAddress;
            // 邮件主题
            $emailArr['subject'] = 'Welcome to ' . Config::get('APP_NAME');
            // 邮件内容
            $emailArr['content'] = $emailContent;
            // 存入验证码信息
            $doValidateRes = $this->_doValidate($sendAddress, $verifyCode, $checkCode, $verityTemplateResult['verify_name'], _getClientIp());
            if (MsgConst::SUCCESS_CODE === $doValidateRes['retCode']) {
//                 信息存入成功才发送验证码(生产模式才发送邮件)
//                if ($this->test == 1) {
//                    $sendVerifyAction = true;
//                } else {
//                    $sendVerifyAction = _sendEmail($emailArr);
//                }
                $sendVerifyAction = _sendEmail($emailArr);
            } else {
                $this->ajaxReturn($doValidateRes['retCode'], $doValidateRes['retMsg']);
                $sendVerifyAction = false;
            }
            if (true === $sendVerifyAction) {
                // 校验码存入session用于比对验证码使用
                Session::set('checkCode', $checkCode);
                $this->ajaxReturn(MsgConst::SUCCESS_CODE, '验证码发送成功');
            } else {
                $this->ajaxReturn(MsgConst::FAIL_CODE, '验证码发送失败');
            }
        } else {
            $this->ajaxReturn(MsgConst::FAIL_CODE, '找不到发送验证码的方式');
        }
    }

    /**
     * 用户注册
     * @return string
     */
    public function register()
    {
        $visitType = Request::instance()->param('visit');
        if ($visitType == 'view') {
            // 加密后按钮的验证码 避免恶意发送邮箱(一般按钮的参数就是模板名称)
            $BtnVerifyCode = _paramHandle('user_register', 'ENCODE', _randString(mt_rand(2, 8), 99));
            return $this->view->fetch('user/register', ['BtnVerifyCode' => $BtnVerifyCode]);
        } elseif ($visitType == 'doRegister') {
            $nickname = Request::instance()->post('nickname') ?: '匿名用户';  // 昵称
            $user_name = Request::instance()->post('user_name');              // 用户名
            $password = Request::instance()->post('password');                // 密码
            $re_password = Request::instance()->post('re_password');          // 确认密码
            $email = Request::instance()->post('email');                      // 邮箱地址
            $verifyCode = Request::instance()->post('verify_code');           // 验证码
            $clientIp = _getClientIp();
            $checkCode = Session::get('checkCode');                           // 校验码
            // 验证数据正确性
            if (!_checkUserName($user_name)) {
                $this->ajaxReturn(MsgConst::LEGAL_CODE, '请输入正确用户名');
            }
            if (empty($password)) {
                $this->ajaxReturn(MsgConst::LEGAL_CODE, '请输入密码');
            }
            if (empty($re_password)) {
                $this->ajaxReturn(MsgConst::LEGAL_CODE, '请再次输入密码');
            }
            if (mb_strlen($password) < 8 || mb_strlen($password) > 20) {
                $this->ajaxReturn(MsgConst::LEGAL_CODE, '密码不能小于8位或大于20位');
            }
            if ($password != $re_password) {
                $this->ajaxReturn(MsgConst::LEGAL_CODE, '两次密码不相符');
            }
            // 检查验证码是否正确
            $checkVerifyCode = $this->_checkVerifyCode($email, $verifyCode, $checkCode, 'user_register', Config::get('SEND_VERIFY_TYPE'));
            if ($checkVerifyCode['retCode'] != MsgConst::SUCCESS_CODE) {
                $this->ajaxReturn($checkVerifyCode['retCode'], $checkVerifyCode['retMsg']);
            }
            // 加密密码（不可随意更改）
            $password = _myMd5($password, Config::get('PASS_ENCRYPT_TIMES'));
            $arrDoRegisterData = [
                'u_nickname'    => $nickname,
                'u_user_name'   => $user_name,
                'u_email'       => $email,
                'u_password'    => $password,
                'u_create_time' => $this->nowTime,
                'u_is_test'     => $this->test,
                'u_state'       => UserConst::USER_NORMAL,
            ];
            $userModel = Loader::model('User');
            $arrResult = $userModel->doRegister($arrDoRegisterData);
            $uid = $userModel->getLastInsID();
            if ($arrResult['retCode'] != MsgConst::SUCCESS_CODE) {
                $this->ajaxReturn($arrResult['retCode'], $arrResult['retMsg']);
            }
            $userRunningData = [
                'ur_uid'        => $uid,
                'ur_user_name'  => $user_name,
                'ur_client_ip'  => $clientIp,
                'ur_login_time' => $this->nowTime,
                'ur_note'       => '用户注册成功自动登陆',
            ];
            LogUtils::userRunningLog($userRunningData);
            $sessionData = [
                'uid'       => $uid,
                'user_name' => $user_name,
            ];
            // 存入登陆信息
            Session::set($this->session_user_info_name, $sessionData);
            // 登录成功后重试次数就清零
            Cookie::set('errRetryNum', 0);
            $this->ajaxReturn(MsgConst::SUCCESS_CODE, '注册成功，页面即将跳转...');
        } else {
            $this->_empty();
        }
    }


    /**
     * 生成验证码图片
     */
    public function validateCodeImage()
    {
        Loader::import('ValidateCodeTool', APP_PATH . 'common/tools/');
        $ValidateCode = new ValidateCode(4, 165, 42);
        $ValidateCode->doimg();
        $strLoginValidateCode = $ValidateCode->getCode();
        Session::set('GraphicValidateCode', $strLoginValidateCode);
        return $strLoginValidateCode;
    }

    /**
     * 登出
     */
    public function logout()
    {
        Session::set($this->session_user_info_name, Null);
        $this->redirect(url('/login/view'));
    }

    /**
     * 验证验证码是否正确
     * @param        $source
     * @param        $verifyCode
     * @param        $checkCode
     * @param        $tempName
     * @param string $sendType
     * @return array
     */
    private function _checkVerifyCode($source, $verifyCode, $checkCode, $tempName, $sendType = 'EMAIL')
    {

        $validateModel = Db::name('verify_validate');
        $where = [
            'v_source'     => $source,
            'v_code'       => $verifyCode,
            'v_check_code' => $checkCode,
            'v_temp_name'  => $tempName,
        ];
        $data = $validateModel->fetchSql(false)->where($where)->find();
        $returnMsg = [];
        if (!$data) {
            $returnMsg['retCode'] = MsgConst::FAIL_CODE;
            $returnMsg['retMsg'] = "验证码错误!";
            return $returnMsg;
        }
        $nowTime = time();
        $checkTime = Config::get('VERIFY_CODE_VALID_TIME') * 60;
        $sendTime = $data['v_time'];
        if ((intval($nowTime) - intval($sendTime)) > $checkTime) {
            $returnMsg['retCode'] = MsgConst::FAIL_CODE;
            $returnMsg['retMsg'] = "验证码已过期!";

            return $returnMsg;
        }
        $returnMsg['retCode'] = MsgConst::SUCCESS_CODE;
        return $returnMsg;
    }


    /**
     * 记录发送验证码的信息
     * @param $source
     * @param $code
     * @param $checkCode
     * @param $tempName
     * @param $clientIp
     * @return array
     */
    private function _doValidate($source, $code, $checkCode, $tempName, $clientIp)
    {
        if (empty($source) || empty($code) || empty($checkCode) || empty($tempName)) {
            return [
                'retCode' => MsgConst::FAIL_CODE,
                'retMsg'  => '发送失败',
            ];
        }

        // 检查邮箱是否被注册
        $checkUserRes = Db::name('user_manage')->where('u_state', 'NEQ', MsgConst::DELETE_CODE)->where('u_email', 'EQ', $source)->count();
        if ($checkUserRes > 0) {
            return [
                'retCode' => MsgConst::FAIL_CODE,
                'retMsg'  => '邮箱已被注册',
            ];
        }
        $nowTime = time();
        $verifyValidateModel = Db::name('verify_validate');
        $findRes = $verifyValidateModel->fetchSql(false)->where([
            'v_source' => ['EQ', $source],
        ])->whereOr([
            'v_client_ip' => ['EQ', $clientIp],
        ])->order('id desc')->find();
        // 十秒内不要频繁发送
        $VERIFY_SEND_INTERVAL_TIMES = Config::get('VERIFY_SEND_INTERVAL_TIMES');
        if ($findRes && (intval($findRes['v_time']) > intval($nowTime + $VERIFY_SEND_INTERVAL_TIMES))) {
            return [
                'retCode' => MsgConst::FAIL_CODE,
                'retMsg'  => '不要过于频繁发送',
            ];
        }
        $data = [
            'v_source'     => $source,
            'v_code'       => $code,
            'v_check_code' => $checkCode,
            'v_time'       => $nowTime,
            'v_temp_name'  => $tempName,
            'v_client_ip'  => $clientIp,
        ];
        $result = $verifyValidateModel->insert($data);
        if ($result === false) {
            return [
                'retCode' => MsgConst::FAIL_CODE,
                'retMsg'  => '发送失败',
            ];
        } else {
            return [
                'retCode' => MsgConst::SUCCESS_CODE,
                'retMsg'  => '发送成功',
            ];
        }

    }

    public function resume()
    {
        return $this->view->fetch('resume/index');
    }

    public function shuKeResume()
    {
        return $this->view->fetch('resume/shuke');
    }

    public function shuKeResumeSendEmail()
    {

        $address = 'shuke@withsawyer.cn';
        $subject = '舒克懒懒，有人想联系联系你';
        $send_name = trim(input('post.name'));
        $send_email = trim(input('post.email'));
        $send_message = trim(input('post.message'));
        $client_ip = _getClientIp();
        // 判断一下数据合法性
        if (empty($send_name)) {
            $this->ajaxReturn(MsgConst::FAIL_CODE, "<div class='error_message'>请填写姓名</div>");
        }
        if (!_checkEmail($send_email)) {
            $this->ajaxReturn(MsgConst::FAIL_CODE, "<div class='error_message'>请填写正确的Emai</l地址");
        }
        if (empty($send_message)) {
            $this->ajaxReturn(MsgConst::FAIL_CODE, "<div class='error_message'>请输入想对我说的内容</div>");
        }
        Request::instance()->ip();
        $runningModel = Db::name('verify_validate');
        $runningData = $runningModel->field('v_time')->where([
            'v_source' => ['EQ', $send_email],
        ])->order('id DESC')->find();

        $runningData2 = $runningModel->field('v_time')->where([
            'v_client_ip' => ['EQ', $client_ip],
        ])->order('id DESC')->find();
        if ($this->nowTime - $runningData['v_time'] <= 60) {
            $this->ajaxReturn(MsgConst::FAIL_CODE, "<div class='error_message'>请不要频繁发送消息</div>");
        }
        if ($this->nowTime - $runningData2['v_time'] <= 60) {
            $this->ajaxReturn(MsgConst::FAIL_CODE, "<div class='error_message'>请不要频繁发送消息</div>");
        }
        // 拼接要发送的参数
        $send_msn = '姓名：' . $send_name . '<br/>Email：' . $send_email . '<br/><br/>消息：' . $send_message;
        $param = [
            'addAddress' => $address,
            'subject'    => $subject,
            'content'    => $send_msn,
        ];
        // 记录一下日志
        $runningModel->insert([
            'v_source'     => $send_email,
            'v_code'       => '舒克懒懒的简历',
            'v_check_code' => '有人发邮件给她',
            'v_time'       => $this->nowTime,
            'v_temp_name'  => '我记录一下',
            'v_client_ip'  => $client_ip,
        ]);
        $result = _sendEmail($param);
        if (!$result) {
            $this->ajaxReturn(MsgConst::FAIL_CODE, '发送失败');
        } else {
            $this->ajaxReturn(MsgConst::SUCCESS_CODE, '发送成功');
        }
    }

}
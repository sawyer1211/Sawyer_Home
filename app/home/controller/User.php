<?php
/**
 * Created by 1211.withsawyer.
 * User: Sawyer Yang
 * Date: 2017/4/18
 * Time: 16:01
 */

namespace app\home\controller;


use app\common\controller\BaseController;
use app\common\consts\MsgConst;
use think\Config;
use think\Db;
use think\Loader;
use think\Request;
use think\Session;

class User extends BaseController
{

    public function login()
    {
        return $this->view->fetch('user/login');
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
            $doValidateRes = $this->doValidate($sendAddress, $verifyCode, $checkCode, $verityTemplateResult['verify_name'], _getClientIp());
            if (MsgConst::SUCCESS_CODE === $doValidateRes['retCode']) {
                // 信息存入成功才发送验证码
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
            $verify = Request::instance()->post('verify');                    // 验证码
            $checkCode = Session::get('checkCode');                                 // 校验码
            if (empty($user_name)) {
                $this->ajaxReturn(MsgConst::LEGAL_CODE, '请输入用户名');
            }
            if (empty($password)) {
                $this->ajaxReturn(MsgConst::LEGAL_CODE, '请输入密码');
            }
            if (empty($re_password)) {
                $this->ajaxReturn(MsgConst::LEGAL_CODE, '请再次输入密码');
            }
            if (empty($re_password)) {
                $this->ajaxReturn(MsgConst::LEGAL_CODE, '请再次输入密码');
            }
            $userModel = Loader::model('User');
            $arrResult = $userModel->doRegister($nickname, $user_name, $password, $re_password, $email, $verify);
        } else {

        }
    }


    /**
     * 验证验证码是否正确
     * @param string $tel
     * @param string $verifyCode
     * @param string $check_code
     * @param string $type
     * @return array
     */
    private function checkVerifyCode($tel, $verifyCode, $check_code, $type)
    {

        $validateModel = M('sms_validate');
        $data = $validateModel->where("v_tel='$tel' AND v_code='$verifyCode' AND v_list='$check_code' AND v_class='$type'")->find();
        $returnMsg = [];
        if (!$data) {
            $returnMsg['retCode'] = false;
            $returnMsg['retMsg'] = "验证码错误!";

            return $returnMsg;
        }
        $nowTime = time();
        $checkTime = C('MOBILE_VERIFY_VALID_TIME') * 60;
        $sendTime = $data['v_time'];
        if (($nowTime - $sendTime) > $checkTime) {
            $returnMsg['retCode'] = false;
            $returnMsg['retMsg'] = "验证码已过期!";

            return $returnMsg;
        }
        $returnMsg['retCode'] = true;

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
    private function doValidate($source, $code, $checkCode, $tempName, $clientIp)
    {
        if (empty($source) || empty($code) || empty($checkCode) || empty($tempName)) {
            return [
                'retCode' => MsgConst::FAIL_CODE,
                'retMsg'  => '发送失败',
            ];
        }
        $nowTime = time();
        $verifyValidateModel = Db::name('verify_validate');
        $findRes = $verifyValidateModel->field('')->where([
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

}
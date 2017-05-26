/**
 * Created by Sawyer Yang on 2017/4/26.
 */
$(document).ready(function () {
    var verify_btn_flag = true;

    // 发送邮箱验证码
    $('#Verify_btn').click(function () {
        var Email = $('#Email').val();
        var btnVerifyCode = $(this).data('btn-verify-code');
        if (!_checkEmail(Email)) {
            showMsg('请输入正确的邮箱地址');
            return;
        }
        if (btnVerifyCode == '') {
            showMsg('验证码发送失败？');
            return;
        }
        if (verify_btn_flag === true) {
            verify_btn_flag = false; // 防止同时多次发送
            settime('Verify_btn'); // 按钮设置倒计时
            $.post(web_url + 'sendVerify', {
                email_address: Email,
                btn_verify_code: btnVerifyCode
            }, function (json) {
                console.log(json);
                if (json.retCode == 1) {
                    showMsg(json.retMsg);
                } else {
                    showMsg(json.retMsg);
                }
                verify_btn_flag = true;
            });
        }
    });

    // 关联回车按键确认//
    $(document).on('keydown', function (event) {
        if (event.keyCode == 13) {
            var $loginBtn = $('#do-login-btn');
            var $registerBtn = $('#register-btn');
            if ($loginBtn.length > 0) {
                $loginBtn.click();
                return false;
            }
            if ($registerBtn.length > 0) {
                $registerBtn.click();
                return false;
            }
        }
    });

    // 提交注册信息
    $(document).on('click', '#register-btn', function () {
        // var $nickname = $('#nickname');
        var $user_name = $('#user_name');
        var $password = $('#password');
        var $re_password = $('#re_password');
        var $Email = $('#Email');
        var $verify_code = $('#verify_code');
        if (!_checkUserName($user_name.val())) {
            $user_name.focus();
            showMsg('请输入正确的用户名');
            return false;
        }
        if ($password.val() == '') {
            $password.focus();
            showMsg('请输入密码');
            return false;
        }
        if ($password.val().length < 8 || $password.val().length > 20) {
            $password.focus();
            showMsg('密码不能小于8位或大于20位');
            return false;
        }
        if ($re_password.val() == '') {
            $re_password.focus();
            showMsg('请再次输入密码');
            return false;
        }
        if ($password.val() != $re_password.val()) {
            $re_password.focus();
            showMsg('两次密码不相符');
            return false;
        }
        if (!_checkEmail($Email.val())) {
            $Email.focus();
            showMsg('请输入正确的邮箱地址');
            return false;
        }
        if ($verify_code.val() == '') {
            $verify_code.focus();
            showMsg('请输入验证码');
            return false;
        }
        // 加载中......
        var loadIndex = layer.load(2);
        $.post(web_url + 'doRgt/doRegister.html', {
            user_name: $user_name.val(),
            password: $password.val(),
            re_password: $re_password.val(),
            email: $Email.val(),
            verify_code: $verify_code.val(),
        }, function (json) {
            if (json.retCode == 1) {
                layer.msg(json.retMsg, {time: 2000}, function () {
                    window.location.href = web_url + 'index.html';
                })
            } else {
                showMsg(json.retMsg);
                layer.close(loadIndex);
            }
        });
    });

    // 提交登录信息
    $(document).on('click', '#do-login-btn', function () {
        // var $nickname = $('#nickname');
        var $account = $('#account');
        var $password = $('#password');
        if (!_checkUserName($account.val()) && !_checkEmail($account.val())) {
            $account.focus();
            showMsg('请输入用户名或者邮箱');
            return false;
        }
        if ($password.val() == '') {
            $password.focus();
            showMsg('请输入密码');
            return false;
        }
        if ($password.val().length < 8 || $password.val().length > 20) {
            $password.focus();
            showMsg('密码不能小于8位或大于20位');
            return false;
        }
        // 加载中......
        var loadIndex = layer.load(2);
        $.post(web_url + 'doLogin/doLogin.html', {
            account: $account.val(),
            password: $password.val(),
        }, function (json) {
            if (json.retCode == 1) {
                layer.msg(json.retMsg, {time: 2000}, function () {
                    window.location.href = web_url + 'index.html';
                });
                return false;
            } else {
                layer.msg(json.retMsg, {time: 2000}, function () {
                    layer.closeAll();
                });
                if (json['data']['errRetryNum'] > 3) {
                    $('#Graphic-Verify-Div').show();
                }
                return false;
            }
        });

    });

    // 局部刷新验证码
    $(document).on('click', '.validate-Code-Image', function () {
        var url = $(this).data('url');
        var newUrl = '';
        if (url == '') {
            return false;
        }
        newUrl = url + '?' + Math.floor(Math.random() * 10) + Math.floor(Math.random() * 10);
        $(this).attr('src', newUrl);
    });

});
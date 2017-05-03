/**
 * Created by Sawyer Yang on 2017/4/26.
 */
$(document).ready(function () {
    var verify_btn_flag = true;

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
                if (json.retCode == '1') {
                    showMsg(json.retMsg);
                } else {
                    showMsg(json.retMsg);
                }
                verify_btn_flag = true;
            });
        }
    })

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
        // var loadIndex = layer.load(2);
        $.post(web_url + 'doRgt/doRegister.html', {
            user_name: $user_name.val(),
            password: $password.val(),
            re_password: $re_password.val(),
            email: $Email.val(),
            verify_code: $verify_code.val(),
        }, function (json) {
            console.log(json);

        });

    });

});
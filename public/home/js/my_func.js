/**
 * Created by Sawyer Yang on 2017/4/28.
 */


/**
 * 发送验证码按钮定时事件
 * @param element
 */
var countdown = 120;
function settime(element) {
    var obj_send = $("#" + element);
    if (countdown == 0) {
        obj_send.attr("disabled", false);
        obj_send.text("重新获取验证码");
        countdown = 120;
        return;
    } else {
        obj_send.unbind("mouseenter").unbind("mouseleave");
        obj_send.attr("disabled", true);
        obj_send.text("重新发送(" + countdown + ")");
        countdown--;
    }
    setTimeout(function () {
        settime(element)
    }, 1000);
}

/**
 * 验证邮箱的合法性
 * @param email
 * @return int
 */
function _checkEmail(email) {
    return /^[a-z0-9]+([._\\-]*[a-z0-9])*@([a-z0-9]+[-a-z0-9]*[a-z0-9]+.){1,63}[a-z0-9]+$/.test(email);
}

/**
 * 判断是否是正整数
 * @param number
 * @return int
 */
function _checkInt(number) {
    return /^[1-9][0-9]*$/.test(number);
}

/**
 * 判断用户名的合法性
 * @param user_name
 * @return int
 */
function _checkUserName(user_name) {
    return /^[a-zA-Z]\w{4,19}$/.test(user_name);
}

/**
 * 自动关闭的提示弹窗
 * @param content 内容
 * @param time 自动关闭时间
 */
function showMsg(content, time) {
    layer.msg(content, {
        time: time ? time : 2000
    });
}
/**
 * Created by Sawyer Yang on 2017/5/25.
 */
$(document).ready(function () {


    var articleId = $('.article-number').val();

    $(document).on('click', '.mobile_previous,.shortcut_previous', function () {
        if (!_checkInt(articleId)) {
            showMsg('左边翻不动啦，试试右边怎样？');
            return false;
        }
        $.ajax({
            url: web_url + 'details/' + articleId + '/turn_left',
            dataType: 'json',
            type: 'get',
            success: function (json) {
                if (json['retCode'] == 1) {
                    window.location.href = web_url + 'details/' + json['data']['page'] + '.html'
                } else {
                    showMsg(json['retMsg']);
                    return false;
                }
            }
        })
    });

    $(document).on('click', '.mobile_next,.shortcut_next', function () {
        if (!_checkInt(articleId)) {
            showMsg('左边翻不动啦，试试右边怎样？');
            return false;
        }
        $.ajax({
            url: web_url + 'details/' + articleId + '/turn_right',
            dataType: 'json',
            type: 'get',
            success: function (json) {
                if (json['retCode'] == 1) {
                    window.location.href = web_url + 'details/' + json['data']['page'] + '.html'
                } else {
                    showMsg(json['retMsg']);
                    return false;
                }
            }
        })
    });
});

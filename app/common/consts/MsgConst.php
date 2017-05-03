<?php

/**
 * Created by PhpStorm.
 * User: Sawyer Yang
 * Date: 2016/9/28
 * Time: 18:04
 */
namespace app\common\consts;
/**
 * 定义返回的错误代码类
 * Class MsgConst
 * @package Common\Common
 */
class MsgConst
{
    //错误代码
    const EMPTY_CODE       = '0';                                           // 数值为空
    const LEGAL_CODE       = '101';                                         // 值不合法
    const SUCCESS_CODE     = '1';                                           // 正确
    const FAIL_CODE        = '-1';                                          // 失败
    const DATA_EXIST_CODE  = '111';                                         // 数据已存在
    const NOT_AUTH_CODE    = '-999';                                        // 没有操作权限
    const ILLEGAL_CODE     = '-101';                                        // 非法访问
    const NOT_LOGIN_CODE   = '-888';                                        // 请登录
    const NOT_API_CODE     = '-202';                                        // 接口不存在
    const DATA_EMPTY       = '1024';                                        // 暂无数据
    const NOT_POSITION     = '109';                                         // 没有定位信息

    const EMPTY_MSG        = '参数为空';
    const LEGAL_MSG        = '您输入的值不合法';
    const SUCCESS_MSG      = 'SUCCESS';
    const FAIL_MSG         = 'FAIL';
    const DATA_EXIST_MSG   = '数据已存在';
    const NOT_AUTH_MSG     = '没有操作权限';
    const ILLEGAL_MSG      = '非法访问';
    const NOT_LOGIN_MSG    = '登录超时或请重新登录';
    const NOT_API_MSG      = '接口不存在';
    const DATA_EMPTY_MSG   = '暂无数据';
    const NOT_POSITION_MSG = '未定位，请先选择服务城市';

    const DELETE_CODE      = -9;                                            // 所有删除状态都为-9
}
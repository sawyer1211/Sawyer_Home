<?php

namespace app\common\model;

use think\Config;
use think\Model;

/**
 * Created by 1211.withsawyer.
 * User: Sawyer Yang
 * Date: 2017/4/26
 * Time: 17:22
 */
class BaseModel extends Model
{
    /**
     * @param       $retCode
     * @param       $retMsg
     * @param array $data
     * @param array $explain
     * @return array
     */
    protected function arrayReturn($retCode, $retMsg, $data = [], $explain = [])
    {
        $returnData = [
            'retCode' => $retCode,
            'retMsg'  => $retMsg,
            'data'    => $data,
        ];
        if (Config::get('isTest') == 1) {
            $returnData['explain'] = $explain;
        }
        return $returnData;
    }
}
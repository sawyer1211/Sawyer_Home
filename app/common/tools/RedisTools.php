<?php

namespace app\common\tools;
use think\Config;

/**
 * Created by 1002.yozhi.
 * User: Sawyer Yang
 * Date: 2017/3/3
 * Time: 11:04
 */
class RedisTools
{
    /**
     * 操作句柄
     * @var string
     * @access protected
     */
    public $handler;

    /**
     * 缓存连接参数
     * @var integer
     * @access protected
     */
    protected $options = array();

    /**
     * 架构函数
     * @param array $options 缓存参数
     * @access public
     */
    public function __construct($options = array())
    {

        if (!extension_loaded('redis')) {
            echo '未加载Redis扩展';
            die;
        }
        $redisConf = Config::get('REDIS_CONF');
        $options = array_merge([
            'host'       => $redisConf['REDIS_HOST'] ?: '127.0.0.1',
            'port'       => $redisConf['REDIS_PORT'] ?: 6379,
            'timeout'    => $redisConf['DATA_CACHE_TIMEOUT'] ?: false,
            'persistent' => false,
        ], $options);

        $this->options = $options;
        $this->options['expire'] = isset($options['expire']) ? $options['expire'] : Config::get('DATA_CACHE_TIME');
        $this->options['prefix'] = isset($options['prefix']) ? $options['prefix'] : Config::get('DATA_CACHE_PREFIX');
        $this->options['length'] = isset($options['length']) ? $options['length'] : 0;
        $func = $options['persistent'] ? 'pconnect' : 'connect';
        $this->handler = new \Redis();
        $options['timeout'] === false ?
            $this->handler->$func($options['host'], $options['port']) :
            $this->handler->$func($options['host'], $options['port'], $options['timeout']);
    }


    /**
     * 读取缓存
     * @access public
     * @param string $name 缓存变量名
     * @return mixed
     */
    public function get($name)
    {
        $value = $this->handler->get($this->options['prefix'] . $name);
        $jsonData = json_decode($value, true);
        return ($jsonData === NULL) ? $value : $jsonData;    //检测是否为JSON数据 true 返回JSON解析数组, false返回源数据
    }

    /**
     * 写入缓存
     * @access public
     * @param string  $name 缓存变量名
     * @param mixed   $value 存储数据
     * @param integer $expire 有效时间（秒）
     * @return boolean
     */
    public function set($name, $value, $expire = null)
    {
        if (is_null($expire)) {
            $expire = $this->options['expire'];
        }
        $name = $this->options['prefix'] . $name;
        //对数组/对象数据进行缓存处理，保证数据完整性
        $value = (is_object($value) || is_array($value)) ? json_encode($value) : $value;
        if (is_int($expire) && $expire) {
            $result = $this->handler->setex($name, $expire, $value);
        } else {
            $result = $this->handler->set($name, $value);
        }
        return $result;
    }

    /**
     * 删除缓存
     * @access public
     * @param string $name 缓存变量名
     * @return boolean
     */
    public function rm($name)
    {
        return $this->handler->delete($this->options['prefix'] . $name);
    }

    /**
     * 移除生存时间到期的key
     * @param $name
     * @return bool
     */
    public function persist($name)
    {
        return $this->handler->persist($this->options['prefix'] . $name);
    }

    /**
     * 得到一个key的生存时间
     * @param $name
     * @return bool
     */
    public function ttl($name)
    {
        return $this->handler->ttl($this->options['prefix'] . $name);
    }

    /**
     * 清除缓存
     * @access public
     * @return boolean
     */
    public function clear()
    {
        return $this->handler->flushDB();
    }

    /**
     * 在名称为key的list左边（尾）添加一个值为value的 元素
     * @param $name
     * @param $value
     * @return int
     */
    public function lPush($name, $value)
    {
        $value = (is_object($value) || is_array($value)) ? json_encode($value) : $value;
        return $this->handler->lPush($this->options['prefix'] . $name, $value);
    }

    /**
     * 在名称为key的list右边（尾）添加一个值为value的 元素
     * @param $name
     * @param $value
     * @return int
     */
    public function rPush($name, $value)
    {
        $value = (is_object($value) || is_array($value)) ? json_encode($value) : $value;
        return $this->handler->rPush($this->options['prefix'] . $name, $value);

    }

    /**
     * 输出名称为key的list左(头)起/右（尾）起的第一个元素，删除该元素
     * @param $name
     * @return mixed|string
     */
    public function lPop($name)
    {
        $value = $this->handler->lPop($this->options['prefix'] . $name);
        $jsonData = json_decode($value, true);
        return ($jsonData === NULL) ? $value : $jsonData;    //检测是否为JSON数据 true 返回JSON解析数组, false返回源数据
    }

    /**
     * 输出名称为key的list左(头)起/右（尾）起的第一个元素，删除该元素
     * @param $name
     * @return mixed|string
     */
    public function rPop($name)
    {
        $value = $this->handler->rPop($this->options['prefix'] . $name);
        $jsonData = json_decode($value, true);
        return ($jsonData === NULL) ? $value : $jsonData;    //检测是否为JSON数据 true 返回JSON解析数组, false返回源数据
    }

    /**
     * 返回名称为key的list有多少个元素
     * @param $name
     * @return int
     */
    public function lLen($name)
    {
        return $this->handler->lLen($this->options['prefix'] . $name);
    }

    public function lRange($name, $start, $end)
    {
        return $this->handler->lRange($this->options['prefix'] . $name, $start, $end);
    }
}
<?php
/**
 * Created by thinkphp5.
 * User: Sawyer Yang
 * Date: 2017/3/14
 * Time: 15:07
 */
use think\Config;

/**
 * MD5加密
 * @param  string $str 加密字符串
 * @param int     $frequency 加密次数
 * @return string $str
 */
function _myMd5($str, $frequency = 1)
{
    for ($i = 1; $i <= $frequency; $i++) {
        $str = md5($str);
    }
    return $str;


}

/**
 * 创建目录
 * @param $Folder 文件夹路径
 * @return string
 */
function _createFolder($Folder)
{
    if (!is_readable($Folder)) {
        _createFolder(dirname($Folder));
        if (!is_file($Folder)) mkdir($Folder, 0777);
        $Folder = "";
    }
    return $Folder;
}

/**
 * 验证邮箱的合法性
 * @param string $email
 * @return int
 */
function _checkEmail($email = '')
{
    return preg_match('/^[a-z0-9]+([._\\-]*[a-z0-9])*@([a-z0-9]+[-a-z0-9]*[a-z0-9]+.){1,63}[a-z0-9]+$/', $email);
}

/**
 * 发送邮件
 * @param array $param 发送的数据
 * @return string
 */
function _sendEmail($param = [])
{
    \think\Loader::import('class#phpmailer', APP_PATH . 'common/tools/');
    \think\Loader::import('class#smtp', APP_PATH . 'common/tools/');

    $email_config = Config::get('email_config');    // 引用邮箱配置信息

//引入PHPMailer的核心文件 使用require_once包含避免出现PHPMailer类重复定义的警告
//示例化PHPMailer核心类
    $mail = new PHPMailer();
//是否启用smtp的debug进行调试 开发环境建议开启 生产环境注释掉即可 默认关闭debug调试模式
//    $mail->SMTPDebug = 1;
//使用smtp鉴权方式发送邮件，当然你可以选择pop方式 sendmail方式等 本文不做详解
//可以参考http://phpmailer.github.io/PHPMailer/当中的详细介绍
    $mail->isSMTP();
//smtp需要鉴权 这个必须是true
    $mail->SMTPAuth = true;
//链接qq域名邮箱的服务器地址
    $mail->Host = $email_config['smtp_host'];
//设置使用ssl加密方式登录鉴权
    $mail->SMTPSecure = $email_config['smtp_ssl'];
//设置ssl连接smtp服务器的远程服务器端口号 可选465或587
    $mail->Port = $email_config['smtp_port'];
//设置smtp的helo消息头 这个可有可无 内容任意
    $mail->Helo = $email_config['smtp_helo'];
//设置发件人的主机域 可有可无 默认为localhost 内容任意，建议使用你的域名
    $mail->Hostname = $email_config['smtp_host_name'];
//设置发送的邮件的编码 可选GB2312 我喜欢utf-8 据说utf8在某些客户端收信下会乱码
    $mail->CharSet = $email_config['smtp_charset'];
//设置发件人姓名（昵称） 任意内容，显示在收件人邮件的发件人邮箱地址前的发件人姓名
    $mail->FromName = $email_config['smtp_from_name'];
//smtp登录的账号 这里填入字符串格式的qq号即可
    $mail->Username = $email_config['smtp_user_name'];
//smtp登录的密码 这里填入“独立密码” 若为设置“独立密码”则填入登录qq的密码 建议设置“独立密码”
    $mail->Password = $email_config['smtp_password'];
//设置发件人邮箱地址 这里填入上述提到的“发件人邮箱”
    $mail->From = $email_config['smtp_from_email'];
//邮件正文是否为html编码 注意此处是一个方法 不再是属性 true或false
    $mail->isHTML($email_config['smtp_is_html']);
//设置收件人邮箱地址 该方法有两个参数 第一个参数为收件人邮箱地址 第二参数为给该地址设置的昵称 不同的邮箱系统会自动进行处理变动 这里第二个参数的意义不大
    if (is_array($param['addAddress'])) {
        foreach ($param['addAddress'] AS $emailAddress) {
            $mail->addAddress($emailAddress['addAddress'], 'My Friend !');
        }
    } else {
        $mail->addAddress($param['addAddress'], 'My Friend !');
    }
//添加多个收件人 则多次调用方法即可
//    $mail->addAddress('xxx@163.com', '晶晶在线用户');
//添加该邮件的主题
    $mail->Subject = $param['subject'] ?: '无主题内容';
//添加邮件正文 上方将isHTML设置成了true，则可以是完整的html字符串 如：使用file_get_contents函数读取本地的html文件
    $mail->Body = $param['content'] ?: '暂无内容显示';
//为该邮件添加附件 该方法也有两个参数 第一个参数为附件存放的目录（相对目录、或绝对目录均可） 第二参数为在邮件附件中该附件的名称
//    $mail->addAttachment('./d.jpg', 'mm.jpg');
//同样该方法可以多次调用 上传多个附件
//    $mail->addAttachment('./Jlib-1.1.0.js', 'Jlib.js');

//发送命令 返回布尔值
//PS：经过测试，要是收件人不存在，若不出现错误依然返回true 也就是说在发送之前 自己需要些方法实现检测该邮箱是否真实有效
    $status = $mail->send();
    //简单的判断与提示信息
    if ($status) {
        return true;
    } else {
        return false;
    }
}

/**
 * 数据URL编码
 * @param $data
 * @return array|string
 */
function _dataToUrlEncode($data)
{
    if (is_array($data)) {
        foreach ($data as $key => $val) {
            $data[$key] = _dataToUrlEncode($data[$key]);
        }
    } else {
        if (is_string($data)) {
            $data = urlencode($data);
        }
    }

    return $data;
}

/**
 * 递归去除null值
 * @param $data
 * @return array|string
 */
function _arrayFilterNull(&$data)
{
    if (is_array($data)) {
        foreach ($data as $key => $val) {
            $data[$key] = _arrayFilterNull($val);
        }
    } elseif (is_null($data)) {
        $data = "";
    } elseif (is_numeric($data)) {
        $data = (string)$data;
    }

    return $data;
}

/**
 * 判断是否是正整数
 * @param $number
 * @return int
 */
function _checkInt($number)
{
    return preg_match("/^[1-9][0-9]*$/", $number);
}
/**
 * 判断用户名的合法性
 * @param $number
 * @return int
 */
function _checkUserName($user_name)
{
    return preg_match("/^[a-zA-Z]\w{4,19}$/", $user_name);
}

/**
 * 产生随机字符串，可用来自动生成验证码
 * @param int    $len 长度
 * @param string $type 字串类型
 * 0-大小写字母 1-数字 2-大写字母 3-小写字母 4-中文 默认-大小写字母和数字混合
 * @param string $addChars 额外字符
 * @return string
 */
function _randString($len = 6, $type = '', $addChars = '')
{
    $str = '';
    switch ($type) {
        case 0:
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' . $addChars;
            break;
        case 1:
            $chars = str_repeat('0123456789', 3);
            break;
        case 2:
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' . $addChars;
            break;
        case 3:
            $chars = 'abcdefghijklmnopqrstuvwxyz' . $addChars;
            break;
        case 4:
            $chars = "们以我到他会作时要动国产的一是工就年阶义发成部民可出能方进在了不和有大这主中人上为来分生对于学下级地个用同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然如应形想制心样干都向变关问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书术状厂须离再目海交权且儿青才证低越际八试规斯近注办布门铁需走议县兵固除般引齿千胜细影济白格效置推空配刀叶率述今选养德话查差半敌始片施响收华觉备名红续均药标记难存测士身紧液派准斤角降维板许破述技消底床田势端感往神便贺村构照容非搞亚磨族火段算适讲按值美态黄易彪服早班麦削信排台声该击素张密害侯草何树肥继右属市严径螺检左页抗苏显苦英快称坏移约巴材省黑武培著河帝仅针怎植京助升王眼她抓含苗副杂普谈围食射源例致酸旧却充足短划剂宣环落首尺波承粉践府鱼随考刻靠够满夫失包住促枝局菌杆周护岩师举曲春元超负砂封换太模贫减阳扬江析亩木言球朝医校古呢稻宋听唯输滑站另卫字鼓刚写刘微略范供阿块某功套友限项余倒卷创律雨让骨远帮初皮播优占死毒圈伟季训控激找叫云互跟裂粮粒母练塞钢顶策双留误础吸阻故寸盾晚丝女散焊功株亲院冷彻弹错散商视艺灭版烈零室轻血倍缺厘泵察绝富城冲喷壤简否柱李望盘磁雄似困巩益洲脱投送奴侧润盖挥距触星松送获兴独官混纪依未突架宽冬章湿偏纹吃执阀矿寨责熟稳夺硬价努翻奇甲预职评读背协损棉侵灰虽矛厚罗泥辟告卵箱掌氧恩爱停曾溶营终纲孟钱待尽俄缩沙退陈讨奋械载胞幼哪剥迫旋征槽倒握担仍呀鲜吧卡粗介钻逐弱脚怕盐末阴丰雾冠丙街莱贝辐肠付吉渗瑞惊顿挤秒悬姆烂森糖圣凹陶词迟蚕亿矩康遵牧遭幅园腔订香肉弟屋敏恢忘编印蜂急拿扩伤飞露核缘游振操央伍域甚迅辉异序免纸夜乡久隶缸夹念兰映沟乙吗儒杀汽磷艰晶插埃燃欢铁补咱芽永瓦倾阵碳演威附牙芽永瓦斜灌欧献顺猪洋腐请透司危括脉宜笑若尾束壮暴企菜穗楚汉愈绿拖牛份染既秋遍锻玉夏疗尖殖井费州访吹荣铜沿替滚客召旱悟刺脑措贯藏敢令隙炉壳硫煤迎铸粘探临薄旬善福纵择礼愿伏残雷延烟句纯渐耕跑泽慢栽鲁赤繁境潮横掉锥希池败船假亮谓托伙哲怀割摆贡呈劲财仪沉炼麻罪祖息车穿货销齐鼠抽画饲龙库守筑房歌寒喜哥洗蚀废纳腹乎录镜妇恶脂庄擦险赞钟摇典柄辩竹谷卖乱虚桥奥伯赶垂途额壁网截野遗静谋弄挂课镇妄盛耐援扎虑键归符庆聚绕摩忙舞遇索顾胶羊湖钉仁音迹碎伸灯避泛亡答勇频皇柳哈揭甘诺概宪浓岛袭谁洪谢炮浇斑讯懂灵蛋闭孩释乳巨徒私银伊景坦累匀霉杜乐勒隔弯绩招绍胡呼痛峰零柴簧午跳居尚丁秦稍追梁折耗碱殊岗挖氏刃剧堆赫荷胸衡勤膜篇登驻案刊秧缓凸役剪川雪链渔啦脸户洛孢勃盟买杨宗焦赛旗滤硅炭股坐蒸凝竟陷枪黎救冒暗洞犯筒您宋弧爆谬涂味津臂障褐陆啊健尊豆拔莫抵桑坡缝警挑污冰柬嘴啥饭塑寄赵喊垫丹渡耳刨虎笔稀昆浪萨茶滴浅拥穴覆伦娘吨浸袖珠雌妈紫戏塔锤震岁貌洁剖牢锋疑霸闪埔猛诉刷狠忽灾闹乔唐漏闻沈熔氯荒茎男凡抢像浆旁玻亦忠唱蒙予纷捕锁尤乘乌智淡允叛畜俘摸锈扫毕璃宝芯爷鉴秘净蒋钙肩腾枯抛轨堂拌爸循诱祝励肯酒绳穷塘燥泡袋朗喂铝软渠颗惯贸粪综墙趋彼届墨碍启逆卸航衣孙龄岭骗休借" . $addChars;
            break;
        default :
            // 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
            $chars = 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789' . $addChars;
            break;
    }
    if ($len > 10) {//位数过长重复字符串一定次数
        $chars = $type == 1 ? str_repeat($chars, $len) : str_repeat($chars, 5);
    }
    if ($type != 4) {
        $chars = str_shuffle($chars);
        $str = substr($chars, 0, $len);
    } else {
        // 中文随机字
        for ($i = 0; $i < $len; $i++) {
            $str .= msubstr($chars, floor(mt_rand(0, mb_strlen($chars, 'utf-8') - 1)), 1);
        }
    }
    return $str;
}

/**
 * 参数处理 加密和解密
 * @author Sawyer
 * @param string $param 参数
 * @param string $action 动作ENCODE|DECODE 加密或者解密
 * @param string $recognitionChars 识别字符【干扰字符串】
 * @return bool|mixed|string
 */
function _paramHandle($param = '', $action = 'ENCODE', $recognitionChars = '')
{
    $rule = [
        '%QRk' => 'A', '%FgG' => 'B', '%DFq' => 'C', '%FSP' => 'D', '%svx' => 'E', '%FWc' => 'F', '%FGs' => 'G', '%BCz' => 'H', '%Dcs' => 'I', '%Gva' => 'J', '%Fsv' => 'K', '%GEi' => 'L', '%VdL' => 'M', '%FGQ' => 'N', '%sFc' => 'O', '%jBl' => 'P', '%ihp' => 'Q', '%Leg' => 'R', '%wOw' => 'S', '%Psx' => 'T', '%zLs' => 'U', '%pss' => 'V', '%xsp' => 'W', '%aDs' => 'X', '%PJN' => 'Y', '%xsd' => 'Z', '%bDw' => 'a', '%DFw' => 'b', '%sFe' => 'c', '%Osv' => 'd', '%Lbt' => 'e', '%Ajm' => 'f', '%fmj' => 'g', '%MMM' => 'h', '%LIw' => 'i', '%veU' => 'j', '%PgM' => 'k', '%JMs' => 'l', '%zLI' => 'm', '%bPP' => 'n', '%NMM' => 'o', '%VSw' => 'p', '%VNs' => 'q', '%NWW' => 'r', '%NOO' => 's', '%III' => 't', '%cMK' => 'u', '%vsw' => 'v', '%Haa' => 'w', '%ibe' => 'x', '%dfw' => 'y', '%Sfe' => 'z', '%Faw' => '=',
    ];
    if ($recognitionChars) {
        $randInt = mt_rand(1, 15);
        $recognitionChars = '#%#' . substr(md5($recognitionChars), $randInt, 15);
    }
    $action != 'DECODE' && $param = $recognitionChars . $param;     // 加密的时候加上干扰字符串
    $action != 'DECODE' && $param = base64_encode($param);  // 加密的时候先base64加密一次
    $action != 'DECODE' && $rule = array_flip($rule);       // 加密的时候先把规则键值互换好对应
    $string = '';                                           // 初始化字符串

    if ($action == 'ENCODE') {
        // 计算出参数的长度 得出循环次数
        $loopNum = mb_strlen($param, 'utf8');
        for ($i = 0; $i < $loopNum; $i++) {
            // 把字符串的每一位根据规则替换成新的字符串并连接，没有定义规则的键就按原字符串保留
            if (array_key_exists($param{$i}, $rule)) {
                $string .= $rule[$param{$i}];
            } else {
                $string .= $param{$i};
            }
        }
    } elseif ($action == 'DECODE') {
        $string = $param;
        // 循环所有规则 进行正则替换成规则对应的原字符
        foreach ($rule AS $key => $value) {
            $string = preg_replace("/(" . $key . ")/", $value, $string);
        }
        // 替换完规则后base64解密
        $string = base64_decode($string);
        // 如果查询到干扰字符串的规则就把干扰字符串截取掉
        $strpos = strpos($string, '#%#');
        if (false !== $strpos) {
            // 截取掉干扰字符串 18=干扰识别符号#%#的3位+干扰字符串15位
            $string = substr($string, 18, strlen($string) - 15);
        }
    }
    return $string;
}

/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
 * @return mixed
 */
function _getClientIp($type = 0, $adv = false)
{
    $type = $type ? 1 : 0;
    static $ip = NULL;
    if ($ip !== NULL) return $ip[$type];
    if ($adv) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if (false !== $pos) unset($arr[$pos]);
            $ip = trim($arr[0]);
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u", ip2long($ip));
    $ip = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}



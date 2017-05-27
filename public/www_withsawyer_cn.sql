# Host: 122.114.42.22  (Version 5.5.42-log)
# Date: 2017-05-27 13:51:30
# Generator: MySQL-Front 6.0  (Build 1.122)


#
# Structure for table "sy_article_label"
#

DROP TABLE IF EXISTS `sy_article_label`;
CREATE TABLE `sy_article_label` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lb_name` varchar(50) NOT NULL DEFAULT '' COMMENT '标签名称',
  `lb_use_rate` int(11) unsigned DEFAULT '0' COMMENT '标签使用次数',
  `lb_creator` int(11) NOT NULL DEFAULT '0' COMMENT '标签创建者【0-系统添加 其他关联user_manage】',
  `lb_create_time` char(10) DEFAULT NULL COMMENT '创建时间',
  `lb_sort` tinyint(3) DEFAULT '0' COMMENT '手动排序【数值越大越靠前排列】',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='文章标签';

#
# Data for table "sy_article_label"
#

/*!40000 ALTER TABLE `sy_article_label` DISABLE KEYS */;
INSERT INTO `sy_article_label` VALUES (1,'无病呻吟',1,0,'1494232117',0),(2,'生活烦恼',0,0,'1494232117',0),(3,'正能量',0,0,'1494232117',0);
/*!40000 ALTER TABLE `sy_article_label` ENABLE KEYS */;

#
# Structure for table "sy_article_publish"
#

DROP TABLE IF EXISTS `sy_article_publish`;
CREATE TABLE `sy_article_publish` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '关联uid【0-系统添加 其他关联user_manage表】',
  `ac_title` varchar(200) NOT NULL DEFAULT '' COMMENT '文章标题',
  `ac_content` text NOT NULL COMMENT '文章内容',
  `ac_label` int(11) DEFAULT NULL COMMENT '文章标签【关联article_label表】',
  `ac_create_time` char(10) DEFAULT NULL COMMENT '添加时间',
  `ac_status` tinyint(1) DEFAULT '1' COMMENT '状态【-9-删除 1-正常】',
  `ac_sort` int(11) unsigned DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='发表的文章';

#
# Data for table "sy_article_publish"
#

INSERT INTO `sy_article_publish` VALUES (1,1,'天气真Tm好','今天买的萨达姆奶茶真好喝！！双击666！！！！！',1,'1494232117',1,6),(2,1,'哇哈哈矿泉水VS萨芬饮料','今天的战果真的是非常激烈呀',2,'1494232116',1,5),(3,1,'飒飒的','隧道股份是的如果是的风格认识',2,'1494232115',1,4),(4,1,'第三方的身份','的说法都是',2,'1494232114',1,3),(5,1,'放到沙发的','啊师傅多少范德萨',2,'1494232113',1,2),(6,1,'asd按时','的说法都是都是 ',2,'1494232112',1,1);

#
# Structure for table "sy_article_running"
#

DROP TABLE IF EXISTS `sy_article_running`;
CREATE TABLE `sy_article_running` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='文章相关流水';

#
# Data for table "sy_article_running"
#


#
# Structure for table "sy_resume_record"
#

DROP TABLE IF EXISTS `sy_resume_record`;
CREATE TABLE `sy_resume_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rs_name` varchar(100) DEFAULT NULL COMMENT '谁的简历',
  `rs_time` datetime DEFAULT NULL COMMENT '最后一次浏览时间',
  `rs_times` int(11) DEFAULT NULL COMMENT '浏览次数',
  PRIMARY KEY (`id`),
  KEY `rs_name` (`rs_name`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='记录一下简历浏览次数';

#
# Data for table "sy_resume_record"
#

/*!40000 ALTER TABLE `sy_resume_record` DISABLE KEYS */;
INSERT INTO `sy_resume_record` VALUES (1,'withsawyer','2017-05-27 13:21:55',99),(2,'shuke','2017-05-26 09:52:19',1),(3,'sawyer','2017-05-26 12:26:01',31);
/*!40000 ALTER TABLE `sy_resume_record` ENABLE KEYS */;

#
# Structure for table "sy_system_param_setting"
#

DROP TABLE IF EXISTS `sy_system_param_setting`;
CREATE TABLE `sy_system_param_setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `set_name` varchar(255) DEFAULT NULL COMMENT '参数变量名称',
  `set_key` varchar(255) DEFAULT NULL COMMENT '参数的键（如果是数组形式就需要默认为空）',
  `set_value` varchar(255) DEFAULT '' COMMENT '参数的值（和名称相对）',
  `set_type` varchar(100) DEFAULT NULL COMMENT '参数类型（辅助查询）',
  PRIMARY KEY (`id`),
  KEY `set_type` (`set_type`),
  KEY `set_name` (`set_name`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 COMMENT='系统参数设置表';

#
# Data for table "sy_system_param_setting"
#

/*!40000 ALTER TABLE `sy_system_param_setting` DISABLE KEYS */;
INSERT INTO `sy_system_param_setting` VALUES (1,'webpage_title','webpage_title','Sawyer\'s Home','webpage'),(2,'webpage_email','webpage_email','issawyer@withsawyer.cn','webpage');
/*!40000 ALTER TABLE `sy_system_param_setting` ENABLE KEYS */;

#
# Structure for table "sy_user_login_running"
#

DROP TABLE IF EXISTS `sy_user_login_running`;
CREATE TABLE `sy_user_login_running` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ur_uid` int(11) NOT NULL DEFAULT '0' COMMENT '关联user_manage表',
  `ur_user_name` varchar(20) DEFAULT NULL COMMENT '用户名',
  `ur_client_ip` varchar(255) DEFAULT NULL COMMENT 'ip地址',
  `ur_login_time` varchar(10) DEFAULT NULL COMMENT '登陆时间',
  `ur_note` varchar(255) DEFAULT NULL COMMENT '备注信息',
  PRIMARY KEY (`id`),
  KEY `uid` (`ur_uid`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='用户登陆记录';

#
# Data for table "sy_user_login_running"
#

/*!40000 ALTER TABLE `sy_user_login_running` DISABLE KEYS */;
INSERT INTO `sy_user_login_running` VALUES (1,1,'withsawyer','0.0.0.0','1493870517','用户注册成功自动登陆'),(2,1,'withsawyer','0.0.0.0','1493886252','用户登陆'),(3,2,'Sawyer1211','192.168.100.108','1493888016','用户注册成功自动登陆'),(4,1,'withsawyer','192.168.100.108','1494226424','用户登陆'),(5,1,'withsawyer','192.168.100.108','1494229651','用户登陆'),(6,3,'luolan12','192.168.100.108','1494587813','用户注册成功自动登陆'),(7,1,'withsawyer','192.168.100.108','1495095728','用户登陆'),(8,2,'Sawyer1211','192.168.100.108','1495292778','用户登陆');
/*!40000 ALTER TABLE `sy_user_login_running` ENABLE KEYS */;

#
# Structure for table "sy_user_manage"
#

DROP TABLE IF EXISTS `sy_user_manage`;
CREATE TABLE `sy_user_manage` (
  `uid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `u_nickname` varchar(20) DEFAULT '匿名用户' COMMENT '用户昵称',
  `u_user_name` varchar(20) DEFAULT NULL COMMENT '用户名（用于登陆）',
  `u_email` varchar(150) DEFAULT NULL COMMENT '用户邮箱（也可用于登陆）',
  `u_password` char(32) DEFAULT NULL COMMENT '登陆密码',
  `u_head_pic` varchar(255) DEFAULT NULL COMMENT '用户头像',
  `u_create_time` varchar(15) DEFAULT NULL COMMENT '注册时间',
  `u_is_test` tinyint(1) DEFAULT '0' COMMENT '是否为测试用户 0-正式 1-测试',
  `u_state` tinyint(1) DEFAULT NULL COMMENT '用户状态 -9-删除 -5-封号 1-正常',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='用户管理表';

#
# Data for table "sy_user_manage"
#

/*!40000 ALTER TABLE `sy_user_manage` DISABLE KEYS */;
INSERT INTO `sy_user_manage` VALUES (1,'匿名用户','withsawyer','472973618@qq.com','a970a7e3b359f88a4732b56050822888',NULL,'1493870517',1,1),(2,'匿名用户','Sawyer1211','707787743@qq.com','a970a7e3b359f88a4732b56050822888',NULL,'1493888016',0,1),(3,'匿名用户','luolan12','554767708@qq.com','08b2ea760bbc5155e197f0bb61f0e6da',NULL,'1494587813',0,1);
/*!40000 ALTER TABLE `sy_user_manage` ENABLE KEYS */;

#
# Structure for table "sy_verify_templates"
#

DROP TABLE IF EXISTS `sy_verify_templates`;
CREATE TABLE `sy_verify_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `verify_name` varchar(255) DEFAULT NULL COMMENT '模板名称',
  `verify_lang` text COMMENT '模板内容【手机短信使用】',
  `verify_template_url` varchar(255) DEFAULT NULL COMMENT '邮箱模板地址【邮箱使用】',
  `verify_type` tinyint(1) DEFAULT '3' COMMENT '验证码类型 1-手机短信 3-邮箱',
  `verify_note` varchar(255) DEFAULT NULL COMMENT '模板的备注',
  `verify_state` tinyint(1) DEFAULT '1' COMMENT '模板状态 -9删除 -1正常',
  PRIMARY KEY (`id`),
  KEY `发送验证码的条件` (`verify_name`,`verify_type`,`verify_state`)
) ENGINE=MyISAM AUTO_INCREMENT=50 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='验证码发送模板';

#
# Data for table "sy_verify_templates"
#

/*!40000 ALTER TABLE `sy_verify_templates` DISABLE KEYS */;
INSERT INTO `sy_verify_templates` VALUES (44,'user_register','尊敬的客户，欢迎注册{project_name}！您的验证码是 {vcode}','html_template/verification_code_template.html',3,'【{project_name}】',1),(45,'user_repass','用户您好！您正在请求修改{project_name}的密码，请确认是否是本人操作！验证码:{vcode}','html_template/verification_code_template.html',3,'您正在请求修改密码，请确认是否是本人操作！验证码:{code},欢迎使用原生态',1),(47,'user_login','尊敬的客户，欢迎登录{project_name}！验证码 {vcode}','html_template/verification_code_template.html',3,'验证码 {vcode}',1),(48,'user_modify','用户您好！您正在请求修改{project_name}的密码，请确认是否是本人操作！验证码:{vcode}','html_template/verification_code_template.html',3,'用户修改密码',1),(49,'user_retrieve','用户您好！您正在请求找回{project_name}的密码，请确认是否是本人操作！验证码:{vcode}','html_template/verification_code_template.html',3,'用户找回密码',1);
/*!40000 ALTER TABLE `sy_verify_templates` ENABLE KEYS */;

#
# Structure for table "sy_verify_validate"
#

DROP TABLE IF EXISTS `sy_verify_validate`;
CREATE TABLE `sy_verify_validate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `v_source` varchar(40) DEFAULT NULL COMMENT '发送来源【手机号或者邮箱】',
  `v_code` varchar(10) DEFAULT NULL COMMENT '验证码',
  `v_check_code` varchar(12) DEFAULT NULL COMMENT '校验码',
  `v_time` varchar(40) DEFAULT NULL COMMENT '验证时间',
  `v_temp_name` varchar(40) DEFAULT NULL COMMENT '验证码用途',
  `v_client_ip` varchar(20) DEFAULT NULL COMMENT '客户端IP地址',
  PRIMARY KEY (`id`),
  KEY `v_source` (`v_source`,`v_code`,`v_check_code`,`v_temp_name`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='验证码存放点';

#
# Data for table "sy_verify_validate"
#

/*!40000 ALTER TABLE `sy_verify_validate` DISABLE KEYS */;
INSERT INTO `sy_verify_validate` VALUES (1,'472973618@qq.com','EcJLfw','bIz7euyirY','1493713818','user_register','0.0.0.0'),(2,'472973618@qq.com','OsHYWa','srDGNa3dIB','1493714014','user_register','0.0.0.0'),(3,'472973618@qq.com','LcgMQb','RWjXHewapI','1493714019','user_register','0.0.0.0'),(4,'472973618@qq.com','CsGMYK','viZTczkfQa','1493716900','user_register','0.0.0.0'),(5,'472973618@qq.com','sXhcqy','Thtw8u9Qna','1493803840','user_register','0.0.0.0'),(6,'472973618@qq.com','ksomRq','ifpJnrAx9t','1493804137','user_register','0.0.0.0'),(7,'254036209@qq.com','FrkiCM','C3gkPdhxaX','1493829372','user_register','192.168.100.108'),(8,'254036209@qq.com','Lfcgij','qVvcRwydAK','1493829390','user_register','192.168.100.108'),(9,'472973618@qq.com','mxtBiL','TnDAGadsIc','1493829431','user_register','192.168.100.108'),(10,'472973618@qq.com','nqSMXN','hkR5zXF6c7','1493868846','user_register','0.0.0.0'),(11,'472973618@qq.com','ndgRAJ','Isb7QmpTZM','1493868925','user_register','0.0.0.0'),(12,'472973618@qq.com','HzdBVT','kxVIZnE8MF','1493870502','user_register','0.0.0.0'),(13,'707787743@qq.com','tVfcXr','JrZWUy3Nq2','1493888001','user_register','192.168.100.108'),(14,'2563193087@qq.com','GCHyki','hB3QRGUkcH','1494587611','user_register','192.168.100.108'),(15,'554767708@qq.com','encFDP','ZpbnDv5Mwf','1494587784','user_register','192.168.100.108'),(16,'191240684@qq.com','JDkjUd','nCQe43RDvi','1495528946','user_register','192.168.100.108'),(17,'191240684@qq.com','QLhktU','K5UiupgkTJ','1495529125','user_register','192.168.100.108'),(18,'254036209@qq.com','SeldjM','DNFncvHuzd','1495529196','user_register','192.168.100.108'),(19,'254036209@qq.com','TIdatn','tM2wiqczQV','1495529242','user_register','192.168.100.108'),(20,'10086@qq.com','舒克懒懒的简历','有人发邮件给她','1495531185','我记录一下','0.0.0.0'),(21,'10086@qq.com','舒克懒懒的简历','有人发邮件给她','1495531260','我记录一下','0.0.0.0'),(22,'10086@qq.com','舒克懒懒的简历','有人发邮件给她','1495531337','我记录一下','0.0.0.0'),(23,'10086@qq.com','舒克懒懒的简历','有人发邮件给她','1495531526','我记录一下','0.0.0.0'),(24,'1563831451@qq.com','舒克懒懒的简历','有人发邮件给她','1495533197','我记录一下','192.168.100.108');
/*!40000 ALTER TABLE `sy_verify_validate` ENABLE KEYS */;

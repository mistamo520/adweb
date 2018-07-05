-- phpMyAdmin SQL Dump
-- version 3.1.2-rc1
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2014 年 03 月 10 日 13:22
-- 服务器版本: 5.0.22
-- PHP 版本: 5.2.17

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- 数据库: `zhongxin`
--

-- --------------------------------------------------------

--
-- 表的结构 `zx_accounts`
--

CREATE TABLE IF NOT EXISTS `zx_accounts` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `version` int(11) NOT NULL COMMENT '乐观锁',
  `userId` varchar(64) NOT NULL COMMENT '对应用户的id',
  `accountNo` varchar(64) DEFAULT NULL COMMENT '账户编码',
  `customType` varchar(16) NOT NULL COMMENT '客户类型',
  `balance` decimal(13,2) DEFAULT '0.00' COMMENT '账户余额:预留',
  `totalOnInvest` decimal(13,2) DEFAULT '0.00' COMMENT '总在投金额:实时计算',
  `totalOnInterest` decimal(13,2) DEFAULT '0.00' COMMENT '总应收利息:每日定时计算',
  `totalHistoryInterest` decimal(13,2) NOT NULL DEFAULT '0.00' COMMENT '历史总收益',
  `totalYield` decimal(6,4) DEFAULT '0.0000' COMMENT '总收益率：预留',
  `createTime` bigint(13) NOT NULL COMMENT '账户创建时间',
  `lastModifyTime` bigint(13) DEFAULT NULL COMMENT '最近修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='资产统计表' AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `zx_accounts`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_advice_contents`
--

CREATE TABLE IF NOT EXISTS `zx_advice_contents` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `adviceContent` text NOT NULL COMMENT '建议内容',
  `userId` bigint(20) NOT NULL COMMENT '用户Id',
  `processResult` text COMMENT '处理结果',
  `operaterId` bigint(20) DEFAULT NULL COMMENT '处理人Id',
  `createTime` bigint(13) NOT NULL COMMENT '创建时间',
  `lastModifyTime` bigint(13) DEFAULT NULL COMMENT '最近修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='反馈建议表' AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `zx_advice_contents`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_backing_rates`
--

CREATE TABLE IF NOT EXISTS `zx_backing_rates` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `bidNo` varchar(32) NOT NULL COMMENT '标的编码',
  `lowCustomNo` varchar(32) NOT NULL COMMENT '托底客户编号',
  `lowestYield` decimal(6,4) NOT NULL DEFAULT '0.0000' COMMENT '最新托底收益率',
  `status` varchar(16) NOT NULL COMMENT '状态：INIT:初始；AUDITED:已审核；PUBLISHED:已发布',
  `createTime` bigint(13) NOT NULL COMMENT '创建时间',
  `lastModifyTime` bigint(13) DEFAULT NULL COMMENT '最近修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='标的托底收益率' AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `zx_backing_rates`
--

-- --------------------------------------------------------

--
-- 表的结构 `zx_bindcard_informations`
--

CREATE TABLE IF NOT EXISTS `zx_bindcard_informations` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `userId` bigint(20) NOT NULL COMMENT '用户Id',
  `bankCardNo` varchar(32) NOT NULL COMMENT '银行卡号',
  `bank` varchar(32) NOT NULL COMMENT '开户行',
  `province` int(10) NOT NULL COMMENT '开户省',
  `city` int(10) NOT NULL COMMENT '开户市',
  `switchName` varchar(256) NOT NULL COMMENT '开户分支机构',
  `address` varchar(256) DEFAULT NULL COMMENT '开户地址',
  `bankCode` varchar(32) DEFAULT NULL COMMENT '银行编码',
  `bindStatus` varchar(16) NOT NULL COMMENT '绑定状态：INIT：初始未绑定；BINDED：已绑定；FAILED：绑定失败',
  `cardFlag` varchar(16) NOT NULL COMMENT '绑定卡标志',
  `bindTime` bigint(13) NOT NULL COMMENT '绑定时间',
  `lastModifyTime` bigint(13) DEFAULT NULL COMMENT '最近修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='银行卡绑定信息表' AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `zx_bindcard_informations`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_business_operator`
--

CREATE TABLE IF NOT EXISTS `zx_business_operator` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `operatorName` varchar(64) NOT NULL COMMENT '操作员登录名',
  `userNickName` varchar(128) DEFAULT NULL COMMENT '操作员别名',
  `userPass` varchar(32) NOT NULL COMMENT '操作员登录密码',
  `userStatus` varchar(16) NOT NULL COMMENT '操作员状态',
  `targetMerchantCustomNo` varchar(16) NOT NULL COMMENT '所属商户客户编码',
  `mobile` varchar(32) DEFAULT NULL COMMENT '操作员手机号码',
  `regTime` bigint(13) NOT NULL COMMENT '操作员注册时间',
  `lastModifyTime` bigint(13) DEFAULT NULL COMMENT '最近修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商户操作员表' AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `zx_business_operator`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_customers`
--

CREATE TABLE IF NOT EXISTS `zx_customers` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `customNo` varchar(64) NOT NULL COMMENT '客户编号',
  `customType` varchar(16) NOT NULL COMMENT '客户类型：USER:个人用户；MER:商户',
  `customClas` varchar(16) NOT NULL COMMENT '客户分类：GUAR:担保；BACKING:托底购买；PRO:标的借款',
  `customName` varchar(64) NOT NULL COMMENT '客户名称',
  `customShortName` varchar(64) NOT NULL,
  `customDesc` text COMMENT '客户介绍',
  `customEmail` varchar(64) DEFAULT NULL COMMENT '客户邮箱地址',
  `customPhone` varchar(64) DEFAULT NULL COMMENT '客户电话',
  `contactor` varchar(32) DEFAULT NULL COMMENT '联系人',
  `creditial` varchar(32) DEFAULT NULL COMMENT '身份证号/营业执照号',
  `platformCustomNo` varchar(64) DEFAULT NULL COMMENT '对应的平台用户或商户编码',
  `createTime` bigint(13) NOT NULL COMMENT '创建时间',
  `lastModifyTime` bigint(13) DEFAULT NULL COMMENT '最近修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='托底购买方和担保方、借款方信息表' AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `zx_customers`
--

-- --------------------------------------------------------

--
-- 表的结构 `zx_error_reports`
--

CREATE TABLE IF NOT EXISTS `zx_error_reports` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `tradeId` varchar(64) NOT NULL COMMENT '自定义交易ID',
  `tradeType` varchar(16) NOT NULL COMMENT '交易类型',
  `amount` decimal(13,2) NOT NULL DEFAULT '0.00' COMMENT '交易金额',
  `status` varchar(16) NOT NULL COMMENT '交易状态',
  `failReason` varchar(64) NOT NULL COMMENT '失败原因',
  `preStepId` varchar(64) DEFAULT NULL COMMENT '下个交易Id',
  `processOperatorId` varchar(64) DEFAULT NULL COMMENT '处理操作员Id',
  `processStatus` varchar(16) DEFAULT NULL COMMENT '处理状态',
  `processContext` text COMMENT '处理内容',
  `createTime` bigint(13) NOT NULL COMMENT '创建时间',
  `lastModifyTime` bigint(13) DEFAULT NULL COMMENT '最近修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='可疑交易记录表' AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `zx_error_reports`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_financial_informations`
--


CREATE TABLE IF NOT EXISTS `zx_financial_informations` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `ownerCustomNo` varchar(64) NOT NULL COMMENT '交易主体',
  `ownerType` varchar(16) NOT NULL COMMENT '交易主体类型',
  `opposCustomNo` varchar(64) NOT NULL COMMENT '交易的另一方参与者',
  `opposType` varchar(16) NOT NULL COMMENT '另一方参与者类型',
  `tradeType` varchar(16) NOT NULL COMMENT '交易类型',
  `fundsDirection` varchar(16) NOT NULL COMMENT '资金方向',
  `tradeAmount` decimal(13,2) NOT NULL DEFAULT '0.00' COMMENT '交易金额',
  `tradeShare` int(11) NOT NULL DEFAULT '0' COMMENT '交易份额',
  `interestAmount` decimal(13,2) DEFAULT '0.00' COMMENT '利息金额',
  `feeAmount` decimal(13,2) DEFAULT '0.00' COMMENT '手续费',
  `bidNo` varchar(64) NOT NULL COMMENT '标的编码',
  `orgOrderNo` varchar(64) NOT NULL COMMENT '原订单号',
  `createTime` bigint(13) NOT NULL COMMENT '创建时间',
  `lastModifyTime` bigint(13) DEFAULT NULL COMMENT '最近修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='资金交易流水信息' AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `zx_financial_informations`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_login_historys`
--

CREATE TABLE IF NOT EXISTS `zx_login_historys` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `userNo` varchar(64) NOT NULL COMMENT '用户编号',
  `userSource` varchar(16) NOT NULL COMMENT '用户来源',
  `userIp` varchar(64) DEFAULT NULL COMMENT '用户IP',
  `browser` varchar(64) DEFAULT NULL COMMENT '用户浏览器类型',
  `browserVersion` varchar(64) DEFAULT NULL COMMENT '浏览器版本号',
  `cookieValue` varchar(64) DEFAULT NULL COMMENT 'Cookie用户识别码',
  `loginTime` bigint(13) NOT NULL COMMENT '用户登录时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='用户登录历史表' AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `zx_login_historys`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_managers`
--

CREATE TABLE IF NOT EXISTS `zx_managers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customid` bigint(20) DEFAULT NULL COMMENT '关联客户号',
  `email` varchar(128) DEFAULT NULL,
  `username` varchar(32) DEFAULT NULL,
  `realname` varchar(32) DEFAULT NULL,
  `password` varchar(32) DEFAULT NULL,
  `mobile` varchar(16) DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT '1' COMMENT '管理员的状态，默认为正常，当为0时，则该账号冻结',
  `power` text COMMENT '权限',
  `ip` varchar(16) DEFAULT NULL,
  `logintime` int(10) DEFAULT NULL,
  `createtime` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNQ_name` (`username`),
  UNIQUE KEY `UNQ_e` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `zx_managers`
--

-- --------------------------------------------------------

--
-- 表的结构 `zx_models`
--

CREATE TABLE IF NOT EXISTS `zx_models` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `pid` int(11) NOT NULL COMMENT '父级ID',
  `name` varchar(128) NOT NULL COMMENT '模块名称',
  `sort` int(4) NOT NULL DEFAULT '0' COMMENT '排序',
  `url` varchar(128) NOT NULL,
  `description` text COMMENT ' 描述',
  `created` bigint(13) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `zx_models`
--

-- --------------------------------------------------------

--
-- 表的结构 `zx_notices`
--

CREATE TABLE IF NOT EXISTS `zx_notices` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `noticeType` varchar(16) DEFAULT NULL COMMENT '公告类型',
  `noticeTitle` varchar(64) DEFAULT NULL COMMENT '公告标题',
  `content` text NOT NULL COMMENT '公告内容',
  `status` varchar(16) NOT NULL COMMENT '状态',
  `createTime` bigint(13) NOT NULL COMMENT '创建时间',
  `lastModifyTime` bigint(13) DEFAULT NULL COMMENT '最近修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='公告表' AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `zx_notices`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_notice_templates`
--

CREATE TABLE IF NOT EXISTS `zx_notice_templates` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `modelType` varchar(16) NOT NULL COMMENT '模板类型',
  `businessType` varchar(16) NOT NULL COMMENT '业务类型',
  `title` varchar(64) DEFAULT NULL COMMENT '通知标题',
  `content` text NOT NULL COMMENT '通知内容',
  `status` varchar(16) NOT NULL COMMENT '状态',
  `createTime` bigint(13) NOT NULL COMMENT '创建时间',
  `lastModifyTime` bigint(13) DEFAULT NULL COMMENT '最近修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='通知模板' AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `zx_notice_templates`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_notification_records`
--

CREATE TABLE IF NOT EXISTS `zx_notification_records` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `modelType` varchar(16) NOT NULL COMMENT '模板类型',
  `target` varchar(64) NOT NULL COMMENT '发送目标：手机号或邮箱地址',
  `businessType` varchar(16) NOT NULL COMMENT '业务类型',
  `title` varchar(64) DEFAULT NULL COMMENT '通知标题',
  `content` text NOT NULL COMMENT '通知内容',
  `status` varchar(16) NOT NULL COMMENT '状态',
  `sendTime` bigint(13) NOT NULL COMMENT '发送时间',
  `lastModifyTime` bigint(13) DEFAULT NULL COMMENT '最近修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='通知发送记录' AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `zx_notification_records`
--

-- --------------------------------------------------------

--
-- 表的结构 `zx_oplogs`
--

CREATE TABLE IF NOT EXISTS `zx_oplogs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `mid` int(11) NOT NULL COMMENT '登录商户ID',
  `content` varchar(128) NOT NULL COMMENT '操作的具体内容',
  `ip` varchar(15) NOT NULL COMMENT '登录者的IP地址',
  `createtime` bigint(13) NOT NULL COMMENT '操作时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='商户登录日志表' AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `zx_oplogs`
--

-- --------------------------------------------------------

--
-- 表的结构 `zx_orders`
--

CREATE TABLE IF NOT EXISTS `zx_orders` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `version` int(11) NOT NULL DEFAULT '0' COMMENT '乐观锁',
  `orderNo` varchar(64) NOT NULL COMMENT '订单号',
  `buyCustomNo` varchar(64) NOT NULL COMMENT '购买客户编码',
  `sellerCustomNo` varchar(64) NOT NULL COMMENT '卖出客户编码',
  `goodsType` varchar(16) NOT NULL COMMENT '商品类型：ORIG:原始标的购买；TRANS：转让信息购买',
  `goodsNo` varchar(64) NOT NULL COMMENT '商品编码',
  `buyType` varchar(16) NOT NULL COMMENT '购买类型：GENE：一般购买；LOW：托底购买',
  `orderValidPeriod` int(10) DEFAULT NULL COMMENT '订单有效期',
  `orderValidEndDate` bigint(13) DEFAULT NULL COMMENT '订单有效期结束日期',
  `goodsName` varchar(64) DEFAULT NULL COMMENT '商品名称',
  `goodsDesc` varchar(128) DEFAULT NULL COMMENT '商品描述',
  `orgBidNo` varchar(64) NOT NULL COMMENT '原始募投标的编码',
  `buyAmount` decimal(13,2) NOT NULL DEFAULT '0.00' COMMENT '购买金额',
  `costAmount` decimal(13,2) DEFAULT '0.00' COMMENT '成本',
  `buyShare` int(11) NOT NULL DEFAULT '0' COMMENT '购买份额',
  `buyerFee` decimal(13,2) DEFAULT '0.00' COMMENT '买方交易手续费',
  `sellerFee` decimal(13,2) DEFAULT '0.00' COMMENT '卖方交易手续费',
  `lockPeriod` int(6) DEFAULT NULL COMMENT '封闭周期',
  `lockStartDate` bigint(13) DEFAULT NULL COMMENT '封闭起始日期',
  `lockEndDate` bigint(13) DEFAULT NULL COMMENT '封闭结束日期',
  `orderStatus` varchar(16) NOT NULL DEFAULT 'INIT' COMMENT '订单状态：INIT:初始化；SUCCESS:购买成功；FAILED:购买失败；TRANSFERED:已转让；TRANSFERING:正在转让',
  `transferedAmount` decimal(13,2) DEFAULT '0.00' COMMENT '已转让金额',
  `transferedShare` int(11) DEFAULT '0' COMMENT '已转让份额',
  `transferingAmount` decimal(13,2) DEFAULT '0.00' COMMENT '正在转让金额',
  `transferingShare` int(11) DEFAULT '0' COMMENT '正在转让份额',
  `achievingInterest` decimal(13,2) DEFAULT '0.00' COMMENT '账面收益',
  `achievedInterest` decimal(13,2) DEFAULT '0.00' COMMENT '已获取收益',
  `transferNo` varchar(64) DEFAULT NULL COMMENT '转让信息编码',
  `buyRequestNo` varchar(64) DEFAULT NULL COMMENT '购买意愿请求编码',
  `lowYield` decimal(13,2) DEFAULT '0.00' COMMENT '托底收益率：托底购买时记录',
  `startDate` bigint(13) DEFAULT NULL COMMENT '起息日：针对原始购买者，起息日为标的起息日；针对转让购买者，起息日为购买日的下一工作日',
  `endDate` bigint(13) DEFAULT NULL COMMENT '项目到期日：针对转让中的出让人，计息结束日为转让购买成交的前一工作日',
  `orderTime` bigint(13) NOT NULL COMMENT '下订单时间',
  `lastModifyTime` bigint(13) DEFAULT NULL COMMENT '最近修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='订单表' AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `zx_orders`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_order_details`
--

CREATE TABLE IF NOT EXISTS `zx_order_details` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `orgOrderNo` varchar(64) NOT NULL COMMENT '原订单号：每笔收益都是跟用户购买订单相关联的，无论是到期还是转让',
  `profitCustomNo` varchar(64) NOT NULL COMMENT '收益客户编码',
  `profitType` varchar(16) NOT NULL COMMENT '收益类型：TRANS：转让收益；EXP:到期',
  `orgBidNo` varchar(64) NOT NULL COMMENT '原始募投标的编码',
  `buyAmount` decimal(13,2) NOT NULL DEFAULT '0.00' COMMENT '原购买金额',
  `buyShare` int(11) NOT NULL DEFAULT '0' COMMENT '原购买份额',
  `fee` decimal(13,2) DEFAULT '0.00' COMMENT '交易手续费',
  `totalProfit` decimal(13,2) NOT NULL DEFAULT '0.00' COMMENT '总收益',
  `onPeriod` int(10) NOT NULL COMMENT '购买周期：单位：天',
  `origOrderTime` bigint(13) NOT NULL COMMENT '原下单时间',
  `createTime` bigint(13) NOT NULL COMMENT '创建时间',
  `lastModifyTime` bigint(13) DEFAULT NULL COMMENT '最近修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户投资收益明细表（针对用户已投资完成的' AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `zx_order_details`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_project_funds`
--


CREATE TABLE IF NOT EXISTS `zx_project_funds` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `projectNo` varchar(64) NOT NULL COMMENT '项目编码',
  `merchantName` varchar(64) NOT NULL COMMENT '商户名称',
  `bankCardNo` varchar(32) NOT NULL COMMENT '银行卡号',
  `bank` varchar(32) NOT NULL COMMENT '开户行',
  `province` varchar(32) NOT NULL COMMENT '开户省',
  `city` varchar(32) NOT NULL COMMENT '开户市',
  `switch` varchar(256) NOT NULL COMMENT '开户分支机构',
  `address` varchar(256) NOT NULL COMMENT '开户地址',
  `amount` decimal(13,2) NOT NULL DEFAULT '0.00' COMMENT '金额',
  `createTime` bigint(13) NOT NULL COMMENT '创建时间',
  `lastModifyTime` bigint(13) DEFAULT NULL COMMENT '最近修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='项目募投资金去向表' AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `zx_project_funds`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_project_informations`
--

CREATE TABLE IF NOT EXISTS `zx_project_informations` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `projectNo` varchar(32) NOT NULL COMMENT '项目编码',
  `projectName` varchar(64) NOT NULL COMMENT '项目名称',
  `projectType` varchar(64) DEFAULT NULL COMMENT '项目类型',
  `projectDescShort` text COMMENT '项目短描述：适应于APP端',
  `projectDesc` text COMMENT '项目描述：适用于PC端',
  `projectCustomNo` varchar(32) NOT NULL COMMENT '借款方客户编号',
  `contactNo` varchar(64) NOT NULL COMMENT '合同编号',
  `financedAmount` decimal(13,2) NOT NULL DEFAULT '0.00' COMMENT '项目募集额',
  `yield` decimal(6,4) NOT NULL DEFAULT '0.0000' COMMENT '项目收益率',
  `startDate` bigint(13) NOT NULL COMMENT '项目起息日',
  `endDate` bigint(13) NOT NULL COMMENT '项目结束日期',
  `guarantorCustomNo` varchar(32) DEFAULT NULL COMMENT '担保方客户编号',
  `createTime` bigint(13) NOT NULL COMMENT '创建时间',
  `lastModifyTime` bigint(13) DEFAULT NULL COMMENT '最近修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='项目信息表' AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `zx_project_informations`
--

-- --------------------------------------------------------

--
-- 表的结构 `zx_project_progress`
--

CREATE TABLE IF NOT EXISTS `zx_project_progress` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `projectNo` varchar(32) NOT NULL COMMENT '项目编码',
  `totalAmount` decimal(13,2) NOT NULL DEFAULT '0.00' COMMENT '项目募集额',
  `totalShare` int(11) NOT NULL DEFAULT '0' COMMENT '标的份额',
  `totalInvestedAmount` decimal(13,2) NOT NULL DEFAULT '0.00' COMMENT '总已投资额',
  `totalInvestedShare` int(11) NOT NULL DEFAULT '0' COMMENT '总已投资份额',
  `totalInvestedRate` decimal(6,4) DEFAULT '0.0000' COMMENT '总已投资额占比',
  `createTime` bigint(13) NOT NULL COMMENT '创建时间',
  `lastModifyTime` bigint(13) DEFAULT NULL COMMENT '最近修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='项目进度信息表' AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `zx_project_progress`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_rate_details`
--

CREATE TABLE IF NOT EXISTS `zx_rate_details` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `feeRuleNo` varchar(64) NOT NULL COMMENT '计费规则编码',
  `start` decimal(13,2) NOT NULL DEFAULT '0.00' COMMENT '起始金额',
  `end` decimal(13,2) NOT NULL DEFAULT '0.00' COMMENT '结束金额',
  `feeRuleType` varchar(16) NOT NULL COMMENT '计费规则类型：AMT:金额；PER:百分比',
  `value` decimal(13,2) DEFAULT '0.00' COMMENT '计费值：如果计费规则类型为AMT,则为具体金额；如果计费规则类型为PER，则为去除百分号的百分比',
  `createTime` bigint(13) NOT NULL COMMENT '创建时间',
  `lastModifyTime` bigint(13) DEFAULT NULL COMMENT '最近修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='费率规则明细' AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `zx_rate_details`
--

-- --------------------------------------------------------

--
-- 表的结构 `zx_rate_rules`
--

CREATE TABLE IF NOT EXISTS `zx_rate_rules` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `feeRuleNo` varchar(64) NOT NULL COMMENT '计费规则编码',
  `feeRuleName` varchar(64) NOT NULL COMMENT '计费规则名称',
  `feeRuleType` varchar(16) NOT NULL COMMENT '计费规则类型：AMT:金额；PER:百分比；LAD:阶梯',
  `value` decimal(13,2) DEFAULT '0.00' COMMENT '计费值:如果计费规则类型为AMT,则为具体金额；如果计费规则类型为PER，则为去除百分号的百分比；如果计费规则类型为LAD，则为空，需要关联费率规则明细找到具体数值',
  `createTime` bigint(13) NOT NULL COMMENT '创建时间',
  `lastModifyTime` bigint(13) DEFAULT NULL COMMENT '最近修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='费率规则' AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `zx_rate_rules`
--

-- --------------------------------------------------------

--
-- 表的结构 `zx_recharge_records`
--

CREATE TABLE IF NOT EXISTS `zx_recharge_records` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `userId` bigint(20) NOT NULL COMMENT '用户Id',
  `orderNo` varchar(32) NOT NULL COMMENT '订单编号',
  `busiType` varchar(64) NOT NULL COMMENT '业务类型',
  `amount` decimal(13,2) NOT NULL DEFAULT '0.00' COMMENT '金额',
  `status` varchar(16) NOT NULL COMMENT '状态',
  `createTime` bigint(13) NOT NULL COMMENT '创建时间',
  `lastModifyTime` bigint(13) DEFAULT NULL COMMENT '最近修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='充值提现记录表' AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `zx_recharge_records`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_record_informations`
--

CREATE TABLE IF NOT EXISTS `zx_record_informations` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `signType` varchar(16) NOT NULL COMMENT '签约类型',
  `signSource` varchar(16) NOT NULL COMMENT '签约来源：PC/QPOS/MR/ZXB',
  `signVersion` varchar(16) DEFAULT NULL COMMENT '签约版本号',
  `signContent` text COMMENT '签约内容',
  `signurl` text COMMENT '支付通协议连接',
  `createTime` bigint(13) NOT NULL COMMENT '创建时间',
  `lastModifyTime` bigint(13) DEFAULT NULL COMMENT '最近修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='签约内容信息' AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `zx_record_informations`
--

-- --------------------------------------------------------

--
-- 表的结构 `zx_record_signings`
--

CREATE TABLE IF NOT EXISTS `zx_record_signings` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `userId` bigint(20) DEFAULT NULL COMMENT '签约用户Id：当签约来源为PC/ZXB时不能为空，对应用户的id',
  `merchantNo` varchar(64) DEFAULT NULL COMMENT '商户编号：当签约来源为QPOS/MR时不能为空',
  `source` varchar(16) NOT NULL COMMENT '签约来源：PC/QPOS/MR/ZXB',
  `signVersion` varchar(16) DEFAULT NULL COMMENT '签约版本号',
  `signTime` bigint(13) NOT NULL COMMENT '签约时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='签约记录表' AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `zx_record_signings`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_smscodes`
--

CREATE TABLE IF NOT EXISTS `zx_smscodes` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `mobile` varchar(12) NOT NULL COMMENT '手机号码',
  `code` int(6) NOT NULL COMMENT '短信验证码',
  `content` varchar(128) NOT NULL COMMENT '短信内容',
  `ret` varchar(2) NOT NULL,
  `mid` int(2) NOT NULL,
  `cpmid` int(10) NOT NULL,
  `type` varchar(12) NOT NULL COMMENT '判断短信发送的类型，register为注册，repass为忘记密码时发送',
  `addtime` bigint(13) NOT NULL COMMENT '发送验证码时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='短信验证码表' AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `zx_smscodes`
--

-- --------------------------------------------------------

--
-- 表的结构 `zx_standard_informations`
--

CREATE TABLE IF NOT EXISTS `zx_standard_informations` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `version` int(11) NOT NULL COMMENT '乐观锁',
  `projectNo` varchar(32) NOT NULL COMMENT '项目编码',
  `bidType` varchar(32) DEFAULT NULL COMMENT '标的所属行业',
  `bidNo` varchar(32) NOT NULL COMMENT '标的编码',
  `bidName` varchar(64) NOT NULL COMMENT '标的名称',
  `bidDescShort` text COMMENT '标的短描述：适应于APP端',
  `bidDesc` text COMMENT '标的描述：适用于PC端',
  `bidCustomNo` varchar(64) NOT NULL COMMENT '借款方客户编号',
  `lowCustomNo` varchar(64) DEFAULT NULL COMMENT '原始标的托底购买客户编号',
  `bidStatus` varchar(16) NOT NULL COMMENT '募投标的状态：INIT：初始;AUDITED：已审核；PUBLISHED：已发布',
  `sortFlag` int(1) NOT NULL DEFAULT '0' COMMENT '排序标记：9对应PUBLISHED；5对应SUCCEED；1对应FINISHED',
  `financedAmount` decimal(13,2) NOT NULL DEFAULT '0.00' COMMENT '标的额',
  `financedShare` int(11) NOT NULL DEFAULT '0' COMMENT '标的份额',
  `leastBuyShare` int(10) NOT NULL DEFAULT '0' COMMENT '最小起买份额',
  `cycle` int(10) NOT NULL COMMENT '标的周期：单位:日',
  `yield` decimal(6,4) NOT NULL DEFAULT '0.0000' COMMENT '标的收益率',
  `lowestYield` decimal(6,4) NOT NULL DEFAULT '0.0000',
  `startDate` bigint(13) NOT NULL COMMENT '标的起息日',
  `endDate` bigint(13) NOT NULL COMMENT '标的到期日',
  `transferForbidPeriod` int(10) DEFAULT NULL COMMENT '标的封闭期',
  `openBidPeriod` int(10) DEFAULT NULL COMMENT '开放募集期',
  `openEndDate` bigint(13) NOT NULL COMMENT '募集结束日期',
  `guarantorCustomNo` varchar(32) DEFAULT NULL COMMENT '担保方客户编号',
  `permitTransferLastDate` bigint(13) DEFAULT NULL COMMENT '允许转让和购买的最后日期',
  `onlineDate` bigint(13) DEFAULT NULL COMMENT '上线日期',
  `createTime` bigint(13) NOT NULL COMMENT '创建时间',
  `lastModifyTime` bigint(13) DEFAULT NULL COMMENT '最近修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='标的信息表' AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `zx_standard_informations`
--

-- --------------------------------------------------------

--
-- 表的结构 `zx_standard_progress`
--

CREATE TABLE IF NOT EXISTS `zx_standard_progress` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `version` int(11) NOT NULL COMMENT '乐观锁',
  `bidNo` varchar(64) NOT NULL COMMENT '标的编码',
  `totalAmount` decimal(13,2) NOT NULL DEFAULT '0.00' COMMENT '标的额',
  `totalShare` int(11) NOT NULL DEFAULT '0' COMMENT '标的份额',
  `totalInvestedAmount` decimal(13,2) DEFAULT '0.00' COMMENT '总已投资额',
  `totalInvestedShare` int(11) DEFAULT '0' COMMENT '总已投资份额',
  `totalInvestedRate` decimal(6,4) DEFAULT '0.0000' COMMENT '总已投资额占比',
  `transferingAmount` decimal(13,2) DEFAULT '0.00' COMMENT '正在转让金额',
  `transferingShare` int(11) DEFAULT '0' COMMENT '正在转让份额',
  `createTime` bigint(13) NOT NULL COMMENT '创建时间',
  `lastModifyTime` bigint(13) DEFAULT NULL COMMENT '最近修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='标的进度信息表' AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `zx_standard_progress`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_standard_rates`
--

CREATE TABLE IF NOT EXISTS `zx_standard_rates` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `bidNo` varchar(64) NOT NULL COMMENT '标的编码',
  `feeType` varchar(16) NOT NULL COMMENT '计费类型：BUYORIG:原始购买；BUYTRANS:转让购买；TRANS:转让；LOWTRANS:托底转让',
  `feeRuleNo` varchar(64) NOT NULL COMMENT '计费规则编码',
  `status` varchar(16) NOT NULL COMMENT '状态：INIT:初始；AUDITED:已审核；PUBLISHED:已发布',
  `createTime` bigint(13) NOT NULL COMMENT '创建时间',
  `lastModifyTime` bigint(13) DEFAULT NULL COMMENT '最近修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='标的费率设置' AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `zx_standard_rates`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_standard_statistics`
--

CREATE TABLE IF NOT EXISTS `zx_standard_statistics` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `version` int(11) NOT NULL DEFAULT '1' COMMENT '乐观锁',
  `bidNo` varchar(64) NOT NULL COMMENT '标的编码',
  `userId` varchar(64) NOT NULL COMMENT '投资用户Id',
  `customType` varchar(16) DEFAULT NULL COMMENT '客户类型',
  `totalInvested` decimal(13,2) NOT NULL DEFAULT '0.00' COMMENT '总投资额',
  `totalInvestedShare` int(11) NOT NULL DEFAULT '0' COMMENT '总投资份额',
  `totalOnInvested` decimal(13,2) DEFAULT '0.00' COMMENT '在投资额',
  `totalOnInvestedShare` int(11) DEFAULT '0' COMMENT '在投资份额',
  `availableTrans` decimal(13,2) DEFAULT '0.00' COMMENT '可转让金额',
  `availableTransShare` int(11) DEFAULT '0' COMMENT '可转让份额',
  `transferedAmount` decimal(13,2) DEFAULT '0.00' COMMENT '已转让金额',
  `transferedShare` int(11) DEFAULT '0' COMMENT '已转让份额',
  `transferingAmount` decimal(13,2) DEFAULT '0.00' COMMENT '正在转让金额',
  `transferingShare` int(11) DEFAULT '0' COMMENT '正在转让份额',
  `achievedInterest` decimal(13,2) DEFAULT '0.00' COMMENT '已获取收益',
  `achievingInterest` decimal(13,2) DEFAULT '0.00' COMMENT '账面收益',
  `createTime` bigint(13) NOT NULL COMMENT '创建时间',
  `lastModifyTime` bigint(13) DEFAULT NULL COMMENT '最近修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='用户标的统计信息' AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `zx_standard_statistics`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_system_parameters`
--

CREATE TABLE IF NOT EXISTS `zx_system_parameters` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `paramName` varchar(128) NOT NULL COMMENT '参数名称',
  `paramKey` varchar(64) NOT NULL COMMENT '参数关键字',
  `paramValue` varchar(128) NOT NULL COMMENT '参数值',
  `createTime` bigint(13) NOT NULL COMMENT '创建时间',
  `lastModifyTime` bigint(13) DEFAULT NULL COMMENT '最近修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='系统全局参数信息表' AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `zx_system_parameters`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_task_errors`
--

CREATE TABLE IF NOT EXISTS `zx_task_errors` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `taskExecuteRecordId` varchar(64) NOT NULL COMMENT '任务执行记录Id',
  `errorKey` varchar(1024) NOT NULL COMMENT '信息关键字',
  `errorValue` text COMMENT '信息内容',
  `createTime` bigint(13) NOT NULL COMMENT '创建时间',
  `lastModifyTime` bigint(13) DEFAULT NULL COMMENT '最近修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='定时任务出错现场信息表' AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `zx_task_errors`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_task_informations`
--

CREATE TABLE IF NOT EXISTS `zx_task_informations` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `version` int(11) NOT NULL COMMENT '版本号：需要乐观锁控制',
  `taskNo` varchar(64) NOT NULL COMMENT '任务编号',
  `taskName` varchar(64) NOT NULL COMMENT '任务名称',
  `schedulerRule` varchar(64) NOT NULL COMMENT '定时规则表达式',
  `frozenStatus` varchar(16) NOT NULL COMMENT '冻结状态',
  `executorNo` varchar(128) NOT NULL COMMENT '执行方',
  `timeKey` varchar(32) NOT NULL COMMENT '执行时间格式',
  `frozenTime` bigint(13) DEFAULT NULL COMMENT '冻结时间',
  `unfrozenTime` bigint(13) DEFAULT NULL COMMENT '解冻时间',
  `createTime` bigint(13) NOT NULL COMMENT '创建时间',
  `lastModifyTime` bigint(13) DEFAULT NULL COMMENT '最近修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='定时任务信息表' AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `zx_task_informations`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_task_records`
--

CREATE TABLE IF NOT EXISTS `zx_task_records` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `taskNo` varchar(64) NOT NULL COMMENT '任务编号',
  `executorNo` varchar(64) NOT NULL COMMENT '执行方',
  `timeKeyValue` varchar(32) DEFAULT NULL COMMENT '执行时间格式值',
  `executeTime` bigint(13) NOT NULL COMMENT '执行时间',
  `taskStatus` varchar(16) NOT NULL COMMENT '任务状态',
  `failcount` int(10) DEFAULT NULL COMMENT '失败统计数',
  `failReason` varchar(64) DEFAULT NULL COMMENT '失败错误描述',
  `createTime` bigint(13) NOT NULL COMMENT '创建时间',
  `lastModifyTime` bigint(13) DEFAULT NULL COMMENT '最近修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='定时任务执行情况记录表' AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `zx_task_records`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_tbl_historys`
--

CREATE TABLE IF NOT EXISTS `zx_tbl_historys` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `ruleId` varchar(64) NOT NULL COMMENT '规则Id',
  `tbl_total` int(11) NOT NULL COMMENT '总资产表',
  `tbl_project` int(11) NOT NULL COMMENT '项目进度表',
  `tbl_bid` int(11) NOT NULL COMMENT '标的进度表',
  `tbl_user_bid` int(11) NOT NULL COMMENT '用户标的统计表',
  `tbl_order` int(11) NOT NULL COMMENT '订单',
  `tbl_transfer` int(11) NOT NULL COMMENT '转让信息',
  `tbl_buying` int(11) NOT NULL COMMENT '购买意愿请求',
  `checkStatus` varchar(16) NOT NULL COMMENT '平衡状态',
  `processResult` text COMMENT '处理结果',
  `operaterId` int(11) DEFAULT NULL COMMENT '处理人Id',
  `createTime` bigint(13) NOT NULL COMMENT '创建时间',
  `lastModifyTime` bigint(13) DEFAULT NULL COMMENT '最近修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='数据平衡检查历史表' AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `zx_tbl_historys`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_transfer_informations`
--

CREATE TABLE IF NOT EXISTS `zx_transfer_informations` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `version` int(11) NOT NULL COMMENT '乐观锁',
  `transferNo` varchar(64) NOT NULL COMMENT '转让编码',
  `transferCustomNo` varchar(64) NOT NULL COMMENT '转让客户编号',
  `transferCustomType` varchar(16) NOT NULL COMMENT '转让客户类型',
  `orgBidNo` varchar(64) NOT NULL COMMENT '原始募投标的编码',
  `transferShare` int(11) NOT NULL DEFAULT '0' COMMENT '转让份额',
  `transferedShare` int(11) NOT NULL DEFAULT '0' COMMENT '已转让份额',
  `validPeriod` int(10) NOT NULL COMMENT '转让有效期',
  `transferEndDate` bigint(13) DEFAULT NULL COMMENT '转让终止时间：根据设置的有效期自动根据转让创建时间+转让有效期计算得出',
  `transferStatus` varchar(16) DEFAULT NULL COMMENT '转让状态：TRASFERING：正在转让；SUCCESS：转让成功；FAILED：转让失败(有效期到)',
  `frozenStatus` varchar(16) NOT NULL COMMENT '冻结状态：FROZEN:已冻结；UNFROZEN:正常/已解冻',
  `transferPriority` int(1) NOT NULL COMMENT '转让优先级',
  `transferTime` bigint(13) NOT NULL COMMENT '转让时间',
  `lastModifyTime` bigint(13) DEFAULT NULL COMMENT '最近修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='转让信息表' AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `zx_transfer_informations`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_transfer_requests`
--

CREATE TABLE IF NOT EXISTS `zx_transfer_requests` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `version` int(11) NOT NULL COMMENT '乐观锁',
  `buyRequestNo` varchar(64) NOT NULL COMMENT '购买请求编码',
  `buyUserId` bigint(20) NOT NULL COMMENT '购买用户Id',
  `BidNo` varchar(64) NOT NULL COMMENT '募投标的编码',
  `requestType` varchar(16) NOT NULL COMMENT '请求类型：ORIG:原始标的购买；TRANS：转让购买',
  `buyingShare` int(11) NOT NULL DEFAULT '0' COMMENT '购买份额',
  `buyedShare` int(11) NOT NULL DEFAULT '0' COMMENT '已购买份额',
  `validPeriod` int(10) NOT NULL COMMENT '购买有效期',
  `buyValidEndDate` bigint(13) DEFAULT NULL COMMENT '购买结束时间',
  `buyRequestStatus` varchar(16) NOT NULL COMMENT '购买请求状态：BUYING：正在转让；SUCCESS：购买成功；PART:部分购买成功（有效期到）；FAILED：购买失败(有效期到)',
  `failReason` varchar(64) DEFAULT NULL COMMENT '失败原因',
  `frozenStatus` varchar(16) DEFAULT NULL COMMENT '冻结状态',
  `buyingType` varchar(16) DEFAULT NULL COMMENT '购买意愿类型',
  `createTime` bigint(13) NOT NULL COMMENT '请求发起时间',
  `lastModifyTime` bigint(13) DEFAULT NULL COMMENT '最近修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='购买意愿请求表' AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `zx_transfer_requests`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_users`
--

CREATE TABLE IF NOT EXISTS `zx_users` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `userNo` varchar(64) NOT NULL COMMENT '用户编号',
  `userNickName` varchar(128) DEFAULT NULL COMMENT '用户别名',
  `userPass` varchar(32) DEFAULT NULL COMMENT '用户登录密码',
  `payPassStatus` varchar(16) DEFAULT NULL COMMENT '用户支付密码设置状态:INIT:初始未设置 SETED:已设置',
  `userStatus` varchar(16) DEFAULT NULL COMMENT '用户状态：NORM：正常；FROZED：已冻结；RMVED：已注销',
  `userType` varchar(16) DEFAULT NULL COMMENT '用户类型:USR:个人用户;MER:商户',
  `userSource` varchar(16) DEFAULT NULL COMMENT '用户来源：PC/QPOS/MR/ZXB',
  `authStatus` varchar(16) DEFAULT NULL COMMENT '实名认证状态：INIT：初始未认证；AUTHED：已认证',
  `bankBindStatus` varchar(16) DEFAULT NULL COMMENT '银行卡绑定状态：INIT：初始未绑定；BINDED：已绑定',
  `bindedBankCardNum` int(3) DEFAULT NULL COMMENT '已绑定银行卡张数',
  `mobile` varchar(32) NOT NULL COMMENT '用户手机号码',
  `platformMerNo` varchar(64) DEFAULT NULL COMMENT '平台商户号：如果为QPOS注册，则此为众信用户关联的支付通QPOS商户号',
  `platformUserId` varchar(64) NOT NULL COMMENT '平台用户Id',
  `platformCustomNo` varchar(64) DEFAULT NULL COMMENT '平台客户号',
  `userRegTime` bigint(13) NOT NULL COMMENT '用户注册时间',
  `lastModifyTime` bigint(13) DEFAULT NULL COMMENT '最近修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='用户表' AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `zx_users`
--

-- --------------------------------------------------------

--
-- 表的结构 `zx_user_informations`
--

CREATE TABLE IF NOT EXISTS `zx_user_informations` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `userId` bigint(20) NOT NULL COMMENT '用户Id',
  `userRealName` varchar(128) NOT NULL COMMENT '用户实名',
  `userCreditial` varchar(64) NOT NULL COMMENT '用户身份证号',
  `authStatus` varchar(16) DEFAULT NULL COMMENT '实名认证状态：INIT：初始未认证；AUTHED：已认证；FAILED：认证失败',
  `authTime` bigint(13) NOT NULL COMMENT '认证时间',
  `lastModifyTime` bigint(13) DEFAULT NULL COMMENT '最近修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='用户实名信息表' AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `zx_user_informations`
--

-- --------------------------------------------------------

--
-- 表的结构 `zx_versions`
--

CREATE TABLE IF NOT EXISTS `zx_versions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` varchar(10) NOT NULL COMMENT 'INIT:初始；PUBLISHED:发布',
  `renewal` varchar(10) DEFAULT NULL COMMENT 'compel:强制更新;hand:手动更新',
  `appurl` varchar(255) NOT NULL,
  `version` char(20) NOT NULL,
  `model` varchar(128) DEFAULT NULL,
  `content` text NOT NULL,
  `code` int(4) NOT NULL COMMENT '版本码',
  `type` int(4) NOT NULL DEFAULT '1',
  `number` int(11) NOT NULL DEFAULT '0',
  `keyvalue` varchar(32) NOT NULL,
  `createtime` bigint(13) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `zx_versions`
--

--
-- 表的结构 `zx_provinces`
--

CREATE TABLE IF NOT EXISTS `zx_provinces` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `provinceNo` varchar(64) NOT NULL COMMENT '省编码',
  `provinceName` varchar(64) NOT NULL COMMENT '省名称',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='省' AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `zx_provinces`
--

--
-- 表的结构 `zx_cities`
--

CREATE TABLE IF NOT EXISTS `zx_cities` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `cityNo` varchar(64) NOT NULL COMMENT '市编码',
  `cityName` varchar(64) NOT NULL COMMENT '市名称',
  `provinceNo` varchar(64) NOT NULL COMMENT '所属省编码',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='市' AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `zx_cities`
--

--
-- 表的结构 `zx_backing_requests`
--

CREATE TABLE IF NOT EXISTS `zx_backing_requests` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `userId` bigint(20) NOT NULL COMMENT '托底转让发起方userId',
  `origBidNo` varchar(64) NOT NULL COMMENT '标的编码',
  `transferAmount` decimal(13,2) DEFAULT '0.00' COMMENT '托底转让金额',
  `transferShare` bigint(20) DEFAULT '0' COMMENT '托底转让份额',
  `accountingPeriod` int(10) NOT NULL COMMENT '入账周期',
  `requestExecuteDate` bigint(20) NOT NULL COMMENT '请求执行日期',
  `requestStatus` varchar(16) NOT NULL COMMENT '请求状态',
  `requestInterest` decimal(6,4) NOT NULL DEFAULT '0.0000' COMMENT '请求发起时托底转让利率',
  `requestFee` decimal(13,2) NOT NULL DEFAULT '0.00' COMMENT '请求发起时托底转让手续费',
  `createTime` bigint(13) NOT NULL COMMENT '创建时间',
  `lastModifyTime` bigint(13) DEFAULT NULL COMMENT '最近修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='用户托底转让请求表' AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `zx_backing_requests`
--

--
-- 表的结构 `zx_transfer_accounts`
--

CREATE TABLE IF NOT EXISTS `zx_transfer_accounts` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `flowNo` varchar(64) NOT NULL,
  `orderNo` varchar(64) DEFAULT NULL,
  `fromCustomNo` varchar(64) NOT NULL COMMENT '源客户编码',
  `fromCustomType` varchar(16) NOT NULL COMMENT '源客户类型',
  `toCustomNo` varchar(64) NOT NULL COMMENT '目标客户编码',
  `toCustomType` varchar(16) NOT NULL COMMENT '目标客户类型',
  `amount` decimal(13,2) NOT NULL DEFAULT '0.00' COMMENT '转账金额',
  `accountTransferStatus` varchar(16) NOT NULL COMMENT '转账状态',
  `hkFlowNo` varchar(64) DEFAULT NULL COMMENT '海科交易流水号',
  `rspCode` varchar(64) DEFAULT NULL COMMENT '返回码',
  `rspMsg` varchar(64) DEFAULT NULL COMMENT '返回消息',
  `createTime` bigint(13) NOT NULL COMMENT '创建时间',
  `lastModifyTime` bigint(13) DEFAULT NULL COMMENT '最近修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='平台转账交易表' AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `zx_transfer_accounts`
--

--
-- 表的结构 `zx_withholding_transactions`
--

CREATE TABLE IF NOT EXISTS `zx_withholding_transactions` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `flowNo` varchar(64) NOT NULL COMMENT '交易流水号',
  `orderNo` varchar(64) DEFAULT NULL COMMENT '关联众信订单号',
  `platformUserId` varchar(64) NOT NULL COMMENT '代扣平台用户ID',
  `amount` decimal(13,2) NOT NULL DEFAULT '0.00' COMMENT '转账金额',
  `hkPayOrderNo` varchar(64) NOT NULL COMMENT '海科返回支付订单号',
  `hkPrdOrderNo` varchar(64) DEFAULT NULL COMMENT '海科返回商品订单号',
  `rspCode` varchar(64) DEFAULT NULL COMMENT '返回码',
  `rspMsg` varchar(64) DEFAULT NULL COMMENT '返回消息',
  `bankCardNo` varchar(64) NOT NULL COMMENT '代扣用户银行卡号',
  `WithHoldStatus` varchar(16) NOT NULL COMMENT '代扣状态',
  `hkFlowNo` varchar(64) DEFAULT NULL COMMENT '海科交易流水号',
  `createTime` bigint(13) NOT NULL COMMENT '创建时间',
  `lastModifyTime` bigint(13) DEFAULT NULL COMMENT '最近修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='平台代扣交易表' AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `zx_withholding_transactions`
--

--
-- 表的结构 `zx_trade_makings`
--

CREATE TABLE IF NOT EXISTS `zx_trade_makings` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `version` bigint(20) NOT NULL,
  `taskName` varchar(64) NOT NULL,
  `taskStatus` varchar(16) NOT NULL,
  `createTime` bigint(20) NOT NULL,
  `lastModifyTime` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='撮合服务执行控制表' AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `zx_trade_makings`
--
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $pagetitle; ?></title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <link rel="stylesheet" type="text/css" href="/static/bootstrap/css/bootstrap.css" media="all"/>
    <link rel="stylesheet" type="text/css" href="/static/layui/css/layui.css" media="all"/>
    <?php if($navigation == "advertising"){?>
    <link rel="stylesheet" type="text/css" href="/static/css/globalforAd.css" media="all"/>
    <?php } else if($navigation == "traffic" ) { ?>
    <link rel="stylesheet" type="text/css" href="/static/css/globalfortraffic.css" media="all"/>
    <?php } else if($navigation == "platform") { ?>
    <link rel="stylesheet" type="text/css" href="/static/css/globalforplatform.css" media="all"/>
    <?php } else { ?>
    <link rel="stylesheet" type="text/css" href="/static/css/globalforAd.css" media="all"/>
    <link rel="stylesheet" type="text/css" href="/static/css/globalfortraffic.css" media="all"/>
    <link rel="stylesheet" type="text/css" href="/static/css/globalforplatform.css" media="all"/>
    <?php }?>
    <style>
        .layui-table-cell { height:auto}
    </style>
</head>
<body>
<div class="layui-layout layui-layout-admin">
    <div class="layui-header">
        <div class="layui-logo">
            <span class="loginname"><?php echo $platform; ?></span>
            <span class="layuitoptags"><?php echo $modename; ?></span>
        </div>
        <!-- 头部区域（可配合layui已有的水平导航） -->
        <ul class="layui-nav">
            <li class="layui-nav-item layui-this">
                <a href="javascript:;"><img src="/static/images/head.png" class="headerpic" /><span>18600904754</span></a>
                <dl class="layui-nav-child">
                    <dd><a href="">退出</a></dd>
                </dl>
            </li>
        </ul>
    </div>
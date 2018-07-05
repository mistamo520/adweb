<?php
define('ORDER_ROOT', str_replace('\\','/',dirname(__FILE__)));
require_once(dirname(__FILE__). '/include/application.php');
//cjt 2014-11-13
require_once(dirname(__FILE__).'/include/Controller.php');

//post后退问题
session_cache_limiter('private, must-revalidate');
header('Cache-control: private, must-revalidate');
/* magic_quota_gpc */
$_GET = magic_gpc($_GET);
$_POST = magic_gpc($_POST);
$_COOKIE = magic_gpc($_COOKIE);

/* process currefer*/
$currefer = uencode(strval($_SERVER['REQUEST_URI']));

/* session,cache,configure,webroot register */
Session::Init();
$INI = ZSystem::GetINI();

/* end */

/* date_zone */
if(function_exists('date_default_timezone_set')) { 
	date_default_timezone_set($INI['system']['timezone']); 
}
/* end date_zone */


/* biz logic */
$login_user_id = ZLogin::GetLoginId();
$login_user = Table::Fetch('zx_users', $login_user_id);


if (!isset($_COOKIE['referer'])) {
	setcookie('referer',$_SERVER['HTTP_REFERER']);
}
//if( ! preg_match('#/wait#i',$_SERVER['SCRIPT_NAME'], $m) && ! preg_match('#/manage/#i',$_SERVER['SCRIPT_NAME'] ) && ! preg_match('#/subscribe#i',$_SERVER['SCRIPT_NAME'] ) ){
//	redirect( WEB_ROOT . '/wait.php');
//}

/* not allow access app.php */
if($_SERVER['SCRIPT_FILENAME']==__FILE__){
	redirect( WEB_ROOT . '/index.php');
}

/* end */
$AJAX = ('XMLHttpRequest' == @$_SERVER['HTTP_X_REQUESTED_WITH']);

if (false==$AJAX) { 
	header('Content-Type: text/html; charset=UTF-8'); 
	//run_cron();
} else {
	header("Cache-Control: no-store, no-cache, must-revalidate");
}
if(!empty($_SESSION['notice'])) {
	//操作提示
	$operateMsg = $_SESSION['notice'];
	unset($_SESSION['notice']);
}

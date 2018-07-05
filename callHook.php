<?php
/* 主请求方法，主要目的拆分URL请求 */
function callHook() 
{
	global $url;
	if(empty($url))
	{
		$group = DEFAULT_GROUP;
		$controller = 'IndexAction';
		$action = 'index';
		$queryString = array();
		$contro = 'index';
	}
	else
	{
		$urlArray = array();
		$urlArray = explode("/",$url);
		$group = $urlArray[0];
		array_shift($urlArray);
		$contro = ucfirst($urlArray[0]);
		$controller = $contro.'Action';
		array_shift($urlArray);
		$action = $urlArray[0];
		array_shift($urlArray);
		$queryString = $urlArray;
	}
	if(file_exists(ROOT.DS.'App'.DS.'controllers'.DS.$group.DS.$controller.'.class.php'))
	{
		require_once(ROOT.DS.'App'.DS.'controllers'.DS.$group.DS.$controller.'.class.php');
	}
	else
	{
		redirect('/');
		/* 生成错误代码 */
		die();
	}
	$dispatch = new $controller($contro,$action);
	if ((int)method_exists($controller, $action)) 
	{
		call_user_func_array(array($dispatch,$action),$queryString);
	} 
	else 
	{
		/* 生成错误代码 */
	}
}
callHook();
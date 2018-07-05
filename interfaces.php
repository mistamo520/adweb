<?php
define('ORDER_ROOT', str_replace('\\','/',dirname(__FILE__)));
require_once(ORDER_ROOT . '/include/appconfig.php');
foreach ($_REQUEST as $key=>$item) {
	$_REQUEST[$key] = filter_keyword( $item ) ;
}
$array_gets = $_GET ;
$array_post = ZPublic::getParamArr() ;
$array_notify = array_merge( urldecode($array_gets) , $array_post );
if (empty($array_notify['app']) && !empty($_REQUEST['app'] ) &&  in_array($_REQUEST['app'], array('web', 'wap','others'))) {
	$array_notify = array_merge( $array_gets , $_POST );
}
$filepath = ORDER_ROOT.'/log/zhongxin/'.date('Y').'/'.date('m').'/'.date('d').'/params/'.$array_notify['app'].'/'.$array_notify['op'].'.txt';
Klogger::log( $filepath  , print_r( log_filter($array_notify) , true )  );
//根据提交参数进行验签
if(!empty($array_notify) && is_array( $array_notify ))
{
	$postsign = $array_notify['sign'] ;
	$app = $array_notify['app'];
	$v = $array_notify['v'];
	$op = $array_notify['op'];
	$source = $array_notify['psource'];
	unset( $array_notify['sign'] ) ;
	unset( $array_notify['app'] );
	unset( $array_notify['v'] );
	unset( $array_notify['op'] );
	unset( $array_notify['psource'] );
	$paramkey = array_keys( $array_notify ) ;
	sort( $paramkey ) ;
	$signstr = '' ;
	foreach ( $paramkey as $key => $val ){
		$signstr .= $array_notify[$val] ;
	}
	//系统分配的密匙
	if(in_array($source, array('ZXWEB', 'QPOS')) || $app == 'others') {
		$sql = "SELECT chanKey FROM zx_channel_keys WHERE interfaceVersion = '{$v}' AND chanCode = '{$source}' LIMIT 1;";
		$keyResult = DB::GetQueryResult($sql);
		$key = empty($keyResult['chankey']) ? '' : $keyResult['chankey'];
	} else {
		$key = $INI['system']['key'];
	}
	//签名
	$sign = md5($signstr.$key);
	if($sign == $postsign )
	{
		//根据参数的值调用对应的接口处理文件
		if( !isset( $op ) || empty( $op ))
		{
			$array['ret'] = 1103;
			$array['msg'] = "接口名称不能为空";
			echo json_encode($array);exit;
		}
		$op = ucfirst($op)."Web";
		if( file_exists( ORDER_ROOT . "/interface/{$app}/{$v}/{$op}.class.php" ) )
		{
			require_once( ORDER_ROOT . "/interface/{$app}/{$v}/{$op}.class.php" ) ;
			//cjt 2014-11-17
			$action = $_REQUEST['action']?$_REQUEST['action']:'index';
			$dispatch = new $op;
			if ((int)method_exists($op, $action)) 
			{
				$res = call_user_func(array($dispatch,$action));
				echo json_encode(array('ret' => 100, 'data' => $res));die;
			} 
			else 
			{
				die();
				/* 生成错误代码 */
			}
		}
		else 
		{
			$array['ret'] = 1104;
			$array['msg'] = "{$app}/{$v}/{$op}.class.php"."不存在";
			echo json_encode($array);exit;
		}
	}
	else 
	{
		$array['ret'] = 1102;
		$array['msg'] = "签名无效";
		echo json_encode($array);exit;
	}
}
else 
{
	$array['ret'] = 1101;
	$array['msg'] = "接口地址错误";
	echo json_encode($array);exit;
}

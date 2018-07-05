<?php
define('ENTRY_ROOT', str_replace('\\','/',dirname(__FILE__)));
require_once(ENTRY_ROOT . '/app.php');
$array_gets = $_GET ;
$array_post = ZPublic::getParamArr() ;
$array_notify = array_merge( $array_gets , $array_post );
$filepath = ENTRY_ROOT.'/log/zhongxin/'.date('Y').'/'.date('m').'/'.date('d').'/entry/'.$array_notify['psource'].'.txt';
Klogger::log( $filepath  , print_r( $array_notify , true )  );
//根据提交参数进行验签
if(!empty($array_notify) && is_array( $array_notify ))
{
	$postsign = $array_notify['sign'] ;
	$v = $array_notify['v'];
	$source = $array_notify['psource'];
	unset( $array_notify['sign'] ) ;
	unset( $array_notify['v'] );
	unset( $array_notify['psource'] );
	$paramkey = array_keys( $array_notify ) ;
	sort( $paramkey ) ;
	$signstr = '' ;
	foreach ( $paramkey as $key => $val ){
		$signstr .= $array_notify[$val] ;
	}
	//系统分配的密匙
	$sql = "SELECT chanKey FROM zx_channel_keys WHERE interfaceVersion = '{$v}' AND chanCode = '{$source}' LIMIT 1;";
	$keyResult = DB::GetQueryResult($sql);
	$key = empty($keyResult['chankey']) ? '' : $keyResult['chankey'];
	//签名
	$sign = md5($signstr.$key);
	$mobile=$array_notify['mobile'];
	$standardid=$array_notify['standardid'];
	$uid=$array_notify['UID'];
	if($sign == $postsign && !empty($mobile))
	{
		$data = WebInterfaces::getData('Register', array(
			'action' => 'checkmobile',
			'mobile' => $mobile,
		));
		if($data['userinfo'])
		{
			redirect(WEB_ROOT."/trust/thirdentry/loginpage/$source/$mobile/$standardid");exit;
		}
		else
		{
			redirect(WEB_ROOT."/trust/thirdentry/registerpage/$source/$mobile");exit;
		}
	}
}else{
	redirect(WEB_ROOT."/trust/login/index");exit;
}

?>
<?php
	require_once('./app.php');
	if($_GET['source']=='78084224f01acfae2eb6c5d2f0978470')
	{
		$sourceName = '网贷财富';
	}
	else if($_GET['source']=='d04796195ba046a5e57d31a2c288162e')
	{
		$sourceName = '网贷第三方';
	}
	else if($_GET['source']=='6b4857603831aae278ac20f9440dca37')
	{
		$sourceName = '融途网'; 
	}
	else if($_GET['source']=='e4c71b043cc3eba21b9eaed8334b195c')
	{
		$sourceName = '网贷界(p2pworld)';
	}
	else if($_GET['source']=='bdc9388f043bd578f7d962355005167b')
	{
		$sourceName = '网贷公社';
	}
	else if($_GET['source']=='2ea2626368da637b68f4712891f4cf5e')
	{
		$sourceName = '网贷天眼';
	}
	else
	{
		$sourceName = '其他';
	}
	$source = $_GET['source']?$_GET['source']:'';
	$ip = getLoginIP();
	$http_referer = $_SERVER['HTTP_REFERER'];
	
	$data = WebInterfaces::getData('Redirect', array(
			'action' => 'index',
			'source'=>$source,
			'sourceName'=>$sourceName,
			'ip'=>$ip,
			'http_referer'=>$http_referer, 
	));
	header("location:http://www.imzhongxin.com");
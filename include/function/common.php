<?php
/* import other */
import('configure');
//import('current');
import('rewrite');
import('utility');
import('pay');
import('sms');

function template($tFile, $folder) {
	global $INI;
	if ( 0===strpos($tFile, 'manage') ) {
		return __template($tFile);
	}
	if ($INI['skin']['template']) {
		$templatedir = DIR_TEMPLATE. '/' . $INI['skin']['template'];
		$checkfile = $templatedir.'/'.$folder . '/html_header.html';
		if ( file_exists($checkfile) ) {
			return __template($INI['skin']['template'].'/'.$folder.'/'.$tFile);
		}
	}
	return __template($tFile, $folder);
}

function render($tFile, $folder, $vs=array()) {
    ob_start();
    foreach($GLOBALS AS $_k=>$_v) {
        ${$_k} = $_v;
    }
	foreach($vs AS $_k=>$_v) {
		${$_k} = $_v;
	}
	include template($tFile, $folder);
    return render_hook(ob_get_clean());
}

function render_hook($c) {
	global $INI;
	$c = preg_replace('#href="/#i', 'href="'.WEB_ROOT.'/', $c);
	$c = preg_replace('#src="/#i', 'src="'.WEB_ROOT.'/', $c);
	$c = preg_replace('#action="/#i', 'action="'.WEB_ROOT.'/', $c);

	/* theme */
	$page = strval($_SERVER['REQUEST_URI']);
	if($INI['skin']['theme'] && !preg_match('#/manage/#i',$page)) {
		$themedir = WWW_ROOT. '/static/theme/' . $INI['skin']['theme'];
		$checkfile = $themedir. '/css/index.css';
		if ( file_exists($checkfile) ) {
			$c = preg_replace('#/static/css/#', "/static/theme/{$INI['skin']['theme']}/css/", $c);
			$c = preg_replace('#/static/img/#', "/static/theme/{$INI['skin']['theme']}/img/", $c);
		}
	}
	//$c = preg_replace('#([\'\=\"]+)/static/#', "$1{$INI['system']['cssprefix']}/static/", $c);
	if (strtolower(cookieget('locale','zh_cn'))=='zh_tw') {
		require_once(DIR_FUNCTION  . '/tradition.php');
		$c = str_replace(explode('|',$_charset_simple), explode('|',$_charset_tradition),$c);
	}
	/* encode id */
	$c = rewrite_hook($c);
	$c = obscure_rep($c);
	return $c;
}

function output_hook($c) {
	global $INI;
	if ( 0==abs(intval($INI['system']['gzip'])))  die($c);
	$HTTP_ACCEPT_ENCODING = $_SERVER["HTTP_ACCEPT_ENCODING"]; 
	if( strpos($HTTP_ACCEPT_ENCODING, 'x-gzip') !== false ) 
		$encoding = 'x-gzip'; 
	else if( strpos($HTTP_ACCEPT_ENCODING,'gzip') !== false ) 
		$encoding = 'gzip'; 
	else $encoding == false;
	if (function_exists('gzencode')&&$encoding) {
		$c = gzencode($c);
		header("Content-Encoding: {$encoding}"); 
	}
	$length = strlen($c);
	header("Content-Length: {$length}");
	die($c);
}

$lang_properties = array();
function I($key) { 
    global $lang_properties, $LC;
    if (!$lang_properties) {
        $ini = DIR_ROOT . '/i18n/' . $LC. '/properties.ini';
        $lang_properties = Config::Instance($ini);
    }
    return isset($lang_properties[$key]) ?
        $lang_properties[$key] : $key;
}

function json($data, $type='eval') {
    $type = strtolower($type);
    $allow = array('eval','alert','updater','dialog','mix', 'refresh');
    if (false==in_array($type, $allow))
        return false;
    Output::Json(array( 'data' => $data, 'type' => $type,));
}

function redirect($url=null, $notice=null, $error=null) {
	$url = $url ? obscure_rep($url) : $_SERVER['HTTP_REFERER'];
	$url = $url ? $url : '/';
	if ($notice) Session::Set('notice', $notice);
	if ($error) Session::Set('error', $error);
    header("Location: {$url}");
    exit;
}
function write_php_file($array, $filename=null){
	$v = "<?php\r\n\$INI = ";
	$v .= var_export($array, true);
	$v .=";\r\n?>";
	return file_put_contents($filename, $v);
}

function write_ini_file($array, $filename=null){   
	$ok = null;   
	if ($filename) {
		$s =  ";;;;;;;;;;;;;;;;;;\r\n";
		$s .= ";; SYS_INIFILE\r\n";
		$s .= ";;;;;;;;;;;;;;;;;;\r\n";
	}
	foreach($array as $k=>$v) {   
		if(is_array($v))   { 
			if($k != $ok) {   
				$s  .=  "\r\n[{$k}]\r\n";
				$ok = $k;   
			} 
			$s .= write_ini_file($v);
		}else   {   
			if(trim($v) != $v || strstr($v,"["))
				$v = "\"{$v}\"";   
			$s .=  "$k = \"{$v}\"\r\n";
		} 
	}

	if(!$filename) return $s;   
	return file_put_contents($filename, $s);
}   

function save_config($type='ini') {
	return configure_save();
	global $INI; $q = ZSystem::GetSaveINI($INI);
	if ( strtoupper($type) == 'INI' ) {
		if (!is_writeable(SYS_INIFILE)) return false;
		return write_ini_file($q, SYS_INIFILE);
	} 
	if ( strtoupper($type) == 'PHP' ) {
		if (!is_writeable(SYS_PHPFILE)) return false;
		return write_php_file($q, SYS_PHPFILE);
	} 
	return false;
}

function save_system($ini) {
	$system = Table::Fetch('system', 1);
	$ini = ZSystem::GetUnsetINI($ini);
	$value = Utility::ExtraEncode($ini);
	$table = new Table('system', array('value'=>$value));
	if ( $system ) $table->SetPK('id', 1);
	return $table->update(array( 'value'));
}

/* user relative */
function need_login($wap=false) {
	if ( isset($_SESSION['userid']) ) {
		if (is_post()) {
			unset($_SESSION['loginpage']);
			unset($_SESSION['loginpagepost']);
		}
		return $_SESSION['userid'];
	}
	if ( is_get() ) {
		Session::Set('loginpage', $_SERVER['REQUEST_URI']);
	} else {
		Session::Set('loginpage', $_SERVER['HTTP_REFERER']);
		Session::Set('loginpagepost', json_encode($_POST));
	}
	redirect( WEB_ROOT . '/trust/login/index' );
}
function need_post() {
	return is_post() ? true : redirect(WEB_ROOT . '/index.php');
}
function need_manager($super=false) {
	if ( isset($_SESSION['admin_id'] )) {
		return $_SESSION['admin_id'];
	}
	
	if ( abs(intval($_SESSION['admin_id'])) == 1 ) return true;
	redirect( WEB_ROOT . '/admin/login.php' );
}
function life_cycle($ajax=true){
	if ( isset($_SESSION['userid']) ) {
		if(isset($_SESSION['zx_life_cycle']) && $_SESSION['zx_life_cycle'] < time() ){
			$loginstatus = false;
			session_destroy();
			if($ajax){
				redirect('/trust/login/index');
			}else{
				echo 'nologin';exit;
			}
		}else {
			$_SESSION['zx_life_cycle'] = time() + 30 * 60;
			$userid = $_SESSION['userid'];
			if ($_SESSION['bindcard'] != true || $_SESSION['auto_status'] != true) {
				//开通存管信息查询
				if( !$_SESSION['bindcard'] || !$_SESSION['auto_status'] ){
					$repositinfo = ZApiDeposit::GetUserDepositInfo($userid,'status,auto_status,ecard_no,cust_type');
    				if(isset($repositinfo['status']) && !empty($repositinfo['status']) && ($repositinfo['status'] == 'NORMAL' || $repositinfo['status'] == 'ACCEPTANCE')){
    				    $_SESSION['bindcard'] = true;
						$_SESSION['custType']=$repositinfo['cust_type'];
						if(isset($repositinfo['auto_status']) && !empty($repositinfo['auto_status']) && $repositinfo['auto_status'] == 'ON' ){
    				        $_SESSION['auto_status'] = true;
    				    }
    				}else{
    				    $bidAccount = ZAccount::getAccountById($userid);
    				    if(isset($bidAccount['totaloninvest']) && $bidAccount['totaloninvest'] > 0 ){
    				        $_SESSION['flag'] = 0;
    				        redirect('/trust/deposit/open');
    				    }else{
    				        $balanceAccount = ZApiCunguan::getLocalBalance($userid);
								if(isset($balanceAccount->data->totalAmount) && $balanceAccount->data->totalAmount>0){
									$_SESSION['flag'] = 0;
									redirect('/trust/deposit/open');
								}
    				    }
    				}
				}
			}
		}
	}
}
function need_partner() {
	return is_partner() ? true : redirect( WEB_ROOT . '/biz/login.php');
}



function is_manager($super=false, $weak=false) {
	global $login_user;
	if ( $weak===false && 
			( !$_SESSION['admin_id'] 
			  || $_SESSION['admin_id'] != $login_user['id']) ) {
		return false;
	}
	if ( ! $super ) return ($login_user['manager'] == 'Y');
	return $login_user['id'] == 1;
}
function is_partner() {
	return ($_SESSION['partner_id']>0);
}

function is_newbie(){ return (cookieget('newbie')!='N'); }
function is_get() { return ! is_post(); }
function is_post() {
	return strtoupper($_SERVER['REQUEST_METHOD']) == 'POST';
}

function is_login() {
	return isset($_SESSION['user_id']);
}

function get_loginpage($default=null) {
	$loginpage = Session::Get('loginpage', true);
	if ($loginpage)  return $loginpage;
	if ($default) return $default;
	return WEB_ROOT . '/index.php';
}



function cookieset($k, $v, $expire=0) {
	$pre = substr(md5($_SERVER['HTTP_HOST']),0,4);
	$k = "{$pre}_{$k}";
	if ($expire==0) {
		$expire = time() + 365 * 86400;
	} else {
		$expire += time();
	}
	setCookie($k, $v, $expire, '/');
}

function cookieget($k, $default='') {
	$pre = substr(md5($_SERVER['HTTP_HOST']),0,4);
	$k = "{$pre}_{$k}";
	return isset($_COOKIE[$k]) ? strval($_COOKIE[$k]) : $default;
}

function moneyit($k) {
	return rtrim(rtrim(sprintf('%.2f',$k), '0'), '.');
}

function debug($v, $e=false) {
	global $login_user_id;
	if ($login_user_id==100000) {
		echo "<pre>";
		var_dump( $v);
		if($e) exit;
	}
}

function getparam($index=0, $default=0) {
	if (is_numeric($default)) {
		$v = abs(intval($_GET['param'][$index]));
	} else $v = strval($_GET['param'][$index]);
	return $v ? $v : $default;
}
function getpage() {
	$c = abs(intval($_GET['page']));
	return $c ? $c : 1;
}
function pagestring($count, $pagesize, $wap=false) {
	$p = new Pager($count, $pagesize, 'page');
	if ($wap) {
		return array($pagesize, $p->offset, $p->genWap());
	}
	return array($pagesize, $p->offset, $p->genBasic());
}
function pagestring2($count, $pagesize, $wap=false) {
	$p = new Pager($count, $pagesize, 'page');
	if ($wap) {
		return array($pagesize, $p->offset, $p->genWap2());
	}
	return array($pagesize, $p->offset, $p->GenBasicNew());
}
function pagestringmerchant($count, $pagesize, $wap=false) {
	$p = new PageMerchant($count, $pagesize, 'page');
	if ($wap) {
		return array($pagesize, $p->offset, $p->genWap());
	}
	return array($pagesize, $p->offset, $p->genBasic());
}

function uencode($u) {
	return base64_encode(urlEncode($u));
}
function udecode($u) {
	return urlDecode(base64_decode($u));
}

function domainit($url) {
	if(strpos($url,'//')) { preg_match('#[//]([^/]+)#', $url, $m);
} else { preg_match('#[//]?([^/]+)#', $url, $m); }
return $m[1];
}

function subtostring( $string , $length=0 ,$type=false )
{
	$string = strip_tags( $string ) ;
	if( empty( $string ) ) return '';
	if( !$length ) return $string ;
	
	if( !$type ){
		$j = 0;
		$newstr = '';
		for($i = 0; $i <= mb_strlen($string); $i++) {
			if( $j > $length ){
				$newstr .= '' ;
				break;
			}
			$word = mb_substr($string, $i, 1, 'utf-8');
			if(ord($word) > 127) {
				$j = $j + 1;
			} else {
				$j = $j + 0.5;
			}
			if( $j <= $length ){
				$newstr .= $word;
			}
		}
	}else{
		if( mb_strlen( $string ) > $length ){
			$newstr = mb_substr( $string , 0 , $length );
			$newstr .= '';
		}else{
			$newstr = $string ;
		}
	}

		return $newstr;
}

// that the recursive feature on mkdir() is broken with PHP 5.0.4 for
function RecursiveMkdir($path) {
	if (!file_exists($path)) {
		RecursiveMkdir(dirname($path));
		@mkdir($path, 0777);
	}
}

function upload_image($input, $image=null, $type='team', $scale=false) {
	$year = date('Y'); $day = date('md'); $n = time().rand(1000,9999).'.jpg';
	$z = $_FILES[$input];
	if ($z && strpos($z['type'], 'image')===0 && $z['error']==0) {
		if (!$image) { 
			RecursiveMkdir( IMG_ROOT . '/' . "{$type}/{$year}/{$day}" );
			$image = "{$type}/{$year}/{$day}/{$n}";
			$path = IMG_ROOT . '/' . $image;
		} else {
			RecursiveMkdir( dirname(IMG_ROOT .'/' .$image) );
			$path = IMG_ROOT . '/' .$image;
		}
		if ($type=='user') {
			Image::Convert($z['tmp_name'], $path, 48, 48, Image::MODE_CUT);
		} 
		else if($type=='team') {
			move_uploaded_file($z['tmp_name'], $path);
		}
		if($type=='team' && $scale) {
			$npath = preg_replace('#(\d+)\.(\w+)$#', "\\1_index.\\2", $path); 
			Image::Convert($path, $npath, 200, 120, Image::MODE_CUT);
			
			$npath = preg_replace('#(\d+)\.(\w+)$#', "\\1_all.\\2", $path); 
			Image::Convert($path, $npath, 315, 190, Image::MODE_CUT);
		}
		return $image;
	} 
	return $image;
}


//创建图片
function createimg($img, $type) {
	if(($type == 'image/jpeg') || ($type == 'image/pjpeg')) {
		$imgs = imagecreatefromjpeg($img);
	} elseif(($type == 'image/png') || ($type == 'image/x-png')) {
		$imgs = imagecreatefrompng($img);
	} elseif($type == 'image/gif') {
		$imgs = imagecreatefromgif($img);
	}
	return $imgs;
}


/**
 * @abstract 按照大小缩略图片
 * @param 图片，宽，高，文件名，类型，是否加水印，水印字符串，透明度
 * */
function thumbimg($imgs, $maxwidth, $maxheight, $filename, $type, $iswatermark=0, $logo="", $watermark = 100) {
	$imgwidth = imagesx($imgs);
	$imgheight = imagesy($imgs);
	if( $maxheight <= 0 ){
		//按照固定宽度缩
		if($imgwidth > $maxwidth) {
			$ratio = $maxwidth / $imgwidth;
		} else {
			$ratio = 1;
		}
		$newwidth = $imgwidth * $ratio;
		$newheight = $imgheight * $ratio;
		$x = ceil(($maxwidth - $newwidth) / 2);
		$y = ceil(($maxheight - $newheight) / 2);

		$newimg = imagecreatetruecolor($newwidth, $newheight);
		imagecopyresampled($newimg, $imgs, 0, 0, 0, 0, $newwidth, $newheight, $imgwidth, $imgheight);
	}else{
		//按照固定宽和高缩略
		if($imgwidth > $maxwidth) {
			$ratio = $maxwidth / $imgwidth;
			$newwidth = $imgwidth * $ratio;
			if($maxheight == 300 && $imgheight < 300){
            	$newheight = $imgheight;
			}else{
				$newheight = $maxheight;
			}
		} else {
			$ratio = $maxwidth / $imgwidth;
			$newwidth = $imgwidth * $ratio;
            $newheight = $imgheight * $ratio;
		}

		$newimg = imagecreatetruecolor($newwidth, $newheight);
		imagecopyresampled($newimg, $imgs, 0, 0, 0, 0, $newwidth, $newheight, $imgwidth, $imgheight);
	}
		
	$flag = self::showimg($newimg, $filename, $watermark, $type);
	ImageDestroy($newimg);
	return $flag ;
	
}

function imgtype($type) {
	$arr = array('image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png', 'image/gif');
	return in_array($type, $arr);
}

//图片，目录，文件名，类型，宽，高，是否是原图，是否加水印，水印字符串
function image_upload($img, $dir, $filename, $type, $width = 200, $height = 0, $isartwork = 0, $iswatermark = 0, $logo = 0) {
	RecursiveMkdir($dir);
	$subfix = imgtype($type);
	if($subfix) {
		if($isartwork == 0) {
			//$filename = $this->randnum(20);
			$imgs = createimg($img, $type);
			$flag = thumbimg($imgs, $width, $height, $dir.'/'.$filename, $type, $iswatermark, $logo);
			ImageDestroy($imgs);
			return $flag;
		} else {
			//原图
			return move_uploaded_file($img, $dir.'/'.$filename);
		}
	}
}

/**
 * 获取当前时间戳，精确到毫秒
 *
 * @return unknown
 */
function microtime_float()
{
   list($t1, $t2) = explode(' ', microtime());    
   return (float)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);  
}
/**
 * 转化时间戳类型
 *
 * @return unknown
 */
function unmicrotime($data){
	return date('Y-m-d', $data);
}
/**
 * 生成缩略图
 *
 * @param unknown_type $img       源文件
 * @param unknown_type $maxwidth  缩略图宽
 * @param unknown_type $maxheight 缩略图高
 * @param unknown_type $dstimg    生成的缩略图文件名[包含路径]
 * @param Boolean $is_cut 是否剪裁
 * @param Boolean $pmode 图片模式，是否保证约定尺寸输出图片（使用白色填充空白部分），0：不保证，1：保证；
 */
function imageResize($img,$maxwidth,$maxheight,$dstimg,$is_cut = false,$pmode=false)
{
	if(empty( $img ) || !file_exists( $img )) return false;
	//根据不同的格式读取源图片
	list($width, $height, $pic_info) = @getimagesize($img);
	switch($pic_info)
	{
		case 1: $image = imagecreatefromgif($img);break;//GIF
		case 2: $image = imagecreatefromjpeg($img);break;//JPG
		case 3: $image = imagecreatefrompng($img);imagesavealpha($image, true);break;//PNG
		case 6: $image = imagecreatefromwbmp($img);break;//BMP
		default:return false;
	}

	//计算成比例的宽高
	if($maxwidth && $width >= $maxwidth)
	{
		$widthratio = $maxwidth/$width;
		$RESIZEWIDTH=true;
	}
	else 
	{
		$RESIZEWIDTH=false;
	}
	if($maxheight && $height >= $maxheight)
	{
		$heightratio = $maxheight/$height;
		$RESIZEHEIGHT=true;
	}
	else 
	{
		$RESIZEHEIGHT=false;
	}

	$newwidth = $width;		// 新图片的宽度
	$newheight = $height;	// 新图片的高度
	$ratio = 1;				// 缩放比例
	$cut_side = 0;			// 剪裁的边，0：不需要；1：宽；2：高；
	$pos_x = 0;				// 偏移X
	$pos_y = 0;				// 偏移Y
	$target_width = $width;		// 原图的目标宽度
	$target_height = $height;	// 原图的目标高度

	if ($is_cut == false)
	{	// 不剪裁，保持原图的完整性
		if($RESIZEWIDTH && $RESIZEHEIGHT)
		{
			$ratio = min($widthratio, $heightratio);
		}
		elseif($RESIZEWIDTH)
		{
			$ratio = $widthratio;
		}
		elseif($RESIZEHEIGHT)
		{
			$ratio = $heightratio;
		}
		else
		{
			$ratio = 1;
		}
		$newwidth = $width * $ratio;
		$newheight = $height * $ratio;
	}
	else
	{	// 剪裁原图，保证目标图片尺寸的完整性
		if($RESIZEWIDTH && $RESIZEHEIGHT)
		{
			if ($widthratio > $heightratio)
			{
				$ratio = $widthratio;
				$newwidth = $width * $widthratio;
				$newheight = $maxheight;
				$cut_side = 2;
			}
			else
			{
				$ratio = $heightratio;
				$newwidth = $maxwidth;
				$newheight = $height * $heightratio;
				$cut_side = 1;
			}
		}
		elseif($RESIZEWIDTH)
		{
			$newheight = $maxheight;
			$cut_side = 2;
		}
		elseif($RESIZEHEIGHT)
		{
			$newwidth = $maxwidth;
			$cut_side = 1;
		}

		if ($cut_side == 1)
		{	// 剪切图片的宽
			$target_width = $newwidth / $ratio;
			$pos_x = ($width - $target_width) / 2;
		}
		else if ($cut_side == 2)
		{	// 剪切图片的高
			$target_height = $newheight / $ratio;
			$pos_y = ($height - $target_height) / 2;
		}
	}

	if(function_exists("imagecopyresampled"))
	{
		if ($pmode)
		{
			$newim = imagecreatetruecolor($maxwidth, $maxheight);//新建一个真彩色图像[黑色图像]
			$white = imagecolorallocate($newim,255,255,255);
			imagefilledrectangle($newim,0,0,$maxwidth,$maxheight,$white);
			imagecolortransparent($newim,$white);
			imagecopyresampled($newim, $image, ($maxwidth-$newwidth)/2, ($maxheight-$newheight)/2, $pos_x, $pos_y, $newwidth, $newheight, $target_width, $target_height);//重采样拷贝部分图像并调整大小
		}
		else
		{
			$newim = imagecreatetruecolor($newwidth, $newheight);//新建一个真彩色图像[黑色图像]
			$white = imagecolorallocate($newim,255,255,255);
			imagefilledrectangle($newim,0,0,$newwidth,$newheight,$white);
			imagecolortransparent($newim,$white);
			imagecopyresampled($newim, $image, 0, 0, $pos_x, $pos_y, $newwidth, $newheight, $target_width, $target_height);//重采样拷贝部分图像并调整大小
		}
	}
	else
	{
		if ($pmode)
		{
			$newim = imagecreate($maxwidth, $maxheight);
			$white = imagecolorallocate($newim,255,255,255);
			imagefilledrectangle($newim,0,0,$maxwidth,$maxheight,$white);
			imagecolortransparent($newim,$white);
			imagecopyresized($newim, $image, ($maxwidth-$newwidth)/2, ($maxheight-$newheight)/2, $pos_x, $pos_y, $newwidth, $newheight, $target_width, $target_height);
		}
		else
		{
			$newim = imagecreate($newwidth, $newheight);
			$white = imagecolorallocate($newim,255,255,255);
			imagefilledrectangle($newim,0,0,$newwidth,$newheight,$white);
			imagecolortransparent($newim,$white);
			imagecopyresized($newim, $image, 0, 0, $pos_x, $pos_y, $newwidth, $newheight, $target_width, $target_height);
		}
	}

	switch($pic_info)
	{
		case 1: imagegif($newim,$dstimg);break;		//GIF
		case 2: imagejpeg($newim,$dstimg,90);break;	//JPG
		case 3: imagepng($newim,$dstimg);break;		//PNG
		default:return false;
	}
	ImageDestroy($newim);
	return true;
}


function imageProduct($img,$maxwidth,$maxheight,$dstimg,$is_cut = false,$pmode=false)
{
	if(empty( $img ) || !file_exists( $img )) return false;
	//根据不同的格式读取源图片
	list($width, $height, $pic_info) = @getimagesize($img);
	switch($pic_info)
	{
		case 1: $image = imagecreatefromgif($img);break;//GIF
		case 2: $image = imagecreatefromjpeg($img);break;//JPG
		case 3: $image = imagecreatefrompng($img);imagesavealpha($image, true);break;//PNG
		case 6: $image = imagecreatefromwbmp($img);break;//BMP
		default:return false;
	}

	//计算成比例的宽高
	if($maxwidth && $width >= $maxwidth)
	{
		$widthratio = $maxwidth/$width;
		$RESIZEWIDTH=true;
	}
	else 
	{
		$RESIZEWIDTH=false;
	}
	if($maxheight && $height >= $maxheight)
	{
		$heightratio = $maxheight/$height;
		$RESIZEHEIGHT=true;
	}
	else 
	{
		$RESIZEHEIGHT=false;
	}

	$newwidth = $width;		// 新图片的宽度
	$newheight = $height;	// 新图片的高度
	$ratio = 1;				// 缩放比例
	$cut_side = 0;			// 剪裁的边，0：不需要；1：宽；2：高；
	$pos_x = 0;				// 偏移X
	$pos_y = 0;				// 偏移Y
	$target_width = $width;		// 原图的目标宽度
	$target_height = $height;	// 原图的目标高度

	if ($is_cut == false)
	{	// 不剪裁，保持原图的完整性
		if($RESIZEWIDTH && $RESIZEHEIGHT)
		{
			$ratio = min($widthratio, $heightratio);
		}
		elseif($RESIZEWIDTH)
		{
			$ratio = $widthratio;
		}
		elseif($RESIZEHEIGHT)
		{
			$ratio = $heightratio;
		}
		else
		{
			$ratio = 1;
		}
		$newwidth = $width * $ratio;
		$newheight = $height * $ratio;
	}
	else
	{	// 剪裁原图，保证目标图片尺寸的完整性
		if($RESIZEWIDTH && $RESIZEHEIGHT)
		{
			if ($widthratio > $heightratio)
			{
				$ratio = $widthratio;
				$newwidth = $width * $widthratio;
				$newheight = $maxheight;
				$cut_side = 2;
			}
			else
			{
				$ratio = $heightratio;
				$newwidth = $maxwidth;
				$newheight = $height * $heightratio;
				$cut_side = 1;
			}
		}
		elseif($RESIZEWIDTH)
		{
			$newheight = $maxheight;
			$cut_side = 2;
		}
		elseif($RESIZEHEIGHT)
		{
			$newwidth = $maxwidth;
			$cut_side = 1;
		}

		if ($cut_side == 1)
		{	// 剪切图片的宽
			$target_width = $newwidth / $ratio;
			$pos_x = ($width - $target_width) / 2;
		}
		else if ($cut_side == 2)
		{	// 剪切图片的高
			$target_height = $newheight / $ratio;
			$pos_y = ($height - $target_height) / 2;
		}
	}

	if(function_exists("imagecopyresampled"))
	{
		if ($pmode)
		{
			if($width < 500)
			{
				$newim = imagecreatetruecolor($width, $width*0.75);//新建一个真彩色图像[黑色图像]
				$white = imagecolorallocate($newim,255,255,255);
				imagefilledrectangle($newim,0,0,$width,$width*0.75,$white);
				imagecolortransparent($newim,$white);
				imagecopyresampled($newim, $image, 0, 0, 0, ($height-$width*0.75)/2, $width, $width*0.75, $width, $width*0.75);//重采样拷贝部分图像并调整大小
			}
			else if($height < 375)
			{
				$newim = imagecreatetruecolor($height*4/3, $height);//新建一个真彩色图像[黑色图像]
				$white = imagecolorallocate($newim,255,255,255);
				imagefilledrectangle($newim,0,0,$width*4/3,$height,$white);
				imagecolortransparent($newim,$white);
				imagecopyresampled($newim, $image, 0, 0, ($width-$height*4/3)/2, 0, $height*4/3, $height, $height*4/3, $height);//重采样拷贝部分图像并调整大小
			}
			else 
			{
				$newim = imagecreatetruecolor($maxwidth, $maxheight);//新建一个真彩色图像[黑色图像]
				$white = imagecolorallocate($newim,255,255,255);
				imagefilledrectangle($newim,0,0,$maxwidth,$maxheight,$white);
				imagecolortransparent($newim,$white);
				imagecopyresampled($newim, $image, ($maxwidth-$newwidth)/2, ($maxheight-$newheight)/2, $pos_x, $pos_y, $newwidth, $newheight, $target_width, $target_height);//重采样拷贝部分图像并调整大小
			}
		}
		else
		{
			$newim = imagecreatetruecolor($newwidth, $newheight);//新建一个真彩色图像[黑色图像]
			$white = imagecolorallocate($newim,255,255,255);
			imagefilledrectangle($newim,0,0,$newwidth,$newheight,$white);
			imagecolortransparent($newim,$white);
			imagecopyresampled($newim, $image, 0, 0, $pos_x, $pos_y, $newwidth, $newheight, $target_width, $target_height);//重采样拷贝部分图像并调整大小
		}
	}
	else
	{
		if ($pmode)
		{
			$newim = imagecreate($maxwidth, $maxheight);
			$white = imagecolorallocate($newim,255,255,255);
			imagefilledrectangle($newim,0,0,$maxwidth,$maxheight,$white);
			imagecolortransparent($newim,$white);
			imagecopyresized($newim, $image, ($maxwidth-$newwidth)/2, ($maxheight-$newheight)/2, $pos_x, $pos_y, $newwidth, $newheight, $target_width, $target_height);
		}
		else
		{
			$newim = imagecreate($newwidth, $newheight);
			$white = imagecolorallocate($newim,255,255,255);
			imagefilledrectangle($newim,0,0,$newwidth,$newheight,$white);
			imagecolortransparent($newim,$white);
			imagecopyresized($newim, $image, 0, 0, $pos_x, $pos_y, $newwidth, $newheight, $target_width, $target_height);
		}
	}

	switch($pic_info)
	{
		case 1: imagegif($newim,$dstimg);break;		//GIF
		case 2: imagejpeg($newim,$dstimg,90);break;	//JPG
		case 3: imagepng($newim,$dstimg);break;		//PNG
		default:return false;
	}
	ImageDestroy($newim);
	return true;
}


function user_image($image=null) {
	global $INI;
	$image = $image ? $image : 'img/user-no-avatar.gif';
	return "/static/{$image}";
}

function team_image($image=null, $index=false , $all=false , $width=0, $height=0) {
	global $INI;
	if (!$image) return null;
	if ($index) {
		$path = WWW_ROOT . '/static/' . $image;
		if( $all )
		{
			$image = preg_replace('#(\d+)\.(\w+)$#', "\\1_all.\\2", $image); 
			$dest = WWW_ROOT . '/static/' . $image;
			if (!file_exists($dest) && file_exists($path) ) {
				Image::Convert($path, $dest, 315, 190, Image::MODE_SCALE);
			}
		}
		else 
		{
			if( $width > 0 && $height > 0 ){
				$image = preg_replace('#(\d+)\.(\w+)$#', "\\1_order.\\2", $image); 
				$dest = WWW_ROOT . '/static/' . $image;
				if (!file_exists($dest) && file_exists($path) ) {
					Image::Convert($path, $dest, $width, $height, Image::MODE_SCALE);
				}
			}else{
				$image = preg_replace('#(\d+)\.(\w+)$#', "\\1_index.\\2", $image); 
				$dest = WWW_ROOT . '/static/' . $image;
				if (!file_exists($dest) && file_exists($path) ) {
					Image::Convert($path, $dest, 200, 120, Image::MODE_SCALE);
				}
			}
		}
	}
	return "/static/{$image}";
}





function down_xls($data, $keynames, $name='dataxls') {
	$xls[] = "<html><meta http-equiv=content-type content=\"text/html; charset=UTF-8\"><body><table border='1'>";
	$xls[] = "<tr><td>ID</td><td>" . implode("</td><td>", array_values($keynames)) . '</td></tr>';
	foreach($data As $o) {
		$line = array(++$index);
		foreach($keynames AS $k=>$v) {
			$line[] = $o[$k];
		}
		$xls[] = '<tr><td>'. implode("</td><td style='vnd.ms-excel.numberformat:@'>", $line) . '</td></tr>';
	}
	$xls[] = '</table></body></html>';
	$xls = join("\r\n", $xls);
	header('Content-Disposition: attachment; filename="'.$name.'.xls"');
	die(mb_convert_encoding($xls,'UTF-8','UTF-8'));
}

function option_hotcategory($zone='city', $force=false, $all=false) {
	$cates = option_category($zone, $force, true);
	$r = array();
	foreach($cates AS $id=>$one) {
		if ('Y'==strtoupper($one['display'])) $r[$id] = $one;
	}
	return $all ? $r: Utility::OptionArray($r, 'id', 'name');
}



function option_yes($n, $default=false) {
	global $INI;
	if (false==isset($INI['option'][$n])) return $default;
	$flag = trim(strval($INI['option'][$n]));
	return abs(intval($flag)) || strtoupper($flag) == 'Y';
}

function option_yesv($n, $default='N') {
	return option_yes($n, $default=='Y') ? 'Y' : 'N';
}

function magic_gpc($string) {
	if(SYS_MAGICGPC) {
		if(is_array($string)) {
			foreach($string as $key => $val) {
				$string[$key] = magic_gpc($val);
			}
		} else {
			$string = stripslashes($string);
		}
	}
	return $string;
}




function interface_post($url, $data)
{
	$filepathlog = ORDER_ROOT . '/log/zhongxin/' . date ( 'Y' ) . '/' . date ( 'm' ) . '/' . date ( 'd' ) . '/web/100/interface_post.txt';
	Klogger::log ( $filepathlog, print_r ( log_filter(array('url' => $url,'data'=>$data)), true ) );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSLVERSION, 3); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_POST, TRUE); 
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt ($ch,CURLOPT_TIMEOUT,60);
    $ret = curl_exec($ch);
    if($ret === false)
	{
		Klogger::log ( $filepathlog, print_r ( array('error' => curl_error($ch)), true ) );
	}
    curl_close($ch);
    Klogger::log ( $filepathlog, print_r ( log_filter(array('ret' => $ret)), true ) );
    return $ret;
}


function get_page_info_search($page, $total_page, $href_string='')
{
	if ($total_page<=6) 
	{ 
		$min = 1;
		$max = $total_page;
		$range = range(1,$total_page);
	} else 
	{
	if($page == 1)
	{
		$min = 1;
		$max = $page + 4;
	}
	else if($page == 2)
	{
		$min = 1;
		$max = $page + 3;
	}
	else if($page > 2 && $page<=$total_page)
	{
		if($page == $total_page -1)
		{
			$min = $page-3;
			$max = $page + 1;
		}
		else if($page == $total_page)
		{
			$min = $page-4;
			$max = $total_page;
		}
		else 
		{
			$min = $page-2;
			$max = $page + 2;
		}
	}
		$range = range($min, $max);
	}
	$page_info = '<div class="page f-cf"><ul class="papg-lst">';
	
	if ($page > 1 && $page < 3) {
		$pre_page = $page - 1;
		$page_info .= "<li><a href=\"{$href_string}?page={$pre_page}\">上一页</a></li>";
	}
	else if($page > 2 && $page<=$total_page)
	{
		if($min == 1)
		{
			$pre_page = $page - 1;
			$page_info .= "<li><a href=\"<li><a href=\"{$href_string}?page={$pre_page}\">上一页</a></li>";
		}
		else if($min == 2)
		{
			$pre_page = $page - 1;
			$page_info .= "<li><a href=\"{$href_string}?page={$pre_page}\">上一页</a><a href=\"{$href_string}?page=1\">1</a></li>";
		}
		else 
		{
			$pre_page = $page - 1;
			$page_info .= "<li><a href=\"{$href_string}?page={$pre_page}\">上一页</a><a href=\"{$href_string}?page=1\">1</a><span>...</span></li>";
		}
	}
	foreach($range AS $one) {
			if ( $one == $page ) {
				$page_info .= "<li><a class='cur'>{$one}</a></li>";
			} else {
				$page_info .= "<li><a href=\"{$href_string}?page={$one}\">{$one}</a></li>";
			}
	}
	if ($page < $total_page) {
		if(($max+1) > $total_page)
		{
			$next_page = $page + 1;
			$page_info .= "<li><a href=\"{$href_string}?page={$next_page}\">下一页</a></li>";
		}
		else if(($max+1) == $total_page)
		{
			$next_page = $page + 1;
			$page_info .= "<li><a href=\"{$href_string}?page={$total_page}\">{$total_page}</a><a href=\"{$href_string}?page={$next_page}\">下一页</a></li>";
		}
		else 
		{
			$next_page = $page + 1;
			$page_info .= "<li><span>...</span><a href=\"{$href_string}?page={$total_page}\">{$total_page}</a><a href=\"{$href_string}?page={$next_page}\">下一页</a></li>";
		}
		//$buffer .= "";
	}
	$page_info .= "</ul></div>";
	return $page_info;
}

/*
*	功能：获取距离
*	参数：地理经度，地理纬度
*/
function getdistance($lng1 = '', $lat1 = '', $lng2 = '', $lat2 = '') {
	
	$earthRadius = 6367000; //approximate radius of earth in meters
	
	$lat1 = ($lat1 * pi() ) / 180;
	$lng1 = ($lng1 * pi() ) / 180;
	
	$lat2 = ($lat2 * pi() ) / 180;
	$lng2 = ($lng2 * pi() ) / 180;
	
	$calcLongitude = $lng2 - $lng1;
	$calcLatitude = $lat2 - $lat1;
	$stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);  
	$stepTwo = 2 * asin(min(1, sqrt($stepOne)));
	
	$calculatedDistance = ($earthRadius * $stepTwo) / 1000;
	
	return round($calculatedDistance, 2);
}

/*
*	功能：转化距离
*	参数：距离
*/
function transformdistance($dist = '0') {
	$dist = floatval(trim($dist));

	if(!is_numeric($dist)) {
		return '0m';
	}
//	if($dist < 1000) {
//		$dist = $dist . 'm';
//	} else {
//		$dist = ceil($dist / 1000) . 'Km';
//	}
	$dist = $dist / 1000;

	return $dist;
}

function dates_range($date1, $date2)
{
   if ( $date1 < $date2 )
   {
       $dates_range[] = $date1;
       $date1 = strtotime( $date1 );
       $date2 = strtotime( $date2 );
       while ( $date1 != $date2 )
       {
           $date1 = mktime(0, 0, 0, date("m", $date1), date("d", $date1)+1, date("Y", $date1));
           $dates_range[] = date('Y-m-d', $date1);
       }
       $dates = array();
       foreach ($dates_range as $key=>$value)
       {
       		$dates[$key]['origin_date'] = $value;
       		$dates[$key]['orgin_sum'] = 0;
       }
   }
   return $dates;
}

function months_range($year)
{
   $dates_range = array($year.'-01',$year.'-02',$year.'-03',$year.'-04',$year.'-05',$year.'-06',$year.'-07',$year.'-08',$year.'-09',$year.'-10',$year.'-11',$year.'-12');
   $dates = array();
   foreach ($dates_range as $key=>$value)
   {
   		$dates[$key]['origin_date'] = $value;
   		$dates[$key]['orgin_sum'] = 0;
   }
   return $dates;
}

/*
*按照数据中某个参数的值从小到大排序
*
*/
function multi_array_sort($multi_array,$sort_key,$sort=SORT_ASC)
{
	if(is_array($multi_array))
	{
		foreach ($multi_array as $row_array)
		{
			if(is_array($row_array))
			{
				$key_array[] = $row_array[$sort_key];
			}else
			{
				return false;
			}
		}
	}
	else
	{
		return false;
	}
	array_multisort($key_array,$sort,$multi_array);
	return $multi_array;
} 

/**
 * @abstract 获取指定二维数组指定列的值，并组成字符串格式，以逗号分隔
 * @param $array 数据数组
 * 		  $v 数据里指定的值
 * 		  $sign 是否需要对值加单引号
 * 
 * @return 以逗号连接连接在一起的字符串
 * */
function ArrayToString( $array , $v='id' , $sign=false , $sep=',' ){
	$output = "" ;
	if( is_array( $array ) ) {
		foreach ( $array as $value ){
			if( is_array( $value ) ){
				foreach ( $value as $key => $val ){
					if( $key == $v ){
						if( $sign ){
							$output .= "'".$val."'".$sep ;
						}else{
							$output .= $val.$sep ;
						}
					}
				}
			}
		}
	}
	
	if( $output ){
		$output = trim( $output , $sep ) ;
	}
	
	return $output ;
}

/*
*根据商品ID来自动生成商品的编号，并确保商品编号的唯一性
*/
function getproductnumber($id,$mark)
{
	if(empty($id))
	{
		return false;
	}
	if(strlen($id) == 1)
	{
		$product_number = $mark.$id.rand(100000,999999);
	}
	else if(strlen($id) == 2)
	{
		$product_number = $mark.$id.rand(10000,99999);
	}
	else if(strlen($id) == 3)
	{
		$product_number = $mark.$id.rand(1000,9999);
	}
	else if(strlen($id) == 4)
	{
		$product_number = $mark.$id.rand(100,999);
	}
	else if(strlen($id) == 5)
	{
		$product_number = $mark.$id.rand(10,99);
	}
	else if(strlen($id) == 6)
	{
		$product_number = $mark.$id.rand(1,9);
	}
	else 
	{
		$product_number = $mark.$id;
	}
	return $product_number;
}

function checkIdenCard($idcard){
    if (empty($idcard)) {
        return false;
    }
    $City = array(
        11=>"北京",12=>"天津",13=>"河北",14=>"山西",15=>"内蒙古",
        21=>"辽宁",22=>"吉林",23=>"黑龙江",
        31=>"上海",32=>"江苏",33=>"浙江",34=>"安徽",35=>"福建",36=>"江西",37=>"山东",
        41=>"河南",42=>"湖北",43=>"湖南",44=>"广东",45=>"广西",46=>"海南",
        50=>"重庆",51=>"四川",52=>"贵州",53=>"云南",54=>"西藏",
        61=>"陕西",62=>"甘肃",63=>"青海",64=>"宁夏",65=>"新疆",
        71=>"台湾",81=>"香港",82=>"澳门",91=>"国外"
    );
     $iSum = 0;
     $idCardLength = strlen($idcard);

     //长度验证
     if (!preg_match('/^\d{17}(\d|x)$/i',$idcard) && !preg_match('/^\d{15}$/i',$idcard)) {
         return false;
     }

    //地区验证
     if (!array_key_exists(intval(substr($idcard,0,2)),$City)) {
         return false;
     }
     // 15位身份证验证生日，转换为18位
     if ($idCardLength == 15) {
         $idcard = substr($idcard,0,6)."19".substr($idcard,6,9);//15to18
         $Bit18 = getVerifyBit($idcard);//算出第18位校验码
         $idcard = $idcard.$Bit18;
     }
     // 判断是否大于2078年，小于1900年
     $year = substr($idcard,6,4);
     if ($year<1900 || $year>2078 ) {
        return false;
     }

    //身份证编码规范验证
    $idcard_base = substr($idcard,0,17);
    if (strtoupper(substr($idcard,17,1)) != getVerifyBit($idcard_base)) {
        return false;
    }
    return $idcard;
}

// 计算身份证校验码，根据国家标准GB 11643-1999
function getVerifyBit($idcard_base) {
    if (strlen($idcard_base) != 17) {
        return false;
    }
    //加权因子
    $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
    //校验码对应值
    $verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4','3', '2');
    $checksum = 0;
    for ($i = 0; $i < strlen($idcard_base); $i++) {
        $checksum += substr($idcard_base, $i, 1) * $factor[$i];
    }
    $mod = $checksum % 11;
    $verify_number = $verify_number_list[$mod];
    return $verify_number;
}

/**
 * 检查手机号是移动，联通
 * 优先检查是否为移动手机号，再次检查联通
 * @param $str
 * @return   boolean
 */
function isMobile($str)
{
    $res = false;
    //先检查是否为联通号码
    $res = preg_match('/^((13[0-2])|145|(15[5-6])|(18[5-6]))\d{8}$/',$str);
    if($res)
    {
    	$type = 'chinaunion';
    }
    else 
    {
    	$type = 'chinamobile';
    }
    return $type; 
//    检查是否为中国移动号码
//    $res = preg_match('/((^134)[0-8]\d{7})|(^(13)[5-9]\d{8})|(^(147)\d{8})|(^(15)[0-27-9]\d{8})|(^(18)[2378]\d{8})$/',$str);
//    if(!$res){
//        检查是否为中国联通号码
//        $res = preg_match('/^((13[0-2])|(15[5-6])|(18[5-6]))\d{8}$/',$str);
//    }
//    if(!$res){
//         检查是否为电信手机号
//        $res = preg_match('/^(133|153|180|189)\d{8}$/',$str);
//    }
//    return $res;
}


/*
*	功能：获取request参数
*	参数：request参数，log
*/
function getparameter($param = '', $log = '', $type = 'ios') {
	$param = array_merge( $_POST , $_GET );
	if(!$param) {
		return array(
			'ret'=>'101',
			'msg'=>'网络等待超时，请查看您的网络设置，或刷新重试'
		);
	}
	if(empty($log)) {
		return array(
			'ret'=>'101',
			'msg'=>'网络等待超时，请查看您的网络设置，或刷新重试'
		);
	}
	$filepath = APP_ROOT.'/log/app/'.date('Y').'/'.date('m').'/'.date('d').'/'.$type.'/'.$log.'.txt';
	Klogger::log( $filepath  , print_r( log_filter($param) , true )  );

	//$str = urldecode($param);
	//$str = substr($str, 10);
	$arr = $param;

	if(!is_array($arr) || !$arr) {
		return array(
			'ret'=>'103',
			'msg'=>'网络等待超时，请查看您的网络设置，或刷新重试'
		);
	}
	$msg = array(
		'ret'=>'100',
		'msg'=>'获取数据成功'
	);

	return array_merge($msg, $arr);
}

/**
 * return boolean
 * 建立文件夹，可建立多层
 * $dir 目录
 */
function createdir($dir){
	//if(!is_dir($dir))return false;
	if(file_exists($dir))return true;
	$dir	= str_replace("\\","/",$dir);
	substr($dir,-1)=="/"?$dir=substr($dir,0,-1):"";
	$dir_arr	= explode("/",$dir);
	foreach($dir_arr as $k=>$a){
		$str	= $str.$a."/";
		if(!$str)continue;
		//echo $str."<br>";
		if(!file_exists($str))mkdir($str,0777);
	}
	return true;
}

/**
 * 取IP
 */
function getLoginIP(){
	if(getenv('HTTP_CLIENT_IP')){
		$ip = getenv('HTTP_CLIENT_IP');
	}
	elseif(getenv('HTTP_X_FORWARDED_FOR')){
		$ip = getenv('HTTP_X_FORWARDED_FOR');
	}
	elseif(getenv('HTTP_X_FORWARDED')){
		$ip = getenv('HTTP_X_FORWARDED');
	}
	elseif(getenv('HTTP_FORWARDED_FOR')){
		$ip = getenv('HTTP_FORWARDED_FOR');
	}
	elseif(getenv('HTTP_FORWARDED')){
		$ip = getenv('HTTP_FORWRDED');
	}
	else{
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	return $ip;
}
//取中文子串
function substr_CN($str,$mylen)
{                                                                                                                                        
	$len=strlen($str);
	$content='';
	$count=0;
	for($i=0;$i<$len;$i++)
	{
	   if(ord(substr($str,$i,1))>127){
	    $content.=substr($str,$i,2);
	    $i++;
	   }else{
	    $content.=substr($str,$i,1);
	   }
	   if(++$count==$mylen){
	    break;
	   }
	}
	return $content;
}

function error_handler($errno, $errstr, $errfile, $errline) {
	switch ($errno) {
		case E_PARSE:
		case E_ERROR:
			echo "<b>Fatal ERROR</b> [$errno] $errstr<br />\n";
			echo "Fatal error on line $errline in file $errfile";
			echo "PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
			exit(1);
			break;
		default: break;
	}
	return true;
}
/* for obscureid */
function obscure_rep($u) {
	if(!option_yes('encodeid')) return $u;
	if(preg_match('#/manage/#', $_SERVER['REQUEST_URI'])) return $u;
	return preg_replace_callback('#(\?|&)id=(\d+)(\b)#i', obscure_cb, $u);
}
function obscure_cb($m) {
	$eid = obscure_eid($m[2]);
	return "{$m[1]}id={$eid}{$m[3]}";
}
function obscure_eid($id) {
	if($id>100000000) return $id;
	return 'ZT'.base64_encode($id<<2);
}
/* end */

/* for post trim */
function trimarray($o) {
	if (!is_array($o)) return trim($o);
	foreach($o AS $k=>$v) { $o[$k] = trimarray($v); }
	return $o;
}
$_POST = trimarray($_POST);
/* end */

/* verifycapctch */
function verify_captcha($reason='none', $rurl=null) {
	if (option_yes($reason, false)) {
		$v = strval($_REQUEST['vcaptcha']);
		if(!$v || !Utility::CaptchaCheck($v)) {
			Session::Set('error', '验证码不匹配，请重新输入');
			//redirect($rurl);
			return false;
		}
	}
	return true;
}
//图片合成
 
//生成二维码
function saveQrcode( $mobile=null , $id=null ){
	if( empty( $mobile ) ) {
		return ;
	}
	include_once( DIR_LIBARAY .'/phpqrcode/qrlib.php') ;
	//二维码包含的内容
	$content = 'http://www.xianhua2000.com/scancodeimg.do?mer_mobile='.$mobile ;
	//二维码图片存放的位置
	$filename = '/static/qrcodeimg/'.$mobile.'_'.time().'.jpg' ;
	QRcode::png($content, WWW_ROOT.$filename, 'H', 10, 2,false,WWW_ROOT.'/static/img/xianhua01.png'); 
	if( !empty( $id ) && is_file( WWW_ROOT . $filename ) ){
		//保存地址到数据库
		$table = new Table('mr_merchants', array('qrcode_img'=>$filename));
		$table->SetPK('id', $id);
		if( !$table->update(array( 'qrcode_img')) ){
			return false ;
		}else {
			return $filename;
		}
		
	}
	return false;	
}
//下载二维码
function downloadqrcode( $filePath=null ){
	if( !$filePath ) return false ;
	
	if( !is_file( WWW_ROOT.$filePath ) ){
		exit('File:'.WWW_ROOT.$filePath.' Not exists!') ;
	}
	$fileName = basename( $filePath ) ;

	// 读取文件 
	if (is_readable(WWW_ROOT.$filePath)) { 
		Header("Content-type: application/octet-stream");
		Header("Accept-Ranges: bytes");
		Header("Accept-Length: ".filesize(WWW_ROOT . $fileName));
		Header("Content-Disposition: attachment; filename=" . $fileName);
		$file = fopen(WWW_ROOT . $filePath,"r"); // 打开文件
		echo fread($file,filesize(WWW_ROOT . $filePath));
		
		exit(); 
	} else { 
		exit('Read file failed!'); 
	} 

}

/**
 * 根据日期返回年龄
 * @param unknown $date
 * @return number
 */
function countAge($date) {
	if(empty($date)) return 0;
	$date=strtotime($date);
	$today=time();
	$age =floor(($today-$date)/86400/365);
	return $age;
}
//传入日期 获取当月的第一天和最后一天日期
function get_month_se($date) 
{ 
	$firstday = date('Y-m-01', strtotime($date."-01")); 
	$lastday = date('Y-m-d', strtotime("$firstday +1 month -1 day")); 
	return array($firstday, $lastday);
}
//敏感词处理
function StringToArray($str)
{
	//把字符串的每个字符转化为数组
	$ar=array();
	$a=(mb_strlen($str));
	for($i=0;$i<$a;$i++)
	{
	$ar[$i]=mb_substr($str, $i, 1, 'utf-8');
	}
	return $ar;
}
function ZhengZeExpr($arry)
{
	//把刚才分割好的数组元素再次分割成新元素数组，并且组成正则表达式
	$array=StringToArray($arry);//把刚才分割好的数组元素再次分割成新元素数组
	$str="/[\d\D]*";
	$count1=count($array);
	if(preg_match("/[".chr(0xb0)."-".chr(0xf7)."]+/",$array[0]))
	{
		//如果是中文敏感词，那么就生成每个字中间不能超过15个字的正则表达式
		for($j=0;$j<$count1;$j++)
		{
		$str=$str.$array[$j]."+";
		if($j<$count1-1)
			$str=$str."[\d\D]{0,15}";
			else
			$str=$str."[\d\D]*/";
		}
	}
	else
	{
		//如果是英文敏感词，那么就生成每个字符中间不能超过15个字符的正则表达式

		for($j=0;$j<$count1;$j++)
		{
			$str=$str.$array[$j]."+";
			if($j<$count1-1)
				$str=$str."[\d\D]{0,5}";
			else
				$str=$str."[\d\D]*/";
		}
	}
	return $str;
}

function checker($arr,$user)
{
	for($i=0;$i<count($arr);$i++)
	{
		$zhengze=ZhengZeExpr($arr[$i]);
		$counter=preg_match($zhengze,$user);
		if($counter>0)
		{
			return $counter;
		}
	}
}
//转换秒数为剩余小时、分钟、秒
function transcountdown($seconds)
{
	if($seconds >= 3600)
	{
		$h = floor($seconds/3600);
		$i = (floor($seconds%3600/60) >= 10)?(floor($seconds%3600/60)):'0'.(floor($seconds%3600/60));
		$s = (floor($seconds%3600%60) >= 10)?(floor($seconds%3600%60)):'0'.(floor($seconds%3600%60));
		$usedtime = str_pad($h,2,'0',STR_PAD_LEFT).':'.str_pad($i,2,'0',STR_PAD_LEFT).':'.str_pad($s,2,'0',STR_PAD_LEFT);
	}
	else
	{
		$h = floor($seconds/3600);
		$i = (floor($seconds%3600/60) >= 10)?(floor($seconds%3600/60)):'0'.(floor($seconds%3600/60));
		$s = (floor($seconds%3600%60) >= 10)?(floor($seconds%3600%60)):'0'.(floor($seconds%3600%60));
		$usedtime = str_pad($h,2,'0',STR_PAD_LEFT).':'.str_pad($i,2,'0',STR_PAD_LEFT).':'.str_pad($s,2,'0',STR_PAD_LEFT);
	}
	return $usedtime;
}
//过滤sql关键字，防止sql注入
function filter_keyword( $string ) 
{ 
	$keyword = ' select| insert| update| delete| AND| OR| UNION|;|*/select|*/insert|*/update|*/delete|*/AND|*/OR|*/UNION'; 
	$arr = explode( '|', $keyword );
	$result = str_ireplace( $arr, '', $string ); 
	if($result==$string)
	{
		return $result;
	}
	else
	{
		return filter_keyword( $result );
	}
}
//获取系统级参数  
function getsystemconfig($key)
{
	$sql = "select paramValue from zx_system_parameters where paramKey='".$key."'";
	$res = DB::GetQueryResult($sql,true);
	return $res['paramvalue']?$res['paramvalue']:'';
} 

//日志数组过滤
function log_filter($array){

	//判断是否是对象，并转换为数组
	if(is_object($array)){
		$array = get_object_vars($array);
	}

	//过滤数组
	$realname = array('LOGINNAME','USERNAME','username','userrealname','realName','name','accountName');	//姓名
    $mobile = array('LOGIN_NAME','USER_ID','CUSERID','userNo','userno','mobileNo','icardpayuser','mobile','newMobile');	//手机号
    $creditial = array('USER_NO','USERNO','CREDNO','USER_NAM','idnumber','idcardno','idcardNo','idNumber','certNo');	//身份证号
    $bankCardNo = array('CARDNO','banknumber','cardnumber','idcardno','accno','idcardNo','cardNo','cardnumber','otherAccno');	//银行卡号

	if(is_array($array)){
		foreach ($array as $key => $value) {
			if(is_array($value)){
				$array[$key] = log_filter($value);
			}else{
				if(stripos($key,'pass') !== false){
					unset($array[$key]);
				}elseif(stripos($key,'mobile') !== false || in_array($key, $mobile)){
					if(stripos($value,'*') === false){
						$array[$key] = str_rep_str($value,'*',3,-4);
					}
				}elseif(stripos($key,'bankCardNo') !== false || in_array($key, $bankCardNo)){
					if(strlen($value) > 4){
							$array[$key] = str_rep_str($value,'*',6,-6);
					}
				}elseif(stripos($key,'creditial') !== false || in_array($key, $creditial)){
					$array[$key] = str_rep_str($value,'*',6,-4);
				}elseif(stripos($key,'realname') !== false || in_array($key, $realname)){
					$array[$key] = mb_substr($value,0,1).'*';
				}
			}
		}
	}

	return $array;
}

//字符串替换有多少个字符替换成多少个字符
function str_rep_str($str,$tstr='*',$start=0,$length=0){

	if($length != 0){
		return substr_replace($str,str_repeat($tstr,strlen(substr($str,$start,$length))),$start,$length);
	}else{
		return substr_replace($str,str_repeat($tstr,strlen(substr($str,$start))),$start);
	}
}

set_error_handler('error_handler');
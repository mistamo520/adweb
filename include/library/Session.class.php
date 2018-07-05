<?php
/**
 * @author: shwdai@gmail.com
 */
class Session
{
	static private $_begin = 0;
	static private $_instance = null;
	static private $_debug = false;

	static public function Init($debug=false)
	{
		global $INI;
	    $filepathlog = ORDER_ROOT . '/log/zhongxin/' . date ( 'Y' ) . '/' . date ( 'm' ) . '/' . date ( 'd' ) . '/Session/init.txt';
	    try{
    		self::$_instance = new Session();
    		self::$_debug = $debug;
//    		ini_set('session.save_handler', 'memcache');
//    		ini_set('session.cookie_domain','.54.193');
//    		ini_set('session.save_path','http://192.168.54.193:11211?persistent=1&weight=1&timeout=1&retry_interval=15,http://192.168.54.194:11211?persistent=1&weight=1&timeout=1&retry_interval=15');
    		session_start();
    		if(isset($_COOKIE['zx_timestr']) && !empty($_COOKIE['zx_timestr'])){
    			session_id($_COOKIE['zx_timestr']);
    		}
	    }catch (Exception $e)
	    {
	        Klogger::log ( $filepathlog, print_r ( $e, true ) );
	    }
	}

	static public function Set($name, $v) 
	{
		$_SESSION[$name] = $v;
	}

	static public function Get($name, $once=false)
	{
		$v = null;
		if ( isset($_SESSION[$name]) )
		{
			$v = $_SESSION[$name];
			if ( $once ) unset( $_SESSION[$name] );
		}
		return $v;
	}

	function __construct()
	{
		self::$_begin = microtime(true);
	}

	function __destruct()
	{
		global $AJAX, $INI;
		if (self::$_debug&&!$AJAX) { echo 'Generation Cost: '.(microtime(true)-self::$_begin).'s, Query Count: ' . DB::$mCount; }
		DB::Close();
		$c = ob_get_clean();
		if ( function_exists('render_hook') ) {
			$c = render_hook($c);
		}
		if ( function_exists('output_hook') ) {
			die(output_hook($c));
		}
		die($c);
	}
}
?>

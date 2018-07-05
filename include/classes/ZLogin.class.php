<?php
/**
 * @author: shwdai@gmail.com
 */
class ZLogin
{
	static public $cookie_name = 'ru';

    static public function GetLoginId() {
        $user_id = abs(intval(Session::Get('userid')));
		
		if ($user_id) self::Login($user_id);
		return $user_id;
    }

	static public function Login($user_id) {
		Session::Set('userid', $user_id);
		return true;
	}

    static public function NeedLogin() {
        $user_id = self::GetLoginId();
        return $user_id ? $user_id : False;
    }

	static public function Remember($user) {
		$zone = "{$user['id']}@{$user['password']}";
		cookieset(self::$cookie_name, base64_encode($zone), 30*86400);
	}

	static public function NoRemember() {
		cookieset(self::$cookie_name, null, -1);	
	}
	static public  function chanSource($source,$ip,$http_referer){
		if($source == '7bb9ca6dbf05b521e816d5a4d34d5030')
		{
			$sourceName = '火币网';
			$u['source'] = 'huobi';
			$u['sourceName'] = $sourceName;
			$u['ip'] = $ip;
			$u['http_referer'] = $http_referer;
			$data['res'] = ZPromotion::AddData($u);
			return $data;
		}
	}
}

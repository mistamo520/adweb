<?php
class Controller{
	protected $controller;
	protected $action;
	protected $login_user_id;
	protected $login_user;
	function __construct($controller,$action)
	{
		$this->controller = $controller;
		$this->action = $action;
//		$this->login_user_id = ZLogin::GetLoginId();
//		$this->login_user  = Table::Fetch('zx_users', $this->login_user_id);
	}
}

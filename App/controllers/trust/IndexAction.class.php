<?php
	class IndexAction  extends Controller{
		//公告列表
		function index($id)
		{
		    global $INI;
			$pagetitle = '广告运营平台';
			$platform = "20楼";
			$modename = "广告运营平台-广告主";
//			life_cycle();
//			$login_user = $this->login_user;
			$navigation = 'advertising';
			$twonavigation = 'platform';
//			$data = WebInterfaces::getData($this->controller, array(
//						'action' => $this->action,
//						'offset' => $offset,
//						'pagesize' => $pagesize
//					)
//				);

			include template('ad_index','advertising/index');
		}
		function platform($id)
		{
			global $INI;
			$pagetitle = '广告运营平台';
			$platform = "20楼";
			$modename = "广告运营平台-平台";
//			life_cycle();
//			$login_user = $this->login_user;
			$navigation = 'advertising';
			$twonavigation = 'platform';
//			$data = WebInterfaces::getData($this->controller, array(
//						'action' => $this->action,
//						'offset' => $offset,
//						'pagesize' => $pagesize
//					)
//				);

			include template('pt_index','platform/index');
		}
		function traffic($id)
		{
			global $INI;
			$pagetitle = '广告运营平台';
			$platform = "20楼";
			$modename = "广告运营平台-流量主";
//			life_cycle();
//			$login_user = $this->login_user;
			$navigation = 'advertising';
			$twonavigation = 'platform';
//			$data = WebInterfaces::getData($this->controller, array(
//						'action' => $this->action,
//						'offset' => $offset,
//						'pagesize' => $pagesize
//					)
//				);

			include template('tf_index','traffic');
		}

	}
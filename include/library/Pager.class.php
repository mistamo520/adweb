<?php
/**
 * @author: shwdai@gmail.com
 */
class Pager{

	public $rowCount = 0;
	public $pageNo = 1;
	public $pageSize = 20;
	public $pageCount = 0;
	public $offset = 0;
	public $pageString = 'page';

	private $script = null;
	private $valueArray = array();

	public function __construct($count=0, $size=20, $string='page')
	{
		$this->defaultQuery();
		$this->pageString = $string;
		$this->pageSize = abs($size);
		$this->rowCount = abs($count);

		$this->pageCount = ceil($this->rowCount/$this->pageSize);
		$this->pageCount = ($this->pageCount<=0)?1:$this->pageCount;
		$this->pageNo = abs(intval(@$_GET[$this->pageString]));
		$this->pageNo = $this->pageNo==0 ? 1 : $this->pageNo;
		$this->pageNo = $this->pageNo>$this->pageCount 
			? $this->pageCount : $this->pageNo;
		$this->offset = ( $this->pageNo - 1 ) * $this->pageSize;
	}

	private function genURL( $param, $value ){
		$valueArray = $this->valueArray;
		$valueArray[$param] = $value;
		return $this->script . '?' . http_build_query($valueArray);
	}

	private function defaultQuery()
	{
		($script_uri = @$_SERVER['SCRIPT_URI']) || ($script_uri = @$_SERVER['REQUEST_URI']);
		$q_pos = strpos($script_uri,'?');
		if ( $q_pos > 0 )
		{
			$qstring = substr($script_uri, $q_pos+1);
			parse_str($qstring, $valueArray);
			$script = substr($script_uri,0,$q_pos);
		}
		else
		{
			$script = $script_uri;
			$valueArray = array();
		}
		$this->valueArray = empty($valueArray) ? array() : $valueArray;
		$this->script = $script;
	}

	public function paginate($switch=1){
		$from = $this->pageSize*($this->pageNo-1)+1;
		$from = ($from>$this->rowCount) ? $this->rowCount : $from;
		$to = $this->pageNo * $this->pageSize;
		$to = ($to>$this->rowCount) ? $this->rowCount : $to;
		$size = $this->pageSize;
		$no = $this->pageNo;
		$max = $this->pageCount;
		$total = $this->rowCount;

		return array(
			'offset' => $this->offset,
			'from' => $from,
			'to' => $to,
			'size' => $size,
			'no' => $no,
			'max' => $max,
			'total' => $total,
		);
	}

	public function GenWap() {
		$r = $this->paginate();
		$pagestring= '<p align="right">';
		if( $this->pageNo > 1 ){
			$pageString.= '4 <a href="' . $this->genURL($this->pageString, $this->pageNo-1) . '" accesskey="4">上页</a>';
		}
		if( $this->pageNo >1 && $this->pageNo < $this->pageCount ){
			$pageString.= '｜';
		}
		if( $this->pageNo < $this->pageCount ) {
			$pageString.= '<a href="' .$this->genURL($this->pageString, $this->pageNo+1) . '" accesskey="6">下页</a> 6';
		}
		$pageString.= '</p>';
		return $pageString;
	}
	public function GenBasic() {
		$r = $this->paginate();
		$buffer = null;
		$index = '首页';
		$pre = '上一页';
		$next = '下一页';
		$last = '末页';

		if ($this->pageCount<=6) { 
			$min = 1;
			$max = $this->pageCount;
			$range = range(1,$this->pageCount);
		} else {
			if($this->pageNo == 1)
			{
				$min = 1;
				$max = $this->pageNo + 4;
			}
			else if($this->pageNo == 2)
			{
				$min = 1;
				$max = $this->pageNo + 3;
			}
			else if($this->pageNo > 2 && $this->pageNo<=$this->pageCount)
			{
				if($this->pageNo == $this->pageCount -1)
				{
					$min = $this->pageNo-3;
					$max = $this->pageNo + 1;
				}
				else if($this->pageNo == $this->pageCount)
				{
					$min = $this->pageNo-4;
					$max = $this->pageCount;
				}
				else 
				{
					$min = $this->pageNo-2;
					$max = $this->pageNo + 2;
				}
			}
			$range = range($min, $max);
		}
		$buffer .= '<div class="page f-cf"><ul class="papg-lst">';
		//$buffer .= "<li>({$this->rowCount})</li>";
		if ($this->pageNo > 1 && $this->pageNo < 3) {
			$buffer .= "<li><a href='".$this->genURL($this->pageString,$this->pageNo-1)."'>{$pre}</a></li>";
		}
		else if($this->pageNo > 2 && $this->pageNo<=$this->pageCount)
		{
			if($min == 1)
			{
				$buffer .= "<li><a href='".$this->genURL($this->pageString,$this->pageNo-1)."'>{$pre}</a></li>";
			}
			else if($min == 2)
			{
				$buffer .= "<li><a href='".$this->genURL($this->pageString,$this->pageNo-1)."'>{$pre}</a><a href='".$this->genURL($this->pageString,1)."'>1</a></li>";
			}
			else 
			{
				$buffer .= "<li><a href='".$this->genURL($this->pageString,$this->pageNo-1)."'>{$pre}</a><a href='".$this->genURL($this->pageString,1)."'>1</a><span>...</span></li>";
			}
		}
		foreach($range AS $one) {
			if ( $one == $this->pageNo ) {
				$buffer .= "<li><a class='cur'>{$one}</a></li>";
			} else {
				$buffer .= "<li><a href='".$this->genURL($this->pageString,$one)."'>{$one}</a></li>";
			}
		}
		if ($this->pageNo < $this->pageCount) {
			if(($max+1) > $this->pageCount)
			{
				$buffer .= "<li><a href='".$this->genURL($this->pageString,$this->pageNo+1)."'>{$next}</a></li>";
			}
			else if(($max+1) == $this->pageCount)
			{
				$buffer .= "<li><a href='".$this->genURL($this->pageString, $this->pageCount)."'>{$this->pageCount}</a><a href='".$this->genURL($this->pageString,$this->pageNo+1)."'>{$next}</a></li>";
			}
			else 
			{
				$buffer .= "<li><span>...</span><a href='".$this->genURL($this->pageString, $this->pageCount)."'>{$this->pageCount}</a><a href='".$this->genURL($this->pageString,$this->pageNo+1)."'>{$next}</a></li>";
			}
			//$buffer .= "";
		}
		return $buffer . '</ul></div>';
	}
	public function GenWap2() {
		$r = $this->paginate();
		$pagestring= '<div class="page"> ';
		if( $this->pageNo > 1 ){
			$pageString.= '4 <a href="' . $this->genURL($this->pageString, $this->pageNo-1) . '" accesskey="4">Prev</a>';
		}
		if( $this->pageNo >1 && $this->pageNo < $this->pageCount ){
			$pageString.= '｜';
		}
		if( $this->pageNo < $this->pageCount ) {
			$pageString.= '<a href="' .$this->genURL($this->pageString, $this->pageNo+1) . '" accesskey="6">Next</a> 6';
		}
		$pageString.= '</div>';
		return $pageString;
	}
	public function GenBasic2() {
		$r = $this->paginate();
		$buffer = null;
		$index = '首页';
		$pre = '上一页';
		$next = '下一页';
		$last = '末页';

		if ($this->pageCount<=6) {
			$min = 1;
			$max = $this->pageCount;
			$range = range(1,$this->pageCount);
		} else {
			if($this->pageNo == 1)
			{
				$min = 1;
				$max = $this->pageNo + 4;
			}
			else if($this->pageNo == 2)
			{
				$min = 1;
				$max = $this->pageNo + 3;
			}
			else if($this->pageNo > 2 && $this->pageNo<=$this->pageCount)
			{
				if($this->pageNo == $this->pageCount -1)
				{
					$min = $this->pageNo-3;
					$max = $this->pageNo + 1;
				}
				else if($this->pageNo == $this->pageCount)
				{
					$min = $this->pageNo-4;
					$max = $this->pageCount;
				}
				else
				{
					$min = $this->pageNo-2;
					$max = $this->pageNo + 2;
				}
			}
			$range = range($min, $max);
		}
		$buffer .= '<div class="page">';
		//$buffer .= "<li>({$this->rowCount})</li>";
		if ($this->pageNo > 1 && $this->pageNo < 3) {
			$buffer .= "<a href='".$this->genURL($this->pageString,$this->pageNo-1)."'>{$pre}</a>";
		}
		else if($this->pageNo > 2 && $this->pageNo<=$this->pageCount)
		{
			if($min == 1)
			{
				$buffer .= "<a href='".$this->genURL($this->pageString,$this->pageNo-1)."'>{$pre}</a>";
			}
			else if($min == 2)
			{
				$buffer .= "<a href='".$this->genURL($this->pageString,$this->pageNo-1)."'>{$pre}</a><a href='".$this->genURL($this->pageString,1)."'>1</a>";
			}
			else
			{
				$buffer .= "<a href='".$this->genURL($this->pageString,$this->pageNo-1)."'>{$pre}</a><a href='".$this->genURL($this->pageString,1)."'>1</a><span>...</span>";
			}
		}
		foreach($range AS $one) {
			if ( $one == $this->pageNo ) {
				$buffer .= "<a class='cur'>{$one}</a>";
			} else {
				$buffer .= "<a href='".$this->genURL($this->pageString,$one)."'>{$one}</a>";
			}
		}
		if ($this->pageNo < $this->pageCount) {
			if(($max+1) > $this->pageCount)
			{
				$buffer .= "<a href='".$this->genURL($this->pageString,$this->pageNo+1)."'>{$next}</a>";
			}
			else if(($max+1) == $this->pageCount)
			{
				$buffer .= "<a href='".$this->genURL($this->pageString, $this->pageCount)."'>{$this->pageCount}</a><a href='".$this->genURL($this->pageString,$this->pageNo+1)."'>{$next}</a>";
			}
			else
			{
				$buffer .= "<span>...</span><a href='".$this->genURL($this->pageString, $this->pageCount)."'>{$this->pageCount}</a><a href='".$this->genURL($this->pageString,$this->pageNo+1)."'>{$next}</a>";
			}
			//$buffer .= "";
		}
		return $buffer . '</div>';
	}
	public function GenBasicNew() {
		$r = $this->paginate();
		$buffer = null;
		$index = '第一页';
		$pre = '<';
		$next = '>';
		$last = '最末页';
		if($this->pageNo+2<=$this->pageCount && $this->pageNo>=3)
		{
			$min = $this->pageNo-2;
			$max = $this->pageNo+2;
		}
		elseif($this->pageNo+2>$this->pageCount && $this->pageNo<3)
		{
			$min = 1;
			$max = $this->pageCount;
		}
		elseif($this->pageNo+2>$this->pageCount && $this->pageNo>=3)
		{
			$min = $this->pageCount - 4 > 0 ? $this->pageCount - 4 : 1;
			$max = $this->pageCount;
		}
		else
		{
			$min = 1;
			$max = $this->pageCount >= 5 ? 5 : $this->pageCount;
		}
		$range = range($min,$max);
		//start
		$buffer .= '<div class="mPage">';
		$buffer .= "<a href='".$this->genurl($this->pageString,1)."' class='first'>{$index}</a>";
		if($this->pageNo>1)
		{
			$buffer .= "<a href='".$this->genurl($this->pageString,$this->pageNo-1)."' class='prev'>{$pre}</a>";
		}
		else
		{
			$buffer .= "<a  class='prev'>{$pre}</a>";
		}
		foreach($range AS $one) 
		{
			if ( $one == $this->pageNo ) 
			{
				$buffer .= "<a class='zActive'>{$one}</a>";
			} 
			else 
			{
				$buffer .= "<a href='".$this->genURL($this->pageString,$one)."'>{$one}</a>";
			}
		}
		if($this->pageNo+1<=$this->pageCount)
		{
			$buffer .= "<a href='".$this->genURL($this->pageString,$this->pageNo+1)."' class='next'>{$next}</a>";
		}
		else
		{
			$buffer .= "<a class='next'>{$next}</a>";
		}
		$buffer .= "<a href='".$this->genurl($this->pageString,$this->pageCount)."' class='first'>{$last}</a>";
		return $buffer . '</div>';
	}
}
?>

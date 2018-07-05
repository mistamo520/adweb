<?php
/**
 * pic.lib.php（图片上传类）
 * 
 */

class pic 
{
	var $sUploadPath;				//图片存储路径
	var $aWaterColor;            // 水印颜色
	var $toFile	= true;			//是否生成文件
	var $fontName;					//使用的TTF字体名称	
	var $useTimeAsFileName = true;	//是否使用时间做为上传后的文件名
	
	function pic($sUploadPath,$aWaterColor="",$sFontPath="")
	{
		$this->sUploadPath	= $sUploadPath;
		$this->aWaterColor = ($aWaterColor) ? $aWaterColor : array("white","red","green","blue","black","grey","purple");			
		$this->fontName	= ($sFontPath) ? $sFontPath."1.ttf" : $sUploadPath."1.ttf";				
	}
	
	
	/**
	 * 单个文件上传
	 *
	 * @param string $sField 表单对象名称
	 * @param unknown_type $i 
	 * @return string
	 */
	
	function uploadPicOne($sField)
	{
		$aFile = $_FILES[$sField];  // Array([name]=>231109440.BMP [type]=>image/bmp [tmp_name] => C:\WINNT\TEMP\php8D.tmp    [error] => 0    [size] => 233794)		
		//print_r($aFile);exit;
		if($aFile['name'])
		{
			$a = pathinfo($aFile["name"]); // Array([dirname] => . [basename] => 231109440.BMP [extension] => BMP)			
			$sExt = $a['extension'];			
			$sDir = date("Y-m-d");
			if(!is_dir($this->sUploadPath.$sDir)) mkdir($this->sUploadPath.$sDir,0777);
						
			if ($this->useTimeAsFileName) 
			{
				$sFileName = $sDir."/".date("dHis").".".$sExt;	
			}
			else 
			{
				$sFileName = $sDir."/".$aFile['name'];
			}
			$sFilePath = $this->sUploadPath . $sFileName;
    		if(copy($aFile['tmp_name'],$sFilePath)) 
    			return $sFileName;
    		else 
    			return "";
		}
		else
		{
			return "";
		}		
	}
	
	/**
	 * 多个文件上传
	 *
	 * @param string $sField 表单对象名称
	 * @param unknown_type $i 
	 * @return string
	 */
	
	function uploadPic($sField)
	{
		$aFile = $_FILES[$sField];  // Array([name]=>231109440.BMP [type]=>image/bmp [tmp_name] => C:\WINNT\TEMP\php8D.tmp    [error] => 0    [size] => 233794)		
		//print_r($aFile['name']);exit;
		if($aFile['name'] != null)
		{
			$iCount = count($aFile['name']);
			//$a = array();
			$sFileValue = "";
			for($i=0; $i<$iCount; $i++)
			{
				$a = pathinfo($aFile["name"][$i]);
				$sExt = $a['extension'];		
			    $sDir = date("Y-m-d");
			    if(!is_dir($this->sUploadPath.$sDir)) mkdir($this->sUploadPath.$sDir,0777);
			    if ($this->useTimeAsFileName) 
			    {
				   $sFileName = $sDir."/".md5(time().rand(10000, 999999)).$i.".".$sExt;	
			    }
			    else 
			    {
				   $sFileName = $sDir."/".$aFile['name'][$i];
			    }
			    $sFilePath = $this->sUploadPath . $sFileName;
			    $sFileValue .= $sFileName.",";
    		    copy($aFile['tmp_name'][$i],$sFilePath);
			}
			return $sFileValue;
		}
		else
		{
			return "";
		}		
	}	
	
	/**
	 * 文件上传
	 *
	 * @param string $sField 表单对象名称
	 * @param unknown_type $i 
	 * @return string
	 */
	
	function uploadPicLogo($sField,$i=0)
	{
		$aFile = $_FILES[$sField];  // Array([name]=>231109440.BMP [type]=>image/bmp [tmp_name] => C:\WINNT\TEMP\php8D.tmp    [error] => 0    [size] => 233794)		
		//print_r($aFile);exit;
		if($aFile['name'])
		{
			$a = pathinfo($aFile["name"]); // Array([dirname] => . [basename] => 231109440.BMP [extension] => BMP)			
			$sExt = $a['extension'];			
			$sDir = date("Y-m-d");
			if(!is_dir($this->sUploadPath.$sDir)) mkdir($this->sUploadPath.$sDir,0777);
						
			if ($this->useTimeAsFileName) 
			{
				$sFileName = $sDir."/".date("dHis").$i.".".$sExt;	
			}
			else 
			{
				$sFileName = $sDir."/".$aFile['name'];
			}
			$sFilePath = $this->sUploadPath . $sFileName;
    		if(copy($aFile['tmp_name'],$sFilePath)) 
    			return $sFileName;
    		else 
    			return "";
		}
		else
		{
			return "";
		}		
	}	
	/**
 	* 获取图片信息
 	*
 	* @param string $sFileName 图片地址
 	* @return array
 	*/
	
	function getImgInfo($sFileName) 
	{
		$sTmpFileName = $this->sUploadPath . $sFileName;		
		$aTemp	= getimagesize($sTmpFileName);	// Array([0] => 629 [1] => 559 [2] => 6 [3] => width="629" height="559" [bits] => 32 [mime] => image/bmp)		
		$aInfo["width"]	= $aTemp[0];
		$aInfo["height"]= $aTemp[1];
		$aInfo["type"]	= $aTemp[2];
		$aInfo["name"]	= $sFileName;  //$aInfo["name"]	= basename($sFileName); // 传回不含路径的档案字串
		$aInfo["size"]  = filesize($sTmpFileName);
		return $aInfo;  // Array ( [width] => 629 [height] => 559 [type] => 6 [name] => 2007-11/021651350.BMP [size] => 1406498 )
	}
	
	//==========================================
	// 函数: makeThumb($sourFile,$width=128,$height=128) 
	// 功能: 生成缩略图(输出到浏览器)
	// 参数: $sourFile 图片源文件
	// 参数: $width 生成缩略图的宽度
	// 参数: $height 生成缩略图的高度
	// 返回: 0 失败 成功时返回生成的图片路径
	//==========================================
	/**
	 * 生成缩略图
	 *
	 * @param string $sFileName 上传后的文件
	 * @param string $iWidth 缩略图宽度
	 * @param string $iHeight 缩略图高度
	 * @return string  
	 */
	
	function makeThumb($sFileName,$iWidth="",$iHeight="")
	{
		$aInfo	= $this->getImgInfo($sFileName);	
		//print_r($aInfo); 
		$sTmpFileName = $this->sUploadPath . $sFileName;			
		$sNewFileName = substr($aInfo["name"], 0, -4) . "_t".substr($aInfo['name'],-4);
		
		switch ($aInfo["type"])
		{
			case 1:	//gif
				$bSrc = imagecreatefromgif($sTmpFileName);
				break;
			case 2:	//jpg
				$bSrc = imagecreatefromjpeg($sTmpFileName);
				break;
			case 3:	//png
				$bSrc = imagecreatefrompng($sTmpFileName);
				break;
			case 6:  //bmp
			    $bSrc = $this->ImageCreateFromBMP($sTmpFileName);
			    break;
			default:
				return "";
				break;
		}
		
		if (!$bSrc) return "";		
		
		$iSrcW	= $aInfo["width"];
		$iSrcH	= $aInfo["height"]; 
		
		$iNewW = $iWidth;
		$iNewH = $iHeight;	
		
		//先计算等比例，
		//Get The Big
		$icalc = 0;
		if ( $iHeight / $iSrcH > $iWidth / $iSrcW )
		{
			$icalc = $iHeight / $iSrcH;
		}
		else
		{
			$icalc = $iWidth / $iSrcW;
		}
		
		$iTempHegiht = $icalc * $iSrcH;
		$iTempWidth = $icalc * $iSrcW;
		
		$iScaleW = round($iTempWidth);
		$iScaleH = round($iTempHegiht);	
			//如果图片比显示的小，周边加白
		if($iSrcW < $iWidth && $iSrcH < $iHeight)
		{
			if (function_exists("imagecreatetruecolor")) //GD2.0.1
			{			
				$bNew = imagecreatetruecolor($iScaleW, $iScaleH);
			}
			else
			{
				$bNew = imagecreate($iScaleW, $iScaleH);
			}
			$white = imagecolorallocate($bNew, 255, 255, 255);
			//铺底色
			imagefill($bNew, 0, 0, $white);
			//Move to Center
			$NewStartX = ($iScaleW -  $iSrcW)/2;
			$NewStartY = ($iScaleH - $iSrcH) / 2;
			//Copy
			ImageCopy($bNew, $bSrc,$NewStartX, $NewStartY, 0,0, $iSrcW, $iSrcH);
		}
		else 
		{
		//缩放
		if (function_exists("imagecreatetruecolor")) //GD2.0.1
		{			
			$bScale = imagecreatetruecolor($iScaleW, $iScaleH);
			ImageCopyResampled($bScale, $bSrc, 0, 0, 0, 0, $iScaleW, $iScaleH, $iSrcW, $iSrcH);
		}
		else
		{
			$bScale = imagecreate($iScaleW, $iScaleH);
			ImageCopyResized($bScale, $bSrc, 0, 0, 0, 0, $iScaleW, $iScaleH, $iSrcW, $iSrcH);
		}
		
		//剪裁
		//Get The Start Point;
		$iNewStartX = ($iScaleW >= $iNewW)? ($iScaleW - $iNewW)/2 :  0;
		$iNewStartY = ($iScaleH >= $iNewH)? ($iScaleH - $iNewH)/2 :  0;
		
		if (function_exists("imagecreatetruecolor")) //GD2.0.1
		{			
			$bNew = imagecreatetruecolor($iNewW, $iNewH);
			ImageCopy($bNew, $bScale, 0, 0,$iNewStartX, $iNewStartY, $iScaleW, $iScaleH);
		}
		else
		{
			$bNew = imagecreate($iNewW, $iNewH);
			ImageCopy($bNew, $bScale, 0, 0, $iNewStartX, $iNewStartY, $iScaleW, $iScaleH);
		}		
		}
		//*/
		if ($this->toFile)
		{			
			if (file_exists($this->sUploadPath . $sNewFileName)) unlink($this->sUploadPath . $sNewFileName);
			if (file_exists($this->sUploadPath . $sFileName)) unlink($this->sUploadPath . $sFileName);
			ImageJPEG($bNew, $this->sUploadPath . $sNewFileName);
			
			ImageDestroy($bScale);
			ImageDestroy($bNew);
			ImageDestroy($bSrc);
			return $sNewFileName;
		}
		else
		{
			ImageJPEG($bScale);
			ImageDestroy($bScale); 
			ImageDestroy($bNew);
			ImageDestroy($bSrc);
		}
	}	
	
	//==========================================
	// 函数: makeWaterMark($sFileName, $sWaterText)
	// 功能: 给图片加水印
	// 参数: $sFileName 图片文件名
	// 参数: $sWaterText 文本数组(包含二个字符串)
	// 返回: 1 成功 成功时返回生成的图片路径
	//==========================================
	
	function makeWaterMark($sFileName,$sWaterText,$iColor=0)
	{
		$aInfo	= $this->getImgInfo($sFileName);	
		$sTmpFileName = $this->sUploadPath . $sFileName;		
		$sNewFileName = substr($aInfo["name"], 0, -4) . "_w.".substr($aInfo['name'],-3);
		switch ($aInfo["type"])
		{
			case 1:	//gif
				$bSrc = imagecreatefromgif($sTmpFileName);
				break;
			case 2:	//jpg
				$bSrc = imagecreatefromjpeg($sTmpFileName);
				break;
			case 3:	//png
				$bSrc = imagecreatefrompng($sTmpFileName);
				break;
			default:
				return "";
				break;
		}
		if (!$bSrc) return "";
		
		$iSrcW	= $aInfo["width"];
		$iSrcH	= $aInfo["height"];
		$iNewW = $iSrcW; $iNewH = $iSrcH;
					
		//*
		if (function_exists("imagecreatetruecolor")) //GD2.0.1
		{
			$bNew = imagecreatetruecolor($iNewW, $iNewH);
			ImageCopyResampled($bNew, $bSrc, 0, 0, 0, 0, $iNewW, $iNewH, $iSrcW, $iSrcH);
		}
		else
		{
			$bNew = imagecreate($iNewW, $iNewH);
			ImageCopyResized($bNew, $bSrc, 0, 0, 0, 0, $iNewW, $iNewH, $iSrcW, $iSrcH);
		}	
		
		$iAlpha = 63; // 半透明
		$red = imageColorAllocateAlpha($bNew, 255, 0, 0,$iAlpha);      //红色
		$green = imagecolorallocatealpha($bNew,0,255,0,$iAlpha);       // 绿色
		$blue = imagecolorallocatealpha($bNew,0,0,255,$iAlpha);       // 蓝色
		$white = imagecolorallocatealpha($bNew,255,255,255,$iAlpha);  // 白色
		$black = imagecolorallocatealpha($bNew,0,0,0,$iAlpha);       // 黑色
		$grey = imagecolorallocatealpha($bNew,192,192,192,$iAlpha);  // 灰色
		$purple = imagecolorallocatealpha($bNew,255,0,255,$iAlpha); // 紫色
		
		// 和imagecolorallocate() 相同，但多了一个额外的透明度参数,其值从0 到127。0 表示完全不透明，127 表示完全透明。
		$aWaterColor = $this->aWaterColor;
		$sColor = $aWaterColor[$iColor];
		//print_r($aWaterColor); echo $sColor; 	exit;
		$color = $$sColor;	
		//echo  $this->fontName;
		
		//@ImageTTFText($bNew, 18, 0, 5, 23, $color, $this->fontName, $sWaterText);  // 加水印文字1 （左上角）
		@ImageTTFText($bNew, 18, 0,$iNewW/2-80, $iNewH/2-5, $color, $this->fontName, $sWaterText);  // 加水印文字1 （中间）
		//@ImageTTFText($bNew, 18, 0,$iNewW-160, $iNewH-5, $color, $this->fontName, $sWaterText);  // 加水印文字1 （右下角）
		// array imagettftext ( resource image, int size, int angle, int x, int y, int color, string fontfile, string text)
		// 将字符串 text 画到 image 所代表的图像上，从坐标 x，y（左上角为 0, 0）开始，角度为 angle，颜色为 color，使用 fontfile 所指定的 TrueType 字体文件。根据 PHP 所使用的 GD 库的不同，如果 fontfile 没有以 '/'开头，则 '.ttf' 将被加到文件名之后并且会搜索库定义字体路径
				
        if ($this->toFile)
		{
			//echo $this->sUploadPath . $sNewFileName;
			if (file_exists($this->sUploadPath . $sNewFileName)) unlink($this->sUploadPath . $sNewFileName);
			ImageJPEG($bNew, $this->sUploadPath . $sNewFileName);
			ImageDestroy($bNew);
			ImageDestroy($bSrc);

			return $sNewFileName;
		}
		else
		{
			ImageJPEG($bNew);
			ImageDestroy($bNew);
			ImageDestroy($bSrc);
		}
	}
	
	//==========================================
	// 函数: makeThumbNew($sourFile,$width=128,$height=128) 
	// 功能: 生成缩略图(输出到浏览器)
	// 参数: $sourFile 图片源文件
	// 参数: $width 生成缩略图的宽度
	// 参数: $height 生成缩略图的高度
	// 返回: 0 失败 成功时返回生成的图片路径
	//==========================================
	/**
	 * 把bmp文件读成资源，通过文件生成图片(bmp格式的)
	 */
	function ImageCreateFromBMP($filename)
{
 //Ouverture du fichier en mode binaire
   if (! $f1 = fopen($filename,"rb")) return FALSE;

 //1 : Chargement des ent?s FICHIER
   $FILE = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread($f1,14));
   if ($FILE['file_type'] != 19778) return FALSE;

 //2 : Chargement des ent?s BMP
   $BMP = unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel'.
                 '/Vcompression/Vsize_bitmap/Vhoriz_resolution'.
                 '/Vvert_resolution/Vcolors_used/Vcolors_important', fread($f1,40));
   $BMP['colors'] = pow(2,$BMP['bits_per_pixel']);
   if ($BMP['size_bitmap'] == 0) $BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];
   $BMP['bytes_per_pixel'] = $BMP['bits_per_pixel']/8;
   $BMP['bytes_per_pixel2'] = ceil($BMP['bytes_per_pixel']);
   $BMP['decal'] = ($BMP['width']*$BMP['bytes_per_pixel']/4);
   $BMP['decal'] -= floor($BMP['width']*$BMP['bytes_per_pixel']/4);
   $BMP['decal'] = 4-(4*$BMP['decal']);
   if ($BMP['decal'] == 4) $BMP['decal'] = 0;

 //3 : Chargement des couleurs de la palette
   $PALETTE = array();
   if ($BMP['colors'] < 16777216)
   {
   $PALETTE = unpack('V'.$BMP['colors'], fread($f1,$BMP['colors']*4));
   }

 //4 : Cr?ion de l'image
   $IMG = fread($f1,$BMP['size_bitmap']);
   $VIDE = chr(0);

   $res = imagecreatetruecolor($BMP['width'],$BMP['height']);
   $P = 0;
   $Y = $BMP['height']-1;
   while ($Y >= 0)
   {
   $X=0;
   while ($X < $BMP['width'])
   {
     if ($BMP['bits_per_pixel'] == 24)
       $COLOR = unpack("V",substr($IMG,$P,3).$VIDE);
     elseif ($BMP['bits_per_pixel'] == 16)
     {
       $COLOR = unpack("n",substr($IMG,$P,2));
       $COLOR[1] = $PALETTE[$COLOR[1]+1];
     }
     elseif ($BMP['bits_per_pixel'] == 8)
     {
       $COLOR = unpack("n",$VIDE.substr($IMG,$P,1));
       $COLOR[1] = $PALETTE[$COLOR[1]+1];
     }
     elseif ($BMP['bits_per_pixel'] == 4)
     {
       $COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
       if (($P*2)%2 == 0) $COLOR[1] = ($COLOR[1] >> 4) ; else $COLOR[1] = ($COLOR[1] & 0x0F);
       $COLOR[1] = $PALETTE[$COLOR[1]+1];
     }
     elseif ($BMP['bits_per_pixel'] == 1)
     {
       $COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
       if    (($P*8)%8 == 0) $COLOR[1] =  $COLOR[1]        >>7;
       elseif (($P*8)%8 == 1) $COLOR[1] = ($COLOR[1] & 0x40)>>6;
       elseif (($P*8)%8 == 2) $COLOR[1] = ($COLOR[1] & 0x20)>>5;
       elseif (($P*8)%8 == 3) $COLOR[1] = ($COLOR[1] & 0x10)>>4;
       elseif (($P*8)%8 == 4) $COLOR[1] = ($COLOR[1] & 0x8)>>3;
       elseif (($P*8)%8 == 5) $COLOR[1] = ($COLOR[1] & 0x4)>>2;
       elseif (($P*8)%8 == 6) $COLOR[1] = ($COLOR[1] & 0x2)>>1;
       elseif (($P*8)%8 == 7) $COLOR[1] = ($COLOR[1] & 0x1);
       $COLOR[1] = $PALETTE[$COLOR[1]+1];
     }
     else
       return FALSE;
     imagesetpixel($res,$X,$Y,$COLOR[1]);
     $X++;
     $P += $BMP['bytes_per_pixel'];
   }
   $Y--;
   $P+=$BMP['decal'];
   }

 //Fermeture du fichier
   fclose($f1);

 return $res;
}
	/**
	 * 生成缩略图,先等比缩放，然后剪裁
	 *
	 * @param string $sFileName 上传后的文件
	 * @param string $iWidth 缩略图宽度
	 * @param string $iHeight 缩略图高度
	 * @return string  
	 */
	
	function makeThumbNew($sFileName,$iWidth="",$iHeight="",$iSmallWidth="",$iSmallHeight="")
	{
		$aInfo	= $this->getImgInfo($sFileName);	
		//print_r($aInfo); 
		$sTmpFileName = $this->sUploadPath . $sFileName;			
		$sNewFileName = substr($aInfo["name"], 0, -4) . "_b".substr($aInfo['name'],-4);
		$sNewSmallFileName = substr($aInfo["name"], 0, -4) . "_s".substr($aInfo["name"], -4);
		switch ($aInfo["type"])
		{
			case 1:	//gif
				$bSrc = imagecreatefromgif($sTmpFileName);
				break;
			case 2:	//jpg
				$bSrc = imagecreatefromjpeg($sTmpFileName);
				break;
			case 3:	//png
				//对于小图片特殊处理;直接copy图片,以避免png格式的黑底
				if( $aInfo["width"] < $iWidth &&  $aInfo["height"] < $iHeight )
				{
					copy($sTmpFileName,$this->sUploadPath.$sNewFileName);
					copy($sTmpFileName,$this->sUploadPath.$sNewSmallFileName);
					return;	
				}
				else
				{
					$bSrc = imagecreatefrompng($sTmpFileName);
				}
				break;
			case 6:
				$bSrc = $this->ImageCreateFromBMP($sTmpFileName);
				break;
			default:
				return "";
				break;
		}
		
		if (!$bSrc) return "";		
		
		$iSrcW	= $aInfo["width"];
		$iSrcH	= $aInfo["height"]; 
		
		$iSmallSrcW = $aInfo["width"];
		$iSmallSrcH = $aInfo["height"];
	
		$iNewW = $iWidth;
		$iNewH = $iHeight;	
		
		$iNewSmallWidth = $iSmallWidth;
		$iNewSmallHeight = $iSmallHeight;
		//先计算等比例，
		//Get The Big
		$icalc = 0;
		if ( $iHeight / $iSrcH > $iWidth / $iSrcW )
		{
			$icalc = $iHeight / $iSrcH;
		}
		else
		{
			$icalc = $iWidth / $iSrcW;
		}
		//Get The Small
		$iCalcSmall = 0;
		if ($iSmallHeight / $iSrcH > $iSmallWidth / $iSrcW)
		{
			$iCalcSmall = $iSmallHeight / $iSrcH;
		}
		else 
		{
			$iCalcSmall = $iSmallWidth / $iSrcW;
		}
		
		$iTempHegiht = $icalc * $iSrcH;
		$iTempWidth = $icalc * $iSrcW;
		
		$iSmallTempHeight = $iCalcSmall * $iSrcH;
		$iSmallTempWidth = $iCalcSmall * $iSrcW;
		
		$iScaleW = round($iTempWidth);
		$iScaleH = round($iTempHegiht);	
		
		$iSmallScaleW = round($iSmallTempWidth);
		$iSmallScaleH = round($iSmallTempHeight);
		
		//缩放或者扩大
		if (function_exists("imagecreatetruecolor")) //GD2.0.1
		{			
			$bScale = imagecreatetruecolor($iScaleW, $iScaleH);
			ImageCopyResampled($bScale, $bSrc, 0, 0, 0, 0, $iScaleW, $iScaleH, $iSrcW, $iSrcH);
			
			$bSmallScale = imagecreatetruecolor($iSmallScaleW, $iSmallScaleH);
			ImageCopyResampled($bSmallScale, $bSrc, 0, 0, 0, 0, $iSmallScaleW, $iSmallScaleH, $iSmallSrcW, $iSmallSrcH);
		}
		else
		{
			$bScale = imagecreate($iScaleW, $iScaleH);
			ImageCopyResized($bScale, $bSrc, 0, 0, 0, 0, $iScaleW, $iScaleH, $iSrcW, $iSrcH);
			
			$bSmallScale = imagecreate($iSmallScaleW, $iSmallScaleH);
			imagecopyresized($bSmallScale, $bSrc, 0, 0, 0, 0, $iSmallScaleW, $iSmallScaleH, $iSrcW, $iSrcH);
		}
		
		//剪裁
		//Get The Start Point;
		$iNewStartX = ($iScaleW >= $iNewW)? ($iScaleW - $iNewW)/2 :  0;
		$iNewStartY = ($iScaleH >= $iNewH)? ($iScaleH - $iNewH)/2 :  0;
		
		$iSmallNewStartX = ($iSmallScaleW >= $iNewSmallWidth) ? ($iSmallScaleW - $iNewSmallWidth)/2 : 0;
		$iSmallNewStartY = ($iSmallScaleH >= $iNewSmallHeight) ? ($iSmallScaleH - $iNewSmallHeight)/2 : 0;
		if (function_exists("imagecreatetruecolor")) //GD2.0.1
		{			
			$bNew = imagecreatetruecolor($iNewW, $iNewH);
	
			ImageCopy($bNew, $bScale, 0, 0,$iNewStartX, $iNewStartY, $iScaleW, $iScaleH);
			
			$bSmallNew = imagecreatetruecolor($iNewSmallWidth, $iNewSmallHeight);
			ImageCopy($bSmallNew, $bSmallScale, 0, 0, $iSmallNewStartX, $iSmallNewStartY, $iSmallScaleW, $iSmallScaleH);
		}
		else
		{
			$bNew = imagecreate($iNewW, $iNewH);
			ImageCopy($bNew, $bScale, 0, 0, $iNewStartX, $iNewStartY, $iScaleW, $iScaleH);
		}		
		
		
		//*/
		if ($this->toFile)
		{			
			if (file_exists($this->sUploadPath . $sNewFileName)) unlink($this->sUploadPath . $sNewFileName);
			if (file_exists($this->sUploadPath . $sNewSmallFileName)) unlink($this->sUploadPath . $sNewSmallFileName);
			if (file_exists($this->sUploadPath . $sFileName)) unlink($this->sUploadPath . $sFileName);
			
			ImageJPEG($bScale, $this->sUploadPath . $sNewFileName);
			ImageJPEG($bSmallNew, $this->sUploadPath . $sNewSmallFileName);
			
			ImageDestroy($bScale);
			ImageDestroy($bSmallScale);
			ImageDestroy($bNew);
			ImageDestroy($bSmallNew);
			ImageDestroy($bSrc);
			return $sNewFileName;
			return $sNewSmallFileName;
		}
		else
		{
			ImageJPEG($bScale);
			ImageDestroy($bScale); 
			ImageDestroy($bSmallScale);
			ImageDestroy($bNew);
			ImageDestroy($bSmallNew);
			ImageDestroy($bSrc);
		}
	}

	
	function setWater($imgSrc,$markImg,$markText,$TextColor,$markPos,$fontType,$markType)
{

    $srcInfo = @getimagesize($imgSrc);
    $srcImg_w    = $srcInfo[0];
    $srcImg_h    = $srcInfo[1];
       
    switch ($srcInfo[2])
    {
        case 1:
            $srcim =imagecreatefromgif($imgSrc);
            break;
        case 2:
            $srcim =imagecreatefromjpeg($imgSrc);
            break;
        case 3:
            $srcim =imagecreatefrompng($imgSrc);
            break;
        default:
            die("不支持的图片文件类型");
            exit;
    }
    if(!strcmp($markType,"img"))
    {
        if(!file_exists($markImg) || empty($markImg))
        {
            return;
        }
        $markImgInfo = @getimagesize($markImg);
        $markImg_w    = $markImgInfo[0];
        $markImg_h    = $markImgInfo[1];
           
        if($srcImg_w < $markImg_w || $srcImg_h < $markImg_h)
        {
            return;
        }
           
        switch ($markImgInfo[2])
        {
            case 1:
                $markim =imagecreatefromgif($markImg);
                break;
            case 2:
                $markim =imagecreatefromjpeg($markImg);
                break;
            case 3:
                $markim =imagecreatefrompng($markImg);
                break;
            default:
                die("不支持的水印图片文件类型");
                exit;
        }
           
        $logow = $markImg_w;
        $logoh = $markImg_h;
    }
       
    if(!strcmp($markType,"text"))
    {
        $fontSize = 16;
        if(!empty($markText))
        {
            if(!file_exists($fontType))
            {
                return;
            }
        }
        else {
            return;
        }
           
        $box = @imagettfbbox($fontSize, 0, $fontType,$markText);
        $logow = max($box[2], $box[4]) - min($box[0], $box[6]);
        $logoh = max($box[1], $box[3]) - min($box[5], $box[7]);
    }
       
    if($markPos == 0)
    {
        $markPos = rand(1, 9);
    }
       
    switch($markPos)
    {
        case 1:
            $x = +5;
            $y = +5;
            break;
        case 2:
            $x = ($srcImg_w - $logow) / 2;
            $y = +5;
            break;
        case 3:
            $x = $srcImg_w - $logow - 5;
            $y = +15;
            break;
        case 4:
            $x = +5;
            $y = ($srcImg_h - $logoh) / 2;
            break;
        case 5:
            $x = ($srcImg_w - $logow) / 2;
            $y = ($srcImg_h - $logoh) / 2;
            break;
        case 6:
            $x = $srcImg_w - $logow - 5;
            $y = ($srcImg_h - $logoh) / 2;
            break;
        case 7:
            $x = +5;
            $y = $srcImg_h - $logoh - 5;
            break;
        case 8:
            $x = ($srcImg_w - $logow) / 2;
            $y = $srcImg_h - $logoh - 5;
            break;
        case 9:
            $x = $srcImg_w - $logow - 5;
            $y = $srcImg_h - $logoh -5;
            break;
        default:
            die("此位置不支持");
            exit;
    }
       
    $dst_img = @imagecreatetruecolor($srcImg_w, $srcImg_h); 
    imagecopy ( $dst_img, $srcim, 0, 0, 0, 0, $srcImg_w, $srcImg_h);
       
    if(!strcmp($markType,"img"))
    {
        imagecopy($dst_img, $markim, $x, $y, 0, 0, $logow, $logoh);
        imagedestroy($markim);
    }
       
    if(!strcmp($markType,"text"))
    {
        $rgb = explode(',', $TextColor);
           
        $color = imagecolorallocate($dst_img, $rgb[0], $rgb[1], $rgb[2]);
        imagettftext($dst_img, $fontSize, 0, $x, $y, $color, $fontType,$markText);
    }
       
    switch ($srcInfo[2])
    {
        case 1:
            imagegif($dst_img, $imgSrc);
            break;
        case 2:
            imagejpeg($dst_img, $imgSrc);
            break;
        case 3:
            imagepng($dst_img, $imgSrc);
            break;
        default:
            die("不支持的水印图片文件类型");
            exit;
    }
    imagedestroy($dst_img);
    imagedestroy($srcim);
}
	
	
}
?>
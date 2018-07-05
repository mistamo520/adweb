<?php
	
	/* Finally, A light, permissions-checking logging class. 
	 * 
	 * Author	: Kenneth Katzgrau < katzgrau@gmail.com >
	 * Date	: July 26, 2008
	 * Comments	: Originally written for use with wpSearch
	 * Website	: http://codefury.net
	 * Version	: 1.0
	 *
	 * Usage: 
	 *		$log = new KLogger ( "log.txt" , KLogger::INFO );
	 *		$log->LogInfo("Returned a million search results");	//Prints to the log file
	 *		$log->LogFATAL("Oh dear.");				//Prints to the log file
	 *		$log->LogDebug("x = 5");					//Prints nothing due to priority setting
	*/
	
	class Klogger
	{
		static public function log( $filepath, $line )
		{
			createdir(dirname($filepath));
			$line = date("Y-m-d H:i:s").'=>'.$line;
			$file_handle = fopen( $filepath , "a" );
			fwrite( $file_handle , $line );
			fclose($file_handle);       
		}
	}


?>
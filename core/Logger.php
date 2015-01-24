<?php

class Logger{
	const NOLOG = -1;
	const ERROR = 0;
	const WARNING = 1;
	const DEBUG = 2;

	function __construct($filename = "projectie.log", $loglevel = self::ERROR){
		$this->filename = $filename;
		$this->loglevel = $loglevel;
	}

	public function log($loglevel, $message){
		//Check if we're even logging the type of message we're receiving, else exit
		if($loglevel > $this->loglevel || $loglevel == self::NOLOG){
			return;
		}

		$datestring = date("[H:i:s]", time());
		$types = array("[ERROR]", "[WARNING]", "[DEBUG]");
		$typestring = $types[$loglevel];

		$logfile = fopen($_SERVER['DOCUMENT_ROOT']."/".$this->filename, 'a'); //This sh*t better not fail

		$backtrace = debug_backtrace();

		$call = "(".$backtrace[1]["file"].":".$backtrace[1]["line"].")";

		fwrite($logfile, $datestring.$typestring.$call.": ".$message."\n");

		fclose($logfile);

	}

	private $filename;
	private $loglevel;
}

?>
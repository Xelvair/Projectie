<?php

class Locale implements ArrayAccess{
	public function load($lang){
		//Generate path and check if file exists
		$locale_filepath = self::localeFilepath($lang);

		if(!file_exists($locale_filepath)){
			write_log(Logger::ERROR, "Failed to load locale file '".$lang."'! Is the file named properly?");
			return false;
		}

		//Load from file and decode into object
		$locale_contents = file_get_contents($locale_filepath);

		$this->locale = json_decode($locale_contents, true);
		if($this->locale){
			write_log(Logger::DEBUG, "Successfully loaded locale '".$lang."'!");
		} else {
			write_log(Logger::ERROR, "Failed to load locale '".$lang."'! Malformed JSON-Object!");
		}
	}

	private function localeFilepath($lang){
		return "../locale/".$lang.".locale";
	}

	public function get($tag){
		if(isset($this->locale)){
			if(isset($this->locale[$tag])){
				return $this->locale[$tag];
			} else {
				write_log(Logger::WARNING, "Failed to load locale string '".$tag."'!");
				return "MISSING_STRING";
			}
		} else {
			write_log(Logger::ERROR, "Cannot retrieve locale string, no locale was loaded!");
			return "MISSING_STRING";
		}
	}

	public function offsetExists($offset){
		return isset($this->locale[$offset]);
	}

	public function offsetGet($offset){
		return $this->locale[$offset];
	}

	public function offsetSet($offset, $value){
		write_log(Logger::ERROR, "Someone tried to set a locale value! Bad programmer!".callinfo());
	}

	public function offsetUnset($offset){
		write_log(Logger::ERROR, "Someone tried to unset a locale value! Bad programmer!".callinfo());
	}

	private $locale = null;
}

?>
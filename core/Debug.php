<?php

function callinfo(){
	$backtrace = debug_backtrace();
	return "(".$backtrace[1]["file"].":".$backtrace[0]["line"].")";
}

?>
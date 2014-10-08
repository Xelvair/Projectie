<?php

interface Model{
	//__construct gets passed the data it needs to know what to load/represent
	function __construct($data = null);
}

?>
<p>This is a test string within ExampleView!</p>
<?php
	global $locale;
	echo $_DATA["examplestring"];	
	echo $locale["test"];
	unset($locale["test"]);
?>
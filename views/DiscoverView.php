<?php
global $locale;
?>

<div class="row">
	<h1><?=$locale["search_tags"]?></h1>
	<div class="col-md-12" style="height: 300px; padding-bottom: 30px">
		<?=Core::view("TagBoxTest", ["editable" => true])?>
	</div>
</div>

<?php

global $locale;
?>

<div class="tagbox" data-tagsource="/project/get_tags/<?=$_DATA["project_id"]?>" data-tagremove="/project/untag/<?=$_DATA["project_id"]?>">
	<h1>Tags</h1>
	<ul class="tagbox-list">
	</ul>
	<div class="tagbox-bottom">
		<button class="tag_btn" id="addtag" data-toggle="popover" data-html="true" data-content="<div id='tag_input_group'><input type='text' class='form-control' placeholder='New tag...' id='input_newtag' onkeypress='submit_tag(event);'/></div>" data-placement="left">             
			<span class="glyphicon glyphicon-plus"></span><strong>tag</strong>
		</button>
	</div>
</div>
<?php

global $locale;
?>
<div style="width: 50%; height: 300px;">
	<div class="tagbox" data-state="list" data-tagsource="/project/get_tags/<?=$_DATA["project_id"]?>" data-tagremove="/project/untag/<?=$_DATA["project_id"]?>">
		<div class="tagbox-inner">
			<div class="tagbox-list">
			</div>
			<div class="tagbox-recommend">
			</div>
		</div>
		<button class="tag-btn" id="addtag" data-toggle="popover" data-html="true" data-content="<div id='tag_input_group'><input type='text' class='form-control' placeholder='New tag...' id='input_newtag' onkeypress='submit_tag(event);'/></div>" data-placement="left">             
			</span><strong>Add Tag</strong>
		</button>
	</div>
</div>
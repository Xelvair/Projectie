<?php

$editable = $_DATA["editable"];
$project_id = isset($_DATA["project_id"]) ? $_DATA["project_id"] : null;

global $locale;
?>

<div class="tagbox" data-state="list">
	<div class="tagbox-inner">
		<div 
			class="tagbox-list tagbox-tags" 
			data-editable="<?=($editable ? 1 : 0)?>" 
			<?php if($project_id){?>
			data-tagsource="/project/get_tags/<?=$project_id?>"
			data-tagremove="/project/untag/<?=$project_id?>" 
			data-tagadd="/project/tag/<?=$project_id?>"
			<?php } ?>
		>
		</div>
		<div class="tagbox-list tagbox-recommend" data-editable="false">
			<input type="text" class="tagbox-recommend-search" placeholder="Suche">
		</div>
	</div>
	<button class="tag-btn" id="addtag" data-toggle="popover" data-html="true" data-content="<div id='tag_input_group'><input type='text' class='form-control' placeholder='New tag...' id='input_newtag' onkeypress='submit_tag(event);'/></div>" data-placement="left">             
		</span><span class="glyphicon glyphicon-plus"></span>
	</button>
</div>
<?php
global $locale;
?>
<div>
<!-- base tag box -->
<div id="tag_box_template" class="tag_box">
	<h1 align="center" style="margin-top:0px;"><small><?=$locale['tags']?></small></h1>
    <ul class="tag_list list-inline">    
    </ul>
</div>

<!-- Goes inside .tag_list for each tag-->
<li id="tag_template" class="tag"><span class="tag_name"></span>
</li>

<!-- Goes inside .tag if editable by user-->
<div id="tag_delete_template" class="tag_delete">
	<a onclick="delete_tag(this, 1);" class="tag_a">
		<span class="glyphicon glyphicon-remove"></span>
	</a>
</div>

<!-- Goes after tag_list if editable-->
<div id="tag_box_bottom_template" class="tag_box_bottom">
	<button class="tag_btn" id="addtag" data-toggle="popover" data-html="true" data-content="<div id='tag_input_group'><input type='text' class='form-control' placeholder='New tag...' id='input_newtag' onkeypress='submit_tag(event);'/></div>" data-placement="left">             
		<span class="glyphicon glyphicon-plus"></span><strong>tag</strong>
	</button>
</div>

<script>
function TagBox_assembly_callback(data){
	var base_elem = $(data.template).find("#tag_box_template").clone().removeAttr("id");

	for(var i = 0; i < data.tags.length; i = i + 1){
		var tag = data.tags[i];

		var tag_elem = $(data.template).find("#tag_template").clone();
		tag_elem.attr("id", "tag_"+tag.tag_id);
		tag_elem.find(".tag_name").text(tag.name);

		if(data.editable){
			var edit_tag_elem = $(data.template).find("#tag_delete_template").clone().removeAttr("id");

			tag_elem.append(edit_tag_elem);
		}

		$(base_elem).children(".tag_list").append(tag_elem);
	}

	if(data.editable){
		var tag_bottom_elem = $(data.template).find("#tag_box_bottom_template").clone().removeAttr("id");

		$(base_elem).append(tag_bottom_elem);
	}

	return base_elem;
}

function TagBox_tag_add_name_retrieval_callback(){
	return $("#input_newtag").val();
}

function TagBox_tag_add_callback(data){
	var tag_elem = $("<li>").attr("id", "tag_" + data.tag_id);
	tag_elem.find(".tag_name").text(data.name);

	$(data.tagbox).find(".tag_list").append();
}

function TagBox_tag_removal_callback(data){
	$(data.tagbox).find("#tag_" + data.tag_id).remove();
}
</script>
</div>
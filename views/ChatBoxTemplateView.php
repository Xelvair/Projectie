<?php
global $locale;
?>
<div>
<!-- base tag box -->
<div id="tag_box_template" class="tag_box">
	<h1 align="center" style="margin-top:0px;"><small><?=$locale['tags']?></small></h1>
    <ul class="tag_list" class="list-inline">    
    </ul>
</div>

<!-- Goes inside .tag_list for each tag-->
<li id="tag_template" class="tag"><span class="tag_name"></span>
</li>

<!-- Goes inside .tag if editable by user-->
<div id="tag_delete_template" class="tag_delete">
	<a onclick="delete_tag(this, <?=$entry["tag_id"]?>);" class="tag_a">
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
	var base_elem = $("#tag_box_template").clone().removeAttr("id");

	for each (tag in data.tags){
		var tag_elem = $("#tag_template").clone();
		tag_elem.attr("id", "tag_"+tag.tag_id);
		tag_elem.find(".tag_name").text(tag.name);

		base_elem.find(".tag_list").append(tag_elem);
	}

	if(data.editable){
		var tag_bottom_elem = $("#tag_box_bottom_template").clone().removeAttr("id");

		base_elem.find(".tag_list").addAfter(tag_bottom_elem);
	}

	return base_elem;
}

function TagBox_tag_add_name_retrieval_callback(){
	return $("#input_newtag").val();
}

function TagBox_tag_add_callback(){

}

function TagBox_tag_removal_callback(){

}
</script>
</div>
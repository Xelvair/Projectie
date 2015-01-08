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
	<span class="glyphicon glyphicon-remove"></span>
</div>

<!-- Goes after tag_list if editable-->
<div id="tag_box_bottom_template" class="tag_box_bottom">
	<button class="tag_btn add_tag" data-trigger="manual" data-toggle="popover" data-html="true" data-content="<div id='tag_input_group'><input type='text' class='form-control' placeholder='New tag...' id='input_newtag'></div>" data-placement="left">             
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
		tag_elem.attr("data_tag_id", tag.tag_id);
		tag_elem.find(".tag_name").text(tag.name);

		if(data.editable){
			var edit_tag_elem = $(data.template).find("#tag_delete_template").clone().removeAttr("id");

			tag_elem.append(edit_tag_elem);

			tag_elem.hover(
				//onHover
				function(){
					var element = $(this);
					tOut = setTimeout(function(){
						$(element).find('.tag_delete').show().animate({width: '20px'},200);
					}, 500);
				},
				//onUnhover
				function(){
					clearTimeout(tOut);
					$(this).find('.tag_delete').animate({width:'1px'},200).delay(250).hide();
				}
			);

			var tag_delete_elem = $(tag_elem).find(".tag_delete");

			$(tag_delete_elem).on("click", function(){
				data.tagbox_obj.onRemoveTag($(this).parent().attr("data_tag_id"));
			});
		}

		$(base_elem).children(".tag_list").append(tag_elem);
	}

	if(data.editable){
		var tag_bottom_elem = $(data.template).find("#tag_box_bottom_template").clone().removeAttr("id");

		$(base_elem).append(tag_bottom_elem);

		add_tag_elem = tag_bottom_elem.find(".add_tag");

		add_tag_elem.click(function(){
			$(add_tag_elem).popover('toggle');

			$("#input_newtag").on("keypress", function(event){
				if(event.charCode == 13){
					$(event.target).val("");
					data.tagbox_obj.onAddTag($(this).val());
				}
			});
			
		});
	}
	return base_elem;
}

function TagBox_tag_add_name_retrieval_callback(){
	return $("#input_newtag").val();
}

function TagBox_tag_add_callback(tagbox_template, tagbox_elem, tag_id, tag_name){
	var tag_elem = $(tagbox_template).find("#tag_template").clone();
		tag_elem.attr("id", "tag_"+tag_id);
		tag_elem.attr("data_tag_id", tag_id);
		tag_elem.find(".tag_name").text("faget");

	console.log($(tagbox_template).find("#tag_template").get());

	$(tagbox_elem).find(".tag_list").append(tag_elem);
}

function TagBox_tag_remove_callback(tagbox_elem, tag_id){
	$(tagbox_elem).find("#tag_" + tag_id).remove();
}
</script>
</div>
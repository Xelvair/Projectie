<?php
#PARAMETERS
#tags
	#tag_id
	#name
#tag_box_title
	
global $locale;
?>
<script>
var popover = "hidden";

$(document).ready(function(){
	var tOut;
	
	$('.tag').hover(
		//onHover
		function(){
			var element = $(this);
			tOut = setTimeout(function(){
				$(element).find('.tag-remove').show().animate({width: '20px'},200);
			}, 500);
		},
		//onUnhover
		function(){
			clearTimeout(tOut);
			$(this).find('.tag-remove').animate({width:'1px'},200).delay(250).hide();
		}
	);
	
	$('#addtag').click(function(){
		if (popover=="shown"){
			$('#addtag').popover('hide');
			popover="hidden";
		} else if (popover == "hidden"){
			$('#addtag').popover('show');
			popover = "shown";
		}
	});
});

function submit_tag(event){
	$('#tag_input_group').removeClass('has-error');
	
	if (event.which == 13 || event.keyCode == 13) {
		var new_tag = document.getElementById('input_newtag').value;
			
		if(new_tag == ""){
			$('#tag_input_group').addClass('has-error');
		} else {
				
			var id = 23;

			$('#addtag').popover('hide');
			$('#tag-list').append('<li class="tag" id="tag'+id+'">'+new_tag+'<div class="tag-remove"><a onclick="delete_tag(this, '+id+');" class="tag_a"><span class="glyphicon glyphicon-remove"></span></a></div></li>');
			
			var tOut;
	
			$('.tag').hover(
				function(){
					var element = $(this);
					tOut = setTimeout(function(){
						$(element).find('.tag-remove').show().animate({width: '20px'},200);
					},500);			
				},
				function(){
					clearTimeout(tOut);
					$(this).find('.tag-remove').animate({width:'1px'},200).delay(250).hide();
				}
			);	
		}
        return false;
	}
    return true;
}

function delete_tag(element, id){
	$('#tag'+id).remove();
}

</script>
<div class="tagbox">
	<?php if($_DATA["tag_box_title"]){ ?>
		<h1 align="center" style="margin-top:0px;"><small><?=$locale['tags']?>ayylmao</small></h1>
	<?php } ?>
	<ul class="list-inline" id="tag-list">
		<?php foreach($_DATA["tags"] as $entry){ ?>
			 <li class="tag" id="tag<?=$entry['tag_id']?>"><?=$entry['name']?>
				 <div class="tag-remove">
					 <a onclick="delete_tag(this, <?=$entry['tag_id']?>);" class="tag_a">
						<span class="glyphicon glyphicon-remove"></span>
					 </a>
				 </div>
			 </li>
		<?php 
		}?>
	</ul>
	<div class="tagbox-bottom">
		<button class="tag_btn" id="addtag" data-trigger="manual" data-toggle="popover" data-html="true" data-content="<div id='tag_input_group'><input type='text' class='form-control' placeholder='New tag...' id='input_newtag' onkeypress='submit_tag(event);'/></div>" data-placement="left">             
			<span class="glyphicon glyphicon-plus"></span><strong>tag</strong>
		</button>
	</div>
</div>
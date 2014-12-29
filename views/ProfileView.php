<?php

#PARAMETERS
#footer : site footer
#user : user
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
				$(element).find('.tag_delete').show().animate({width: '20px'},200);
			}, 500);
		},
		//onUnhover
		function(){
			clearTimeout(tOut);
			$(this).find('.tag_delete').animate({width:'1px'},200).delay(250).hide();
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
			$('#tag-list').append('<li class="tag" id="tag'+id+'">'+new_tag+'<div class="tag_delete"><a onclick="delete_tag(this, '+id+');" class="tag_a"><span class="glyphicon glyphicon-remove"></span></a></div></li>');
			
			var tOut;
	
			$('.tag').hover(
				function(){
					var element = $(this);
					tOut = setTimeout(function(){
						$(element).find('.tag_delete').show().animate({width: '20px'},200);
					},500);			
				},
				function(){
					clearTimeout(tOut);
					$(this).find('.tag_delete').animate({width:'1px'},200).delay(250).hide();
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

<div class="row">
    <div class="col-md-3 col-xs-3" style="padding-top:20px;">
        <img src="<?=abspath("/public/images/default-profile-pic.png")?>" class="img-responsive img-rounded profile-pic">
    </div>
    <div class="col-md-5 col-xs-9">          
         <h1><?=$_DATA['user']["username"]?></h1>
                 <dl class="dl-horizontal">
                  <dt><?=$locale['projects_created']?>:</dt>
                  <dd><?=sizeof($_DATA['user']["created_projects"])?></dd>
                  <dt><?=$locale['projects_involved']?>:</dt>
                  <dd><?=sizeof($_DATA['user']["project_participations"])?></dd>
                </dl>     
    </div>
    <div class="col-md-4">
        <div class="row">
            <div class="tag_box">
            	<h1 align="center" style="margin-top:0px;"><small><?=$locale['tags']?></small></h1>
                <ul class="list-inline" id="tag-list">
                    <?php foreach($_DATA["user"]["tags"] as $entry){ ?>
                         <li class="tag" id="tag<?=$entry['tag_id']?>"><?=$entry['name']?>
	                         <div class="tag_delete">
		                         <a onclick="delete_tag(this, <?=$entry["tag_id"]?>);" class="tag_a">
		                         	<span class="glyphicon glyphicon-remove"></span>
		                         </a>
	                         </div>
                         </li>
                    <?php 
					}?>
                </ul>
            	<div class="tag_box_bottom">
					<button class="tag_btn" id="addtag" data-toggle="popover" data-html="true" data-content="<div id='tag_input_group'><input type='text' class='form-control' placeholder='New tag...' id='input_newtag' onkeypress='submit_tag(event);'/></div>" data-placement="left">             
						<span class="glyphicon glyphicon-plus"></span><strong>tag</strong>
					</button>
				</div>
            </div>
        </div>
    </div>
</div><!--row-->
<hr style="box-shadow:  2px 2px 5px 0px rgba(50, 48, 50, 0.5);"/>
<div class="row">
    <div class="col-md-6 col-xs-12 content_list">
    <h2 align="center" class="content_heading"><?=$locale['projects_created']?></h2>
        <?php foreach($_DATA["user"]["created_projects"] as $entry){ ?>
            <div class="media">
                <a class="pull-left" href="#">
                    <img class="media-object img-rounded" src="<?=$entry['thumb']?>" alt="...">
                </a>
                <div class="media-body">
                    <h4 class="media-heading"><?=$entry['title']?> 1</h4>
                    <?=$entry['desc']?>
                </div>
            </div><hr style="margin:15px;"/><!--media--> 
         <?php }?> 
    </div><!--col-->
    <div class="col-md-6 col-xs-12 content_list">
    <h2 align="center" class="content_heading"><?=$locale['projects_involved']?></h2>
        <?php foreach($_DATA["user"]["project_participations"] as $entry){ ?>
        <div class="media">
            <a class="pull-left" href="#">
                <img class="media-object img-rounded" src="<?=$entry['thumb']?>" alt="...">
            </a>
            <div class="media-body">
                <h4 class="media-heading"><?=$entry['title']?></h4>
                <?=$entry['desc']?>
            </div>
        </div><hr style="margin:15px;"/><!--media--> 
        <?php }?>
    </div><!--col-->
</div>
<?=$_DATA['footer']?>
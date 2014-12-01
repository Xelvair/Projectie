<?php

#PARAMETERS
#profile_pic : Profil pic
#username : username
#sum_projects_created : Sum projects created
#sum_projects_involved : sum projects involved
#skill : array skills
#created_project : created porjects
#involved_project : involved projects
global $locale;
?>

<script>
var popover = "hidden";

$(document).ready(function(){
	/*$('.tag').on('mouseenter', function(){
		$(this).find('.tag_delete').animate({width:'toggle'},200);
	});
	
	$('.tag').on('mouseleave',function(){
		$(this).find('.tag_delete').animate({width:'toggle'},200);
	});
	*/
	var tOut;
	
	$('.tag').hover(function(){
		var element = $(this);
		tOut = setTimeout(function(){
					$(element).find('.tag_delete').show().animate({width: '20px'},200);
				},500);
		
			
		
			
		},function(){
			clearTimeout(tOut);
			$(this).find('.tag_delete').animate({width:'1px'},200).delay(250).hide();
			
			});
	
	
	$('#addskill').click(function(){
		if(popover=="shown"){
			$('#addskill').popover('hide');
			popover="hidden";
		}else if(popover == "hidden"){
			$('#addskill').popover('show');
			popover = "shown";
		}
		
	});
	
});

function submit_skill(event){
	
	$('#skill_input_group').removeClass('has-error');
	
	if (event.which == 13 || event.keyCode == 13) {
		
			var new_skill = document.getElementById('input_newskill').value;
			
			if(new_skill == ""){
				
				$('#skill_input_group').addClass('has-error');
				
			}else{
				
			var id = 23;
			
            $('#addskill').popover('hide');
			$('#skill-list').append('<li class="tag" id="skill'+id+'">'+new_skill+'<div class="tag_delete"><a onclick="delete_skill(this, '+id+');" class="skill_a"><span class="glyphicon glyphicon-remove"></span></a></div></li>');
			
			var tOut;
	
	$('.tag').hover(function(){
		var element = $(this);
		tOut = setTimeout(function(){
					$(element).find('.tag_delete').show().animate({width: '20px'},200);
				},500);
		
			
		
			
		},function(){
			clearTimeout(tOut);
			$(this).find('.tag_delete').animate({width:'1px'},200).delay(250).hide();
			
			});
			
			}
            return false;
        }
        return true;
}

function delete_skill(element, id){

		$('#skill'+id).remove();
	
}

</script>

<div class="row">
    <div class="col-md-3 col-xs-3" style="padding-top:20px;">
        <img src="<?=$_DATA['profile_pic']?>" class="img-responsive img-rounded">
    </div>
    <div class="col-md-5 col-xs-9">          
         <h1><?=$_DATA['username']?></h1>
                 <dl class="dl-horizontal">
                  <dt><?=$locale['projects_created']?>:</dt>
                  <dd><?=$_DATA['sum_projects_created']?></dd>
                  <dt><?=$locale['projects_involved']?>:</dt>
                  <dd><?=$_DATA['sum_projects_involved']?></dd>
                </dl>     
    </div>
    <div class="col-md-4">
        <div class="row">
            <div class="skill_box">
            <h1 align="center" style="margin-top:0px;"><small><?=$locale['skills']?></small></h1>
                <ul class="list-inline" id="skill-list">
                    <?php foreach($_DATA["skill"] as $entry){ 
							$skill = explode('|',$entry);
								?>
                         <li class="tag" id="skill<?=$skill[1]?>"><?=$skill[0]?><div class="tag_delete"><a onclick="delete_skill(this, <?=$skill[1]?>);" class="skill_a"><span class="glyphicon glyphicon-remove"></span></a></div></li>
                    <?php 
					}?>
                </ul>
            	<div class="skill_box_bottom">
                <button class="skill_btn" id="addskill"  data-toggle="popover" data-html="true" data-content="<div id='skill_input_group'><input type='text' class='form-control' placeholder='New skill...' id='input_newskill' onkeypress='submit_skill(event);'/></div>" data-placement="left">             
                                <span class="glyphicon glyphicon-plus"></span><strong>Skill</strong>
                </button>
               </div>
            </div>
        </div>
    </div>
</div><!--row-->
<hr style="box-shadow:  2px 2px 5px 0px rgba(50, 48, 50, 0.5);"/>
<div class="row">
    <div class="col-md-6 col-xs-12 content_list">
    <h1 align="center" class="content_heading"><?=$locale['projects_created']?></h1>
        <?php foreach($_DATA["created_project"] as $entry){ ?>
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
    <h1 align="center" class="content_heading"><?=$locale['projects_involved']?></h1>
        <?php foreach($_DATA["involved_project"] as $entry){ ?>
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
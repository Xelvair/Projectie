<?php
#PARAMETERS
#footer : Footer
#upload_picture_modal : Upload Picture Modal


global $locale;
?>
<script>
	$(document).ready(function(){

		var tOut;
		
		$('#title').hover(function(){
			tOut = setTimeout(function(){
				$('#title').popover('show');			
				},2000);
		},function(){
			clearTimeout(tOut);
			$('#title').popover('destroy');
		});
		
		$('#subtitle').hover(function(){
			tOut = setTimeout(function(){
				$('#subtitle').popover('show');			
				},2000);
		},function(){
			clearTimeout(tOut);
			$('#subtitle').popover('destroy');
		});
	
		$('#desc').hover(function(){
			tOut = setTimeout(function(){
				$('#desc').popover('show');			
				},2000);
		},function(){
			clearTimeout(tOut);
			$('#desc').popover('destroy');
		});	

		$('#create_project_submit').on("click", function(){
			console.log("pls");
			$('#create_project_form').submit();
		});
	});
	
	function hover(element) {
		element.setAttribute('src', '../public/images/upload_pic_hover.png');
	}
	
	function unhover(element) {
		element.setAttribute('src', '../public/images/upload_pic.png');
	}
</script>
<div class="row" id="banner_wrap">
        <img class="img-responsive banner" src="../public/images/default-banner.png"/>
        <div id="upload_tag">
        	<a href='#PicUploadModal' data-toggle='modal'>
            	<img src="../public/images/upload_pic.png" onmouseover="hover(this);" onmouseout="unhover(this);"/>
            </a>
        </div>
</div>
<div class="row" style="margin-top:20px; margin-bottom:20px;">
    <div class="col-xs-2">
    <a href='#PicUploadModal' data-toggle='modal'>
    	<img class="img-rounded img-responsive" src="../public/images/default-profile-pic.png"/>
     </a>
    </div>
    <div class="col-xs-10" style="padding-left: 5%; padding-right: 5%;">
    <form id="create_project_form" action="<?=abspath("/project/create")?>&redirect=/index" method="POST">
            <div class="form-group" id="title_group">
                <input type="text" name="title" class="form-control .input-lg" 
                id="title" data-toggle="popover" data-placement="bottom" data-trigger="focus" placeholder="Project Title" data-content="<?=$locale['create_title_title']?>"/>
            </div>
            <div class="form-group" id="subtitle_group">
                <input type="text" name="subtitle" class="form-control .input-lg" id="subtitle"
                 data-toggle="popover" data-placement="bottom" placeholder="Subtitle" data-content="<?=$locale['create_subtitle_title']?>"/>
            </div>
            <div class="form-group" id="description_group">
                <textarea name="description" rows="8" class="form-control" id="desc" data-toggle="popover" data-placement="bottom" placeholder="Description" 
                data-content="<?=$locale['create_desc_title']?>"></textarea>
            </div>
            <div class="form-group pull-right">
            <button id="create_project_submit" type="button" class="btn btn-default"><?=$locale['create']?></button>        
            </div>
     </form>
    </div>
</div>
 <?=$_DATA['footer']?>
 <?=$_DATA['upload_picture_modal']?>
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
			var form_data = $('#create_project_form').serialize();

			$.post("<?=abspath('/project/create')?>", form_data, function(result){
				result_obj = JSON.parse(result);
				if(result_obj.ERROR === undefined){
					window.location = "<?=abspath('/project/show/')?>" + result_obj.project_id;
				} else {
					alert("There was an error with the submission, we're sorry!");
				}
			})
		});
	});
	
	function hover(element) {
		element.setAttribute('src', '../public/images/upload_pic_hover.png');
	}
	
	function unhover(element) {
		element.setAttribute('src', '../public/images/upload_pic.png');
	}
</script>
<div class="row">
	<div class="col-xs-12 create-project-introduction">
		<h1>Creating a new Project</h1>
		<p>
		Create a Project on Projectie and find people willing to contribute!<br><br>
		It's as easy as entering a title for your project, a subtitle and a description, then adding a Banner to make your venture more appealing to others.
		</p>
	</div>
</div>
<div class="row" style="margin-top:20px; margin-bottom:20px;">
    <div class="col-xs-12">
    <form id="create_project_form" action="<?=abspath("/project/create")?>&redirect=/index" method="POST">
            <div class="form-group" id="title_group">
          		<label for="title"><?=$locale['project_title']?></label>
              <input type="text" name="title" class="form-control .input-lg" autocomplete="off"
              id="title" data-toggle="popover" data-placement="bottom" data-trigger="focus" placeholder="<?=$locale['create_title_title']?>..."/>
            </div>
            <div class="form-group" id="subtitle_group">
          		<label for="subtitle"><?=$locale['subtitle']?></label>
              <input type="text" name="subtitle" class="form-control .input-lg" id="subtitle" autocomplete="off"
               data-toggle="popover" data-placement="bottom" placeholder="<?=$locale['create_subtitle_title']?>..."/>
            </div>
            <div class="form-group" id="description_group">
          		<label for="desc"><?=$locale['desc']?></label>
              <textarea name="description" rows="8" class="form-control" id="desc" autocomplete="off" data-toggle="popover" data-placement="bottom" placeholder="<?=$locale['create_desc_title']?>..."></textarea>
            </div>
            <div class="form-group pull-right">
            <button id="create_project_submit" type="button" class="btn btn-default"><?=$locale['create']?></button>        
            </div>
     </form>
    </div>
</div>
<?=$_DATA['upload_picture_modal']?>
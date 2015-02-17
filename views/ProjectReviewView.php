<?php
#PARAMETERS
#
#thumb : project pic
#members : number of members
#favs : number of favs
#title : project title
#description : project description
#project_id : project id
global $locale;
?>

<a class="neutral" href="<?=abspath('project/show/').$_DATA['project_id']?>">
	<div class="project_preview">
		<div class="preview_head">
			<div class="head_wrap">
				<img src="<?=abspath($_DATA['title_picture']['file_path'])?>">
				<div class="preview_properties pull-right">
					<div class="favs">
						<span class="favs_number"><?=number_format($_DATA["fav_count"], 0, ",", ".")?></span>
						<span class="glyphicon glyphicon-star project-fav" data-project-id="<?=$_DATA["project_id"]?>"></span>
					</div>
					<div class="members">
						<?=number_format($_DATA["participator_count"], 0, ",", ".")?>
						<span class="glyphicon glyphicon-user"></span>
					</div>
				</div>
			</div>
		</div>
		<div class="preview_content">
			<h3><?=$_DATA["title"]?></h3>
			<?=$_DATA["description"]?>
		</div>
	</div>
</a>
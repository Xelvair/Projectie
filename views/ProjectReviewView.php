<?php
#PARAMETERS
#
#thumb : project pic
#members : number of members
#favs : number of favs
#title : project title
#desc : project description
#id : project id
global $locale;
?>

<a class="neutral" href="<?=abspath('project/show/').$_DATA['id']?>">
	<div class="project_preview">
		<div class="preview_head">
			<div class="head_wrap">
				<img src="<?=$_DATA['thumb']?>">
				<div class="preview_properties pull-right">
					<div class="favs">
						<?=number_format($_DATA["favs"], 0, ",", ".")?>
						<span class="glyphicon glyphicon-star"></span>
					</div>
					<div class="members">
						<?=number_format($_DATA["members"], 0, ",", ".")?>
						<span class="glyphicon glyphicon-user"></span>
					</div>
				</div>
			</div>
		</div>
		<div class="preview_content">
			<h3><?=$_DATA["title"]?></h3>
			<?=$_DATA["desc"]?>
		</div>
	</div>
</a>
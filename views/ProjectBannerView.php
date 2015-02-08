<?php
#projects : array of project info
#		project_id : id of project
#		fav_count : number of favs
#		participator_count : number of participators
#editable : whether the current user can open the title upload modal

write_log(Logger::DEBUG, print_r($_DATA, true));
?>
<div class="project-banner-backdrop">
	
</div>
<div class="project-banner" data-editable="<?=(int)!!$_DATA['editable']?>">
	<div class="project-banner-inner">
		<div class="project-banner-arrow-left"></div>
		<div class="project-banner-arrow-right"></div>
		<div class="project-banner-info">
			<span class="project-banner-info-participator-count">&nbsp;</span><span class="glyphicon glyphicon-user"></span>
			<span class="project-banner-info-fav-count">&nbsp;</span><span class="glyphicon glyphicon-star"></span>
		</div>
		<div class="project-banner-content">
			<ul class="project-banner-content-list">
				<?php foreach($_DATA["projects"] as $project){ ?>
					<?php $editable = (isset($project["editable"]) ? $project["editable"] : false) ?>
					<li class="project-banner-content-list-item" data-fav-count="<?=$project["fav_count"]?>" data-participator-count="<?=$project["participator_count"]?>" data-editable="<?=(int)$editable?>">
						<a href="/project/show/<?=$project["project_id"]?>"><img src="<?=$project["title_picture"]["file_path"]?>"></a>
					</li>
				<?php } ?>
			</ul>
		</div>
	</div>
</div>
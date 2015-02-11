<?php
#projects : array of project info
#		project_id : id of project
#		fav_count : number of favs
#		participator_count : number of participators
#editable : whether the current user can open the title upload modal

require_once(abspath_lcl("/templates/upload_title_picture_modal.html"));
?>
<div class="project-banner-backdrop">
	
</div>
<div class="project-banner" data-editable="<?=(int)!!$_DATA['editable']?>">
	<div class="project-banner-inner">
		<div class="project-banner-arrow-left"></div>
		<div class="project-banner-arrow-right"></div>
		<div class="project-banner-title">
			asdf
		</div>
		<div class="project-banner-edit">
			<a href="#" class="project-banner-edit-btn" data-toggle="modal" data-target="#upload_picture_modal"><span class="glyphicon glyphicon-edit"></span></a>
		</div>
		<div class="project-banner-info">
			<span class="project-banner-info-participator-count">&nbsp;</span><span class="glyphicon glyphicon-user"></span>
			<span class="project-banner-info-fav-count">&nbsp;</span><span class="project-fav glyphicon glyphicon-star" data-project-id=""></span>
		</div>
		<div class="project-banner-content">
			<ul class="project-banner-content-list">
				<?php foreach($_DATA["projects"] as $project){ ?>
					<?php $editable = (isset($project["editable"]) ? $project["editable"] : false) ?>
					<li class="project-banner-content-list-item" data-project-id="<?=$project['project_id']?>" data-project-title="<?=$project["title"]?>" data-fav-count="<?=$project["fav_count"]?>" data-participator-count="<?=$project["participator_count"]?>" data-editable="<?=(int)$editable?>">
						<a href="/project/show/<?=$project["project_id"]?>"><img src="<?=abspath($project["title_picture"]["file_path"])?>"></a>
					</li>
				<?php } ?>
			</ul>
		</div>
	</div>
</div>
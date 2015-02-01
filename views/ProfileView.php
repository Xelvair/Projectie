<?php
#PARAMETERS
#user : user
	#username
	#created_projects
	#project_participations
#tag_box
#projects_involved
#projects_created
global $locale;
?>
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
    <div class="col-md-4" style="padding-top: 10px;">
		<?=$_DATA['tag_box']?>
    </div>
</div><!--row-->
<hr style="box-shadow:  2px 2px 5px 0px rgba(50, 48, 50, 0.5);"/>
<div class="row">
    <div class="col-md-6 col-xs-12 content_list">
	<h3><?=$locale['projects_involved']?></h3><hr>
		<?php for($i = 0; $i < sizeof($_DATA["projects_involved"]); $i++){
				echo $_DATA["projects_involved"][$i];
		}  ?>
    </div><!--col-->
    <div class="col-md-6 col-xs-12 content_list">
	<h3><?=$locale['projects_created']?></h3><hr>
		<?php for($i = 0; $i < sizeof($_DATA["projects_created"]); $i++){
				echo $_DATA["projects_created"][$i];
		}  ?>
    </div><!--col-->
</div>

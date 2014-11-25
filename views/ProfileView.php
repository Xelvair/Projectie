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
                <ul>
                    <?php foreach($_DATA["skill"] as $entry){ ?>
                         <li><?=$entry?></li>
                    <?php }?>
                </ul>
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
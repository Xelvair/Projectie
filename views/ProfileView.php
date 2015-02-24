<?php
#PARAMETERS
#user : user
#viewer_can_edit : whether the viewer can edit the user

global $locale;

$user = $_DATA["user"];
?>

<div class="row row-eq-height profile-top-row">
  <div class="col-md-3" style="height: 200px">
    <img class="profile-picture" src="<?=abspath($user->getPicture()->file_path)?>">
  </div>
  <div class="col-md-4">
    <h3 class="user-name"><?=$user->username?></h3>
    <span class="user-info"><span class="user-info-title">User since:</span><span class="user-info-value"><?=date("j.n.Y", $user->create_time)?></span></span>
    <span class="user-info"><span class="user-info-title">Created Projects:</span><span class="user-info-value"><?=$user->getCreatedProjectsCount()?></span></span>
    <span class="user-info"><span class="user-info-title">Participated Projects:</span><span class="user-info-value"><?=$user->getParticipatedProjectsCount()?></span></span>
  </div>
  <div class="col-md-5">
    <div style="height: 180px;">
    <?=Core::view("UserTagBox", ["user" => $user, "editable" => $_DATA["viewer_can_edit"]])?>
    </div>
  </div>
</div>
<div class="row">
  <?php
    $projects_created = $user->getCreatedProjects();
    if(sizeof($projects_created) > 0){
  ?>
    <div class="col-md-12 content_list">
      <span class="list-headline"><?=$locale["projects_created"]?></span>
      <hr>
      <div class="row">
        <?php foreach($projects_created as $project_created){ ?>
          <div class="col-md-4">
            <?=Core::view("ProjectReview", [
              "project_id" => $project_created->project_id,
              "title" => $project_created->title,
              "subtitle" => $project_created->subtitle,
              "title_picture" => [
                "file_path" => $project_created->getTitlePicture()->file_path
              ],
              "participator_count" => $project_created->getMemberCount(),
              "fav_count" => $project_created->getFavCount()
            ])?>
          </div>
        <?php } ?>
      </div>
    </div>
  <?php } ?>
  <?php 
    $projects_involved = $user->getJoinedProjects();
    if(sizeof($projects_involved) > 0){ 
  ?>
    <div class="col-md-12 content_list">
      <span class="list-headline"><?=$locale["projects_involved"]?></span>
      <hr>
      <div class="row">
        <?php foreach($projects_involved as $project_involved){ ?>
          <div class="col-md-4">
            <?=Core::view("ProjectReview", [
              "project_id" => $project_involved->project_id,
              "title" => $project_involved->title,
              "subtitle" => $project_involved->subtitle,
              "title_picture" => [
                "file_path" => $project_involved->getTitlePicture()->file_path
              ],
              "participator_count" => $project_involved->getMemberCount(),
              "fav_count" => $project_involved->getFavCount()
            ])?>
          </div>
        <?php } ?>
      </div>
    </div>
  <?php } ?>
</div>
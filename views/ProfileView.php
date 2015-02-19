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
<?php

global $locale;

#PARAMETERS

#user : User obj of user that sent the request
#viewer_can_edit : whether the viewer can deny or accept the request

require_once(abspath_lcl("/templates/accept_participation_request_modal.html"));
require_once(abspath_lcl("/templates/deny_participation_request_modal.html"));

$user = $_DATA["user"];
?>

<div class="request-entry">
	<img src="<?=abspath($user->getPicture()->file_path)?>" class="pull-left media-object-small">
	<h1 class="user" user-id="<?=$user->user_id?>"><?=$user->username?></h1>
	<span class="request-entry-tagline">
		<?php 
			$first = true;
			foreach($user->getTags() as $tag){
				if(!$first){
					echo "● ";
				} else {
					$first = false;
				}
				echo "<span class='request-entry-tag'>".$tag->name."</span> ";
			} 
		?>
	</span>
	<?php if($_DATA["viewer_can_edit"]){ ?>
		<div class="request-entry-buttons">
			<a href="#" data-toggle="modal" data-target="#deny_participation_request_modal" data-participation-request-id="<?=$_DATA["participatioN_request_id"]?>" data-username="<?=$user->username?>">
				<div class="request-entry-btn deny">Deny</div>
			</a>
			<a href="#" data-toggle="modal" data-target="#accept_participation_request_modal" data-participation-request-id="<?=$_DATA["participatioN_request_id"]?>" data-username="<?=$user->username?>">
			<div class="request-entry-btn accept">Accept</div>
			</a>
		</div>
	<?php } ?>
</div>
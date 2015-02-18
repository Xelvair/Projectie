<?php

global $locale;

#PARAMETERS

#user : User obj of user that sent the request

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
</div>
<?php

global $locale;
?>


<div class="request-entry">
	<img src="<?=abspath("/public/images/default-profile-pic.png")?>" class="pull-left media-object-small">
	<h1 class="user" user-id="<?=$_DATA["user"]["user_id"]?>"><?=$_DATA["user"]["username"]?></h1>
	<span class="request-entry-tagline">
		<?php 
			$first = true;
			foreach($_DATA["user"]["tags"] as $tag){
				if(!$first){
					echo "● ";
				} else {
					$first = false;
				}
				echo "<span class='request-entry-tag'>".$tag["name"]."</span> ";
			} 
		?>
	</span>
</div>
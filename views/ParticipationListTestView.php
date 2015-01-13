<?php

require_once(abspath_lcl("/templates/position_remove_modal.html"));
require_once(abspath_lcl("/templates/position_leave_modal.html"));

$is_position_filled = !empty($_DATA["project_position"]["user_id"]);

# project_position : assoc array of project_position as in database
# project_position[user] : assoc array of user as in database
?>

<?php if($is_position_filled){ ?>
<div class='participation_entry active_participation'>
<?php } else { ?>
<div class='participation_entry open_participation'>
<?php } ?>
	<div><?=$_DATA["project_position"]["job_title"]?></div>
	<div>
		<div>
			<img class="media-object" src="/public/images/default-profile-pic.png"></div>
		<div>
            <?php if($is_position_filled){ ?>
                <h1><?=$_DATA["project_position"]["user"]["username"]?></h1>
                <h2>Participator since <?=date("j.n.Y", $_DATA["project_position"]["participator_since"])?></h2>
            <?php } else {?>
                <h1>Open Position</h1>
                <h2></h2>
            <?php } ?>
			<div>
				<div class="rights"><span class="glyphicon glyphicon-ok"></span> Rights<span class="caret"></span></div>
				<a href="#" data-toggle="modal" data-target="#position_remove_modal" 
                <?php if($is_position_filled){ ?>
                    data-username="<?=$_DATA["project_position"]["user"]["username"]?>"
                <?php } ?>
                >
					<div class="kick"><span class="glyphicon glyphicon-remove"></span> Kick</div>
				</a>
				<a href="#" data-toggle="modal" data-target="#position_leave_modal">
					<div class="leave"><span class="glyphicon glyphicon-remove"></span> Leave</div>
				</a>
			</div>
		</div>
	</div>
	<div class="participation_entry_rights_rolldown">
		<hr>
		fawfawfawf
	</div>
</div>
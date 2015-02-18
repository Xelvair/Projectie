<?php

require_once(abspath_lcl("/templates/position_remove_modal.html"));
require_once(abspath_lcl("/templates/position_leave_modal.html"));
require_once(abspath_lcl("/templates/position_kick_modal.html"));
require_once(abspath_lcl("/templates/position_participate_modal.html"));

$is_position_filled = !empty($_DATA["project_position"]["user_id"]);

$user = $_DATA["project_position"]["user"];

# project_position : assoc array of project_position as in database
# project_position[user] : assoc array of user as in database
# flags : flags for functions
?>

<?php if($is_position_filled){ ?>
<div class='participation_entry active_participation'>
<?php } else { ?>
<div class='participation_entry open_participation'>
<?php } ?>
	<div><?=$_DATA["project_position"]["job_title"]?></div>
	<div>
		<div>
			<img class="media-object" src="<?=$user ? abspath($user->getPicture()->file_path) : abspath("/public/images/default-profile-pic.png")?>"></div>
		<div>
            <?php if($is_position_filled){ ?>
                <h1><?=$user->username?></h1>
                <h2>Participator since <?=date("j.n.Y", $_DATA["project_position"]["participator_since"])?></h2>
            <?php } else {?>
                <h1>Open Position</h1>
                <h2></h2>
            <?php } ?>
			<div>
                <?php if(array_search("RIGHTS", $_DATA["flags"]) !== false){ ?>
				    <div class="rights"><span class="glyphicon glyphicon-ok"></span> Rights<span class="caret"></span></div>
				<?php } ?>

                <?php if(array_search("KICK", $_DATA["flags"]) !== false){ ?>
                    <a href="#" data-toggle="modal" data-target="#position_kick_modal" 
                        data-username="<?=$user->username?>" 
                        data-project-position-id="<?=$_DATA["project_position"]["project_position_id"]?>"
                    >
    					<div class="kick"><span class="glyphicon glyphicon-remove"></span> Kick</div>
    				</a>
                <?php } ?>

                <?php if(array_search("REMOVE", $_DATA["flags"]) !== false){ ?>
                    <a href="#" data-toggle="modal" data-target="#position_remove_modal" 
                       data-position-title="<?=$_DATA["project_position"]["job_title"]?>" 
                       data-project-position-id="<?=$_DATA["project_position"]["project_position_id"]?>"
                    >
                        <div class="remove"><span class="glyphicon glyphicon-remove"></span> Remove</div>
                    </a>
                <?php } ?>

                <?php if(array_search("LEAVE", $_DATA["flags"]) !== false){ ?>
    				<a href="#" data-toggle="modal" data-target="#position_leave_modal" data-project-position-id="<?=$_DATA["project_position"]["project_position_id"]?>">
    					<div class="leave"><span class="glyphicon glyphicon-remove"></span> Leave</div>
    				</a>
                <?php } ?>

                <?php if(array_search("PARTICIPATE", $_DATA["flags"]) !== false){ ?>
                    <a href="#" data-toggle="modal" data-target="#position_participate_modal" 
                    data-project-position-id="<?=$_DATA["project_position"]["project_position_id"]?>" 
                    data-project-title="<?=$_DATA["project_position"]["project"]["title"]?>"
                    >
                        <div class="participate"><span class="glyphicon glyphicon-ok"></span> Participate</div>
                    </a>
                <?php } ?>
                <?php if(array_search("CANCEL_REQUEST", $_DATA["flags"]) !== false){ ?>
                        <div class="cancel-request"><span class="glyphicon glyphicon-ok"></span> Participation Requested</div>
                <?php } ?>
			</div>
		</div>
	</div>
	<div class="participation_entry_rights_rolldown">
		<hr>
		<?=print_r($_DATA["flags"], true)?>
	</div>
</div>
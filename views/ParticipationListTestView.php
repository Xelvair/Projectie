<?php
$id_removemodal = rand();
$id_leavemodal = rand();

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
				<a href="#" data-toggle="modal" data-target="#modal<?=$id_removemodal?>">
					<div class="kick"><span class="glyphicon glyphicon-remove"></span> Kick</div>
				</a>
				<a href="#" data-toggle="modal" data-target="#modal<?=$id_leavemodal?>">
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

<div id="modal<?=$id_removemodal?>" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">Are you sure?</h4>
            </div>
            <div class="modal-body">
                <h3>Do you really want to remove <?=$_DATA["project_position"]["user"]["username"]?> from the Project?</h3>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger">Remove</button>
        </div>
    </div>
  </div>
</div>

<div id="modal<?=$id_leavemodal?>" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">Are you sure?</h4>
            </div>
            <div class="modal-body">
                <h3>Do you really want to leave the Project?</h3>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger">Leave</button>
        </div>
    </div>
  </div>
</div>
<?php
$rnd = rand();
?>

<div class='participation_entry active_participation'>
	<div>Programmer</div>
	<div>
		<div>
			<img class="media-object" src="/public/images/default-profile-pic.png"></div>
		<div>
			<h1>Max Staats</h1>
			<h2>Participator since 7.1.2015</h2>
			<div>
				<div class="rights"><span class="glyphicon glyphicon-ok"></span> Rights<span class="caret"></span></div>
				<a href="#" data-toggle="modal" data-target="#modal<?=$rnd?>">
					<div class="kick"><span class="glyphicon glyphicon-remove"></span> Kick</div>
				</a>
			</div>
		</div>
	</div>
	<div class="participation_entry_rights_rolldown">
		<hr>
		fawfawfawf
	</div>
</div>

<div id="modal<?=$rnd?>" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">Are you sure?</h4>
            </div>
            <div class="modal-body">
                <h3>Do you really want to remove Max Staats from the Project?</h3>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger">Remove</button>
        </div>
    </div>
  </div>
</div>
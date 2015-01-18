<?php
#PARAMETERS
# member_list : html of list of members
# can_add_position : whether we should show the "Add new Position" button
# project_id : id of the project

global $locale;
?>

<div>
<?=$_DATA["member_list"]?>
</div>

<?php if($_DATA["can_add_position"]){ ?>
<hr>
<button type="button" class="btn btn-default center-block" data-toggle="modal" data-target="#add_position_modal"><?=$locale["add_new_position"]?>...</button>

<div id="add_position_modal" class="modal fade" tabindex="-1" role="dialog" data-project-id="<?=$_DATA["project_id"]?>">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel"><?=$locale["add_new_position"]?></h4>
            </div>
            <div class="modal-body">
                <h3><?=$locale["enter_new_position_title"]?></h3>
                <input type="text" autocomplete="off" class="form-control new-position-title-input">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" data-dismiss="modal"><?=$locale["cancel"]?></button>
                <button type="button" class="btn btn-default confirm"><?=$locale["add_new_position"]?></button>
        </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function(){
	$("#add_position_modal").on("click", ".confirm", function(event){
		var project_id = $(event.delegateTarget).attr("data-project-id");
		var new_position_title = $(event.delegateTarget).find(".new-position-title-input").val();

		Projectie.Project.addPosition(project_id, new_position_title);
        location.reload();
	});
});

</script>
<?php } ?>
<?php
#Parameters
#title
#desc
#thumb
#time
#creator
	#name
	#id
 ?>
<div class="media">
	<a class="pull-left" href="#">
		<img class="media-object img-responsive" src="<?=$_DATA['thumb'];?>" alt="...">
	</a> 
	<div class="media-body">
        <h4 class="media-heading"><?=$_DATA['title'];?></h4>
        <?=$_DATA['desc'];?>
    </div>
	<div class="media-footer">
		<small class="media-time-container text-muted pull-right"><span class="glyphicon glyphicon-time"></span><span class="chat-msg-time"><?=$_DATA['time']?></span></small>
		<?php if(isset($_DATA['creator']['id']) && $_DATA['creator']['id'] != "")
			{ ?> <span class="user media-source" user-id="<?=$_DATA['creator']['id']?>"><small class="source-container text-muted"><?=$_DATA['creator']['name']?></small></span>
			<?php
			} ?>
	</div>
</div><hr/><!--media-->
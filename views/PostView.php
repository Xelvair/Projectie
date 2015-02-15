<?php
#PARAMETERS
#
#project : Project were its from /NOT NECESSARY
	#id : id of project
	#title : title of project
#creator : creator of post
	#id
	#name
#time : time created
#title : title of post  /NOT NECESSARY
#content : post content

$post = $_DATA["post"];
?>
<div class="post">
	<a href="#"><span class="glyphicon glyphicon-remove pull-right remove_btn"></span></a>
	<div class="post-head">
		<a class="pull-left" href="#">
			<img class="media-object" src="<?=abspath('/public/images/default-profile-pic.png')?>">
		</a>
		<?php if(isset($post->project_id)){ ?>
			<a href="<?=abspath("/project/show/").$post->getAuthor()->user_id?>">
				<h3><?=$post->getProject()->title?></h3>
			</a>
			<h3>
				<small>
					<a href="<?=abspath('/profile/show/').$post->getAuthor()->user_id?>">
						<span class="user" user-id="<?=$post->getAuthor()->user_id?>"><?=$post->getAuthor()->username?></span>
					</a>
					<span><?=date("d.m.Y\ H:i",$post->post_time)?></span>
				</small>
			</h3>
		<?php }else{?>
			<a href="<?=abspath("/profile/show/").$post->getAuthor()->user_id?>">
				<h3><span class="user" user-id="<?=$post->getAuthor()->user_id?>"><?=$post->getAuthor()->username?></span></h3>
			</a>
			<h3><small><span><?=date("d.m.Y\ H:i",$post->post_time)?></span></small></h3>
		<?php } ?>
	</div>
	<div class="post-body">
		<?php if(isset($post->title) && $post->title != ""){ ?>
			<h4><?=$post->title?></h4>
		<?php } ?>
		<p><?=$post->content?></p>
	</div>
</div>
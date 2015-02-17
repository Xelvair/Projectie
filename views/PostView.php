<?php
#PARAMETERS
#
#post : ProjectNews object of the news post
#user_is_author : whether the user can edit/delete the news post
#show_project_title : whether the project title should be shown

$post = $_DATA["post"];
?>
<div class="post" id="post_id_<?=$post->project_news_id?>">
	<?php if($_DATA["user_is_author"]){ ?>
		<span class="glyphicon glyphicon-remove pull-right remove_btn" onclick="remove_news(<?=$post->project_news_id?>);"></span>
	<?php } ?>
	<div class="post-head">
		<a class="pull-left" href="#">
			<img class="media-object" src="<?=abspath('/public/images/default-profile-pic.png')?>">
		</a>
		<?php if(isset($post->project_id) && $_DATA["show_project_title"]){ ?>
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
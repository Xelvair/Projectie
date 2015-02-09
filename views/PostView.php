<?php
#PARAMETERS
#
#project : Project were its from /NOT NECESSERY
	#id : id of project
	#title : title of project
#creator : creator of post
	#id
	#name
#time : time created
#title : title of post  /NOT NECESSERY
#content : post content

 foreach($_DATA['post'] as $post){ ?>

	<div class="post">
		<a href="#"><span class="glyphicon glyphicon-remove pull-right remove_btn"></span></a>
		<div class="post-head">
			<a class="pull-left" href="#">
				<img class="media-object" src="<?=abspath('/public/images/default-profile-pic.png')?>">
			</a>
			<?php if(isset($post["project"])){ ?>
				<a href="<?=abspath("/project/show/").$post["project"]["id"]?>">
					<h3><?=$post["project"]["title"]?></h3>
				</a>
				<h3>
					<small>
						<a href="<?=abspath('/profile/show/').$post['creator']['id']?>">
							<span class="user" user-id="<?=$post["creator"]["id"]?>"><?=$post["creator"]["name"]?></span>
						</a>
						<span><?=date("d.m.Y\ H:i",$post["time"])?></span>
					</small>
				</h3>
			<?php }else{?>
				<a href="<?=abspath("/profile/show/").$post["creator"]["id"]?>">
					<h3><span class="user" user-id="<?=$post["creator"]["id"]?>"><?=$post["creator"]["name"]?></span></h3>
				</a>
				<h3><small><span><?=date("d.m.Y\ H:i",$post["time"])?></span></small></h3>
			<?php } ?>
		</div>
		<div class="post-body">
			<?php if(isset($post["title"]) && $post["title"] != ""){ ?>
				<h4><?=$post["title"]?></h4>
			<?php } ?>
			<p><?=$post["content"]?></p>
		</div>
	</div>

<?php } ?>
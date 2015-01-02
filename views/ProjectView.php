<?php
#PARAMETERS
#user_review
#project
	#participators
		#id
		#username
	#desc
	#subtitle
	#title
	#header
	#fav_count
	#time
#news_feed
#footer

global $locale;
?>

<script>
$(document).ready(function(){
	
	$('#btn_add_fav').on('click', function(){
		if($(this).hasClass('fav_btn_selected')){
			$(this).removeClass('fav_btn_selected');
		}else{
			$(this).addClass('fav_btn_selected');
		}
	});
	
	$('#btn_upvote').on('click', function(){
		$('#btn_downvote').removeClass('downvote_selected');
		if($(this).hasClass('upvote_selected')){
			$(this).removeClass('upvote_selected');
		}else{
			$(this).addClass('upvote_selected');
		}	
	});
	
	$('#btn_downvote').on('click', function(){
		$('#btn_upvote').removeClass('upvote_selected');
		if($(this).hasClass('downvote_selected')){
			$(this).removeClass('downvote_selected');
		}else{
			$(this).addClass('downvote_selected');
		}	
	});
});
</script>
<div class="row" id="banner_wrap">
        <img class="img-responsive banner" src="<?=$_DATA['project']['header']?>"/>
		<div id="project-title">
			<?=$_DATA['project']['title']?>
		</div>
</div>
<div class="row" style="margin-top: 15px; margin-bottom: 20px;">
	<div class="col-md-2">
		<ul class="list-group">
		<?php foreach ($_DATA["project"]["participators"] as $participators){ ?>
			<li class="user list-group-item" user-id="<?=$participators['id']?>"><?=$participators['username']?></li>
		<?php }?>
		</ul>
	</div>
	<div class="col-md-10">
		<div class="row">
			<div class="tabpanel">
					<ul class="nav nav-tabs">
						<li role="presentation" class="active"><a href="#home" id="home-btn" aria-controls="home" role="tab" data-toggle="tab">Home</a></li>
						<li role="presentation" ><a href="#conversations" id="conversation-btn" aria-controls="conversations" role="tab" data-toggle="tab"><?=$locale['conversations']?></a></li>
					</ul>
					<div class="tab-content project-desc-tab">
						<div role="tabpanel" class="tab-pane fade in active" id="home">
							<div class="row">
								<div class="col-md-6">
									<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
										<div class="panel panel-default">
											<div class="panel-heading" role="tab" id="headingOne">
												<h4 class="panel-title">
													<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
													<?=$locale['project_desc']?>
													</a>
												</h4>
											</div>
											<div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
												<div class="panel-body">
													<?=$_DATA['project']['desc']?>
												</div>
											</div>
										</div>
									</div>
								</div><!--COl END-->
								<div class="col-md-6">
									<div class="project-property">
										<div class="property-title property-item"><?=$locale['favorites']?>:</div><div class="property-item"><?=$_DATA['project']['fav_count']?></div>
									</div>
									<div class="project-property">
										<div class="property-title property-item"><?=$locale['created_on']?>:</div><div class="property-item"><?=$_DATA['project']['time']?></div>
									</div>
									<div class="project-property">
										<div class="property-title property-item">Voting:</div>
										<div class="property-item">
											<div class="progress">
												<div class="progress-bar progress-bar-danger" role="progressbar" style="width: 30%;" id="down_vote_bar">
												30%
												</div>
											  
												<div class="progress-bar progress-bar-success" role="progressbar" style="width: 70%;" id="up_vote_bar">
												70%
												</div>
											</div>
										</div>
									</div>
									<div class="row project-property">
										<div class="col-xs-6">
											<div class="btn-group" role="group" aria-label="...">
											  <button type="button" class="btn btn-vote" id="btn_upvote"><span class="glyphicon glyphicon-chevron-up"></span></button>
											  <button type="button" class="btn btn-vote" id="btn_downvote"><span class="glyphicon glyphicon-chevron-down"></span></button>
											</div>
										</div>
										<div class="col-xs-6">
											 <button type="button" class="btn btn-vote" id="btn_add_fav"><span class="glyphicon glyphicon-star"></span></button>
										</div>
									</div>
								</div><!-- COL END-->
							</div><!--Row End-->
							<div class="row" style="margin-top: 20px;">
								<div class="col-md-12">
									<form>
									  <div class="input-group">
										<input type="text" class="form-control custom-control no_right_border" placeholder="Post title..." id="post_title"/>
										<textarea class="form-control custom-control no_right_border" placeholder="<?=$locale['write_something']?>"rows="3" style="resize:none" id="post_input"></textarea>     
											<span class="input-group-addon btn btn-default" style="border-style: none;" id="post_btn">Post</span>
										</div>
									</form>
									<?=$_DATA['news_feed']?>
								</div>
							</div>
						</div>
						<div role="tabpanel" class="tab-pane fade" id="conversations">
						<div class="row" id="chat-row">
								<div id="chat-wrapper" class="col-md-12 chat-col pull-left">
									<div id="chat-head" class="text-center">
										<div class="row">
											<div class="col-xs-1">
												<h2><span class="glyphicon glyphicon-comment"></span></h2>
											</div>
											<div class="col-xs-10">
												<h2 id="chat-title">
												 <?=$_DATA['project']['title']?>
												</h2>
											</div>f
										</div>
									</div>
									<div id="chat-box">
										<ul id="chat">
									  </ul>
									</div>
									<div id="chat-footer">
										<form>
										  <div class="input-group">
											<textarea class="form-control custom-control" placeholder="<?=$locale['write_something']?>"rows="3" style="resize:none" id="chat_input"></textarea>     
										   <span class="input-group-addon btn btn-default" id="chat_send"><?=$locale['send']?></span>
											</div>
									  </form>
									</div>	
								</div>
							</div>
						</div><!--Conversation tab End-->
					</div><!--Tab content END-->
			</div><!--TABPanel END-->
		</div><!--Row END-->
	</div>
</div>

<?=$_DATA['user_review']?>

<?=$_DATA['footer']?>
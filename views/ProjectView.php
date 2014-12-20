<?php
#PARAMETERS
#news_feed
#project_title

global $locale;
?>

<script>
$(document).ready(function(){
	
	$('#fav_btn').on('click', function(){
		
		
	});
});
</script>
<div class="row" id="banner_wrap">
        <img class="img-responsive banner" src="../public/images/default-banner.png"/>
		<div id="project-title">
			<?=$_DATA['project_title']?>
		</div>
</div>
<div class="row" style="margin-top: 15px; margin-bottom: 20px;">
	<div class="col-md-2">
		<ul class="list-group">	
			<li class="user list-group-item" user-id="1">admin</li>
			<li class="user list-group-item" user-id="2">mitarbeiter</li>
		</ul>
	</div>
	<div class="col-md-10">
		<div class="row">
			<div class="tabpanel">
					<ul class="nav nav-tabs">
						<li role="presentation" class="active"><a href="#home" id="home-btn" aria-controls="home" role="tab" data-toggle="tab">Home</a></li>
						<li role="presentation" ><a href="#conversations" id="conversation-btn" aria-controls="conversations" role="tab" data-toggle="tab">Conversations</a></li>
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
													Project Description
													</a>
												</h4>
											</div>
											<div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
												<div class="panel-body">
													seas des is des beste PRoject auf projectie und projectie is de beste seitn euw
												</div>
											</div>
										</div>
									</div>
								</div><!--COl END-->
								<div class="col-md-6">
									<div class="project-property">
										<div class="property-title property-item">Favs:</div><div class="property-item">340</div><div class="fav_btn_container"><button id="fav_btn"><span class="glyphicon glyphicon-star" id="fav-star"></span></button></div>
									</div>
									<div class="project-property">
										<div class="property-title property-item">Created on:</div><div class="property-item">23.11.2014</div>
									</div>
								</div><!-- COL END-->
							</div><!--Row End-->
							<div class="row" style="margin-top: 20px;">
								<div class="col-md-12">
									<form>
									  <div class="input-group">
										<textarea class="form-control custom-control" placeholder="Write something..."rows="3" style="resize:none" id="post_input"></textarea>     
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
												 <?=$_DATA['project_title']?>
												</h2>
											</div>
										</div>
									</div>
									<div id="chat-box">
										<ul id="chat">
									  </ul>
									</div>
									<div id="chat-footer">
										<form>
										  <div class="input-group">
											<textarea class="form-control custom-control" placeholder="Write something..."rows="3" style="resize:none" id="chat_input"></textarea>     
										   <span class="input-group-addon btn btn-default" id="chat_send">Send</span>
											</div>
									  </form>
									</div>	
								</div>
	
							</div>
							Turn down for WHAT!
						</div><!--Conversation tab End-->
					</div><!--Tab content END-->
			</div><!--TABPanel END-->
		</div><!--Row END-->
	</div>
</div>

<?=$_DATA['user_review']?>

<?=$_DATA['footer']?>
<?php
#PARAMETERS
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
#tag_box

global $locale;
?>

<script>
var desc_click = 0;

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
	
	$('#desc_wrap').on('click', function(){
		if(desc_click == 0){
			desc_click = 1;
			var desc = $(this).text();
			$(this).hide();
			$('#desc_update_wrap').show();
			$('#desc_area').val(desc);
		}

	});
	
	
});

function update_desc(){
	var desc = $('#desc_area').val();
	
	/*UPDATE DESCRIPTION PHP PLACEHOLDER*/
	
	exit_desc(desc);
}

function exit_desc(desc){

	$('#desc_area').val('');
	$('#desc_update_wrap').hide();
	$('#desc_wrap').show();
	desc_click = 0;
	
	if(desc != null){
		$('#desc_wrap').text(desc);
	}
}
</script>
<div class="row" id="banner_wrap">
        <img class="img-responsive banner" src="<?=$_DATA['project']['header']?>"/>
		<div id="project-title">
			<?=$_DATA['project']['title']?>
		</div>
</div>
<div class="row" style="margin-top: 15px; margin-bottom: 20px;">
	<div class="col-md-12">
		<div class="row">
			<div class="tabpanel">
					<ul class="nav nav-tabs">
						<li role="presentation" class="active"><a href="#home" id="home-btn" aria-controls="home" role="tab" data-toggle="tab">Home</a></li>
						<li role="presentation" ><a href="#conversations" id="conversation-btn" aria-controls="conversations" role="tab" data-toggle="tab"><?=$locale['conversations']?></a></li>
						<li role="presentation" ><a href="#members" id="member-btn" aria-controls="members" role="tab" data-toggle="tab"><?=$locale['members']?></a></li>
					</ul>
					<div class="tab-content project-desc-tab">
						<div role="tabpanel" class="tab-pane fade in active" id="home">
							<div class="row">
								<div class="col-md-8">
									<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
										<div class="panel panel-default">
											<div class="panel-heading" role="tab" id="headingOne">
												
													<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
													<h4 class="panel-title">
													<?=$locale['project_desc']?>
													</h4>
													</a>
												
											</div>
											<div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
												<div class="panel-body" id="desc_panel">
													<div id="desc_wrap"><?=$_DATA['project']['desc']?> ölaskdfölk asöldfk öalsmfieijf asldkfleinf alsdkfef asijfd efojasdf epfojas dfpoka ölaskdfölk asöldfk öalsmfieijf asldkfleinf alsdkfef asijfd efojasdf epfojas dfpoka ölaskdfölk asöldfk öalsmfieijf asldkfleinf alsdkfef asijfd efojasdf epfojas dfpoka ölaskdfölk asöldfk öalsmfieijf asldkfleinf alsdkfef asijfd efojasdf epfojas dfpoka ölaskdfölk asöldfk öalsmfieijf asldkfleinf alsdkfef asijfd efojasdf epfojas dfpoka ölaskdfölk asöldfk öalsmfieijf asldkfleinf alsdkfef asijfd efojasdf epfojas dfpoka ölaskdfölk asöldfk öalsmfieijf asldkfleinf alsdkfef asijfd efojasdf epfojas dfpoka</div>
													<div id="desc_update_wrap" style="display: none;">
														<textarea name="desc" rows="6" class="form-control" id="desc_area" style="margin-bottom: 15px;"></textarea>
														<button onclick="update_desc()" class="btn btn-default pull-left"><?=$locale['update']?></button>
														<button onclick="exit_desc();" class="btn pull-right"><?=$locale['close']?></button>
													</div>
												</div>
											</div>
										</div>
										<div class="panel-heading tag-heading" role="tab" id="headingTwo">
											<a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
												<h4 class="panel-title">
													<?=$locale['tags']?>
												</h4>
											</a>
										</div>
										<div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
											<div class="panel-body" id="tag_panel">
												<?=$_DATA['tag_box']?>
											</div>
										</div>
									</div>
								</div><!--COl END-->
								<div class="col-md-4">
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
										<input type="text" class="form-control custom-control no_right_border" placeholder="<?=$locale['post_title']?>..." id="post_title"/>
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
											<textarea class="form-control custom-control" placeholder="<?=$locale['write_something']?>"rows="3" style="resize:none" id="chat_input"></textarea>     
										   <span class="input-group-addon btn btn-default" id="chat_send"><?=$locale['send']?></span>
											</div>
									  </form>
									</div>	
								</div>
							</div>
						</div><!--Conversation tab End-->
						<div role="tabpanel" class="tab-pane fade" id="members">
						<?=$_DATA["project"]["member_list"]?>
						</div>
					</div><!--Tab content END-->
			</div><!--TABPanel END-->
		</div><!--Row END-->
	</div>
</div>
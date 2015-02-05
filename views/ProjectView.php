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

$show_requests = isset($_DATA["project"]["panels"]["requests_panel"]["viewable"]) && $_DATA["project"]["panels"]["requests_panel"]["viewable"];
$show_private_conversation = isset($_DATA["project"]["panels"]["private_conversation_panel"]["viewable"]) && $_DATA["project"]["panels"]["private_conversation_panel"]["viewable"];
$show_public_conversation = isset($_DATA["project"]["panels"]["public_conversation_panel"]["viewable"]) && $_DATA["project"]["panels"]["public_conversation_panel"]["viewable"];
$show_members = isset($_DATA["project"]["panels"]["members_panel"]["viewable"]) && $_DATA["project"]["panels"]["members_panel"]["viewable"];
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
	
	$('#desc_area').keypress(function(){
		$(this).parent().removeClass('has-error');
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
	
	$('#post_btn').on('click', function(){
		post();
	});
	
	
});

function post(){
	var title = $('#post_title').val();
	var content = $('#post_content').val();
	
	if(content != ""){
		$('#post_title').val('');
		$('#post_content').val('');
		$.post( "<?=abspath('/project/post_news')?>",{
			title: title,
			content: content,
			project_id : <?=$_DATA["project"]["project_id"]?>
			}).done(function(data){
				var result = JSON.parse(data);
				var title_result = "is ka titel";
				var content_result	= result.content;
				var post_id_result = result.project_news_id;
				var time_result = result.post_time;
				
				//alert(title_result + "|" +content_result+ "|" +post_id_result+ "|" +time_result );
				
				$.post("<?=abspath('/project/post_html')?>",{
					title : title_result,
					content : content_result,
					time : time_result,
					post_id : post_id_result }).done(function(data){
						//alert(data);
						$('.post').before(data);
					});
				
			});
	}
}

function update_desc(){
	var desc = $('#desc_area').val();
	if(desc != ""){
		/*UPDATE DESCRIPTION PHP PLACEHOLDER*/	
		exit_desc(desc);
	}else{
		$('#desc_update_wrap').addClass('has-error');
	}
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
<?php
echo Core::view("ProjectBanner", [
	"projects" => [
		[
			"project_id" => $_DATA["project"]["project_id"],
			"fav_count" => $_DATA["project"]["fav_count"],
			"participator_count" => sizeof($_DATA["project"]["participators"])
		]
	]
]);
?>
<div class="row" style="margin-bottom: 20px;">
	<div class="col-md-12 content-col">
		<div class="row">
			<div class="tabpanel">
					<ul class="nav nav-tabs">
						<li role="presentation" class="active"><a href="#home" id="home-btn" aria-controls="home" role="tab" data-toggle="tab">Home</a></li>

						<?php if($show_public_conversation){ ?>
						<li role="presentation" ><a href="#public_conversation_panel" id="public-conversation-btn" aria-controls="public_conversation_panel" role="tab" data-toggle="tab"><?=$locale['public_conversation']?></a></li>
						<?php } ?>

						<?php if($show_private_conversation){ ?>
							<li role="presentation" ><a href="#private_conversation_panel" id="conversation-btn" aria-controls="private_conversation_panel" role="tab" data-toggle="tab"><?=$locale['private_conversation']?></a></li>
						<?php } ?>

						<?php if($show_members){ ?>
						<li role="presentation" ><a href="#members_panel" id="member-btn" aria-controls="members_panel" role="tab" data-toggle="tab"><?=$locale['members']?></a></li>
						<?php } ?>

						<?php if($show_requests){ ?>
							<li role="presentation" ><a href="#requests_panel" id="request-btn" aria-controls="requests_panel" role="tab" data-toggle="tab"><?=$locale['requests']?></a></li>
						<?php } ?>

					</ul>
					<div class="tab-content project-desc-tab">
						<div role="tabpanel" class="tab-pane fade in active" id="home">
							<div class="row project-heading">
								<div class="col-md-7">
									<h1 class="project-title"><?=$_DATA["project"]["title"]?></h1>
									<div class="panel-body" id="desc_panel">
										<div id="desc_wrap"><?=$_DATA['project']['description']?></div>
										<div id="desc_update_wrap" class="form-group" style="display: none;">
											<textarea name="desc" rows="6" class="form-control" id="desc_area" style="margin-bottom: 15px;"></textarea>
											<button onclick="update_desc()" class="btn btn-default pull-left"><?=$locale['update']?></button>
											<button onclick="exit_desc();" class="btn pull-right"><?=$locale['close']?></button>
										</div>
									</div>
								</div><!--COl END-->
								<div class="col-md-5 stretch-vertical">
									<?=Core::view("TagBoxTest", ["project_id" => $_DATA["project"]["project_id"], "editable" => $_DATA["user_can_edit"]]);?>
								</div><!-- COL END-->
							</div><!--Row End-->
							<div class="row" style="margin-top: 20px;">
								<div class="col-md-12">
									<form>
										<div class="input-group post_group">
											<input type="text" class="" placeholder="<?=$locale['post_title']?>..." id="post_title"/>
											<textarea class="" placeholder="<?=$locale['write_something']?>"rows="3" style="resize:none" id="post_content"></textarea>
											<span class="btn btn-default pull-right" style="border-style: none;" id="post_btn">Post</span>
										</div>
									</form>
								</div>
							</div>
							<div class="row" style="margin-top: 20px;">
								<div class="col-md-12">
									<h2><?=$locale['news_feed']?></h2>
									<?=$_DATA['news_feed']?>
								</div>
							</div>
						</div>

						<?php if($show_public_conversation){ ?>
							<div role="tabpanel" class="tab-pane fade" id="public_conversation_panel">
							<?=$_DATA["project"]["panels"]["public_conversation_panel"]["content"]?>
							</div><!--Conversation tab End-->
						<?php } ?>

						<?php if($show_members){ ?>
							<div role="tabpanel" class="tab-pane fade" id="members_panel">
							<?=$_DATA["project"]["panels"]["members_panel"]["content"]?>
							</div>
						<?php } ?>

						<?php if($show_private_conversation){ ?>
							<div role="tabpanel" class="tab-pane fade" id="private_conversation_panel">
							<?=$_DATA["project"]["panels"]["private_conversation_panel"]["content"]?>
							</div>
						<?php } ?>

						<?php if($show_requests){ ?>
							<div role="tabpanel" class="tab-pane fade" id="requests_panel">
							<?=$_DATA["project"]["panels"]["requests_panel"]["content"]?>
							</div>
						<?php } ?>
					</div><!--Tab content END-->
			</div><!--TABPanel END-->
		</div><!--Row END-->
	</div>
</div>
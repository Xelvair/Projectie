<script>

$(document).ready(function(){

	$('#home-btn').on('click', function(){

		$('#home').tab('show');

	});
	
	$('#conversation-btn').on('click', function(){
	

	});
});
</script>
<div class="row" id="banner_wrap">
        <img class="img-responsive banner" src="../public/images/default-banner.png"/>
</div>
<div class="row" style="margin-top: 15px;">
	<div class="col-md-2 .hidden-xs">
		<ul>	
			<li>boss: is boss</li>
			<li>mitarbeiter</li>
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
									<div class="property-title property-item">Favs:</div><div class="property-item">340</div>
								</div>
								<div class="project-property">
									<div class="property-title property-item">Created on:</div><div class="property-item">23.11.2014</div>
								</div>
							</div><!-- COL END-->
						</div>
						<div role="tabpanel" class="tab-pane fade" id="conversations">
							Turn down for WHAT!
						</div>
					</div><!--Tab content END-->
			</div><!--TABPanel END-->
		</div><!--Row END-->
	</div>
</div>

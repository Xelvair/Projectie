<?php
global $locale;
?>

<div class="row">
	<div class="col-md-12 page-header text-center">
		<h1><?=$locale["about_us"]?></h1>
	</div>
</div>
<div class="row">
	<div class="col-md-12 content_list">
		<span class="list-headline"><span class="glyphicon glyphicon-home"></span><?=$locale["contact"]?></span>
        <hr>
		<table class="table">
			<tr>
				<th>
					<?=$locale["adress"]?>:
				</th>
				<td>©Projectie GmbH<br>
					Zernattostraße 2<br>
					9800 Spittal an der Drau<br>
					Austria
				</td>
			</tr>
			<tr>
				<th>
					E-mail:
				</th>
				<td>
					office@projectie.com
				</td>
			</tr>
		</table>
	</div>
</div>
<div class="row">
	<div class="col-md-12 content_list">
		<span class="list-headline"><span class="glyphicon glyphicon-user"></span>Team</span>
		<hr>
		<div class="row">
			<div class="col-md-6">
				<div class="row" style="margin-top: 20px;">
					<div class="col-xs-6">
						<img src="<?=abspath('public/images/marvin.jpg')?>" style="width: 250px;" class="img img-responsive">
					</div>
					<div class="col-xs-6">
						<h2>Marvin Doerr</h2>
						<h2><small><?=$locale['project_leader']?>, <?=$locale['programming']?>, <?=$locale['web_design']?></small><h2>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="row" style="margin-top: 20px;">
					<div class="col-xs-6">
						<img src="<?=abspath('public/images/max.jpg')?>" style="width: 250px;" class="img img-responsive">
					</div>
					<div class="col-xs-6">
						<h2>Max Staats</h2>
						<h2><small><?=$locale['programming']?>, <?=$locale['web_design']?></small><h2>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6">
				<div class="row" style="margin-top: 20px;">
					<div class="col-xs-6">
						<img src="<?=abspath('public/images/mario.jpg')?>" style="width: 250px;" class="img img-responsive">
					</div>
					<div class="col-xs-6">
						<h2>Mario Buchacher</h2>
						<h2><small><?=$locale['manuel']?>, <?=$locale['video_editing']?>,  <?=$locale['presentation']?></small><h2>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="row" style="margin-top: 20px;">
					<div class="col-xs-6">
						<img src="<?=abspath('public/images/heimo.jpg')?>" style="width: 250px;" class="img img-responsive">
					</div>
					<div class="col-xs-6">
						<h2>Heimo Aschbacher</h2>
						<h2><small><?=$locale['manuel']?>, <?=$locale['video_editing']?>,  <?=$locale['presentation']?></small><h2>
					</div>
				</div>
			</div>
		</div>
		
	</div>
</div>

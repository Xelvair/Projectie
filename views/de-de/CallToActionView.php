<div class="row cta">
	<div class="col-md-6">
		<h3 class="cta-heading">Sie haben eine Vision für ein Projekt?</h1>
		<p class="cta-text">
			Suchen Sie jemanden mit Marketingerfahrung?<br>
			Oder einfach nach Leuten wie Ihnen?<br>
			Vielleicht einen Programmierer?<br>
			Oder einen Designer?<br>
		</p>
	</div>
	<div class="col-md-6">
		<?php if($_DATA["user"]){ ?>
			<a href="<?=abspath("/project/createnew")?>">
		<?php } else { ?>
			<a href="#loginModal" role="button" data-toggle="modal">
		<?php } ?>
			<div class="cta-btn">
				Projekt Erstellen!
			</div>
		</a>
	</div>
</div>
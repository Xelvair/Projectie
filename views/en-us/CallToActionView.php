<div class="row cta">
	<div class="col-md-6">
		<h3 class="cta-heading">Have a vision for a project?</h1>
		<p class="cta-text">
			Looking for like-minded people?<br>
			Someone with marketing skills?<br>
			Maybe a Programmer<br>
			Or a designer?<br>
		</p>
	</div>
	<div class="col-md-6">
		<?php if($_DATA["user"]){ ?>
			<a href="<?=abspath("/project/createnew")?>">
		<?php } else { ?>
			<a href="#loginModal" role="button" data-toggle="modal">
		<?php } ?>
			<div class="cta-btn">
				Create a Project!
			</div>
		</a>
	</div>
</div>
<div class="row" style="margin-bottom: 20px;">
	<div class="col-md-12">
		<h1 class="text-center"><?=$_DATA["list_title"]?></h1>
		<hr />
	</div>
</div>

<?php

for($i = 0; $i < sizeof($_DATA["list"]); $i += 2){ ?>
<div class="row">
	<div class="col-md-6">
		<?=$_DATA["list"][$i]?>
	</div>
	<div class="col-md-6">
		<?php if(isset($_DATA["list"][$i + 1])){
			echo $_DATA["list"][$i + 1];
		}
		?>
	</div>
</div>
<?php }?>
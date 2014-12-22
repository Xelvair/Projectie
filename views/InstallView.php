<?php
global $locale;
?>
<div class="jumbotron">
  <div class="container">
  	<form method="POST">
	   	<h1><?=$locale["welcome_install"]?></h1>
	  	<p><?=$locale["db_prompt"]?></p>
	  	<div class="input-append">
	  		<input type="text" name="db_name" class="form-control">
	  		<input type="submit" name="submit" class="btn btn-primary" value="<?=$locale["go"]?>">
	  	</div>
    </form>
    <?php switch($_DATA["action"]){
			case "success":
			?>
				<p class="text-success"><?=$locale["db_init_succ"]?></p>
				<p class="text-success"><?=$locale["forwarding"]?></p>
				<script>setTimeout(function(){window.location = "/home";}, 1000);</script>
			<?php
	    break;
	    case "failure":
	    ?><p class="text-danger"><?=$locale["db_init_fail"]?></p><?php
	   	break;
 		}?>
  </div>
</div>
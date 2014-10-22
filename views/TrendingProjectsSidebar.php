<?php
//PARAMETERS
//entries : currently trending projects
//entries["thumb"] : entry thumbnail
//entries["title"] : entry title
//entries["desc"] : entry description
?>
<div class="row">          
  <div class="col-md-10" id="page_content_left">
  </div><!--page-content-left-->
  <div class="col-md-2" id="page_content_right">
  	<div class="h4" id="content_right_header"><strong>Trending Projects</strong></div>
  	<?php foreach($_DATA["entries"] as $entry){ ?>
  	<div class="thumbnail">
  		<img data-src="holder.js/300x300" alt="..." src="<?=$entry["thumb"]?>">
  		<div class="caption">
  			<h4><?=$entry["title"]?></h4>
  			<p><?=$entry["desc"]?></p>
  		</div>
  	</div>
  	<?php }?>
  </div><!--page-content-right-->
</div>
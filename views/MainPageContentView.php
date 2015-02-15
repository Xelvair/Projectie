<?php
#PARAMETERS
#left_col : left col
#mid_col : middle col
#right_col : right col
#footer : footer
#top_project : best projects :
#thumb : pic
#title: project title
#desc: project description

global $locale;
?>

<div class="row" class="carousel-row">
	<div class="col-md-12 project-banner-wrapper">
    <?=Core::view("ProjectBanner", $_DATA["top_project"])?>           		    
	</div><!--col-md-12-->
</div><!--carousel_row-->

<div id="page_list_wrapper">
  <div class="row">
    <div class="col-md-6 content_list">
      <span class="list-headline"><span class="glyphicon glyphicon-asterisk"></span><?=$locale['new_projects']?></span>
      <hr>
      <?php for($i = 0; $i < sizeof($_DATA["new"]); $i++){
    		echo $_DATA["new"][$i];
      } ?>
    </div>
    <div class="col-md-6 content_list">
      <span class="list-headline"><span class="glyphicon glyphicon-fire"></span><?=$locale['trending_projects']?></span>
      <hr>
      <?php for($i = 0; $i < sizeof($_DATA["trending"]); $i++){
      		echo $_DATA["trending"][$i];
      }  ?>
    </div>
  </div><!--row-->
  <div class="row">
    <div class="col-md-12 content_list">
      <span class="list-headline"><span class="glyphicon glyphicon-refresh"></span><?=$locale['news']?></span>
      <hr>
      <?php 
        $posts = ProjectNews::newest();

        $post_data = array_map(function($entry){
          return ["post" => $entry];
        }, $posts);

        echo Core::view_batch("Post", $post_data); 
      ?>
    </div>
  </div>
</div>
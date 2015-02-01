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
    <div class="col-md-4 content_list">
      <span class="list-headline"><?=$locale['new_projects']?></span>
      <hr>
      <?php for($i = 0; $i < sizeof($_DATA["new"]); $i++){
    		echo $_DATA["new"][$i];
      } ?>
    </div><!--col-md-4-->
    <div class="col-md-4 content_list">
      <span class="list-headline"><?=$locale['trending_projects']?></span>
      <hr>
      <?php for($i = 0; $i < sizeof($_DATA["trending"]); $i++){
      		echo $_DATA["trending"][$i];
      }  ?>
    </div><!--col-md-4-->
    <div class="col-md-4 content_list">
      <span class="list-headline"><?=$locale['news']?></span>
      <hr>
      <?php for($i = 0; $i < sizeof($_DATA["news"]); $i++){
      		echo $_DATA["news"][$i];
      }   ?>
    </div><!--col-md-4-->
  </div><!--row-->
</div>
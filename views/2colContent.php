<?php
#PARAMETERS
//left_content : content left
//right_content : content right

?>
<div class="row">          
  <div class="col-md-10" id="page_content_left">
 
  </div><!--page-content-left-->
  <?=$_DATA['left_content']?>
  <div class="col-md-2" id="page_content_right">
  
    <?=$_DATA['right_content']?>
  </div><!--page-content-right-->
</div>
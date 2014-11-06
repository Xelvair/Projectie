<?php
global $locale;
?>

<div class="row" style="padding-left: 20px; padding-right: 20px;">

	<div class="col-md-4 text-left col-xs-4">
    
    	<ul class="list-inline">
        	<li>©2014 Projectie</li>
            <li><a href="#"><?= $locale['about']?></a></li>
        
        </ul>

       
	</div> 
    
    <div class="col-md-4 col-xs-4 text-center">
    	<ul class="list-inline">
       		 <li>Online: 103.232</li>
        </ul>
    </div>
    
    <div class="col-md-4 col-xs-4 text-right">
    
        <ul class="list-inline">
        	<li><?php echo $locale['logged_in_as'].": ";  if(isset($_DATA['username']) && $_DATA['username'] != ""){ 
			echo $_DATA['username']; 
			
			}else{
				echo $locale['guest'];
			}?></li>
            
            <?php
			if(!isset($_DATA['username']) || $_DATA['username'] == ""){ 
           		echo "<li><a  href='#registerModal' data-toggle='modal'>".$locale["register"]."?</a></li>";
			}
				?>
        
        </ul>
   
    
    </div>
    
     
<div>
<hr style="margin-top: -10px;"/>
<?php
global $locale;
?>

<div class="row" style="padding-left: 20px; padding-right: 20px; margin-top: 20px;">
	<div class="col-md-4 text-left col-xs-4">
    	<ul class="list-inline">
        	<li>©2014 - <?php echo date("Y"); ?> Projectie</li>
            <li><a href="<?=abspath('about')?>"><?= $locale['about']?></a></li>
        </ul>
	</div> 
    <div class="col-md-4 col-xs-4 text-center">
    	<ul class="list-inline">
       		 <li>Online: 103.232</li>
        </ul>
    </div>
    <div class="col-md-4 col-xs-4 text-right">
        <ul class="list-inline">
        	<li><?php echo $locale['logged_in_as'].": ";  
                if(isset($_DATA['user']) && $_DATA['user'] != ""){ 
                    echo $_DATA['user']; 
    			
    			}else{
    				echo $locale['guest'];
    			}?>
            </li>
            
            <?php
			if($_DATA['user'] == null){ 
           		echo "<li><a  href='#registerModal' data-toggle='modal'>".$locale["register"]."?</a></li>";
			}
				?>
        </ul>
    </div>
<div>
<hr style="margin-top: -10px;"/>
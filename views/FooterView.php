<?php
global $locale;
?>

<div class="row">

	<div class="col-md-12">
    
    
        <p align="center">
        	<label class="footer_label"><a href="#"><?= $locale['about']?></a></label>
            <label class="footer_label" style="padding-right: 50px;">Projectie© 2014</label>
            <label class="footer_label"><?php if(isset($_DATA['username']) && $_DATA['username'] != ""){ 
			echo $_DATA['username']; 
			
			}else{
				echo $locale['guest'];
			}?>
            </label>
            
            <?php
			if(!isset($_DATA['username']) || $_DATA['username'] == ""){ 
           		echo "<label class='footer_label'><a  href='#registerModal' data-toggle='modal'>".$locale["register"]."?</a></label>";
			}
				?>
            <label class="footer_label">Online: 234.930</label>
            
        
        </p>
       <hr style="margin-top: -10px;"/>
	</div> 
<div>

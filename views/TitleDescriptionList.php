<?php
#PARAMENTERS
#list_title : Title of list	
#entries: 
#thumb: thumb
#title: title
#des: description			
                
                
?>               

    <h3><?=$_DATA['list_title']?></h3>
    <hr />
    
    <?php foreach($_DATA["entries"] as $entry){ ?>
        <div class="media">
          <a class="pull-left" href="#">
            <img class="media-object" src="<?=$entry['thumb'];?>" alt="...">
          </a>
          <div class="media-body">
            <h4 class="media-heading"><?=$entry['title'];?></h4>
            <?=$entry['desc'];?>
          </div>
        </div><hr/><!--media-->
     <?php }?>   

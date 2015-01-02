<?php
#PARAMENTERS
#list_title : Title of list	
#entries: 
	#thumb: thumb
	#title: title
	#des: description
	#creator
		#id
		#name
	#source
	#time
                

if(isset($_DATA['list_title'])){                
?>               
<h3><?php echo($_DATA['list_title'] != null ? $_DATA['list_title'] : "");?></h3>
<hr />
<?php 
}
foreach($_DATA["entries"] as $entry){ ?>
    <div class="media">
      <a class="pull-left" href="#">
        <img class="media-object img-responsive" src="<?=$entry['thumb'];?>" alt="...">
      </a>
      <div class="media-body">
        <h4 class="media-heading"><?=$entry['title'];?></h4>
        <?=$entry['desc'];?>
      </div>
	  <div class="media-footer">
		<small class="media-time-container text-muted pull-right"><span class="glyphicon glyphicon-time"></span><span class="chat-msg-time"><?=$entry['time']?></span></small>
		<?php if(isset($entry['creator']['id']) && $entry['creator']['id'] != "")
			{ ?> <a href="<?=abspath("profile");?>"  class="user" user-id="<?=$entry['creator']['id']?>"><small class="source-container text-muted"></span><span class="media-source"><?=$entry['creator']['name']?></span></small></a>
			<?php
			} ?>
	  </div>
    </div><hr/><!--media-->
 <?php }?>   

<?php
#PARAMENTERS
#list_title : Title of list	
#entries: 
	#project_id
	#thumb: thumb
	#title: title
	#description: description
	#creator
		#id
		#name
	#source
	#annotation

                

if(isset($_DATA['list_title'])){                
?>               
<h3><?php echo($_DATA['list_title'] != null ? $_DATA['list_title'] : "");?></h3>
<hr />
<?php 
}
foreach($_DATA["entries"] as $entry){ ?>
	<?php 
		$project_href = isset($entry["project_id"]) ? abspath("/project/show/".$entry["project_id"]) : "#"; 
	?>

	<a class="title-desc-list" href="<?=$project_href?>">
    <div class="media">
        <img class="media-object pull-left img-responsive" src="<?=abspath("/public/images/default-profile-pic.png")?>" alt="...">
      <div class="media-body">
        <h4 class="media-heading"><?=$entry['title'];?></h4>
        <?=$entry['description'];?>
      </div>
	  <div class="media-footer">
	  	<?php if(isset($_DATA["annotation"])){ ?>
			<small class="media-time-container text-muted pull-right"><span class="chat-msg-time"><?=$entry['annotation']?></span></small>
	 	<?php } ?>
	  </div>
    </div><hr/><!--media-->
    </a>
 <?php }?>   

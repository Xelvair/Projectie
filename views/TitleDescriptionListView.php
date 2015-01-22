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
		<?php if(isset($entry['creator']) && !empty($entry['creator'])){ ?> 
			<span class="user media-source" user-id="<?=$entry['creator']['id']?>"><small class="source-container text-muted"><?=$entry['creator']['name']?></small></span>
		<?php } ?>
	  </div>
    </div><hr/><!--media-->
    </a>
 <?php }?>   

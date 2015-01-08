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


?>

  <div class="row" id="carousel_row">
                	<div class="col-md-12">
                    
                    	<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
                          <!-- Indicators -->
                          <ol class="carousel-indicators">
                       	<?php 
						$i = 0;
						foreach($_DATA["top_project"] as $entry){ ?>
                            <li data-target="#carousel-example-generic" data-slide-to="<?=$i?>"></li>
                          	<?php $i++;
							
							}?>
                          </ol>
                        
                          <!-- Wrapper for slides -->
                          <div class="carousel-inner">
                          	
                          	<?php
							 $i = 0;
							 
							 foreach($_DATA["top_project"] as $entry){ ?>
                            
                            <div class="item <?php if($i==0){
								echo "active";
							}?>">
                              <img src="<?=$entry['thumb']?>" alt="..." class="img-responsive">
                              <div class="carousel-caption">
                                <h4><?=$entry['title']?></h4>
                                <p><?=$entry['desc']?></p>
                              </div>
                            </div>
                           
                            <?php $i++; }?> 
                            
                          </div><!--carousell-inner-->
                        
                          <!-- Controls -->
                          <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
                            <span class="glyphicon glyphicon-chevron-left"></span>
                          </a>
                          <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
                            <span class="glyphicon glyphicon-chevron-right"></span>
                          </a>
                        </div><!--carousell-->
                
            		    
                	</div><!--col-md-12-->
                </div><!--carousel_row-->
                <div id="page_list_wrapper">
                    <div class="row">
                        <div class="col-md-4 content_list">
                        
                            <?=$_DATA['left_col']?>
                            
                        </div><!--col-md-4-->
                        
                        <div class="col-md-4 content_list">
                        
                          <?=$_DATA['mid_col']?>
                        
                        </div><!--col-md-4-->
                        
                        <div class="col-md-4 content_list">
                        
                           <?=$_DATA['right_col']?>
                        
                        </div><!--col-md-4-->
                    </div><!--row-->
                </div>
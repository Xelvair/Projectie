<?php
global $locale;
?>
<div id="PicUploadModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
   <div class="modal-dialog">
       <div class="modal-content">
           <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="glyphicon glyphicon-remove"></span></button>
              <h2 class="text-center"><?=$locale['upload_pic']?></h2>
          </div>
          <div class="modal-body">
          	<input id="input-1" type="file" class="file">
           </div>
       </div>
   </div>
</div><!--loginModal-->

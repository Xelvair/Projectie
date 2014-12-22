<div class="user-review">
<img src="../public/images/default-profile-pic.png" class="img-rounded pull-left" height="50" width="50"/>
<h3 class="user-review-title" id="user-review-username">Username</h3>
<p>Projects involved: 30</p>
</div>

<script>
var mouse_x, mouse_y;
$(document).mousemove(function(event) {
        mouse_x = event.pageX;
        mouse_y = event.pageY;
		
});

$(document).ready(function(){
	var tOut;
		$('.user-review').hide();
		$('.user').hover(function(){
			var id = $(this).attr( "user-id" );
			tOut = setTimeout(function(){
					$('.user-review').animate({left: mouse_x+"px", top: mouse_y+"px"});
					
					$.ajax({url: "<?=abspath("/auth/get_user/")?>"+id}).done(function(data){
					var result = JSON.parse(data);
					
					$('#user-review-username').text(result.username);
					$('.user-review').fadeIn();
					
					});
					
					
			
						
				
					
				},1000);
		},function(){
			clearTimeout(tOut);
			$('.user-review').fadeOut();		
		});

});
</script>
<form>
	<textarea id="code"></textarea><br>
	<input type="button" id="code_submit" value="Debug">
</form>
<div id="result">
</div>

<script>
	$(document).ready(function(){
		$("#code_submit").on("click", function(){
			$.ajax({
				url: "http://localhost:8001/Debug/debug",
				type: "POST",
				data: "code=" + $("#code").val(),
				success: function(data){
					$("#result").html(data);
				}
			});
		});
	});
</script>
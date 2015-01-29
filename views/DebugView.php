<h1>HTTP-Post Debugging</h1>
<form id="post_debug_form">
	<input type="text" id="post_debug_dest" value="http://localhost:8001/">
	<table id="post_debug_table">
	</table>
	<input type="button" id="add_entry_row" value="Add Row">
	<input type="button" id="send_post_debug" value="Send">
</form>
<div id="post_debug_result" style="overflow: scroll;"></div>

<script>
var row_count = 0;
function add_entry_row(){
	row_count = row_count + 1;
	$("#post_debug_table").append("\
		<tr>\
			<td>\
				<input type='text' id='debug_name_"+row_count+"' placeholder='field name'>\
			</td>\
			<td>\
				<input type='text' id='debug_value_"+row_count+"' placeholder='field_value'>\
			</td>\
		</tr>\
	");
}

function send_post_debug(){
	var post_data = {};
	for(var i = 1; i <= row_count; i++){
		post_data[$("#debug_name_"+i).val()] = $("#debug_value_"+i).val();
	}
	
	$.post(
		$("#post_debug_dest").val(), 
		post_data
	).done(
		function(data){
			$("#post_debug_result").html(data);
		}
	);
}

$(document).ready(function(){
	$("#add_entry_row").on("click", function(){
		add_entry_row();
	});

	$("#send_post_debug").on("click", function(){
		send_post_debug();
	});
});

</script>

<hr>

<h1>PHP Debugging</h1>
<form>
	<textarea rows="20" cols="120" id="code">
$dbez = Core::model("DBEZ");
$auth = Core::model("Auth", $dbez);
$chat = Core::model("Chat", $dbez);
$project = Core::model("Project", $dbez);
$tag = Core::model("Tag", $dbez);
	</textarea><br>
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
				data: "code=" + encodeURIComponent($("#code").val()),
				success: function(data){
					$("#result").html(data);
				}
			});
		});
	});
</script>
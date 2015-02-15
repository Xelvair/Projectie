<?php
require_once(abspath_lcl("templates/project_preview.html"));

global $locale;
?>

<div class="row">
	<h1><?=$locale["search_tags"]?></h1>
	<div id="tag_search_wrapper" class="col-md-12" style="height: 300px; padding-bottom: 30px; clear: both;">
		<?=Core::view("TagBoxTest", ["editable" => true])?>
	</div>
	<hr style="clear: both;">
</div>
<div id="tag_search_result">
	<div class="row">
		<div id="tag_search_result_1" class="col-md-4"></div>
		<div id="tag_search_result_1" class="col-md-4"></div>
		<div id="tag_search_result_1" class="col-md-4"></div>
	</div>
</div>

<script>
	$(document).ready(function(){
		var search_tagboxlist = $("#tag_search_wrapper").find(".tagbox-tags")[0];

		$(search_tagboxlist).on("pj.tagboxlist.change", function(){
			var tagboxlist_tags = $(search_tagboxlist).children().get();

			var tags = [];
			tagboxlist_tags.forEach(function(tag){
				tags.push($(tag).attr("data-tag-id"));
			});

			$.post(Projectie.server_addr + "/project/search_by_tags", {tags : tags}, function(result){
				console.log(result);
				var projects = JSON.parse(result);

				$("#tag_search_result").empty();
				$("#tag_search_result").append(Projectie.create_project_list(projects, 3));
			});
		});

		$(search_tagboxlist).trigger("pj.tagboxlist.change");
	});	
</script>
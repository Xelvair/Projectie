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

				var search_result_elem = $("<div></div>");
				for(var i = 0; i < projects.length; i = i + 3){
					var search_result_row_elem = $("<div class='row'></div>");

					for(var j = i; j < Math.min(i + 3, projects.length); j = j + 1){
						var search_result_entry_elem = $("<div class='col-md-4'></div>");
						var project_preview_elem = $("#project_preview").clone().show().removeAttr("id");

						var project = projects[j];

						$(project_preview_elem).attr("href", "/project/show/" + project.project_id) 
						$(project_preview_elem).find(".favs").prepend(project.fav_count);
						$(project_preview_elem).find(".members").prepend(project.participator_count);
						$(project_preview_elem).find(".preview_content>h3").prepend(project.title);
						$(project_preview_elem).find(".preview_content").append(project.description);
						$(project_preview_elem).find("img").attr("src", Projectie.server_addr + project.title_picture.file_path);

						$(search_result_entry_elem).append(project_preview_elem);

						$(search_result_row_elem).append(search_result_entry_elem);
					}					

					$(search_result_elem).append(search_result_row_elem);
				}

				$("#tag_search_result").empty();
				$("#tag_search_result").append($(search_result_elem).contents());
			});
		});

		$(search_tagboxlist).trigger("pj.tagboxlist.change");
	});	
</script>

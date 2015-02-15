Projectie.create_project_list = function(projects, cols){

	var search_result_elem = $("<div></div>");
	for(var i = 0; i < projects.length; i = i + 1){
			var search_result_entry_elem = $("<div class='col-md-" + parseInt(12/cols) + "'></div>");
			var project_preview_elem = $("#project_preview").clone().show().removeAttr("id");

			var project = projects[i];

			$(project_preview_elem).attr("href", "/project/show/" + project.project_id) 
			$(project_preview_elem).find(".favs").prepend(project.fav_count);
			$(project_preview_elem).find(".members").prepend(project.participator_count);
			$(project_preview_elem).find(".preview_content>h3").prepend(project.title);
			$(project_preview_elem).find(".preview_content").append(project.description);
			$(project_preview_elem).find("img").attr("src", Projectie.server_addr + project.title_picture.file_path);

			$(search_result_entry_elem).append(project_preview_elem);	

		$(search_result_elem).append(search_result_entry_elem);
	}

	return search_result_elem;
}
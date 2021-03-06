$(document).ready(function(){
	if(Projectie.current_user !== undefined){
		$(".project-fav").each(function(){
			var project_id = $(this).attr("data-project-id");

			$.post(Projectie.server_addr + "/project/is_favorite", {project_id : project_id}, function(result){
				var is_favorite = parseInt(result);

				if(is_favorite){
					$(this).addClass("project-fav-active");
				}
			}.bind(this));
		});
	}
});

$(document).on("click", ".project-fav", function(e){
	e.stopPropagation();
	e.preventDefault();

	if(Projectie.current_user !== undefined){
		var project_id = $(e.currentTarget).attr("data-project-id");

		if($(e.currentTarget).hasClass("project-fav-active")){
			$.post(Projectie.server_addr + "/project/unfavorite", {project_id : project_id}, function(result){
				result_obj = JSON.parse(result);
				if(result_obj.ERROR){
					alert(result_obj.ERROR);
				} else {
					var favs_span = $(e.currentTarget).prev('.favs_number');
					var favs = parseInt(favs_span.text());
					favs = favs - 1;
					favs_span.text(favs);
					
					$(".project-fav[data-project-id="+project_id+"]").toggleClass("project-fav-active").prev().text(favs);
					
				}
			});
		} else {
			$.post(Projectie.server_addr + "/project/favorite", {project_id : project_id}, function(result){
				result_obj = JSON.parse(result);
				if(result_obj.ERROR){
					alert(result_obj.ERROR);
				} else {
					var favs_span = $(e.currentTarget).prev('.favs_number');
					var favs = parseInt(favs_span.text());
					favs = favs + 1;
					favs_span.text(favs);
					$(".project-fav[data-project-id="+project_id+"]").toggleClass("project-fav-active").prev().text(favs);

				}
			});
		}
	} else {
		console.log("not logged in");
	}
});
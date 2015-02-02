$(document).ready(function(){
	$(".tagbox").on("mouseover", ".tag", function(e){
		$(e.currentTarget).children("span.tag-remove").show(50);
	});

	$(".tagbox").on("mouseleave", ".tag", function(e){
		$(e.currentTarget).children("span.tag-remove").hide(50);
	});

	$(".tagbox").on("click", ".tag-remove", function(e){
		var tag_id = $(e.currentTarget).closest(".tag").attr("data-tag-id");
		var tagremove = $(e.delegateTarget).attr("data-tagremove");

		$.post(Projectie.server_addr + tagremove, {
			tag_id : tag_id
		}).success(function(result){
			var result_obj = JSON.parse(result);

			if(result_obj.ERROR){
				alert(result_obj.ERROR);
			} else {
				$(e.currentTarget).closest(".tag").remove();
			}
		});
	})

	$(".tagbox").each(function(idx){
		var tagsource = $(this).attr("data-tagsource");

		$.get(tagsource, function(result){
			result_obj = JSON.parse(result);

			result_obj.forEach(function(tag){
				var tag_elem = $("<div class='tag' data-tag-id='"+tag.tag_id+"'>"+tag.name+"<span style='display: none;' class='tag-remove glyphicon glyphicon-remove'></span></div>");

				$(this).find(".tagbox-list").append(tag_elem);

				tag_elem.trigger("pj.tagadd");
			}.bind(this));
		}.bind(this));
	});

	$(".tagbox").on("click", ".tag-btn", function(e){
		$(e.delegateTarget).find(".tagbox-overlay").fadeToggle();
	});
});
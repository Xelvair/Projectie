function fit_divs(){
	$(".tagbox").each(function(){
		var parent_height = $(this).innerHeight();
		var parent_width = $(this).innerWidth();

		$(this).children(".tagbox-list, .tagbox-recommend").each(function(){
			$(this).height(parent_height - ($(this).outerHeight(true) - $(this).height()));
			$(this).width(parent_width - ($(this).outerWidth(true) - $(this).width()));
		});
	});
}

function scroll_divs(){
	$(".tagbox").each(function(){
		var state = $(this).attr("data-state");
		var left_mult = (state == "recommend" ? 1 : 0);
		var new_left = -$(this).width() * left_mult;

		$(this).find(".tagbox-inner").animate({left : new_left});
	});
}

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

	$(window).on("resize", fit_divs);

	fit_divs();

	$(".tagbox").on("click", ".tag-btn", function(e){
		var invert_state = function(state){return (state == "list" ? "recommend" : "list");}

		$(e.delegateTarget).attr("data-state", invert_state($(e.delegateTarget).attr("data-state")));

		scroll_divs();
	})
});
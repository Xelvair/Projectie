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

function scroll_divs(instant){
	$(".tagbox").each(function(){
		var state = $(this).attr("data-state");
		var left_mult = (state == "recommend" ? 1 : 0);
		var new_left = -$(this).width() * left_mult;

		if(instant){
			$(this).find(".tagbox-inner").css({left : new_left});
		} else {
			$(this).find(".tagbox-inner").animate({left : new_left});
		}
	});
}

$(document).ready(function(){

	$(".tagbox").on("mouseover", ".tag", function(e){
		$(e.currentTarget).children("span.tag-remove").show(50);
	});

	$(".tagbox").on("mouseleave", ".tag", function(e){
		$(e.currentTarget).children("span.tag-remove").hide(50);
	});

	$(window).on("resize", function(){fit_divs(); scroll_divs(true);});

	fit_divs();

	$(".tagbox").on("click", ".tag-btn", function(e){
		$(e.delegateTarget).trigger("pj.tagbox.switch");
	});

	$(".tagbox").on("pj.tagbox.switch", function(e){
		var invert_state = function(state){return (state == "list" ? "recommend" : "list");}

		$(this).attr("data-state", invert_state($(this).attr("data-state")));

		var state = $(this).attr("data-state");
		var left_mult = (state == "recommend" ? 1 : 0);
		var new_left = -$(this).width() * left_mult;

		$(this).find(".tagbox-inner").animate({left : new_left});
	});

	/* TAGBOX-LIST */
	$(document).on("pj.tagboxlist.loadtags", ".tagbox-list", function(e){
		var tagsource = $(e.currentTarget).attr("data-tagsource");

		if(tagsource){
			$.get(Projectie.server_addr + tagsource, function(result){
				var tags_obj = JSON.parse(result);

				$(e.currentTarget).trigger("pj.tagboxlist.replacetags", [tags_obj]);
			}.bind(this));
		}
	});

	$(document).on("pj.tagboxlist.replacetags", ".tagbox-list", function(e, tags){
		$(e.currentTarget).find(".tag").remove();

		tags.forEach(function(tag){
			var tag_elem = $("<div class='tag'></div>").attr("data-tag-id", tag.tag_id).text(tag.name);

			if($(e.currentTarget).attr("data-editable") == 1){
				var remove_elem = $("<span style='display: none;' class='tag-remove glyphicon glyphicon-remove'></span>");
				$(tag_elem).append(remove_elem);
			}

			$(e.currentTarget).append(tag_elem);
		});
	});

	$(document).on("pj.tagboxlist.removetag", ".tagbox-list", function(e, tag){
		var tagdelete = $(e.currentTarget).attr("data-tagremove");

		if(tagdelete){
			$.post(Projectie.server_addr + tagdelete, tag, function(result){
				var result = JSON.parse(result);

				if(!result.ERROR){
					$(e.currentTarget).find(".tag[data-tag-id="+tag.tag_id+"]").remove();
					$(e.currentTarget).siblings(".tagbox-recommend").trigger("pj.tagboxlistrecommend.refresh");
				} else {
					alert(result.ERROR);
				}
			});
		} else {
			$(e.currentTarget).find(".tag[data-tag-id="+tag.tag_id+"]").remove();
			$(e.currentTarget).siblings(".tagbox-recommend").trigger("pj.tagboxlistrecommend.refresh");
		}
	});

	$(document).on("pj.tagboxlist.addtag", ".tagbox-list", function(e, tag){
		var tag_elem = $("<div class='tag'></div>").attr("data-tag-id", tag.tag_id).text(tag.name);

		var tagadd = $(this).attr("data-tagadd");

		if(tagadd){
			$.post(Projectie.server_addr + tagadd, tag, function(result){
				var result = JSON.parse(result);

				if(result.ERROR){
					alert(result.ERROR);
				} else {
					if($(e.currentTarget).attr("data-editable") == 1){
						var remove_elem = $("<span style='display: none;' class='tag-remove glyphicon glyphicon-remove'></span>");
						$(tag_elem).append(remove_elem);
					}
					$(this).append(tag_elem);
				}
			}.bind(this));
		} else {
			if($(e.currentTarget).attr("data-editable") == 1){
				var remove_elem = $("<span style='display: none;' class='tag-remove glyphicon glyphicon-remove'></span>");
				$(tag_elem).append(remove_elem);
			}
			$(this).append(tag_elem);
		}
	});
	
	$(document).on("pj.tagboxlistrecommend.refresh", ".tagbox-recommend", function(e){
		var search_string = $(e.currentTarget).find(".tagbox-recommend-search").val();

		$.post(Projectie.server_addr + "/tag/get_recommendations", {search_string : search_string}, function(result){
			var result_obj = JSON.parse(result);

			$(e.currentTarget).trigger("pj.tagboxlist.replacetags", [result_obj]);

			var tags_elem = $(e.currentTarget).siblings(".tagbox-tags").get(0);

			$(tags_elem).find(".tag").each(function(){
				$(e.currentTarget).find(".tag[data-tag-id="+ $(this).attr("data-tag-id") +"]").addClass("tag-exists-already");
			});
		});
	});

	$(document).on("click", ".tag-remove", function(e){
		var parent = $(e.currentTarget).closest(".tagbox-list").get();
		var tag_id = $(e.currentTarget).closest(".tag").attr("data-tag-id");

		$(parent).trigger("pj.tagboxlist.removetag", {tag_id : tag_id});
	});

	$(document).on("keyup", ".tagbox-recommend-search", function(e){
		var tagbox_recommend_elem = $(e.currentTarget).closest(".tagbox-recommend");

		$(tagbox_recommend_elem).trigger("pj.tagboxlistrecommend.refresh");
	});

	$(document).on("click", ".tagbox-recommend .tag:not(.tag-exists-already)", function(){
		$(this).addClass("tag-exists-already");

		var tags_elem = $(this).closest(".tagbox-recommend").siblings(".tagbox-tags").get(0);

		var tag_id = $(this).attr("data-tag-id");
		var name = $(this).text();

		$(tags_elem).trigger("pj.tagboxlist.addtag", {tag_id : tag_id, name : name});
	});

	$(".tagbox-list").trigger("pj.tagboxlist.loadtags");
	$(".tagbox-recommend").trigger("pj.tagboxlistrecommend.refresh");

});

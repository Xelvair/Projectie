function fit_backdrop(){
	$(".project-banner-backdrop").each(function(){
		var backdrop_height = $($(this).siblings(".project-banner")[0]).outerHeight() + $($(this).siblings(".project-banner")[0]).position().top;

		$(this).css({height: backdrop_height});
	});
}

function fit_images(){
	$(".project-banner-content-list-item").each(function(){
		var container_width = $(this).closest(".project-banner-content").width();
		var container_height = $(this).closest(".project-banner-content").height();
		$(this).width(container_width);
		$(this).height(container_height);
	});
}

function set_banner_info(elem, info, animate_time){
	var info_elem = $(elem).find(".project-banner-info")[0];

	$(info_elem).fadeOut(animate_time / 2, function(){
		$(info_elem).find(".project-banner-info-participator-count").text(info.participatorcount.toLocaleString());
		$(info_elem).find(".project-banner-info-fav-count").text(info.favcount.toLocaleString());

		$(info_elem).fadeIn(animate_time / 2);
	});
}

function scroll_banner(elem, magnitude, animate_time){
	var list_elem = $(elem).find(".project-banner-content-list");

	var current_elem_index = 	parseInt($(elem).attr("data-current-elem"));

	var slide_right = (magnitude > 0);
	var slide_left = (magnitude < 0);

	var element_width = $(elem).find(".project-banner-content").width();

	if(slide_right && current_elem_index >= $(list_elem).children().length - 1){
		current_elem_index = 1;
		$(list_elem).css({left: -(element_width * current_elem_index)});
	}

	if(slide_left && current_elem_index <= 0){
		current_elem_index = $(list_elem).children().length - 2;
		$(list_elem).css({left: -(element_width * current_elem_index)});
	}

	var next_elem_index =  current_elem_index + magnitude;

	$(elem).attr("data-current-elem", next_elem_index);

	var scroll_left = -(next_elem_index * element_width);

	$(list_elem).animate({left: scroll_left}, animate_time);

	var list_item_elem = $(list_elem).children()[next_elem_index];

	var info = {
		favcount : parseInt($(list_item_elem).attr("data-fav-count")),
		participatorcount : parseInt($(list_item_elem).attr("data-participator-count"))
	}

	set_banner_info(elem, info, animate_time);
}

$(document).ready(function(){
	fit_images();
	fit_backdrop();

	$(".project-banner").each(function(){
		var content_list = $(this).find(".project-banner-content-list");

		if($(content_list).children().length > 1){
			var new_first_elem = $(content_list).children().last().clone();
			var new_last_elem = $(content_list).children().first().clone();

			$(content_list).prepend(new_first_elem);
			$(content_list).append(new_last_elem);

			$(this).attr("data-current-elem", 1);

			scroll_banner(this, 1, 0);

			var interval_fn = function(){
				scroll_banner(this, 1, 500);
			}.bind(this);

			var interval = setInterval(interval_fn, 10000);

			$(".project-banner").on("click", function(){
				clearInterval(interval);
				interval = setInterval(interval_fn, 10000);
			});

		} else {
			$(this).find(".project-banner-arrow-left").hide();
			$(this).find(".project-banner-arrow-right").hide();

			$(this).attr("data-current-elem", 0);

			scroll_banner(this, 0, 0);
		}
	});

	$(".project-banner").on("click", ".project-banner-arrow-left", function(e){
		scroll_banner(e.delegateTarget, -1, 500);
	});

	$(".project-banner").on("click", ".project-banner-arrow-right", function(e){
		scroll_banner(e.delegateTarget, 1, 500);
	});

	$(window).on("resize", function(){
		fit_images();
		$(".project-banner").each(function(){
			scroll_banner(this, 0, 0);
		});
		fit_backdrop();
	});
});
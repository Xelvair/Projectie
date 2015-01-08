Projectie.Tagging.TagBox = function(data){
	that = this;

	this.container_elem = data.container_elem;
	if(!this.container_elem || !(this.container_elem instanceof jQuery)){
		throw new Error("TagBox constructor requires container_elem param as jQuery object!");
	}
	this.id = data.id;
	this.data_src = data.data_src || null;
	this.template_src = data.template_src || (Projectie.server_addr + "/Template/TagBox");
	this.template_res = null;
	this.editable = data.editable || null;
	this.assembly_func = data.assembly_func || function(data){console.log(data)};
	this.remove_callback = data.remove_callback || function(data){console.log(data)};
	this.add_callback = data.add_callback || function(data){console.log(data)};

	(function(){
		var data_result_obj = null;
		var template_result_obj = null;

		var check_completion = function(){
			if(data_result_obj && template_result_obj){
				$("head").append($(template_result_obj).find("script"));
				html_elem = window[this.assembly_func]({
					editable : data_result_obj.editable,
					tags : data_result_obj.tags,
					template : template_result_obj,
					tagbox_obj : that
				});
				html_elem.attr("id", this.id);
				this.container_elem.append(html_elem);
			}
		}.bind(this);

		if(this.data_src !== null){
			$.ajax(this.data_src).done(function(data_result){
				data_result_obj = JSON.parse(data_result);
				if(this.editable !== null){
					data_result_obj.editable = this.editable;
				} else {
					this.editable = data_result_obj.editable;
				}
				check_completion();
			}.bind(this));
		}

		$.ajax(this.template_src).done(function(template_result){
			template_result_obj = $("<output>").append($.parseHTML(template_result, null, true));
			that.template_res = template_result_obj;
			check_completion();
		});

	}.bind(this))();

	this.onAddTag = function(tag_name){
		window[this.add_callback](this.template_res, this.container_elem, 1, tag_name);
	}

	this.onRemoveTag = function(tag_id){
		window[this.remove_callback](this.container_elem, tag_id);
	}
}
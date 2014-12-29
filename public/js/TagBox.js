Projectie.Tagging.TagBox = function(data){
	this.container_elem = data.container_elem;
	if(!this.container_elem || !(this.container_elem instanceof jQuery)){
		throw new Error("TagBox constructor requires container_elem param as jQuery object!");
	}
	this.data_src = data.data_src || null;
	this.template_src = data.template_src || (Projectie.server_addr + "/Template/TagBox");
	this.editable = data.editable || null;
	this.assembly_func = data.assembly_func || function(data){console.log(data)};

	(function(){
		var data_result_obj = null;
		var template_result_obj = null;

		var check_completion = function(){
			if(data_result_obj && template_result_obj){
				$("head").append($(template_result_obj).find("script"));
				html_elem = window[this.assembly_func]({
					editable : data_result_obj.editable,
					tags : data_result_obj.tags,
					template : template_result_obj
				});
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
			check_completion();
		});

	}.bind(this))();
}
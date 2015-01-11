(function(){
	$(document).ready(function(){
		$(".participation_entry").on("click", ".rights", function(e){
			e.stopPropagation();
			console.log("dblfgt");
			console.log(e);
			var rights_rolldown = $(e.delegateTarget).find(".participation_entry_rights_rolldown").get();

			$(rights_rolldown).slideToggle(250);
		});
	});
})();
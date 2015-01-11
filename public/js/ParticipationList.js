(function(){
	$(document).ready(function(){
		$(".participation_entry").on("click", ".rights", function(e){
			e.stopPropagation();
			var rights_rolldown = $(e.delegateTarget).find(".participation_entry_rights_rolldown");

			$(rights_rolldown).slideToggle(250);
		});
	});
})();
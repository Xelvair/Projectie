<div id="tagbox_container">
</div>

<script>
var tagbox = Projectie.Tagging.TagBox({
	data_src : Projectie.server_addr + "/project/get_tag_meta/1",
	template_src : Projectie.server_addr + "/template/tagbox",
	container_elem : $("#tagbox_container"),
	assembly_func : "TagBox_assembly_callback"
});
</script>
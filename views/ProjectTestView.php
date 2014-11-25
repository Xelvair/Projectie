<?php
#PARAMETERS
#projects : list of all projects as returned by mysqli_result::fetch_all()
?>

<form method="POST" action="<?=abspath("/project/create&redirect=/test/project")?>">
	<table>
		<tr>
			<td>Title</td>y
			<td><input type="text" name="title"></td>
		</tr>
		<tr>
			<td>Subtitle</td>
			<td><input type="text" name="subtitle"></td>
		</tr>
		<tr>
			<td>Description</td>
			<td><textarea name="description"></textarea></td>
		</tr>
		<tr>
			<td><input type="submit"></td>
		</tr>
	</table>
</form>
<table border="1px">
	<tr>
		<th>id</th>
		<th>creator_id</th>
		<th>create_time</th>
		<th>title</th>
		<th>subtitle</th>
		<th>description</th>
	</tr>
	<?php foreach($_DATA["projects"] as $project){ ?>
		<tr>
			<td><?=$project["project_id"]?></td>
			<td><?=$project["creator_id"]?></td>
			<td><?=$project["create_time"]?></td>
			<td><?=$project["title"]?></td>
			<td><?=$project["subtitle"]?></td>
			<td><?=$project["description"]?></td>
		</tr>
	<?php } ?>
</table>
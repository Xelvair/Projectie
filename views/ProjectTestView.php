<form method="POST" action="<?=abspath("/test/project_action")?>">
	<table>
		<tr>
			<td>Title</td>
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
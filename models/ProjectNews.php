<?php

class ProjectNews extends ActiveRecord{
	/* PUBLIC STATIC */
	public static function getTableName(){
		return "project_news";
	}

	public static function newest($count = 3){
		global $mysqli;

		$news_entries = $mysqli->query("SELECT project_news_id FROM project_news WHERE active = 1 ORDER BY post_time DESC LIMIT ".$count)->fetch_all(MYSQLI_ASSOC);

		$result = [];
		foreach($news_entries as $news_entry){
			array_push($result, self::get((integer)$news_entry["project_news_id"])); 
		}

		return $result;
	}

	public static function newestFromProject($project_id, $count = 3){
		global $mysqli;

		$query_news_entries = $mysqli->prepare("SELECT project_news_id FROM project_news WHERE project_id = ? AND active = 1 ORDER BY post_time DESC LIMIT ?");
		$query_news_entries->bind_param("ii", $project_id, $count);
		$query_news_entries->execute();

		$news_entries = $query_news_entries->get_result()->fetch_all(MYSQLI_ASSOC);

		$result = [];
		foreach($news_entries as $news_entry){
			array_push($result, self::get((integer)$news_entry["project_news_id"]));
		}

		return $result;
	}

	/* PUBLIC */
	public function getProject(){
		return $this->getRelative("Project", "project_id");
	}

	public function getAuthor(){
		return $this->getRelative("User", "author_id");
	}
}

?>
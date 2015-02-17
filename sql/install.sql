DROP TABLE IF EXISTS user;
CREATE TABLE user (
	user_id       int NOT NULL AUTO_INCREMENT,
	create_time   int NOT NULL,
	email         varchar(128) NOT NULL,
	username      varchar(32) NOT NULL,
	lang          varchar(10) NOT NULL,
	picture_id 		int NOT NULL DEFAULT 1,
	password_salt varchar(8) NOT NULL,
	password_hash varchar(32) NOT NULL,
	is_admin      boolean NOT NULL DEFAULT 0,
	active        boolean NOT NULL DEFAULT 1,
	PRIMARY KEY (user_id)
);

INSERT INTO user (user_id, email, username, lang, password_salt, password_hash, is_admin, active)
VALUES (1, 'admin@projectie.com', 'admin', 'de-de', 'a77239ba', '73bfd1bfe49f8b4e238c94f3a6a6ef4d', true, true);

DROP TABLE IF EXISTS project;
CREATE TABLE project (
	project_id      	int NOT NULL AUTO_INCREMENT,
	creator_id      	int NOT NULL,
	create_time     	int NOT NULL,
	title           	varchar(256) NOT NULL,
	subtitle        	varchar(124) NOT NULL,
	description     	text NOT NULL,
	title_picture_id	int NOT NULL,
	public_chat_id  	int NOT NULL,
	private_chat_id 	int NOT NULL,
	active          	boolean NOT NULL DEFAULT 1,
	PRIMARY KEY(project_id)
);

INSERT INTO project (creator_id, create_time, title, subtitle, description, title_picture_id, active)
VALUES(1, UNIX_TIMESTAMP(), "Sample Project", "Sample Subtitle", "Sample Description", 1, true);

DROP TABLE IF EXISTS project_position;
CREATE TABLE project_position(
	project_position_id     int NOT NULL AUTO_INCREMENT,
	project_id              int NOT NULL,
	user_id                 int,
	job_title               varchar(128) NOT NULL,
	can_delete              boolean NOT NULL,
	can_edit                boolean NOT NULL,
	can_communicate         boolean NOT NULL,
	can_add_participants    boolean NOT NULL,
	can_remove_participants boolean NOT NULL,
	participator_since			int,
	PRIMARY KEY(project_position_id)
);

INSERT INTO project_position (project_id, user_id, job_title, can_delete, can_edit, can_communicate, can_add_participants, can_remove_participants, participator_since)
VALUES(1, 1, "Creator", 1, 1, 1, 1, 1, 0);

DROP TABLE IF EXISTS project_participation_request;
	CREATE TABLE project_participation_request (
	project_participation_request_id int NOT NULL AUTO_INCREMENT,
	project_position_id              int NOT NULL,
	user_id                          int NOT NULL,
	request_type                     enum("USER_TO_PROJECT", "PROJECT_TO_USER") NOT NULL,
	chat_id                          int NOT NULL,
	PRIMARY KEY(project_participation_request_id)
);

DROP TABLE IF EXISTS chat;
CREATE TABLE chat (
	chat_id    int NOT NULL AUTO_INCREMENT,
	creator_id int NOT NULL,
	title      varchar(256) NOT NULL,
	access     enum("PUBLIC", "PRIVATE") NOT NULL,
	PRIMARY KEY(chat_id)
);

INSERT INTO chat (creator_id, title, access)
VALUES (1, "Test Chat", "PUBLIC");

DROP TABLE IF EXISTS chat_participation;
CREATE TABLE chat_participation (
	chat_participation_id int NOT NULL AUTO_INCREMENT,
	chat_id               int NOT NULL,
	participant_id        int NOT NULL,
	PRIMARY KEY (chat_participation_id)
);

DROP TABLE IF EXISTS chat_message;
CREATE TABLE chat_message (
	chat_message_id int NOT NULL AUTO_INCREMENT,
	chat_id         int NOT NULL,
	user_id         int NOT NULL,
	chat_session_id int NOT NULL,
	send_time       int NOT NULL,
	message         varchar(512) NOT NULL,
	PRIMARY KEY (chat_message_id)
);

DROP TABLE IF EXISTS tag;
CREATE TABLE tag (
	tag_id int NOT NULL AUTO_INCREMENT,
	name   varchar(64) NOT NULL,
	PRIMARY KEY(tag_id)
);

DROP TABLE IF EXISTS project_tag;
CREATE TABLE project_tag (
	project_tag_id int NOT NULL AUTO_INCREMENT,
	project_id     int NOT NULL,
	tag_id         int NOT NULL,
	PRIMARY KEY(project_tag_id)
);

DROP TABLE IF EXISTS user_tag;
CREATE TABLE user_tag (
	user_tag_id int NOT NULL AUTO_INCREMENT,
	user_id     int NOT NULL,
	tag_id      int NOT NULL,
	PRIMARY KEY(user_tag_id)
);

DROP TABLE IF EXISTS project_fav;
CREATE TABLE project_fav (
	project_fav_id int NOT NULL AUTO_INCREMENT,
	project_id     int NOT NULL,
	user_id        int NOT NULL,
	fav_time       int NOT NULL,
	PRIMARY KEY(project_fav_id)
);

DROP TABLE IF EXISTS project_news;
CREATE TABLE project_news (
	project_news_id int NOT NULL AUTO_INCREMENT,
	project_id      int NOT NULL,
	author_id       int NOT NULL,
	post_time       int NOT NULL,
	title 					varchar(128) NOT NULL,
	content         text NOT NULL,
	last_editor     int DEFAULT NULL,
	last_edit_time  int DEFAULT NULL,
	active          boolean NOT NULL DEFAULT 1,
	PRIMARY KEY(project_news_id)
);

DROP TABLE IF EXISTS picture;
CREATE TABLE picture (
	picture_id 			int NOT NULL AUTO_INCREMENT,
	file_path 			varchar(128) NOT NULL,
	upload_date 		int NOT NULL,
	uploader_id 		int NOT NULL,		
	PRIMARY KEY(picture_id)				
);

INSERT INTO picture (picture_id, file_path, upload_date, uploader_id) VALUES (1, "/public/images/default-banner.png", 0, 0);

/* PRESET TAGS */
INSERT INTO tag (name) VALUES 
("C"),("C++"),("C#"),("JavaScript"),("PHP"),("Java"),("Ruby"),("Perl"),("Objective-C"),("Python"),("SQL"),("MATLAB"),("ABAP"),("COBOL"),("Assembly"),
("VisualBasic"),("R"),("D"),("Delphi"),("SocialMedia"),("Administrative"),("Backend"),("Frontend"),("Server"),("3D"),("2D"),("OpenGL"),("DirectX"),
("SDL"),("Music"),("Sports"),("Recreative"),("Art"),("Economics"),("Politics"),("Learning"),("Technology"),("Robotics"),("ArtificialIntelligence"),("SystemsProgramming")
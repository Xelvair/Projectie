CREATE TABLE IF NOT EXISTS user (
	user_id int(11) NOT NULL AUTO_INCREMENT,
	create_time int(11) NOT NULL,
	email varchar(128) NOT NULL,
	username varchar(32) NOT NULL,
	password_salt varchar(8) NOT NULL,
	password_hash varchar(32) NOT NULL,
	PRIMARY KEY (user_id)
);

INSERT INTO user (user_id, email, username, password_salt, password_hash) VALUES
(1, 'admin@projectie.com', 'admin', 'a77239ba', '73bfd1bfe49f8b4e238c94f3a6a6ef4d');

CREATE TABLE IF NOT EXISTS project (
	project_id int NOT NULL AUTO_INCREMENT,
	creator_id int NOT NULL,
	create_time int NOT NULL,
	title varchar(256) NOT NULL,
	subtitle varchar(512) NOT NULL,
	description text NOT NULL,
	PRIMARY KEY(project_id)
);
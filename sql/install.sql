CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(128) NOT NULL,
  `username` varchar(32) NOT NULL,
  `password_salt` varchar(8) NOT NULL,
  `password_hash` varchar(32) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

INSERT INTO `user` (`user_id`, `email`, `username`, `password_salt`, `password_hash`) VALUES
(1, 'admin@projectie.com', 'admin', 'a77239ba', '73bfd1bfe49f8b4e238c94f3a6a6ef4d');

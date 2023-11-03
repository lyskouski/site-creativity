INSERT INTO `access` (`id`,`access_id`,`title`) VALUES (0,NULL,'LB_ACCESS_ADMIN');
INSERT INTO `access` (`id`,`access_id`,`title`) VALUES (6,NULL,'LB_ACCESS_OTHER');
INSERT INTO `action` (`id`,`url`,`action`) VALUES (1,'index','index');
INSERT INTO `access_action` (`id`,`action_id`,`access_id`,`permission`) VALUES (1,1,6,1);
INSERT INTO `user` (`id`, `username`, `cookie`) VALUES (0,'Anonimus',NULL);
INSERT INTO `user_access` (`id`, `user_id`, `access_id`) VALUES (1,0,6);
INSERT INTO `user` (`id`, `username`, `cookie`) VALUES (1,'Admin',NULL);
INSERT INTO `user_access` (`id`, `user_id`, `access_id`) VALUES (2,1,0);
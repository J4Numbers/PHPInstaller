/**
 * Copyright 2014 Matthew Ball (CyniCode/M477h3w1012)
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

CREATE TABLE `@images` (
  `image_id` integer(11) not null auto_increment,
  `image_type` varchar(25) not null default '',
  `image` blob not null,
  `image_size` varchar(25) not null default '',
  `image_ctgy` varchar(25) not null default '',
  `image_name` varchar(50) not null default '',
  primary key (`image_id`)
);

-- TODO: Make a default user image --
INSERT INTO `@images` (`image_type`,`image`,`image_size`,`image_ctgy`,`image_name`) VALUES
  ();

CREATE TABLE `@groups`(
  `group_id` integer(11) not null auto_increment,
  `group_name` varchar(64) not null,
  `group_color` varchar(6) not null,
  `group_info` text not null,
  primary key(`group_id`)
);

INSERT INTO `@groups` (`group_name`,`group_color`,group_info) VALUES
  ('Administrators','FF0000','The people running the damn thing'),
  ('Moderators','0000FF','The people who clean up yo\' shit'),
  ('Registered','5C5C5C','You.');

CREATE TABLE `@permissions`(
  `permission_id` integer(11) not null auto_increment,
  `permission_value` varchar(128) not null,
  primary key (`permission_id`)
);

INSERT INTO `@permissions` (`permission_value`) VALUES
  ('general.can_post_thread'),('general.can_post_reply'),('general.can_view'),
  ('general.can_quote'),('general.can_use_bbcode'),('general.can_post_polls'),
  ('general.can_link_images'),('general.can_use_links'),('general.can_report_posts'),
  ('group.can_edit_bio' /* 10 */ ),('group.can_post_reply'),('group.can_post_announcement'),
  ('group.can_remove_members'),('group.can_add_members'),('group.can_invite_members' /* 15 */ ),
  ('group.can_delete_threads'),('group.can_post_thread'),('group.can_view'),
  ('group.can_post_polls'),('group.can_sticky'),('group.can_lock'),
  ('group.can_warn_users'),('group.can_edit_users'),('group.can_edit_group_settings' /* 24 */ ),
  ('moderator.can_edit_posts'),('moderator.can_delete_posts'),('moderator.can_view'),
  ('moderator.can_move_threads'),('moderator.can_delete_threads'),('moderator.can_move_posts' /* 30 */ ),
  ('moderator.can_warn_users'),('moderator.can_post_thread'),('moderator.can_post_reply'),
  ('moderator.can_quote'),('moderator.can_use_bbcode'),('moderator.can_post_polls'),
  ('moderator.can_link_images'),('moderator.can_use_links'),('moderator.can_lock_posts' /* 39 */ ),
  ('admin.can_post_thread'),('admin.can_post_reply'),('admin.can_view'),
  ('admin.can_quote'),('admin.can_use_bbcode'),('admin.can_post_polls' /* 45 */ ),
  ('admin.can_link_images'),('admin.can_use_links'),('admin.can_edit_posts'),
  ('admin.can_warn_users'),('admin.can_delete_posts'),('admin.can_lock_posts'),
  ('admin.can_sticky_posts'),('admin.can_move_threads'),('admin.can_delete_threads'),
  ('admin.can_move_posts'),('admin.can_edit_users'),('admin.can_edit_forum'),
  ('admin.can_edit_groups'),('admin.can_edit_ranks'),('admin.can_edit_server_info' /* 60 */ ),
  ('admin.can_edit_mailer_settings'),('admin.can_view_admin_logs'),('admin.can_edit_post_settings'),
  ('admin.can_edit_thread_settings'),('admin.can_edit_signature_settings'),('admin.can_ban_user' /* 66 */ );

CREATE TABLE `@group_status` (
  `status_id` integer(11) NOT NULL AUTO_INCREMENT,
  `status_name` VARCHAR(64) NOT NULL,
  PRIMARY KEY (`status_id`)
);

INSERT INTO `@group_status` (`status_name`) VALUES
  ('user'),('moderator'),('admin');

CREATE TABLE `@status_permissions`(
  `status_id` integer(11) not null,
  `permission_id` integer(11) not null,
  FOREIGN KEY (`status_id`) REFERENCES `@group_status`(`status_id`),
  FOREIGN KEY (`permission_id`) REFERENCES `@permissions`(`permission_id`),
  UNIQUE KEY `status_permissions` (`status_id`,`permission_id`)
);

INSERT INTO `@status_permissions` (`status_id`,`permission_id`) VALUES
  ('1','11'),('1','17'), ('1','18'),('1','19'),
  ('2','11'),('2','17'), ('2','18'),('2','19'),
  ('2','12'),('2','13'), ('2','14'),('2','15'),
  ('2','20'),('2','21'), ('2','22'),('3','11'),
  ('3','17'),('3','18'), ('3','19'),('3','12'),
  ('3','13'),('3','14'), ('3','15'),('3','20'),
  ('3','21'),('3','22'), ('3','10'),('3','16'),
  ('3','23'),('3','24');

CREATE TABLE `@group_permissions`(
  `group_id` integer(11) not null,
  `permission_id` integer(11) not null,
  foreign key (`group_id`) REFERENCES `@groups`(`group_id`),
  foreign key (`permission_id`) REFERENCES `@permissions`(`permission_id`),
  UNIQUE KEY `group_permissions` (`group_id`,`permission_id`)
);

INSERT INTO `@group_permissions` (`group_id`,`permission_id`) VALUES
  ('1','1'),('1','2'),('1','3'),('1','4'),('1','5'),('1','6'),('1','7'),('1','8'),('1','9'),('1','10'),
  ('1','11'),('1','12'),('1','13'),('1','14'),('1','15'),('1','16'),('1','17'),('1','18'),('1','19'),('1','20'),
  ('1','21'),('1','22'),('1','23'),('1','24'),('1','25'),('1','26'),('1','27'),('1','28'),('1','29'),('1','30'),
  ('1','31'),('1','32'),('1','33'),('1','34'),('1','35'),('1','36'),('1','37'),('1','38'),('1','39'),('1','40'),
  ('1','41'),('1','42'),('1','43'),('1','44'),('1','45'),('1','46'),('1','47'),('1','48'),('1','49'),('1','50'),
  ('1','51'),('1','52'),('1','53'),('1','54'),('1','55'),('1','56'),('1','57'),('1','58'),('1','59'),('1','60'),
  ('1','61'),('1','62'),('1','63'),('1','64'),('1','65'),('1','66'),

  ('2','1'),('2','2'),('2','3'),('2','4'),('2','5'),('2','6'),('2','7'),('2','8'),('2','9'),('2','10'),
  ('2','11'),('2','12'),('2','13'),('2','14'),('2','15'),('2','16'),('2','17'),('2','18'),('2','19'),('2','20'),
  ('2','21'),('2','22'),('2','23'),('2','24'),('2','25'),('2','26'),('2','27'),('2','28'),('2','29'),('2','30'),
  ('2','31'),('2','32'),('2','33'),('2','34'),('2','35'),('2','36'),('2','37'),('2','38'),('2','39'),

  ('3','1'),('3','2'),('3','3'),('3','4'),('3','5'),('3','6'),('3','7'),('3','8'),('3','9');

CREATE TABLE `@ranks`(
  `rank_id` integer(11) not null auto_increment,
  `rank_name` varchar(64) not null,
  `rank_color` varchar(6) not null,
  primary key(`rank_id`)
);

INSERT INTO `@ranks` (`rank_name`,`rank_color`) VALUES
  ('','000000');

CREATE TABLE `@config`(
  `config_id` integer(11) not null auto_increment,
  `config_name` varchar(64) not null,
  `config_value` varchar(64) not null,
  primary key(`config_id`)
);

CREATE TABLE `@bbcode`(
  `code_id` integer(11) not null auto_increment,
  `code_rule` varchar(128) not null,
  `code_replacement` varchar(128) not null,
  primary key(`code_id`)
);

INSERT INTO `@bbcode` (`code_rule`,`code_replacement`) VALUES
  ('/(.*)?\[b\](.*)\[/b\](.*)?/i','$1<strong>$2</strong>$3'),
  ('/(.*)?\[i\](.*)\[/i\](.*)?/i','$1<em>$2</em>$3'),
  ('/(.*)?\[u\](.*)\[/i\](.*)?/i','$1<u>$2</u>$3'),
  ('/(.*)?\[img\](https?:\/\/[a-zA-Z0-9\.\/_]+[.]{1}[a-z]{2,5}[\/\w]*\.jpg|png|gif|jpeg|swf)\[/img](.*)?/i',
   '$1<img src=\'$2\'/>$3'),
  ('/(.*)?\[url=(https?:\/\/[a-zA-Z0-9\.\/_]+[.]{1}[a-z]{2,5}[\/\w]*)\](.*)\[/url\](.*)/i',
   '$1<url href=\'$2\'>$3</url>$4');

CREATE TABLE `@users`(
  `user_id` integer(11) not null auto_increment,
  `username` varchar(64) not null,
  `username_cased` varchar(64) not null,
  `primary_group_id` integer(11) not null default '3',
  `rank_id` integer(11) not null default '1',
  `user_email` varchar(128) not null,
  `password` varchar(64) not null,
  `time_reg` integer(11) not null,
  `time_pass_altered` integer(11) not null,
  `user_timezone` decimal(5,2) not null,
  `user_color` varchar(6),
  `user_avatar` integer(11) default '1',
  primary key(`user_id`),
  foreign key(`primary_group_id`) references `@groups`(`group_id`),
  foreign key(`rank_id`) references `@ranks`(`rank_id`),
  foreign key(`user_avatar`) references `@images`(`image_id`)
);

CREATE TABLE `@user_meta`(
  `user_id` integer(11) not null,
  `user_sig` TEXT default '',
  `user_bio` TEXT default '',
  FOREIGN KEY (`user_id`) REFERENCES `@users`(`user_id`)
);

CREATE TABLE `@user_permissions`(
  `user_id` integer(11) not null,
  `permission_id` integer(11) not null,
  `value` tinyint(1) not null default '1' comment '1 for true, 0 for false',
  FOREIGN KEY (`user_id`) REFERENCES `@users`(`user_id`),
  FOREIGN KEY (`permission_id`) REFERENCES `@permissions`(`permission_id`),
  UNIQUE KEY `user_permissions` (`user_id`,`permission_id`)
);

CREATE TABLE `@private_messages`(
  `priv_msg_id` integer(11) not null auto_increment,
  `sender_id` integer(11) not null,
  `receiver_id` integer(11) not null,
  `msg_contents` text not null,
  `msg_time` integer(11) not null,
  primary key(`priv_msg_id`),
  foreign key(`sender_id`) references `@users`(`user_id`),
  foreign key(`receiver_id`) references `@users`(`user_id`)
);

CREATE TABLE `@private_messages_user_mailing_list`(
  `priv_msg_id` integer(11) not null,
  `receiver_id` integer(11) not null,
  foreign key (`priv_msg_id`) references `@private_messages`(`priv_msg_id`),
  foreign key (`receiver_id`) references `@users`(`user_id`),
  constraint `unique_message_recip` unique (`priv_msg_id`,`receiver_id`)
);

CREATE TABLE `@private_messages_group_mailing_list`(
  `priv_msg_id` integer(11) not null,
  `group_id` integer(11) not null,
  foreign key (`priv_msg_id`) references `@private_messages`(`priv_msg_id`),
  foreign key (`group_id`) references `@groups`(`group_id`),
  constraint `unique_message_group` unique (`priv_msg_id`,`group_id`)
);

CREATE TABLE `@user_groups` (
  `user_id` integer(11) NOT NULL,
  `group_id` integer(11) NOT NULL,
  `joined_on` integer(11) NOT NULL,
  `status_id` INTEGER(11) NOT NULL,
  FOREIGN KEY (`user_id`) REFERENCES `@users`(`user_id`),
  FOREIGN KEY (`group_id`) REFERENCES `@groups`(`group_id`),
  FOREIGN KEY (`status_id`) REFERENCES `@group_status`(`status_id`),
  UNIQUE KEY `forum_user_groups` (`user_id`,`group_id`)
);

CREATE TABLE `@forums`(
  `forum_id` integer(11) not null  auto_increment,
  `forum_name` varchar(64) not null,
  `forum_access_group` integer(11) default '3',
  primary key(`forum_id`),
  FOREIGN KEY (`forum_access_group`) REFERENCES `@groups`(`group_id`)
);

INSERT INTO `@forums` (`forum_name`) VALUES
  ('Welcome to the Cyni-Forums');

CREATE TABLE `@forum_group_permissions`(
  `forum_id` integer(11) not null,
  `group_id` integer(11) not null,
  `code` tinyint(1) DEFAULT '1' COMMENT '1 is allowed, 0 is blocked',
  FOREIGN KEY (`forum_id`) REFERENCES `@forums`(`forum_id`),
  FOREIGN KEY (`group_id`) REFERENCES `@groups`(`group_id`)
);

CREATE TABLE `@forum_user_permissions`(
  `forum_id` integer(11) not null,
  `user_id` integer(11) not null,
  `code` tinyint(1) DEFAULT '1' COMMENT '1 is allowed, 0 is blocked',
  FOREIGN KEY (`forum_id`) REFERENCES `@forums`(`forum_id`),
  FOREIGN KEY (`user_id`) REFERENCES `@users`(`user_id`)
);

CREATE TABLE `@categories`(
  `category_id` integer(11) not null auto_increment,
  `forum_id` integer(11) not null,
  `category_title` varchar(64) not null,
  `category_info` varchar(255),
  `category_access_group` integer(11) default '3',
  primary key(`category_id`),
  foreign key(`forum_id`) references `@forums`(`forum_id`),
  FOREIGN KEY (`category_access_group`) REFERENCES `@groups`(`group_id`)
);

INSERT INTO `@categories` (`forum_id`,`category_title`) VALUES
  ('1','Hi!');

CREATE TABLE `@cat_group_permissions`(
  `category_id` integer(11) not null,
  `group_id` integer(11) not null,
  `code` tinyint(1) DEFAULT '1' COMMENT '1 is allowed, 0 is blocked',
  FOREIGN KEY (`category_id`) REFERENCES `@categories`(`category_id`),
  FOREIGN KEY (`group_id`) REFERENCES `@groups`(`group_id`)
);

CREATE TABLE `@cat_user_permissions`(
  `category_id` integer(11) not null,
  `user_id` integer(11) not null,
  `code` tinyint(1) DEFAULT '1' COMMENT '1 is allowed, 0 is blocked',
  FOREIGN KEY (`category_id`) REFERENCES `@categories`(`category_id`),
  FOREIGN KEY (`user_id`) REFERENCES `@users`(`user_id`)
);

CREATE TABLE `@threads`(
  `thread_id` integer(11) not null auto_increment,
  `user_id` integer(11) not null,
  `created_on` integer(11) not null,
  `title` varchar(255) not null,
  `last_updated` integer(11) not null,
  `updated_by` integer(11) not null,
  `thread_access_group` integer(11) default '3',
  primary key(`thread_id`),
  foreign key(`user_id`) references `@users`(`user_id`),
  foreign key(`updated_by`) references `@users`(`user_id`),
  FOREIGN KEY (`thread_access_group`) REFERENCES `@groups`(`group_id`)
);

CREATE TABLE `@thread_group_permissions`(
  `thread_id` integer(11) not null,
  `group_id` integer(11) not null,
  `code` tinyint(1) DEFAULT '1' COMMENT '1 is allowed, 0 is blocked',
  FOREIGN KEY (`thread_id`) REFERENCES `@threads`(`thread_id`),
  FOREIGN KEY (`group_id`) REFERENCES `@groups`(`group_id`)
);

CREATE TABLE `@thread_user_permissions`(
  `thread_id` integer(11) not null,
  `user_id` integer(11) not null,
  `code` tinyint(1) DEFAULT '1' COMMENT '1 is allowed, 0 is blocked',
  FOREIGN KEY (`thread_id`) REFERENCES `@threads`(`thread_id`),
  FOREIGN KEY (`user_id`) REFERENCES `@users`(`user_id`)
);

CREATE TABLE `@posts`(
  `post_uid` bigint not null auto_increment,
  `thread_id` integer(11) not null,
  `user_id` integer(11) not null,
  `time_posted` integer(11) not null,
  `post_content` text not null,
  primary key(`post_uid`),
  foreign key(`thread_id`) references `@threads`(`thread_id`),
  foreign key(`user_id`) references `@users`(`user_id`)
);
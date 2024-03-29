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

CREATE TABLE `@admins`(
  `user_id` integer(11) not null auto_increment,
  `username` varchar(64) not null,
  `username_cased` varchar(64) not null,
  `user_email` varchar(128) not null,
  `password` varchar(64) not null,
  `time_reg` integer(11) not null,
  `time_pass_altered` integer(11) not null,
  primary key(`user_id`)
);
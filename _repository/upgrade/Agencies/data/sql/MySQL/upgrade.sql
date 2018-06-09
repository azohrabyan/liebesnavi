

ALTER TABLE PH7_Messenger ADD COLUMN chatter_id INT(10) UNSIGNED DEFAULT 0;

ALTER TABLE PH7_Members ADD COLUMN is_fake BOOL DEFAULT false;

CREATE TABLE IF NOT EXISTS PH7_Chatter (
  chatter_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  name VARCHAR(255) DEFAULT 0,
  username VARCHAR(40),
  password VARCHAR(120),
  agency_id INT(10) DEFAULT 0,
  status VARCHAR(10) default '',
  enabled tinyint(1) unsigned DEFAULT 1,
  PRIMARY KEY (chatter_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS PH7_ChatterChats (
  chatter_chat_id int(10) unsigned not null auto_increment,
  chatter_id int(10) unsigned not null,
  fake_user varchar(40) not null,
  chat_partner varchar(40) not null,
  created datetime not null,
  primary key (chatter_chat_id),
  constraint unq_chat_pair unique (fake_user, chat_partner),
  constraint foreign key fk_chatter_id (chatter_id) REFERENCES PH7_Chatter (chatter_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS PH7_ChatAgency (
  agency_id int(10) unsigned not null auto_increment,
  agency_name varchar(40) not null,
  admin_username varchar(40),
  password varchar(120),
  primary key (agency_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
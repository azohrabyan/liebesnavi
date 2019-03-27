

ALTER TABLE PH7_Messenger ADD COLUMN chatter_id INT(10) UNSIGNED DEFAULT 0;

ALTER TABLE PH7_Members ADD COLUMN is_fake BOOL DEFAULT false;

CREATE TABLE IF NOT EXISTS PH7_ChatAgency (
  profileId int(10) unsigned not null auto_increment,
  agency_name varchar(40) not null,
  username varchar(40),
  email varchar(100),
  password varchar(120),
  lastActivity datetime null,
  primary key (profileId),
  index pk_agency_id using btree (profileId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table if not exists PH7_ChatAgencyAttemptsLogin
(
  attemptsId int unsigned auto_increment
    primary key,
  ip         varchar(45) default '' not null,
  attempts   smallint(5) unsigned   not null,
  lastLogin  datetime               not null,
  constraint ip
  unique (ip)
)
  engine = InnoDB
  charset = utf8;

create table PH7_ChatAgencyLogLogin
(
  logId    mediumint(10) unsigned auto_increment
    primary key,
  email    varchar(120) default ''             not null,
  username varchar(64) default ''              not null,
  password varchar(40)                         null,
  status   varchar(60) default ''              not null,
  ip       varchar(45) default ''              not null,
  dateTime timestamp default CURRENT_TIMESTAMP not null
  on update CURRENT_TIMESTAMP
)
  engine = InnoDB
  charset = utf8;

create table if not exists PH7_ChatAgencyLogSess
(
  profileId    int unsigned                    not null
    primary key,
  username     varchar(40)                         null,
  password     varchar(240)                        null,
  email        varchar(120)                        null,
  firstName    varchar(50)                         null,
  lastName     varchar(50)                         null,
  sessionHash  varchar(40)                         not null,
  idHash       char(32)                            not null,
  lastActivity int unsigned                        not null,
  location     varchar(255)                        null,
  ip           varchar(45) default '127.0.0.1'     not null,
  userAgent    varchar(100)                        not null,
  guest        smallint unsigned default '1'       not null,
  dateTime     timestamp default CURRENT_TIMESTAMP not null
  on update CURRENT_TIMESTAMP,
  constraint PH7_ChatAgencyLogSess_ibfk_1
  foreign key (profileId) references PH7_ChatAgency (profileId)
)
  engine = InnoDB
  charset = utf8;

create index lastActivity
  on PH7_ChatAgencyLogSess (lastActivity);

create index sessionHash
  on PH7_ChatAgencyLogSess (sessionHash);

CREATE TABLE IF NOT EXISTS PH7_Chatter (
  profileId int(10) unsigned NOT NULL AUTO_INCREMENT,
  name VARCHAR(255) DEFAULT 0,
  email VARCHAR(100) DEFAULT 0,
  username VARCHAR(40),
  password VARCHAR(120),
  agency_id INT(10) unsigned DEFAULT 0,
  status VARCHAR(10) default '',
  enabled tinyint(1) unsigned DEFAULT 1,
  lastActivity datetime null,
  PRIMARY KEY (profileId),
  constraint foreign key fk_agency_id (agency_id) references PH7_ChatAgency (profileId) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS PH7_ChatterChats (
  chatter_chat_id int(10) unsigned not null auto_increment,
  chatter_id int(10) unsigned not null,
  fake_user varchar(40) not null,
  chat_partner varchar(40) not null,
  created datetime not null,
  primary key (chatter_chat_id),
  constraint unq_chat_pair unique (fake_user, chat_partner),
  constraint foreign key fk_chatter_id (chatter_id) REFERENCES PH7_Chatter (profileId) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE PH7_Chatter ADD COLUMN lastActivity datetime null;
ALTER TABLE PH7_ChatAgency ADD COLUMN lastActivity datetime null;

CREATE TABLE IF NOT EXISTS PH7_ChatterNotes (
  chatter_note_id int(10) unsigned not null auto_increment,
  chatter_id int(10) unsigned not null,
  fake_user varchar(40) not null,
  chat_partner varchar(40) not null,
  notes text,
  created datetime not null,
  primary key (chatter_note_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- schhedule_dev.sql  Wed Aug 28 08:35:37 CDT 2019
-- This web application is here to develop a cleaner, more efficient
-- table/editor frontend for a database. The basic working idea is
-- that of a list of members, which can be a subordinate, a
-- supervisor, or have some more complex relationship. It basically 
-- determines relationships between member sand when they are scheduled
-- to interact with each other.
-- 

CREATE DATABASE schedule;

CREATE USER 'schedule_admin'@'localhost' IDENTIFIED BY 'minim_d0Gcart';
CREATE USER 'schedule_admin'@'%' IDENTIFIED BY 'minim_d0Gcart';

GRANT  ALL ON schedule.*   TO 'schedule_admin'@'localhost';
GRANT  ALL ON schedule.*   TO 'schedule_admin'@'%';

-- INSERT INTO member (authority,member_id,last_name,first_name,email) VALUES (10, "stopher","Schulte","Christopher","cfschulte@humonc.wisc.edu");
-- INSERT INTO member (authority,member_id,last_name,first_name,email) VALUES (10, "nprew","Rew","Natalie","natalierew@gmail.com");
-- INSERT INTO member (authority,member_id,last_name,first_name,email) VALUES (1, "schultz","Schultz","Mathew","schultz@humonc.wisc.edu");

-- 
-- member - 
-- This is the basic information about a user.
--  do we need a password for this or are we going to 
--  assume that this will be under something else like 
--  a wordpress login or shibboleth group?
DROP TABLE IF EXISTS `member`;
CREATE TABLE `member` (
  `id`                INT NOT NULL AUTO_INCREMENT,
  `authority`         INT DEFAULT 0,
  `member_id`         VARCHAR(16) NOT NULL,
  `last_name`         VARCHAR(32) DEFAULT NULL,
  `other_names`       VARCHAR(128) DEFAULT NULL,
  `first_name`        VARCHAR(32) DEFAULT NULL,
  `phone`             VARCHAR(32) DEFAULT NULL,
  `email`             VARCHAR(64) DEFAULT NULL,
  `address`           VARCHAR(256) DEFAULT NULL,
  `city`              VARCHAR(256) DEFAULT NULL,
  `state_provence`    VARCHAR(256) DEFAULT NULL,
  `country`           VARCHAR(256) DEFAULT NULL,
  `zipcode`           VARCHAR(16) DEFAULT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (authority) REFERENCES authority (`id`)
)  ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


--
-- relationship 
--  Do we want to handle dates here? How do we actually 
--  define relationship_type? For something like a 
--  lover/lover, we would make a bijective (i.e. two
--  relationship entries) mapping. This is analogous to 
--  the pointer list in a member "structure."

DROP TABLE IF EXISTS `relationship`;
CREATE TABLE `relationship` (
  `id`                INT NOT NULL AUTO_INCREMENT,
  `from_member`       INT NOT NULL REFERENCES member (`id`),
  `to_member`         INT NOT NULL REFERENCES member (`id`),
  `relationship_type` INT ,
  PRIMARY KEY (`id`),
  FOREIGN KEY (relationship_type)  REFERENCES relationship_type (`id`)
)  ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


--
-- relationship_type 
--  Here, we are going to let the user define the relationship 
--  types. For example, supervisor/supervised, czar/volunteer...

DROP TABLE IF EXISTS `relationship_type`;
CREATE TABLE `relationship_type` (
  `id`              INT NOT NULL AUTO_INCREMENT,
  `description`     VARCHAR(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
)  ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

LOAD DATA LOCAL INFILE 'relationship_type.txt' INTO TABLE relationship_type FIELDS TERMINATED BY '\t' LINES TERMINATED BY '\n' (id, description);

--
-- authority 
--

DROP TABLE IF EXISTS `authority`;
CREATE TABLE `authority` (
  `id`              INT NOT NULL AUTO_INCREMENT,
  `description`     VARCHAR(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
)  ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

LOAD DATA LOCAL INFILE 'authority.txt' INTO TABLE authority FIELDS TERMINATED BY '\t' LINES TERMINATED BY '\n' (id, description);


-- 
-- Event/Appointment/occasion/engagement/commitment
-- 



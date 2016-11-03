CREATE DATABASE adshow;
USE adshow;

CREATE TABLE department
(
  ID INT(10) unsigned PRIMARY KEY NOT NULL AUTO_INCREMENT,
  department VARCHAR(45) NOT NULL,
  title VARCHAR(255)
);
CREATE TABLE playlist
(
  ID INT(10) unsigned PRIMARY KEY NOT NULL AUTO_INCREMENT,
  name VARCHAR(45) NOT NULL,
  active SMALLINT(5) unsigned DEFAULT '1' NOT NULL,
  departmentIDfk INT(10) unsigned NOT NULL,
  global VARCHAR(45) DEFAULT '0',
  screenOrientation TINYINT(1) DEFAULT '0' NOT NULL,
  CONSTRAINT playlist_department_ID_fk FOREIGN KEY (departmentIDfk) REFERENCES department (ID)
);
CREATE INDEX playlist_department_index ON playlist (departmentIDfk);
CREATE TABLE screen
(
  ID INT(10) unsigned PRIMARY KEY NOT NULL AUTO_INCREMENT,
  location VARCHAR(45) NOT NULL,
  orientation TINYINT(4) DEFAULT '0' NOT NULL,
  departmentIDfk INT(10) unsigned,
  pcName VARCHAR(255),
  playlistIDfk INT(10) unsigned,
  CONSTRAINT screen_department_ID_fk FOREIGN KEY (departmentIDfk) REFERENCES department (ID),
  CONSTRAINT screen_playlist_ID_fk FOREIGN KEY (playlistIDfk) REFERENCES playlist (ID)
);
CREATE INDEX screen_department_ID_fk ON screen (departmentIDfk);
CREATE INDEX screen_playlist_index ON screen (playlistIDfk);
CREATE TABLE slide
(
  ID INT(10) unsigned PRIMARY KEY NOT NULL AUTO_INCREMENT,
  active TINYINT(3) unsigned DEFAULT '1' NOT NULL,
  title VARCHAR(255),
  text TEXT,
  playtime INT(10) unsigned,
  templateName VARCHAR(255) NOT NULL,
  playlistID INT(10) unsigned NOT NULL,
  imageURL VARCHAR(255),
  timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
  markdownEnabled TINYINT(1) unsigned DEFAULT '1' NOT NULL,
  CONSTRAINT slide_playlist_ID_fk FOREIGN KEY (playlistID) REFERENCES playlist (ID)
);
CREATE INDEX slide_playlist_ID_fk ON slide (playlistID);
CREATE TABLE user
(
  ID INT(10) unsigned PRIMARY KEY NOT NULL AUTO_INCREMENT,
  sNumber VARCHAR(8) DEFAULT '' NOT NULL,
  departmentIDfk INT(10) unsigned,
  permission TINYINT(11) unsigned DEFAULT '0' NOT NULL,
  firstname TEXT,
  lastname TEXT,
  owner TINYINT(11) unsigned DEFAULT '0' NOT NULL,
  global VARCHAR(45) DEFAULT '0',
  CONSTRAINT user_department_fk FOREIGN KEY (departmentIDfk) REFERENCES department (ID)
);
CREATE UNIQUE INDEX sNumber ON user (sNumber);
CREATE INDEX user_department_fk_idx ON user (departmentIDfk);
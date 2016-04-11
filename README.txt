Project folder 'github-api' can be installed and run using MAMP/WAMP in your local web server directory:

e.g.:       /Applications/MAMP/htdocs/github-api
web path:   http://localhost/ivankanski/github-api/


Requires PHP 5.5+ to support generator iterators.
PHP Server Version (Development): 5.5.42
PHP compiled with PDO library (default as of PHP 5.4) --with-pdo-mysql=mysqlnd

MySQL Version: 5.5.26

MySQL connection params can be found and modified in 'github-api/db_connect.php'.
(Would not normally put connect info in the public application directory on a production site but did so here for convenience.)

**********************************************************
SQL to create database and table structure:
**********************************************************
-- Host: localhost
-- Generation Time: Apr 11, 2016 at 11:44 AM
-- MySQL Server version: 5.5.42
-- PHP Version: 5.5.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

CREATE DATABASE IF NOT EXISTS `github_projects` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `github_projects`;

CREATE TABLE IF NOT EXISTS `repos` (
  `repo_id` int(10) unsigned NOT NULL,
  `repo_name` varchar(64) NOT NULL,
  `repo_url` varchar(128) NOT NULL,
  `repo_created` datetime NOT NULL,
  `repo_pushed` datetime NOT NULL,
  `repo_descript` varchar(512) NOT NULL,
  `repo_stars` mediumint(8) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `repos`
  ADD PRIMARY KEY (`repo_id`);

**********************************************************
Change DB User and Password in 'db_connect.php' to an existing db user or assign the one already defined to this database.
**********************************************************
$user       = 'webuser';
$password   = 'webtest';
$dbname     = 'github_projects';
$host       = 'localhost';
$port       = 3306;

**********************************************************

Application info:

There are 4 class member properties that can be set after instantiating the class as shown at the top of index.php. If they are not set default values will be used.

Since this was a basic one page app I didn't involve the overhead of a framework or display templating engine.

The API repository projects returned are stored in the `repos` table, and updated if their values change in subsequent calls.

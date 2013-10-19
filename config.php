<?php
/*-----------------------
Nakid Setup Configuration
-----------------------*/
//INFORMATION IS CORRECT - Set to 'true' once all the information in this file is correct
define('NAKID_READY', true);
//DATABASE INFORMATION
define('NAKID_DBHOSTNAME', 'localhost'); //The hostname of your database server.
define('NAKID_DBUSERNAME', 'root'); //The username used to connect to the database
define('NAKID_DBPASSWORD', ''); //The password used to connect to the database
define('NAKID_DBDATABASE', 'nakid'); //The name of the database you want to connect to
//WEBSITE INFORMATION
define('NAKID_WEBSITE', 'http://localhost'); //The domain you are installing Nakid on (ie "mywebsite.com")
define('NAKID_PATH', '/cms/'); //Path to where you are installing Nakid (With starting and ending slash "/cms/")
define('NAKID_KEY', 'bG9jYWxob3N0'); //Licensing For Nakid CMS
//OPTIONAL VALUES
define('NAKID_TABLE_PREFIX','nakid_'); //Prefix for all tables made by Nakid
define('NAKID_ENCRYPTION_KEY','N4k1d_CM5'); //Change this key to a random string, ie: r32Nd3Ks4N2
define('NAKID_THEME','default'); //The visual theme nakid will use
/*-----------------------
THE VALUES BELOW SHOULD NOT NEED TO BE CHANGED
-------------------------*/
define('NAKID_URL', NAKID_WEBSITE.NAKID_PATH);
/*-----------------------
NAKID CMS is developed and maintained by Jeff Kilroy (jeffkilroy.com)
-------------------------*/
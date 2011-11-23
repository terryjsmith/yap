<?php

/****************************************

  Copyright 2010 Terry J. Smith

  Licensed under the Apache License, Version 2.0 (the "License");
  you may not use this file except in compliance with the License.
  You may obtain a copy of the License at

  http://www.apache.org/licenses/LICENSE-2.0

  Unless required by applicable law or agreed to in writing, software
  distributed under the License is distributed on an "AS IS" BASIS,
  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
  See the License for the specific language governing permissions and
  limitations under the License.

****************************************/

/* You can edit some stuff starting here */

// Change this value to define whether this is a production server; this will affect error reporting
define('PRODSERV', false);

// Directory definitions
define('ROOT_DIR', getcwd() . '/');
define('LIB_DIR', ROOT_DIR . 'lib/');
define('VIEW_DIR', ROOT_DIR . 'views/');
define('MODEL_DIR', ROOT_DIR . 'models/');
define('CONTROLLER_DIR', ROOT_DIR . 'controllers/');
define('CONFIG_DIR', ROOT_DIR . 'config/');

/* Stop editing here */

include(LIB_DIR . 'utility.php');

// Set error level
if(PRODSERV)
	error_reporting(0);
else
	error_reporting(E_ALL);

// Set up the library includes
set_include_path(CONTROLLER_DIR . PATH_SEPARATOR . LIB_DIR . PATH_SEPARATOR . MODEL_DIR . PATH_SEPARATOR . get_include_path());

// Include database settings
include(CONFIG_DIR . 'database.php');

// Make sure we can autoload files
function __autoload($class)
{
	if(!@include("$class.php"))
		@include(strtolower($class).".php");
}

// Get our connection to the database
$dbtype = DB_TYPE;
$dbconn = new $dbtype;
if(strlen(DB_HOST) && strlen(DB_USER) && strlen(DB_PASS) && strlen(DB_NAME))
	$dbconn->initialize(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Set up our routing controller
$routing_controller = new RoutingController;

?>

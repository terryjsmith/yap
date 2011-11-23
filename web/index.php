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

// Include our initialization file
require(dirname(getcwd()) . "/lib/init.php");

// Grab the RoutingController object and route this call to the appropriate controller/module
global $routing_controller;
$routing_controller->route();

?>

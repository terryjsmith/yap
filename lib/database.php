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

// Our abstract database class on which we can build actual database implementations (MySQL, Postgres, Oracle, etc.)
abstract class Database {
	protected $conn = 0;
	protected $server_list = array();

	abstract public function initialize($host, $user, $pass, $database);
	abstract public function query($query, $vars = array());
	abstract public function object_query($query, $vars = array());
	abstract public function array_query($query, $vars = array());
	abstract public function object_array_query($query, $vars = array());
}


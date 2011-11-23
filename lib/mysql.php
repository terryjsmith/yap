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

// Our MySQL implementation of the abstract database class
class MySQL extends Database {
	// Initialize the database connection
	function initialize($host, $user, $pass, $database) {
		if(is_array($host))
			$this->server_list = $host;
		else
			array_push($this->server_list, $host);

		$count = 0;
		while(!$this->conn) {
			if($count > count($this->server_list))
				break;

			$this->conn = @mysql_pconnect($this->server_list[rand(0, sizeof($this->server_list) - 1)], $user, $pass);
			$count++;
		}

		if($this->conn)
			@mysql_select_db($database, $this->conn);
	}

	// Execute a query, returning the raw results
	function query($query, $vars = array()) {
		foreach($vars as $key => $value)
		{
			$value = mysql_real_escape_string($value);
			$query = str_replace(":$key", $value, $query);
		}

		$results = @mysql_query($query, $this->conn);
		if(mysql_errno())
			error_log("MySQL Error: ".mysql_error()."\nQuery: ".$query);

		if(LOG_QUERY_COUNT) {
			global $__query_count;
			$__query_count++;
		}

		return($results);
	}

	// Execute a query, returning a single object
	function object_query($query, $vars = array()) {
		$results = $this->query($query, $vars);
		if(@mysql_num_rows($results))
		{
			return(@mysql_fetch_object($results));
		}

		return(false);
	}

	// Execute a query, returning an array of results
	function array_query($query, $vars = array()) {
		$results = $this->query($query, $vars);
                if(@mysql_num_rows($results))
                {
                        return(@mysql_fetch_array($results));
                }

                return(false);
	}

	// Execute a query, returning an array of objects
	function object_array_query($query, $vars = array()) {
		$results = $this->query($query, $vars);
		if(mysql_num_rows($results))
                {
                        $array = array();
			$object = @mysql_fetch_object($results);
			while(is_object($object))
			{
				array_push($array, $object);
				$object = @mysql_fetch_object($results);
			}

			if(!sizeof($array))
				return(false);

			return($array);
                }

                return(false);
	}
}


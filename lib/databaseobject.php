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

// Our core database object class; this will be extended by our models
class DatabaseObject {
	// Whether this is a new row or not
	protected $new_row = false;

	// The primary key and primary key value to update a row
	protected $primary_key = "";
	protected $primary_key_value = "";

	// If this is a new row, set the appropriate flag and then save it
	function create() {
		$this->new_row = true;
		$this->save();
	}

	// Fetch an object of this object type from the database
	static function fetch($vars = array(), $orderby = array()) {
		global $dbconn;

		// Get the table name
		$table = strtolower(get_called_class());
		$type = get_called_class();

		// Put together the query
		$lookups = array();
		foreach($vars as $key => $value) {
			$value = mysql_real_escape_string($value);
			array_push($lookups, "`$key` = '$value'");
		}
		$search = implode(" AND ", $lookups);

		// Query our DB
		if(count($vars))
			$query = "SELECT * FROM $table WHERE $search";
		else
			$query = "SELECT * FROM $table";

		if(sizeof($orderby)) {
			if(isset($orderby['col'])) {
				$query .= " ORDER BY `{$orderby['col']}`";
				if(isset($orderby['order']))
					$query .= " {$orderby['order']}";
			}
		}

		$result = $dbconn->object_query($query);

		if(is_object($result)) {
			$class = new $type;
			foreach(get_object_vars($result) as $var => $value)
				$class->$var = $value;

			$key = $class->_getPrimaryKey();
			$class->_setPrimaryKeyValue($result->$key);
		}
		else
			return(false);

		return($class);
	}

	function _getPrimaryKeyValue() {
		return($this->primary_key_value);
	}

	function _getPrimaryKey() {
		return($this->primary_key);
	}

	// Set the primary key
	function _setPrimaryKey($key) {
		$this->primary_key = $key;
	}

	// Set the primary key value for updating
	function _setPrimaryKeyValue($value) {
		$this->primary_key_value = $value;
	}

	// Get all of the columns for this table
	function getColumns() {
		global $dbconn;

                // Get the table name
                $table = strtolower(get_called_class());

		// Get ready to pass back
		$variables = array();
		$result = $dbconn->object_array_query("SHOW COLUMNS FROM $table");

		// Get the primary key so we don't include it
		$primary_key = $this->_getPrimaryKey();
		if(is_array($result)) {
			foreach($result as $row) {
				if($row->Field != $primary_key)
					array_push($variables, $row->Field);
			}
		}

		return($variables);
	}

	// Fetch an array of objects from the database that meet these params
	static function fetchArray($vars = array(), $orderby = array()) {
		global $dbconn;

		// Get the table name
                $table = strtolower(get_called_class());

                // Put together the query
                $lookups = array();
                foreach($vars as $key => $value) {
			$value = mysql_real_escape_string($value);
                        array_push($lookups, "`$key` = '$value'");
		}
                $search = implode(" AND ", $lookups);

		// Query our DB
                if(count($vars))
                        $query = "SELECT * FROM $table WHERE $search";
                else
                        $query = "SELECT * FROM $table";

                if(sizeof($orderby)) {
                        if(isset($orderby['col'])) {
                                $query .= " ORDER BY `{$orderby['col']}`";
                                if(isset($orderby['order']))
                                        $query .= " {$orderby['order']}";
                        }
                }

                $result = $dbconn->object_array_query($query);

		if(is_array($result)) {
			$collection = array();
			foreach($result as $object) {
				$name = get_called_class();
				$obj = new $name;
				
				foreach(get_object_vars($object) as $var => $value)
					$obj->$var = $value;

				$key = $obj->_getPrimaryKey();
				$obj->_setPrimaryKeyValue($obj->$key);

				array_push($collection, $obj);
			}

			return($collection);
		}
		else
			return(array());
	}

	// Save this object to the database
	function save() {
		global $dbconn;

		// Get the MySQL columns to update
		$cols = implode(",", get_object_vars($this));

		// Get the values to update
		$vals = array();
		foreach(get_object_vars($this) as $key => $val) {
			if(!in_array($key, array_keys(get_class_vars('DatabaseObject')))) {
				if(!$this->new_row) {
					if(!strcmp($key, $this->_getPrimaryKey()))
						continue;
				}

				$val = mysql_real_escape_string($val);
				$vals[$key] = "'$val'";
			}
		}

		$values = implode(',', $vals);

		// Make those values usable for our MySQL class
		$keys = array_keys($vals);
		for($i = 0; $i < sizeof($keys); $i++)
			$keys[$i] = "`{$keys[$i]}`";

		$cols = implode(",", $keys);

		// Get the table name from the class
		$table = strtolower(get_class($this));

		if($this->new_row) {
			$dbconn->query("INSERT INTO $table ($cols) VALUES($values)");
			$this->{$this->primary_key} = mysql_insert_id();
			return(mysql_insert_id());
		}
		else {
			$updates = array();
			foreach($vals as $key => $value) {
				if((!in_array($key, array_keys(get_class_vars('DatabaseObject')))) && strcmp($key, $this->primary_key)) {
					array_push($updates, "`$key` = $value");
				}
			}

			$values = implode(",", $updates);
			$key = $this->primary_key;
			$dbconn->query("UPDATE $table SET $values WHERE `$key` = '{$this->primary_key_value}'");
		}
	}
}


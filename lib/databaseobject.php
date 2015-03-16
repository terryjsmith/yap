<?php

// Our core database object class; this will be extended by our models
class DatabaseObject {
	// Whether this is a new row or not
	protected $new_row = false;

	// The primary key and primary key value to update a row
	static protected $primary_key = "";

	// If this is a new row, set the appropriate flag and then save it
	function create() {
		$this->new_row = true;
		$this->save();
	}

	// Fetch an object of this object type from the database
	static function fetch($vars = array(), $orderby = array()) {
		$dbconn = DatabaseFactory::get();
		
		$table = strtolower(get_called_class());
		$result = $dbconn->object_query($table, $vars, $orderby, $table);
		return($result);
	}

	// Fetch an array of objects from the database that meet these params
	static function fetch_array($vars = array(), $orderby = array()) {
		$dbconn = DatabaseFactory::get();

                $table = strtolower(get_called_class());
                $result = $dbconn->object_array_query($table, $vars, $orderby, $table);
		return($result);
	}

	// Save this object to the database
	function save() {
		$dbconn = DatabaseFactory::get();

		// Get the database columns to update
		$cols = implode(",", get_object_vars($this));

		// Get the values to update
		$vals = array();
		foreach(get_object_vars($this) as $key => $val) {
			if($key == self::$primary_key) continue;

			$vals[$key] = $this->$key;
		}

		// Get the table name from the class
		$table = strtolower(get_class($this));

		// If this is a new row, add it
		if($this->new_row) {
			$this->{self::$primary_key} = $dbconn->insert($table, $vals);
			return($this->{self::$primary_key});
		}
		
		// Otherwise, do an update
		$search = array(self::$primary_key => $this->{self::$primary_key});
		$dbconn->update($table, $search, $vals);
	}
}


<?php

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

			$this->conn = new mysqli($this->server_list[rand(0, sizeof($this->server_list) - 1)], $user, $pass, $database);
			if($this->conn->connect_error) {
				error_log("Unable to connect to MySQL: {$this->conn->connect_error}");
				$this->conn = false;
			}
			$count++;
		}
	}

	// Execute a query, returning the raw results
	function query($table, $vars = array(), $flags = array()) {
		// Put together the query
                $lookups = array();
                foreach($vars as $key => $value) {
                        array_push($lookups, "`$key` = ':$key'");
                }
                $search = implode(" AND ", $lookups);

                // Query our DB
                if(count($vars))
                        $query = "SELECT * FROM `$table` WHERE $search";
                else
                        $query = "SELECT * FROM `$table`";

                if(sizeof($flags)) {
                        if(isset($flags['ordercol'])) {
                                $query .= " ORDER BY `{$flags['ordercol']}`";
                                if(isset($flags['order']))
                                        $query .= " {$flags['order']}";
                        }
                }

		foreach($vars as $key => $value)
		{
			$value = $this->conn->real_escape_string($value);
			$query = str_replace(":$key", $value, $query);
		}

		if(!$result = $this->conn->query($query))
			error_log("MySQL Error: " . $this->conn->error . "\nQuery: " . $query);

		return($result);
	}

	// Execute a query, returning a single object
	function object_query($query, $vars = array(), $flags = array(), $object_type = 'stdClass') {
		$result = $this->query($query, $vars);
		if(@$result->num_rows) {
			return(@$result->fetch_object($object_type));
		}

		return(false);
	}

	// Execute a query, returning an array of results
	function array_query($table, $vars = array(), $flags = array()) {
		$result = $this->query($query, $vars);
		if(@$result->num_rows) {
                        return(@$result->fetch_array(MYSQLI_ASSOC));
                }

                return(false);
	}

	// Execute a query, returning an array of objects
	function object_array_query($table, $vars = array(), $flags = array(), $object_type = 'stdClass') {
		$result = $this->query($table, $vars);
		if(@$result->num_rows) {	
                        $array = array();
			while($object = $result->fetch_object($object_type)) {
				array_push($array, $object);
			}

			if(!sizeof($array))
				return(false);

			return($array);
                }

                return(false);
	}

	function insert($table, $vars = array()) {
		// Break out the columns and values
                $cols = array();
		$values = array();
                foreach($vars as $key => $value) {
			if($key == 'new_row') continue;
			array_push($cols, "`$key`");
			array_push($values, "'" . $this->conn->real_escape_string($value) . "'");
                }

		$keys = implode(',', $cols);
		$values = implode(',', $values);

		$query = "INSERT INTO `$table` ($keys) VALUES($values)";

                if(!$result = $this->conn->query($query))
                        error_log("MySQL Error: " . $this->conn->error . "\nQuery: " . $query);

                return($this->conn->insert_id);
	}

        function delete($table, $vars = array()) {
		$values = array();
		foreach($vars as $key => $value) {
			array_push($values, "`$key` = '" . $this->conn->real_escape_string($value) . "'");
		}

		$search = implode(" AND ", $values);
		$query = "DELETE FROM `$table` WHERE $search";

		if(!$result = $this->conn->query($query))
                        error_log("MySQL Error: " . $this->conn->error . "\nQuery: " . $query);
	
		return($this->conn->affected_rows);
	}

        function update($table, $search = array(), $vars = array()) {
		// First put together our vars/cols to update
		$values = array();
                foreach($vars as $key => $value) {
			if($key == 'new_row') continue;
                        array_push($values, "`$key` = '" . $this->conn->real_escape_string($value) . "'");
                }

                $update = implode(", ", $values);

		// Then put together our search query
		$values = array();
                foreach($search as $key => $value) {
                        array_push($values, "`$key` = '" . $this->conn->real_escape_string($value) . "'");
                }

                $search = implode(" AND ", $values);

		$query = "UPDATE `$table` SET $update WHERE $search";

		// Then execute
		if(!$result = $this->conn->query($query))
                        error_log("MySQL Error: " . $this->conn->error . "\nQuery: " . $query);
        
                return($this->conn->affected_rows);
	}
}


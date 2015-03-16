<?php

// Our abstract database class on which we can build actual database implementations (MySQL, Postgres, Oracle, etc.)
abstract class Database {
	// Our internal connection object
	protected $conn = false;

	// A list of available servers to connect to (can be array to connect to randomly)
	protected $server_list = array();

	// Initialize our database
	abstract public function initialize($host, $user, $pass, $database);

	// Our available query functions
	abstract public function raw_query($query, $vars = array());
	abstract public function query($table, $vars = array(), $flags = array());
	abstract public function raw_array_query($query, $vars = array());
	abstract public function array_query($table, $vars = array(), $flags = array());
	abstract public function raw_object_query($query, $vars = array());
	abstract public function object_query($table, $vars = array(), $flags = array(), $object_tye = 'stdClass');
	abstract public function raw_object_array_query($query, $vars = array());
	abstract public function object_array_query($table, $vars = array(), $flags = array(), $object_type = 'stdClass');

	// Other database functions
	abstract public function insert($table, $vars);
	abstract public function delete($table, $vars);
	abstract public function update($table, $search, $vars);
}

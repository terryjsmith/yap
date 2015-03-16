<?php

// Our database factory instantiates our implementation-dependent database class
class DatabaseFactory {
	// Our singleton instance of the database
	static $instance = false;

	// Get the instance of the DB
	static function get($class = false) {
		if(self::$instance)
			return(self::$instance);

		self::$instance = new $class;
		self::$instance->initialize(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);
		return(self::$instance);
	}
};

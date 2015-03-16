<?php

class Example extends DatabaseObject {
	function __construct() {
		self::$primary_key = 'example_id';
	}
}

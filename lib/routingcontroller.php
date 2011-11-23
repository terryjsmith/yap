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

// THe core routing controller class, making pretty much the whole MVC thing possible
class RoutingController {
	// GET variables
	private $get_vars = array();

	// POST variables
	private $post_vars = array();

	// The controller and module being called
	private $controller;
	private $module;

	// Retrieve a GET variable if it exists, false otherwise
	function get_variable($var) {
		if(isset($this->get_vars[$var]))
			return $this->get_vars[$var];
		
		return(false);
	}

	// Retrieve a POST variable if it exists, false otherwise
	function post_variable($var) {
		if(isset($this->post_vars[$var]))
                        return $this->post_vars[$var];

                return(false);
	}

	// The core route function, verifies the existence of the controller, module function and calls them
	function route() {
		// Break apart the URL into usable parts
		$path = parse_url(
			(isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . 	// Scheme
			$_SERVER['HTTP_HOST'] . 					// Hostname
			$_SERVER['REQUEST_URI']						// Path and query string
		);

		parse_str(@$path['query'], $vars);

		// Initialize our request variables
		$this->get_vars = $vars;

		// Next insert the POST variables (these overwrite the equivalent GET variables)
		foreach($_POST as $key => $value)
			$this->post_vars[$key] = $value;

		// Get the controller and module
		$temp = explode("/", substr($path['path'], 1));
		$this->controller = (@$temp[0]) ? strtolower($temp[0]) : "index";
		$this->module = (@$temp[1]) ? strtolower($temp[1]) : "index";

		// Make sure the controller file exists
		if(!file_exists(CONTROLLER_DIR . "{$this->controller}Controller.php")) {
			if(!PRODSERV) {
				$file = CONTROLLER_DIR . "{$this->controller}Controller.php";
				die("Controller file does not exist ($file).");
			}
			else {
				$this->controller = "error";
				$this->module = "index";
			}
		}
		
		// Make sure the class exists
		if(!class_exists("{$this->controller}Controller")) {
			if(!PRODSERV)
				die("Controller class does not exist.");
			else {
				$this->controller = "error";
				$this->module = "index";
			}
		}

		// Make sure the handler function exists
		if(!method_exists("{$this->controller}Controller", "{$this->module}Handler")) {
			if(!PRODSERV)
				die("No handler function defined in {$this->controller}Controller.");
			else {
				$this->controller = "error";
				$this->module = "index";
			}
		}

		// Check if our view exists
                $controller = strtolower(substr($this->controller, 0, 1)) . substr($this->controller, 1);
                if(!file_exists(VIEW_DIR . "$controller/{$this->module}.php")) {
                        if(!PRODSERV) {
                                $file = VIEW_DIR . "$controller/{$this->module}.php";
                                die("View file does not exist ($file).");
                        }
			else {
				$this->controller = "error";
				$this->module = "index";
			}
                }

		// Finally, route away
		$class = $this->controller . "Controller";
		$controller = new $class($this->controller, $this->module);
		$method = "{$this->module}Handler";
		$controller->$method();

		// Render
		$controller->render();
	}
}


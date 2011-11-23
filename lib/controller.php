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

// Our core controller class, this represents the first part of the URL: /controller/module
class Controller {
	// The template we will render
	private $template;

	// Variables to be passed to the template
	protected $vars = array();

	// Internal variables telling us the controller name and module name for rendering later
	private $controller;
	private $module;

	// Initialize the controller (called from the RoutingController)
	function Controller($controller, $module) {
		// Create a new template
		$this->template = new Template;

		// Save our information
		$this->controller = $controller;
		$this->module = $module;

		// Make sure we pass this to the view if the user wants to reference it
		$this->template->set('controller', $controller);
		$this->template->set('module', $module);
	}

	// Return a GET variable if it exists
	function get($var) {
		global $routing_controller;
		return($routing_controller->get_variable($var));
	}

	// Return a POST variable if it exists
	function post($var) {
		global $routing_controller;
		return($routing_controller->post_variable($var));
	}

	// Return a COOKIE if it exists
	function cookie($var) {
		if(isset($_COOKIE[$var]))
			return($_COOKIE[$var]);

		return(false);
	}

	// Set variable to be sent to the view/template
	function set_var($var, $value) {
		$this->vars[$var] = $value;
	}

	// Change the layout from the default
	function set_layout($layout) {
		$this->template->set_layout($layout);
	}

	// Redirect the user to an external URL
	function redirect_external($location) {
		header("Location: $location");
	}

	// Redirect to an internal URL by controller, module and query params
	function redirect($controller, $module = 'index', $query = array()) {
		if(strcmp($module, "index"))
			$location = "/$controller/$module";
		else
			$location = "/$controller";

		if(sizeof($query)) {
			$location .= '?';
			foreach($query as $var => $value)
				$location .= urlencode($var) . '=' . urlencode($value) . '&';

			$location = substr($location, 0, strlen($location) - 1);
		}

		header("Location: $location");
	}

	// Render the view/template (called by RoutingController automatically)
	function render() {
		// Set our template variables first
		foreach($this->vars as $key => $value)
			$this->template->set($key, $value);

		// Render
		$this->controller = strtolower(substr($this->controller, 0, 1)) . substr($this->controller, 1);
		$this->template->render(VIEW_DIR . "/{$this->controller}/{$this->module}.php");
	}
}
